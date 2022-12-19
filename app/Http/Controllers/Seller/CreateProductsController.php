<?php

namespace App\Http\Controllers\Seller;

use App\Enums\Activity\Activities;
use App\Enums\Activity\ActivityType;
use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\Stock\ATransactionTypes;
use App\Enums\StoreTypes\StoreType;
use App\Events\Inventory\StockMovement;
use App\Events\Product\VisitProduct;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\CreateProductByBarcodeRequest;
use App\Http\Requests\Products\CreateRetailerProductRequestV2;
use App\Http\Requests\Products\CreateRetailerProductRequestV2Step2;
use App\Http\Requests\Products\CreateSupplierProductRequestV2;
use App\Http\Requests\Products\CreateSupplierProductRequestV2Step2;
use App\Lib\Helpers\Categories\CategoryHelper;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\Rate\RateHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\BarcodeProduct;
use App\Models\Category;
use App\Models\CategorySize;
use App\Models\Color;
use App\Models\PackingUnit;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\SellerFavorite;
use App\Models\Store;
use App\Models\User;
use App\Repositories\ActivitiesRepository;
use App\Repositories\BarCodeRepository;
use App\Repositories\ColorRepository;
use App\Repositories\CreateProductRepository;
use App\Repositories\PackingUnitRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductStoreRepository;
use App\Repositories\ProductStoreStockRepository;
use App\Repositories\StoreRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ReadyForShipmentRequest;
use App\Http\Resources\Seller\Products\ProductsSupplierStep1Resource;
use App\Models\Warehouse;
use App\Repositories\WarehousesRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CreateProductsController extends BaseController
{
    public $productsRepo;
    public $productStoreRepository;
    public $warehousesRepository;
    public $lang;

    public function __construct(ProductRepository $productRepository, ProductStoreRepository $productStoreRepository, WarehousesRepository $warehousesRepository, Request $request)
    {
        $this->productsRepo = $productRepository;
        $this->productStoreRepository = $productStoreRepository;
        $this->warehousesRepository = $warehousesRepository;
        $this->lang = LangHelper::getDefaultLang($request);
    }


    public function storeSupplierProductV2(CreateSupplierProductRequestV2 $request)
    {
        try {

            if (!CategoryHelper::checkCategoryLevel('sub_sub_category', $request->category_id)) {
                return $this->error(['message' => trans('messages.category.un_valid_parent')]);
            }
            $where = ['store_type_id' => StoreType::SUPPLIER];
            $store = StoreRepository::getStoreByUserId($request->user_id, $where);

            if (!$store) {
                return $this->error(['message' => trans('messages.product.seller_type_not_supplier')]);
            }

            $request->owner_id = $request->user_id;
            $request->channel = 'seller-app';
            $request->store_id = $store->id;

            $product = CreateProductRepository::createProductV2($request);
            if (!$product)
                return $this->notFound();
            return $this->created(['message' => trans('messages.product.created'), 'data' => new ProductsSupplierStep1Resource($product)]);

        } catch (\Exception $e) {
            Log::error('error in storeSupplierProductV2 of seller CreateProducts' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function storeSupplierProductV2Step2(CreateSupplierProductRequestV2Step2 $request)
    {
        try {
            DB::beginTransaction();
            $where = ['store_type_id' => StoreType::SUPPLIER];
            $store = StoreRepository::getStoreByUserId($request->user_id, $where);

            if (!$store)
                return $this->error(['message' => trans('messages.general.store_exists')]);

            $productStore = $this->productStoreRepository->getProductStore($request->product_id, $store->id);

            if (!$productStore)
                return $this->error(['message' => trans('messages.cart.product_not_found')]);

            $colorExists = ColorRepository::checkIfColorExists($productStore->id, $request->color_id);

            if ($colorExists)
                return $this->error(['message' => trans('messages.colors.exists')]);

            $packingUnitProduct = PackingUnitRepository::packingUnitProduct($request->product_id);
            $productAttrs = ProductRepository::productAttrs($packingUnitProduct->id);
            $total_packing_unit_count = 0;
            $total_basic_unit_count = 0;
            foreach ($productAttrs as $attr) {
                $total_basic_unit_count += $attr['quantity'];
            }
            $barcodeProduct = BarCodeRepository::createBarcodeProduct($request, $store->id);

            Product::where('id',$request->product_id)->update(['youtube_link']);

            if (!$barcodeProduct)
                return $this->error(['message' => trans('messages.product.barcode_used')]);

            foreach ($request->images as $image) {
                CreateProductRepository::createProductImage($image, $request);
                $total_packing_unit_count += $request->quantity;
            }
            foreach ($productAttrs as $productAttr) {
                $stock = $total_basic_unit_count * $request->quantity;
                ProductStoreStockRepository::createProductStoreStock($productStore->id, $productAttr, $request, $stock);
            }
            $totalStock = $total_basic_unit_count * $total_packing_unit_count;
            event(new StockMovement($totalStock, $request->product_id, ATransactionTypes::PRODUCT, $store->id));
            $data['ref_id'] = $request->product_id;
            $data['user_id'] = $request->seller_id;
            $data['action'] = Activities::CREATE_PRODUCT_S2;
            $data['type'] = ActivityType::PRODUCT;
            ActivitiesRepository::log($data);
            DB::commit();

            return $this->created(['message' => trans('messages.actions.product_added')]);
        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
            Log::error('error in storeSupplierProductV2Step2 of seller CreateProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeRetailerProductV2(CreateRetailerProductRequestV2 $request)
    {
        try {
            if (!$request->category_id || !CategoryHelper::checkCategoryLevel('sub_sub_category', $request->category_id)) {
                return $this->error(['message' => trans('messages.category.un_valid_parent')]);
            }

            $where = ['store_type_id' => StoreType::RETAILER];
            $store = StoreRepository::getStoreByUserId($request->user_id, $where);

            if (!$store)
                return $this->error(['message' => trans('messages.general.store_exists')]);

            $request->owner_id = $request->user_id;
            $request->channel = 'seller-app';
            $request->store_id = $store->id;

            $product = CreateProductRepository::createRetailerProductV2($request);

            if (!$product)
                return $this->error(['message' => trans('messages.general.error')]);

            return $this->created(['message' => trans('messages.product.created'), 'data' => $product]);

        } catch (\Exception $e) {
            Log::error('error in storeRetailerProductV2 of seller CreateProducts' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeRetailerProductV2Step2(CreateRetailerProductRequestV2Step2 $request)
    {
        try {
            DB::beginTransaction();

            $store = StoreRepository::getStoreByUserId($request->user_id, ['store_type_id' => StoreType::RETAILER]);

            if (!$store)
                return $this->error(['message' => trans('messages.general.store_exists')]);


            $productStore = $this->productStoreRepository->getProductStore($request->product_id, $store->id);

            Product::where('id',$request->product_id)->update(['youtube_link']);


            if (!$productStore)
                return $this->error(['message' => trans('messages.product.no_store')]);

            $colorExists = ColorRepository::checkIfColorExists($productStore->id, $request->color_id);

            if ($colorExists)
                return $this->error(['message' => trans('messages.colors.exists')]);

            $barcodeProduct = BarCodeRepository::createBarcodeProduct($request, $store->id);
            if (!$barcodeProduct)
                return $this->error(['message' => trans('messages.product.barcode_used')]);

            $product = Product::query()->where('id', $request->product_id)->
            where('owner_id', $request->user_id)->where('activation', true)->where('reviewed', false)->first();

            if (!$product)
                return $this->error(['message' => trans('messages.product.no_product')]);

            $stock = 0;
            foreach ($request->sizes as $size) {
                $stock += $size['quantity'];
                //TODO validate duplicate data like colors on stocks, images and barcodes
                // inserted before this step
                ProductStoreStockRepository::createProductStoreStock($productStore->id, $size, $request, $size['quantity']);
            }

            foreach ($request->images as $image) {
                ProductImage::create([
                    'image' => UploadImage::uploadImageToStorage($image, 'products/' . $request->product_id),
                    'color_id' => $request->color_id,
                    'product_id' => $request->product_id
                ]);
            }

            event(new StockMovement($stock, $request->product_id, ATransactionTypes::PRODUCT, $store->id));
            DB::commit();
            return $this->created(['message' => trans('messages.product.created')]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in storeRetailerProductV2Step2 of seller CreateProducts' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function findProductByBarcode(Request $request): \Illuminate\Http\JsonResponse
    {
        try {

            $productId = BarcodeProduct::query()->where('barcode', $request['barcode'])->first()->product_id ?? null;

            if (!$productId)
                return $this->error(['message' => trans('messages.product.wrong_barcode')]);

            $product =
                Product::query()
                    ->select([
                        'products.id',
                        'products.name as product_name',
                        'products.description',
                        'products.material_id',
                        'products.consumer_price',
                        'products.material_rate',
                        "materials.name_$this->lang as material_name",
                        'products.owner_id',
                        'brands.id as brand_id',
                        "brands.name_$this->lang as brand_name",
                        'categories.id as category_id',
                        'categories.name_' . $this->lang . ' as category_name',
                    ])
                    ->join('categories', 'categories.id', '=', 'products.category_id')
                    ->leftJoin('materials', 'products.material_id', '=', 'materials.id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                    ->where('products.reviewed', true)
                    ->find(4);

            if (!$product)
                return $this->error(['message' => trans('messages.product.product_under_review')]);

            $productColors = ProductImage::query()->select(['color_id', "colors.name_$this->lang as color_name", 'image'])
                ->join('colors', 'colors.id', '=', 'product_images.color_id')
                ->where('product_id', $productId)->groupBy('name_ar', 'color_id', 'image')->get();

            $colors = [];
            $newArray = [];

            foreach ($productColors->groupBy('color_id') as $color => $productColor) {
                for ($i = 0; $i < count($productColor); $i++) {
                    $newArray[$i]['color_id'] = $color;
                    $newArray[$i]['color_name'] = $productColor[$i]['color_name'];
                    if ($color == $productColor[$i]['color_id']) {
                        $newArray[$i]['images'][] = config('filesystems.aws_base_url') . $productColor[$i]['image'];
                    }
                }
                $colors[] = $productColor[0];

            }
            foreach ($colors as $color) {
                $colorOfRow = Color::query()->select('name_' . $this->lang . ' as name')->where('id', $color->color_id)->first();
                $color->color_name = $colorOfRow->name;
                $color->image = config('filesystems.aws_base_url') . $color->image;
            }

            $product->images_colors_list = $newArray;
            $product->deafult_colors = $colors;

            $sizes = CategorySize::query()
                ->select(['sizes.size', 'sizes.id as size_id'])
                ->join('sizes', 'sizes.id', '=', 'category_size.size_id')
                ->where('category_id', $product->category_id)
                ->get();

            $product->sizes = $sizes;

            return $this->success(['message' => trans('messages.general.listed'), 'data' => $product]);

        } catch (\Exception $e) {
            Log::error('error in get Product By Barcode' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function cloneProduct(CreateProductByBarcodeRequest $request)
    {
        try {

            $productStore = ProductStore::query()->with('productStoreStock')->where('barcode_text', $request->barcode)->first();


            $product = Product::query()->find($productStore['product_id']);


            CreateProductRepository::cloneProduct($request, $product, $productStore->productStoreStock);

            return $this->created(['message' => trans('messages.product.created')]);

        } catch (\Exception $e) {
            return $e;
            Log::error('error in Create Product By Barcode' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getProductAttributes($productId)
    {
        try {
            $product = Product::query()->find($productId);
            if (!$product) {
                return $this->notFound();
            }
            $packingUnitProduct = PackingUnitProduct::query()
                ->select('basic_unit_count', 'id')
                ->where('product_id', $productId)
                ->where('basic_unit_count', '!=', 1)
                ->first();
            if (!$packingUnitProduct) {
                return $this->error(['message' => trans('messages.general.error')]);
            }
            $attributes = PackingUnitProductAttribute::query()
                ->select('sizes.size', 'quantity')
                ->join('sizes', 'sizes.id', '=', 'packing_unit_product_attributes.size_id')
                ->where('packing_unit_product_id', $packingUnitProduct->id)
                ->get();
            $data = [
                'total_count' => $packingUnitProduct->basic_unit_count,
                'attributes' => $attributes
            ];
            return $this->success([
                'message' => trans('messages.general.success'),
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('error in Create Product By Barcode' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Change products to a system warehouse for shipping
     *
     * @param ReadyForShipmentRequest $request
     * @return Response
     */
    public function ReadyForShip(ReadyForShipmentRequest $request)
    {
        try {
            $warehouse = Warehouse::findOrFail($request->warehouse_id);

            if (!$warehouse)
                return $this->error(['message' => trans('messages.warehouse.not_found')]);

            if ($warehouse->user_id != null)
                return $this->error(['message' => trans('messages.warehouse.not_owner')]);

            $data = $this->warehousesRepository->add_products($request->validated());

            return $this->success([
                'success' => true,
                'message' => 'WareHouses',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('error in relating products to warehouse' . __LINE__ . $e);
            dd($e->getMessage());
            return $this->connectionError($e);
        }
    }
}
