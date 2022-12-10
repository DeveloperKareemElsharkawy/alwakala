<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Enums\UserTypes\UserType;
use App\Events\Logs\DashboardLogs;
use App\Events\Product\ReviewProduct;
use App\Exports\Dashboard\ProductsExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Images\UploadImageRequest;
use App\Http\Requests\Products\CreateProductRequest;
use App\Http\Requests\Products\DeleteProductRequest;
use App\Http\Requests\Products\SyncProductBadgesRequests;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Rate\RateHelper;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\BadgeProduct;
use App\Models\BarcodeProduct;
use App\Models\Bundle;
use App\Models\Offer;
use App\Models\OrderProduct;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductRate;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\StockMovement;
use App\Models\User;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\Products\ProductsGetInfoResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class ProductsController
 *  to add product
 *
 */
class ProductsController extends BaseController
{

    private $lang;
    protected $repo;
    protected $rateHelper;

    const REVIEWED = 1;
    const NON_REVIEWED = 2;
    const NON_COMPLETED = 3;

    public function __construct(Request $request, ProductRepository $repo, RateHelper $rateHelper)
    {
        $this->repo = $repo;
        $this->lang = LangHelper::getDefaultLang($request);
        $this->rateHelper = $rateHelper;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:1,2,3',
                'type' => 'required|in:1,2',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $offset = $request->query('offset') ? $request->query('offset') : 0;
            $limit = $request->query('limit') ? $request->query('limit') : config('dashboard.pagination_limit');
            // $column='products.description';
            // if ($request->status != self::NON_COMPLETED) {
            //     $column = 'product_store_stock.available_stock as stock';
            // }

            $query = Product::query()
                ->select('products.id',
                    'products.name',
                    'products.category_id',
                    // $column,
                    'products.brand_id',
                    'products.owner_id',
                    'products.channel',
                    'products.consumer_price',
                    'products.reviewed',
                    'products.material_rate',
                    'products.created_at',
                    'products.policy_id',
                    'products.shipping_method_id',
                    "materials.name_$this->lang as material_name",
                    "policies.name_$this->lang as policy_name",
                    "shipping_methods.name_$this->lang as shipping_method_name"
//                    DB::raw('SUM(product_store_stock.stock) as stock'),
//                    DB::raw('SUM(product_store_stock.available_stock) as available_stock'),
//                    DB::raw('SUM(product_store_stock.reserved_stock) as reserved_stock'),
//                    DB::raw('SUM(product_store_stock.sold) as sold')
                )
                ->leftJoin('materials', 'products.material_id', '=', 'materials.id')
                ->leftJoin('policies', 'products.policy_id', '=', 'policies.id')
                ->leftJoin('shipping_methods', 'products.shipping_method_id', '=', 'shipping_methods.id')
                ->leftJoin('product_store', 'product_store.product_id', '=', 'products.id')
                // ->leftJoin('product_store_stock', 'product_store_stock.product_store_id', '=', 'product_store.id')
                ->leftJoin('stores', 'products.owner_id', '=', 'stores.user_id')
                ->orderBy('products.updated_at', 'desc');
            if ($request->type==StoreType::SUPPLIER) {
                $query->where('stores.store_type_id', StoreType::SUPPLIER);
            }
            if ($request->type==StoreType::RETAILER) {
                $query->where('stores.store_type_id', StoreType::RETAILER);
            }
            if ($request->status == self::NON_COMPLETED) {
                //->leftJoin('product_store', 'product_store.product_id', '=', 'products.id')
                   // ->leftJoin('product_store_stock', 'product_store.id', '=', 'product_store_stock.product_store_id')
                $query->whereNull('product_store_stock.product_store_id');

            } elseif ($request->status == self::REVIEWED) {
                $query->where('reviewed', true);
            } elseif ($request->status == self::NON_REVIEWED) {
                $query->where('reviewed', false);
                  //  ->leftJoin('product_store', 'product_store.product_id', '=', 'products.id')
                   // ->leftJoin('product_store_stock', 'product_store.id', '=', 'product_store_stock.product_store_id')
                    // ->groupBy(['products.id', 'materials.name_ar', 'materials.name_en','product_store_stock.available_stock',"policies.name_$this->lang"])
                    // ->whereNotNull('product_store_stock.product_store_id');
            }
            $query->with(['category', 'brand', 'owner','policy','shippingMethod']);
//                ->leftJoin('product_store', 'product_store.product_id', '=', 'products.id')
//                ->leftJoin('stores', 'product_store.store_id', '=', 'stores.id')
//                ->leftJoin('product_store_stock', 'product_store.id', '=', 'product_store_stock.product_store_id')


            if ($request->filled("id")) {
                $query->where('products.id', intval($request->id));
            }
            if ($request->filled("name")) {
                $searchQuery = "%" . $request->get("name") . "%";
                $query->where('products.name', "ilike", $searchQuery);
            }
            if ($request->filled('category')) {
                $query->where('products.category_id', intval($request->category));
            }
            if ($request->filled('brand')) {
                $query->where('products.brand_id', intval($request->brand));
            }
            if ($request->filled('owner')) {
                $query->where('products.owner_id', intval($request->owner));
            }
            if ($request->filled('policy')) {
                $query->where('products.policy_id', intval($request->policy));
            }
            if ($request->filled('shipping_method')) {
                $query->where('products.shipping_method_id', intval($request->shipping_method));
            }


            if ($request->filled("sort_by_id")) {
                $query->orderBy('products.id', $request->sort_by_id);
            }
            if ($request->filled("sort_by_name")) {
                $query->orderBy('products.name', $request->sort_by_name);
            }
            if ($request->filled('sort_by_category')) {
                $query->orderBy('products.category_id', $request->sort_by_category);
            }
            if ($request->filled('sort_by_brand')) {
                $query->orderBy('products.brand_id', $request->sort_by_brand);
            }
            if ($request->filled('sort_by_owner')) {
                $query->orderBy('products.owner_id', $request->sort_by_owner);
            }
            if ($request->filled('channel')) {
                $query->where('products.channel', $request->channel);
            }
            if ($request->filled('consumer_price')) {
                $query->where('products.consumer_price', $request->consumer_price);
            }
            $count = $query->get()->count();

            $products = $query->offset($offset)->limit($limit)->get();

            return response()->json([
                'success' => true,
                'message' => 'Products',
                'data' => $products,
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'total' => $count,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param $productId
     * @return JsonResponse
     */
    public function showInfo($productId)
    {
        try {
            $product = Product::query()
                ->where('products.id', $productId)
                ->first();
            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'no product found',
                    'data' => ''
                ], AResponseStatusCode::SUCCESS);
            }
            return response()->json([
                'status' => true,
                'message' => 'Product',
                'data' => new ProductsGetInfoResource($product),
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in showInfo of dashboard products' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    /**
     * @param $productId
     * @param $storeId
     * @return JsonResponse
     */
    public function showStock($productId, $storeId)
    {
        try {
            $productStock = ProductStoreStock::query()
                ->with(['color', 'size', 'product_store'])
                ->select(
                    'product_store_stock.id',
                    'product_store_stock.stock',
                    'product_store_stock.reserved_stock',
                    'product_store_stock.available_stock',
                    'product_store_stock.sold',
                    'product_store_stock.returned',
                    'product_store_stock.size_id',
                    'product_store_stock.color_id',
                    'product_store_stock.product_store_id'
                )
                // ->join('product_store', 'product_store.id', '=', 'product_store_stock.product_store_id')
                // ->where('product_store.product_id', $productId)
                // ->where('product_store.store_id', $storeId)
                ->whereHas('product_store', function ($q)use ($productId, $storeId) {
                    $q->where('product_id',$productId);
                    $q->where('store_id',$storeId);
                })
                ->get();

            if (!count($productStock)) {
                return response()->json([
                    'status' => false,
                    'message' => 'data found',
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            return response()->json([
                'success' => true,
                'message' => 'Product',
                'data' => $productStock
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in showStock of dashboard products' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }

    }

    /**
     * @param $productId
     * @param $storeId
     * @return JsonResponse
     */
    public function showStoreDetails($productId)
    {
        try {
            $productStore = ProductStore::query()
                ->select('product_store.id',
                    'product_store.publish_app_at',
                    'product_store.views',
                    'product_store.price',
                    'product_store.net_price',
                    // 'product_store.show_to_consumer',
                    'product_store.discount',
                    'product_store.net_price',
                    'product_store.discount_type',
                    'product_store.free_shipping',
                    'stores.id as store_id',
                    'stores.name as store_name'
                )
                ->join('stores', 'product_store.store_id', '=', 'stores.id')
                ->where('product_id', $productId)
                ->get();
            foreach ($productStore as $product) {
                $product->discount_type_id = (int)$product->discount_type;
                if ($product->discount_type == DiscountTypes::AMOUNT) {
                    $product->discount_type = 'AMOUNT';
                } else {
                    $product->discount_type = 'PERCENTAGE';
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'store details',
                'data' => $productStore
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in showStoreDetails of dashboard products' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }

    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function showBarcodesImages($id)
    {
        try {
            $attributes = BarcodeProduct::query()
                ->select('id', 'barcode', 'color_id')
                ->with(['color' => function ($q) {
                    $q->select('id', 'name_' . $this->lang . ' as name', 'hex');
                }])
                ->with(['images' => function ($q) use ($id) {
                    $q->where('product_id', $id);
                }])
                ->where('product_id', $id)
                ->get();

            foreach ($attributes as $attribute) {
                foreach ($attribute->images as $attr) {
                    $attr->image = config('filesystems.aws_base_url') . $attr->image;
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Images',
                'data' => $attributes
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in showBarcodesImages of dashboard products' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    /**
     * @param $productId
     * @param $storeId
     * @return JsonResponse
     */
    public function showBundles($productId, $storeId)
    {
        try {
            $productBundle = Bundle::query()
                ->select('id', 'product_id', 'quantity', 'price')
                ->where('product_id', $productId)
                ->where('store_id', $storeId)
                ->get();

            if (!count($productBundle)) {
                return response()->json([
                    'status' => false,
                    'message' => 'no Bundle found',
                    'data' => ''
                ], AResponseStatusCode::SUCCESS);
            }

            return response()->json([
                'success' => true,
                'message' => 'Images',
                'data' => $productBundle
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in showBundles of dashboard products' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function getOwnersForSelection()
    {
        try {
            $owners = User::select('name', 'id')
                ->where('type_id', UserType::SELLER)->get();
            return response()->json([
                'status' => true,
                'message' => 'owners retrieved successfully',
                'data' => $owners
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getOwnersForSelection of dashboard products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getProductDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $productDetails = Product::query()->where('products.id', (int)$request->product_id)
                ->with(['category' => function ($query) {
                    $query->select('id', 'name_en', 'name_ar', 'activation', 'description');
                }])->with(['brand' => function ($query) {
                    $query->select('id', 'name_'.$this->lang, 'activation');
                }])->with(['owner' => function ($query) {
                    $query->select('id', 'name', 'email', 'mobile', 'activation');
                }])->with(['packingUnit' => function ($query) {
                    $query->select('packing_units.id', 'name_en', 'name_ar');
                }])->with(['packingUnitAttributes' => function ($query) {
                    $query->join('sizes', 'packing_unit_product_attributes.size_id', '=', 'sizes.id')
                    ->join('packing_unit_product', 'packing_unit_product.id', '=', 'packing_unit_product_attributes.packing_unit_product_id')
                     ->select('packing_unit_product_attributes.id', 'quantity', 'sizes.size', 'packing_unit_product_id');
                }])
                ->with(['bundles' => function ($query) {
                    $query->select('bundles.id', 'quantity', 'price', 'product_id');
                }])->with(['barcodes' => function ($query) {
                    $query->select('id', 'barcode', 'color_id', 'product_id');
                }])
                ->with(['images' => function ($query) {
                    $query->select('id', 'image', 'product_id');
                }])
                ->select('products.id', 'name_en', 'name_ar', 'description', 'channel', 'activation', 'category_id', 'brand_id', 'owner_id', 'reviewed', 'products.material_rate',
                    "materials.name_$this->lang as material_name")
                ->leftJoin('materials', 'products.material_id', '=', 'materials.id')
                ->first();

            foreach ($productDetails->images as $image) {
                $image->image = config('filesystems.aws_base_url') . $image->image;
            }

            return response()->json([
                'message' => '',
                'data' => $productDetails
            ], AResponseStatusCode::SUCCESS);


        } catch (\Exception $e) {
            dd($e);
            Log::error('error in getProductDetails of dashboard products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function approvePendingProduct(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            // $productStore = ProductStore::query()->where('product_id', $request->product_id)->first();
            // $productStoreStock = ProductStoreStock::query()->where('product_store_id', $productStore->id)->first();
            // if (empty($productStoreStock)) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => trans('messages.product.cant_reviewed'),
            //         'data' => ''
            //     ], AResponseStatusCode::BAD_REQUEST);
            // }
            $product = Product::query()->where('id', $request->product_id)->first();
            $this->repo->approveProduct($product);
            // $product_image = ProductImage::query()->where('product_id', $request->product_id)->first();
            $logData['id'] = $product->id;
            $logData['ref_name_ar'] = $product->name;
            $logData['ref_name_en'] = $product->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::APPROVE_PRODUCT;
            event(new DashboardLogs($logData, 'products'));
            // event(new ReviewProduct([$product->owner_id], $request->product_id, $product_image->image));

            return response()->json([
                'status' => true,
                'message' => trans('messages.product.reviewed'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in approvePendingProduct of dashboard products' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function getProductsForSelection(Request $request)
    {
        try {
            $query = Product::query()
                ->select(['id', 'name']);
            $query->where('activation', true);
            $query->where('reviewed', true);
            if ($request->filled('category')) {
                $query->whereIn('category_id', explode(',', $request->query('category')));
            }
            if ($request->filled('name')) {
                $query->where('name', "like", "%" . $request->query('name') . "%");
            }
            $products = $query->get();
            return response()->json([
                'message' => '',
                'data' => $products
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getProductsForSelection of dashboard products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function syncBadges(SyncProductBadgesRequests $request)
    {
        try {
            $this->repo->syncBadges($request->validated());
            return response()->json([
                'message' => 'badges',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in Export Products in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    public function export(Request $request)
    {
        try {
            return Excel::download(new ProductsExport($request), 'products.xlsx');
        } catch (\Exception $e) {
            Log::error('error in Export Products in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * View all images in product.
     * @param Request $request
     * @return JsonResponse
     */
    public function viewGallery(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
        ]);

        if ($validator->fails()) {
            return ValidationError::handle($validator);
        }
        $images = ProductImage::query()->where('product_id', $request->product_id)->get();
        $productsImages = [];
        foreach ($images as $image) {
            $productsImages[$image->id] = config('filesystems.aws_base_url') . $image->image;
        }

        return response()->json([
            'message' => 'products images',
            'data' => $productsImages
        ], AResponseStatusCode::SUCCESS);
    }

    /**
     * Delete image in product.
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteImage(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'image_id' => 'required|integer|exists:product_images,id',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $image = ProductImage::query()->where('id', $request->image_id)->first();
            Storage::disk('s3')->delete($image->image);
            $image->delete();
            return response()->json([
                'message' => 'delete product image',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in delete Product image in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    /**
     * Highlight product.
     * @param Request $request
     * @return JsonResponse
     */
    public function highlightProduct(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer|exists:products,id',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $product = Product::query()->where('id', $request->product_id)->first();
            if ($product->highlight) {
                $product->highlight = false;
            } else {
                $product->highlight = true;
            }

            return response()->json([
                'message' => 'product highlight',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in Product highlight in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Make image as primary image to use it in thumbnail.
     * @param Request $request
     * @return JsonResponse
     */
    public function makeImagePrimary(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'image_id' => 'required|integer|exists:product_images,id',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $image = ProductImage::query()->where('id', $request->image_id)->first();
            if ($image->is_primary) {
                $image->is_primary = false;
            } else {
                $image->is_primary = true;
            }
            return response()->json([
                'message' => 'product image was made primary',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in delete Product image in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Upload product image.
     * @param UploadImageRequest $request
     * @return JsonResponse
     */
    public function uploadProductImage(UploadImageRequest $request): JsonResponse
    {
        try {
            $image = new ProductImage();
            $image->product_id = $request->product_id;
            $image->color_id = $request->color_id;
            $image->image = UploadImage::uploadImageToStorage($request->image, 'products/' . $request->product_id);
            $image->save();
            return response()->json([
                'message' => 'upload product image',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        }
        catch (\Exception $e) {
            Log::error('error in upload Product image in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function deleteProduct(Request $request){

        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:products,id',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            DB::beginTransaction();
            PackingUnitProduct::query()->where('product_id',$request->id)->delete();
            BadgeProduct::query()->where('product_id',$request->id)->delete();
            OrderProduct::query()->where('product_id',$request->id)->delete();
            $unit= PackingUnitProduct::query()->where('product_id',$request->id)->first();
            if($unit) {
                PackingUnitProductAttribute::query()->where('packing_unit_product_id', $unit->id)->delete();
                $unit->delete();
            }
            BarcodeProduct::query()->where('product_id',$request->id)->delete();
            ProductRate::query()->where('product_id',$request->id)->delete();
            $images= ProductImage::query()->where('product_id',$request->id)->get();
            foreach ($images as $image){
                Storage::disk('s3')->delete($image->image);
                $image->delete();
            }
            StockMovement::query()->where('product_id',$request->id)->delete();
            $storeStock= ProductStore::query()->where('product_id',$request->id)->first();
            if($storeStock){
            ProductStoreStock::query()->where('product_store_id',$storeStock->id)->delete();
            $storeStock->delete();
                }
            Product::query()->where('id',$request->id)->delete();

            DB::commit();
            return response()->json([
                'message' => 'product deleted',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        }
        catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in delete Product in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
