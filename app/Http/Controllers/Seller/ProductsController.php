<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Product\VisitProduct;
use App\Events\Store\FavoriteProduct;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\GetProductRequest;
use App\Http\Requests\Products\GetProductsRequest;
use App\Http\Resources\Seller\Store\SellerRateCollection;
use App\Http\Resources\Seller\Store\SellerRateResource;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Pagination\PaginationHelper;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\Rate\RateHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\BarcodeProduct;
use App\Models\Color;
use App\Models\PackingUnit;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\SellerFavorite;
use App\Models\SellerRate;
use App\Models\Store;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;


class ProductsController extends BaseController
{

    public $productsRepo;
    public $storesRepo;
    private $lang;
    protected $rateHelper;

    public function __construct(ProductRepository $productRepository, StoreRepository $storeRepository, Request $request, RateHelper $rateHelper)
    {
        $this->productsRepo = $productRepository;
        $this->storesRepo = $storeRepository;
        $this->lang = LangHelper::getDefaultLang($request);
        $this->rateHelper = $rateHelper;
    }

    public function getProduct(GetProductRequest $request)
    {
        try {
            $storeType = null;
            $userId = UserId::UserId($request);
            if ($userId) {
                $store = Store::query()->where('user_id', $userId)
                    ->first();
                $storeType = is_null($store) ? null : $store->store_type_id;
            }
            $product = Product::query()
                ->where('id', $request->product_id)
                ->where('reviewed', true)
                ->first();

            if (!$product) {
                return $this->error(['message' => trans('messages.product.product_under_review')]);
            }

            $productStore =
                ProductStore::query()
                    ->select([
                        'products.id as product_id',
                        'products.name as product_name',
                        'products.description',
                        'product_store_stock.available_stock',
                        'product_store_stock.reserved_stock',
                        'product_store_stock.sold',
                        'product_store.publish_app_at',
                        'product_store.barcode',
                        'product_store.price',
                        'product_store.discount',
                        'product_store.net_price',
                        'product_store.id',
                        'stores.address as store_address',
                        'stores.id as store_id',
                        'stores.latitude',
                        'stores.longitude',
//                        'stores.delivery_days',
                        'stores.store_type_id',
//                        'stores.delivery_hours',
                        'product_store.free_shipping',
                        'products.material_rate',
                        "materials.name_$this->lang as material_name",
                        'stores.name as store_name',
                        "brands.name_$this->lang as brand_name",
                        'brands.id as brand_id',
                        "policies.name_$this->lang as policy_name",
                        'policies.id as policy_id',
                        "shipping_methods.name_$this->lang as shipping_method_name",
                        'shipping_methods.id as shipping_method_id',
                        'categories.id  as category_id',
                        'categories.name_' . $this->lang . ' as category_name',
                        DB::raw("(SELECT CASE WHEN SUM(seller_rates.rate) / COUNT(seller_rates.rate) IS NOT NULL THEN CAST( CAST(SUM(seller_rates.rate) AS FLOAT) / CAST( COUNT(seller_rates.rate) AS FLOAT)  AS FLOAT)
                                        ELSE 0 END  as rate FROM seller_rates WHERE seller_rates.rated_id = products.id and rated_type = '" . Product::class . "')"),
                        DB::raw("(SELECT COUNT(seller_rates.review) as comments FROM seller_rates WHERE seller_rates.rated_id = products.id and rated_type = '" . Product::class . "')"),
                        DB::raw("(SELECT COUNT(*) AS fav_count FROM seller_favorites WHERE seller_favorites.favorited_type = '" . Product::class . "' AND seller_favorites.favorited_id = '" . $request->product_id . "')"),
                    ])
                    ->join('products', 'products.id', '=', 'product_store.product_id')
                    ->join('product_store_stock', 'product_store.id', '=', 'product_store_stock.product_store_id')
                    ->join('categories', 'categories.id', '=', 'products.category_id')
                    ->join('stores', 'stores.id', '=', 'product_store.store_id')
                    ->leftJoin('materials', 'products.material_id', '=', 'materials.id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                    ->leftJoin('policies', 'policies.id', '=', 'products.policy_id')
                    ->leftJoin('shipping_methods', 'shipping_methods.id', '=', 'products.shipping_method_id')
                    ->where('products.reviewed', true)
                    ->where('product_store.product_id', $request->product_id)
                    ->where('product_store.store_id', $request->store_id)
                    ->first();

            if (!$productStore) {
                return $this->error(['message' => trans('messages.product.product_not_found')]);
            }

            $isActive = false;
            if ($userId) {
                $isActive = User::query()
                    ->select('activation')
                    ->where('id', $userId)
                    ->first()->activation;
            }
            $productStore->price = ProductHelper::canShowPrice($userId, $isActive, $productStore->price);
            $productStore->net_price = ProductHelper::canShowPrice($userId, $isActive, $productStore->net_price);
            if ($productStore->discount != 0 && $userId && $isActive) {
                $product->has_discount = true;
                $productStore->discount_type = 'percentage';
                $productStore->discount = $productStore->discount . '%';
            } else {
                $productStore->has_discount = false;
            }

            $rates = SellerRate::query()->where('rated_store_id', $productStore->store_id)
                ->where('rated_id', $productStore->product_id)->where('rated_type', Product::class)
                ->with('Rater')
                ->limit(3)->get();

            $productStore->rates = SellerRateResource::collection($rates);

            $productStore->store_avg_rate = $this->rateHelper->getAverageRate($productStore->store_id, Store::class);

            $productStore->store_logo = null;

            if ($productStore->store->logo) {
                $productStore->store_logo = config('filesystems.aws_base_url') . $productStore->store->logo;
            }

            if ($productStore->barcode) {
                $productStore->barcode = config('filesystems.aws_base_url') . $productStore->barcode;
            }

            if ($userId) {
                $isFav = SellerFavorite::query()
                    ->where('favoriter_type', User::class)
                    ->where('favorited_id', $request->product_id)
                    ->where('favorited_type', Product::class)
                    ->where('favoriter_id', $userId)
                    ->first();
                if ($isFav) {
                    $productStore->is_favourit = true;
                } else {
                    $productStore->is_favourit = false;
                }
            } else {
                $productStore->is_favourit = false;
            }

            if (is_null($productStore)) {
                return response()->json([
                    'status' => false,
                    'messages' => trans('messages.product.product_not_found'),
                    'data' => [],
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $productColors = ProductImage::query()
                ->select(['color_id', 'image'])
                ->where('product_id', $productStore->product_id)
                ->get()->groupBy('color_id');

            $allImages = [];
            $colors = [];
            $newArray = [];
            $productColorsLoop = 0;
            $productAvailableStatus = true;
            foreach ($productColors as $color => $productColor) {

                $productColorImages = [];
                $colorOfRow = Color::query()->select('name_' . $this->lang . ' as name')->where('id', $color)->first();
                $newArray[$productColorsLoop]['color_name'] = $colorOfRow->name;
                $newArray[$productColorsLoop]['color_id'] = $color;
                foreach ($productColor as $productColorImage) {
                    $productColorImages[] = config('filesystems.aws_base_url') . $productColorImage['image'];
                }
                $newArray[$productColorsLoop]['images'] = $productColorImages;

                $colors[] = $productColor[0];
                $images = [];
                foreach ($productColor as $colorImage) {
                    $allImages[] = config('filesystems.aws_base_url') . $productColor[0]->image;
                    $images[] = config('filesystems.aws_base_url') . $colorImage->image;
                    unset($productColor->image);
                    unset($productColor->color);
                }
                $productColor = $images;
                $productColors[$color] = $productColor;

                $productColorsLoop++;
            }

            $defaultColor = [];
            foreach ($newArray as $color) {
                $colorOfRow = Color::query()->select('name_' . $this->lang . ' as name')->where('id', $color['color_id'])->first();
                $defaultColor['color_name'] = $colorOfRow->name;
                $defaultColor['color_id'] = $color['color_id'];
                $defaultColor['default_image'] = count($color['images']) ? $color['images'][0] : null;
                $defaultColor['images'] = $color['images'];
                $barcodeProduct = BarcodeProduct::query()->where([['color_id', $color['color_id']], ['product_id', $productStore['product_id']]])->first();
                $defaultColor['barcode'] = $barcodeProduct->barcode;
            }

            $productStore->images = $newArray;
            $productStore->default_color = $defaultColor;
            $sizes = ProductStoreStock::query()
                ->select(['sizes.size', 'sizes.id as size_id', 'product_store_stock.available_stock'])
                ->join('sizes', 'sizes.id', '=', 'product_store_stock.size_id')
                ->where('product_store_stock.product_store_id', $productStore->id)
                ->distinct('product_store_stock.size_id')
                ->get();
            $packingUnits = PackingUnitProduct::query()
                ->select([
                    DB::raw("packing_unit_product.basic_unit_count packing_unit_name"),
                    'packing_unit_product.packing_unit_id as packing_unit_id',
                    'packing_unit_product.basic_unit_count as basic_unit_count',
                    'packing_unit_product.basic_unit_id as basic_unit_id',
                ])
                ->where('packing_unit_product.basic_unit_count', '>', 1)
                ->where('product_id', '=', $request->product_id)
                ->join('packing_units', 'packing_units.id', '=', 'packing_unit_product.packing_unit_id')
                ->get();

            $packingUnitProductId = PackingUnitProduct::query()
                ->where('product_id', $request->product_id)
                ->where('packing_unit_id', 1)
                ->first();

            if (count($packingUnits)) {
                $basic_unit = PackingUnit::query()->find($packingUnits[0]->basic_unit_id);

                $packingUnitAttrs = PackingUnitProductAttribute::query()
                    ->join('sizes', 'sizes.id', '=', 'packing_unit_product_attributes.size_id')
                    ->where('packing_unit_product_id', $packingUnitProductId->id)
                    ->get();


                foreach ($packingUnits as $packingUnit) {
                    if ($packingUnit->basic_unit_count != 1) {

                        $packingUnit->packing_unit_name = $packingUnit->packing_unit_name . ' ' . $basic_unit->name_ . $this->lang;
                        foreach ($packingUnitAttrs as $packingUnitAttr) {
                            $packingUnit->sizes .= $packingUnitAttr->size . ' -';
                        }
                    } else {
                        $packingUnit->packing_unit_name = $packingUnit->packing_unit_name . ' ' . $basic_unit->name_ . $this->lang;
                    }
                }


                foreach ($sizes as $size) {
                    $packingUnitSize = $packingUnitAttrs->where('size_id', $size->size_id)->first();
                    $size->unit_quantity = 0;
                    if ($packingUnitSize) {
                        $size->unit_quantity = $packingUnitSize->quantity;
                        $size->is_available = $packingUnitSize->quantity < $size->available_stock;
                    }
                    if ($packingUnitSize && $packingUnitSize->quantity > $size->available_stock) {
                        $productAvailableStatus = false;
                    }
                }
            }
            $productStore->sizes = $sizes;
            $productStore->is_available = $productAvailableStatus;

            $productStore->packing_units = $packingUnits;
            $unit_details_arr = [];
            $unit_details = PackingUnitProduct::query()->where('product_id', $request->product_id)->first();
            foreach ($unit_details->attributes as $unit) {
                $unit_details_arr[] = [
                    'unit_id' => $unit_details->id,
                    'attribute_id' => $unit->id,
                    'size' => $unit->size->size,
                    'quantity' => $unit->quantity
                ];
            }
            $productStore->unit_details = $unit_details_arr;

//            $bundles = Bundle::query()->select(['quantity', 'price'])
//                ->where('product_id', $productStore->product_id)
//                ->orderBy('quantity', 'asc')
//                ->get();
//            $prevQuantity = '0';
//            foreach ($bundles as $bundle) {
//                if ($bundle->quantity >= 1000000) {
//                    $prevQuantity = '';
//                } else {
//                    $prevQuantity = $prevQuantity + 1;
//                }
//                $bundle->quntity_range = $prevQuantity . ' - ' . $bundle->quantity;
//                if ($bundle->quantity >= 1000000) {
//                    $bundle->quntity_range = '';
//                }
//                $prevQuantity = $bundle->quantity;
//                unset($bundle->quantity);
//            }
//            $productStore->price_range = $bundles;

            foreach ($productStore->packing_units as $packing_unit) {
                if ($packing_unit->basic_unit_count == 1) {
                    $packing_unit->is_basic_unit = true;
                } else {
                    $packing_unit->is_basic_unit = false;
                }
            }
            $productStore->images_slider = $allImages;
            $productStore->status = ProductHelper::productStatus($productStore->available_stock, $productStore->publish_app_at, true);

            event(new VisitProduct($request, $userId, $request->product_id));

            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $productStore,
            ], AResponseStatusCode::SUCCESS);

        } catch (Exception $e) {
            Log::error('error in getProduct of seller products' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }


    public function getProductByBarcode(Request $request)
    {
        try {
            $storeType = null;
            $userId = UserId::UserId($request);
            if ($userId) {
                $store = Store::query()->where('user_id', $userId)
                    ->first();
                $storeType = is_null($store) ? null : $store->store_type_id;
            }

            $productStore = ProductStore::query()->where('barcode_text', $request->barcode)->first();

            if (!$productStore) {
                return $this->error(['message' => trans('messages.general.not_found')]);
            }

            $product = Product::query()
                ->where('id', $productStore->product_id)
                ->where('reviewed', true)
                ->first();

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'messages' => trans('messages.product.product_under_review'),
                    'data' => [],
                ], AResponseStatusCode::FORBIDDEN);
            }

            $productStore =
                ProductStore::query()
                    ->select([
                        'products.id as product_id',
                        'products.name as product_name',
                        'products.description',
                        'product_store_stock.available_stock',
                        'product_store_stock.reserved_stock',
                        'product_store_stock.sold',
                        'product_store.publish_app_at',
                        'product_store.price',
                        'product_store.discount',
                        'product_store.net_price',
                        'product_store.id',
                        'stores.address as store_address',
                        'stores.id as store_id',
                        'stores.latitude',
                        'stores.longitude',
//                        'stores.delivery_days',
                        'stores.store_type_id',
//                        'stores.delivery_hours',
                        'product_store.free_shipping',
                        'products.material_rate',
                        "materials.name_$this->lang as material_name",
                        'stores.name as store_name',
                        "brands.name_$this->lang as brand_name",
                        'brands.id as brand_id',
                        "policies.name_$this->lang as policy_name",
                        'policies.id as policy_id',
                        "shipping_methods.name_$this->lang as shipping_method_name",
                        'shipping_methods.id as shipping_method_id',
                        'categories.id as category_id',
                        'categories.name_' . $this->lang . ' as category_name',
                        DB::raw("(SELECT CASE WHEN SUM(seller_rates.rate) / COUNT(seller_rates.rate) IS NOT NULL THEN CAST( CAST(SUM(seller_rates.rate) AS FLOAT) / CAST( COUNT(seller_rates.rate) AS FLOAT)  AS FLOAT)
                                        ELSE 0 END  as rate FROM seller_rates WHERE seller_rates.rated_id = products.id and rated_type = '" . Product::class . "')"),
                        DB::raw("(SELECT COUNT(seller_rates.review) as comments FROM seller_rates WHERE seller_rates.rated_id = products.id and rated_type = '" . Product::class . "')"),
                        DB::raw("(SELECT COUNT(*) AS fav_count FROM seller_favorites WHERE seller_favorites.favorited_type = '" . Product::class . "' AND seller_favorites.favorited_id = '" . $productStore->product_id . "')"),
                    ])
                    ->join('products', 'products.id', '=', 'product_store.product_id')
                    ->join('product_store_stock', 'product_store.id', '=', 'product_store_stock.product_store_id')
                    ->join('categories', 'categories.id', '=', 'products.category_id')
                    ->join('stores', 'stores.id', '=', 'product_store.store_id')
                    ->leftJoin('materials', 'products.material_id', '=', 'materials.id')
                    ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                    ->leftJoin('policies', 'policies.id', '=', 'products.policy_id')
                    ->leftJoin('shipping_methods', 'shipping_methods.id', '=', 'products.shipping_method_id')
                    ->where('products.reviewed', true)
                    ->where('product_store.product_id', $productStore->product_id)
                    ->where('product_store.store_id', $productStore->store_id)
                    ->first();

            $isActive = false;
            if ($userId) {
                $isActive = User::query()
                    ->select('activation')
                    ->where('id', $userId)
                    ->first()->activation;
            }
            $productStore->price = ProductHelper::canShowPrice($userId, $isActive, $productStore->price);
            $productStore->net_price = ProductHelper::canShowPrice($userId, $isActive, $productStore->net_price);
            if ($productStore->discount != 0 && $userId && $isActive) {
                $product->has_discount = true;
                $productStore->discount_type = 'percentage';
                $productStore->discount = $productStore->discount . '%';
            } else {
                $productStore->has_discount = false;
            }
            $rates = SellerRate::query()->where('rated_store_id', $productStore->store_id)
                ->where('rated_id', $productStore->product_id)->where('rated_type', Product::class)
                ->with('Rater')
                ->limit(3)->get();
            $productStore->rates = SellerRateResource::collection($rates);

            $productStore->store_avg_rate = $this->rateHelper->getAverageRate($productStore->store_id, Store::class);

            if ($userId) {
                $isFav = SellerFavorite::query()
                    ->where('favoriter_type', User::class)
                    ->where('favorited_id', $productStore->product_id)
                    ->where('favorited_type', Product::class)
                    ->where('favoriter_id', $userId)
                    ->first();
                if ($isFav) {
                    $productStore->is_favourit = true;
                } else {
                    $productStore->is_favourit = false;
                }
            } else {
                $productStore->is_favourit = false;
            }

            if (is_null($productStore)) {
                return response()->json([
                    'status' => false,
                    'messages' => trans('messages.product.product_not_found'),
                    'data' => [],
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $productColors = ProductImage::query()
                ->select(['color_id', 'image'])
                ->where('product_id', $productStore->product_id)
                ->get()->groupBy('color_id');

            $allImages = [];
            $colors = [];
            $newArray = [];
            $productColorsLoop = 0;
            $productAvailableStatus = true;
            foreach ($productColors as $color => $productColor) {

                $productColorImages = [];
                $colorOfRow = Color::query()->select('name_' . $this->lang . ' as name')->where('id', $color)->first();
                $newArray[$productColorsLoop]['color_id'] = $color;
                $newArray[$productColorsLoop]['color_name'] = $colorOfRow->name;
                foreach ($productColor as $productColorImage) {
                    $productColorImages[] = config('filesystems.aws_base_url') . $productColorImage['image'];
                }
                $newArray[$productColorsLoop]['images'] = $productColorImages;

                $colors[] = $productColor[0];
                $images = [];
                foreach ($productColor as $colorImage) {
                    $allImages[] = config('filesystems.aws_base_url') . $productColor[0]->image;
                    $images[] = config('filesystems.aws_base_url') . $colorImage->image;
                    unset($productColor->image);
                    unset($productColor->color);
                }
                $productColor = $images;
                $productColors[$color] = $productColor;

                $productColorsLoop++;
            }

            $defaultColor = [];
            $defaultColorCounter = 0;
            foreach ($newArray as $color) {
                $colorOfRow = Color::query()->select('name_' . $this->lang . ' as name')->where('id', $color['color_id'])->first();
                $defaultColor[$defaultColorCounter]['color_id'] = $color['color_id'];
                $defaultColor[$defaultColorCounter]['color_name'] = $colorOfRow->name;
                $defaultColor[$defaultColorCounter]['default_image'] = count($color['images']) ? $color['images'][0] : null;
                $defaultColor[$defaultColorCounter]['images'] = $color['images'];
                $barcodeProduct = BarcodeProduct::query()->where([['color_id', $color['color_id']], ['product_id', $productStore['product_id']]])->first();
                $defaultColor[$defaultColorCounter]['barcode'] = $barcodeProduct->barcode;
                $defaultColorCounter++;
            }

            $productStore->images = $newArray;
            $productStore->default_color = $defaultColor;
            $sizes = ProductStoreStock::query()
                ->select(['sizes.size', 'sizes.id as size_id', 'product_store_stock.available_stock'])
                ->join('sizes', 'sizes.id', '=', 'product_store_stock.size_id')
                ->where('product_store_stock.product_store_id', $productStore->id)
                ->distinct('product_store_stock.size_id')
                ->get();
            $packingUnits = PackingUnitProduct::query()
                ->select([
                    DB::raw("packing_unit_product.basic_unit_count packing_unit_name"),
                    'packing_unit_product.packing_unit_id as packing_unit_id',
                    'packing_unit_product.basic_unit_count as basic_unit_count',
                    'packing_unit_product.basic_unit_id as basic_unit_id',
                ])
                ->where('packing_unit_product.basic_unit_count', '>', 1)
                ->where('product_id', '=', $productStore->product_id)
                ->join('packing_units', 'packing_units.id', '=', 'packing_unit_product.packing_unit_id')
                ->get();

            $packingUnitProductId = PackingUnitProduct::query()
                ->where('product_id', $productStore->product_id)
                ->where('packing_unit_id', 1)
                ->first();

            if (count($packingUnits)) {
                $basic_unit = PackingUnit::query()->find($packingUnits[0]->basic_unit_id);

                $packingUnitAttrs = PackingUnitProductAttribute::query()
                    ->join('sizes', 'sizes.id', '=', 'packing_unit_product_attributes.size_id')
                    ->where('packing_unit_product_id', $packingUnitProductId->id)
                    ->get();

                foreach ($packingUnits as $packingUnit) {
                    if ($packingUnit->basic_unit_count != 1) {

                        $packingUnit->packing_unit_name = $packingUnit->packing_unit_name . ' ' . $basic_unit->name_ . $this->lang;
                        foreach ($packingUnitAttrs as $packingUnitAttr) {
                            $packingUnit->sizes .= $packingUnitAttr->size . ' -';
                        }
                    } else {
                        $packingUnit->packing_unit_name = $packingUnit->packing_unit_name . ' ' . $basic_unit->name_ . $this->lang;
                    }
                }
            }


            foreach ($sizes as $size) {
                $packingUnitSize = $packingUnitAttrs->where('size_id', $size->size_id)->first();
                $size->unit_quantity = 0;
                if ($packingUnitSize) {
                    $size->unit_quantity = $packingUnitSize->quantity;
                    $size->is_available = $packingUnitSize->quantity < $size->available_stock;
                }
                if ($packingUnitSize->quantity > $size->available_stock) {
                    $productAvailableStatus = false;
                }
            }

            $productStore->sizes = $sizes;
            $productStore->is_available = $productAvailableStatus;

            $productStore->packing_units = $packingUnits;
            $unit_details_arr = [];
            $unit_details = PackingUnitProduct::query()->where('product_id', $productStore->product_id)->first();
            foreach ($unit_details->attributes as $unit) {
                $unit_details_arr[] = [
                    'unit_id' => $unit_details->id,
                    'attribute_id' => $unit->id,
                    'size' => $unit->size->size,
                    'quantity' => $unit->quantity
                ];
            }
            $productStore->unit_details = $unit_details_arr;

//            $bundles = Bundle::query()->select(['quantity', 'price'])
//                ->where('product_id', $productStore->product_id)
//                ->orderBy('quantity', 'asc')
//                ->get();
//            $prevQuantity = '0';
//            foreach ($bundles as $bundle) {
//                if ($bundle->quantity >= 1000000) {
//                    $prevQuantity = '';
//                } else {
//                    $prevQuantity = $prevQuantity + 1;
//                }
//                $bundle->quntity_range = $prevQuantity . ' - ' . $bundle->quantity;
//                if ($bundle->quantity >= 1000000) {
//                    $bundle->quntity_range = '';
//                }
//                $prevQuantity = $bundle->quantity;
//                unset($bundle->quantity);
//            }
//            $productStore->price_range = $bundles;

            foreach ($productStore->packing_units as $packing_unit) {
                if ($packing_unit->basic_unit_count == 1) {
                    $packing_unit->is_basic_unit = true;
                } else {
                    $packing_unit->is_basic_unit = false;
                }
            }
            $productStore->images_slider = $allImages;
            $productStore->status = ProductHelper::productStatus($productStore->available_stock, $productStore->publish_app_at, true);

            event(new VisitProduct($request, $userId, $productStore->product_id));

            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $productStore,
            ], AResponseStatusCode::SUCCESS);

        } catch (Exception $e) {
            Log::error('error in getProduct of seller products' . __LINE__ . $e);
            return $e->getMessage();
            return $this->connectionError($e);
        }
    }

    public function rateProduct(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|numeric|exists:products,id',
                'store_id' => 'required|numeric|exists:stores,id',// TODO check if rate should be for uniq products
                'rate' => 'required|numeric|min:1|max:5',
                'review' => 'string|max:255',
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,jpg'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $images = [];
            if ($request->has('images')) {
                foreach ($request->images as $image) {
                    $images[] = UploadImage::uploadImageToStorage($image, 'Feeds/store/rates');
                }
            }

            $sellerRate = SellerRate::query()->where([
                ['rater_type', User::class],
                ['rater_id', $request->user_id],
                ['rated_type', Product::class],
                ['rated_id', $request->product_id],
                ['rated_store_id', $request->store_id],
            ])->first();

            if ($sellerRate) {
                $sellerRate->rate = $request->rate;
                $sellerRate->review = $request->review;
                if (count($images))
                    $sellerRate->images = json_encode($images);
                $sellerRate->save();
            } else {
                $sellerRate = new SellerRate();
                $sellerRate->rater_type = User::class;
                $sellerRate->rater_id = $request->user_id;
                $sellerRate->rated_type = Product::class;
                $sellerRate->rated_id = $request->product_id;
                $sellerRate->rated_store_id = $request->store_id;
                $sellerRate->rate = $request->rate;
                $sellerRate->review = $request->review;
                if (count($images))
                    $sellerRate->images = json_encode($images);
                $sellerRate->save();
            }

            return response()->json([
                'success' => true,
                'message' => trans('messages.product.review_added'),
                'data' => '',
            ], AResponseStatusCode::SUCCESS);

        } catch (Exception $e) {
            Log::error('error in rateProduct of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function toggleFavoriteProduct(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|numeric|exists:products,id',
                'store_id' => 'required|numeric|exists:stores,id'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            if (!$this->storesRepo->ifAllowTofollow($request)) {
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.stores.favorite_denied'),
                    'data' => []
                ]);
            }

            $findFavoritedProduct = SellerFavorite::query()
                ->where('favoriter_type', '=', User::class)
                ->where('favoriter_id', '=', $request->user_id)
                ->where('favorited_type', '=', Product::class)
                ->where('favorited_id', '=', $request->product_id)
                ->where('store_id', '=', $request->store_id)
                ->first();


            if (is_null($findFavoritedProduct)) {

                $favorite = new SellerFavorite();
                $favorite->favoriter_type = User::class;
                $favorite->favoriter_id = $request->user_id;
                $favorite->favorited_type = Product::class;
                $favorite->favorited_id = $request->product_id;
                $favorite->store_id = $request->store_id;
                $favorite->save();
                $store = Store::query()->where('id', $request->store_id)->first();
                $product_image = ProductImage::query()->where('product_id', $request->product_id)->first();
                event(new \App\Events\Product\FavoriteProduct([$store->user_id], $request->product_id, $product_image->image));
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.product.favorite'),
                    'data' => '',
                ], AResponseStatusCode::SUCCESS);

            } else {

                $findFavoritedProduct->delete();
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.product.unfavourite'),
                    'data' => '',
                ], AResponseStatusCode::SUCCESS);

            }

        } catch (Exception $e) {
            Log::error('error in toggleFavoriteProduct of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    private function validateProductData($data)
    {
        try {
            $colors = [];
//        $barcodes = [];
            foreach ($data as $color) {
                $colors[] = $color['color_id'];
//            $barcodes[] = $color['barcode'];
            }

            if (count($colors) != count(array_unique($colors))) {
                return false;
            }

//        if (count($barcodes) != count(array_unique($barcodes))) {
//            return false;
//        }

            return true;
        } catch (Exception $e) {
            Log::error('error in validateProductData of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function getProductRates($productId, $storeId, Request $request)
    {
        try {
            if (!ProductStore::query()->where('product_id', $productId)->where('store_id', $storeId)->count()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.product.product_not_found'),
                    'data' => '',
                ], AResponseStatusCode::BAD_REQUEST);
            }
            $productRates = DB::select("SELECT
                       seller_rates.id,
                        seller_rates.rate,
                        seller_rates.review,
                        seller_rates.images,
                        seller_rates.created_at,
                        users.id as rater_id,
                        users.name as rated_by,
                        users.image as rater_image
                        From seller_rates
                        JOIN users on users.id = seller_rates.rater_id
                        WHERE rated_id = ?
                        AND rated_store_id = ?
                        AND rated_type = 'App\Models\Product'
                        ", [$productId, $storeId]);

            $productRates = PaginationHelper::arrayPaginator($productRates, $request, 10);

            return new SellerRateCollection($productRates);

        } catch (Exception $e) {
            Log::error('error in getProductRates of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getStoreOfProduct($storeId, Request $request)
    {
        try {
            $userId = UserId::UserId($request);
            $limit = 3;
            $pagination = 0;
            $stores = $this->storesRepo->getStores($request, $userId, $limit, $pagination, null, $storeId);

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $stores,
            ], AResponseStatusCode::SUCCESS);

        } catch (Exception $e) {
            Log::error('error in getStoreOfProduct of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function relatedProducts($productId, GetProductsRequest $request)
    {
        try {

            $userId = UserId::UserId($request);
            $storeId = null;
            if ($userId) {
                $storeId = Store::query()
                    ->select('id')
                    ->where('user_id', $userId)
                    ->first()->id;
            }

            $arrayOfParameters['pagination'] = 10;
            $arrayOfParameters['limit'] = 0;
            $arrayOfParameters['isStoreProfile'] = false;
            $arrayOfParameters['request'] = $request;
            $arrayOfParameters['userId'] = $userId;
            $arrayOfParameters['storeId'] = $storeId;
            $products = $this->productsRepo->getProducts($arrayOfParameters);


            return response()->json([
                'status' => true,
                'message' => trans('messages.product.products'),
                'data' => $products
            ], AResponseStatusCode::SUCCESS);

        } catch (Exception $e) {
            Log::error('error in relatedProducts of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
