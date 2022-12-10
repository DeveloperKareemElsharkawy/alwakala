<?php

namespace App\Http\Controllers\Seller;


use App\Enums\Activity\Activities;
use App\Enums\Activity\ActivityType;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\Stock\ATransactionTypes;
use App\Enums\StoreTypes\StoreType;
use App\Events\Inventory\StockMovement;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Products\CreateSellerInventoryRequest;
use App\Lib\Helpers\Authorization\AuthorizationHelper;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\Rate\RateHelper;
use App\Lib\Log\ValidationError;
use App\Models\BarcodeProduct;
use App\Models\Bundle;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\Product;
use App\Enums\Product\AProductStatus;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\Seller;
use App\Models\Store;
use App\Repositories\ActivitiesRepository;
use App\Repositories\InventoryRepository;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InventoryController extends BaseController
{
    private $lang;
    protected $rateHelper;

    public function __construct(Request $request, RateHelper $rateHelper)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->rateHelper = $rateHelper;
    }

    public function getInventory(Request $request)
    {
        try {
            $status = null;
            if ($request->filled('status')) {
                $status = $request->status;
            }
            $products = InventoryRepository::getInventoryQuery($request, $status);
            $storeId = StoreRepository::getStoreByUserId($request->user_id);
            foreach ($products as $product) {
                $product->discount = $product->discount . '%';
                if ($product->productImage->image) {
                    $product->image = config('filesystems.aws_base_url') . $product->productImage->image;
                }
                $product->status = ProductHelper::productStatus($product->available_stock, $product->publish_at_date, $product->reviewed);
                unset($product->productImage);
                $product->rate = $this->rateHelper->getAverageRate($product->product_id, Product::class);
            }
            if (is_null($storeId)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.sections.inventory'),
                    'data' => ''
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'inventory',
                'data' => $products
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return $e;
            Log::error('error in getInventory of seller Inventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param Request $request
     * @param $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductInventory(Request $request, $productId)
    {
        try {
            $seller = Seller::query()->select('store_id')->where('user_id', $request->user_id)->first();
            $store = Store::query()
                ->select('id', 'store_type_id')
                ->where('id', $seller->store_id)
                ->first();
            $isOwner = Product::query()
                ->where('owner_id', $request->user_id)
                ->where('id', $productId)
                ->first();
            if (!AuthorizationHelper::isAuthorized('product_id', $productId, 'store_id', $store->id, ProductStore::class)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.inventory.unauthorized'),
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }
            if ($isOwner) {
                if ($store->store_type_id == StoreType::SUPPLIER) {
                    $productInventory = $this->productInventoryForSupplier($productId, $store->id);
                    $productInventory->is_owner = true;
                    $productInventory->stackholeder = 'supplier';
                } else {
                    $productInventory = $this->productInventoryForRetailer($productId, $store->id);
                    $productInventory->is_owner = true;
                    $productInventory->stackholeder = 'retailer';
                }
            } else {
                if ($store->store_type_id == StoreType::SUPPLIER) {
                    $productInventory = $this->productInventoryForSupplier($productId, $store->id);
                    $productInventory->is_owner = false;
                    $productInventory->stackholeder = 'supplier';
                } else {
                    $productInventory = $this->productInventoryForRetailer($productId, $store->id);
                    $productInventory->is_owner = false;
                    $productInventory->stackholeder = 'retailer';
                }
            }

            return response()->json([
                'status' => true,
                'message' => trans('messages.inventory.product_inventory'),
                'data' => $productInventory
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getInventory of seller Inventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeInventory(CreateSellerInventoryRequest $request)
    {
        try {
            $store = Store::query()->where('user_id', $request->user_id)
                ->where('store_type_id', StoreType::RETAILER)
                ->first();

            DB::beginTransaction();
            foreach ($request->colors as $product_color) {
                ProductStore::query()->updateOrInsert([
                    'product_id' => $request->product_id,
                    'store_id' => $store->id,
                    'stock' => $product_color['stock'],
                    'available_stock' => $product_color['stock'],
                    'reserved_stock' => 0,
                    'sold' => 0,
                    'size_id' => $product_color['size_id'],
                    'color' => $product_color['color'],
                    'returned' => 0,
                    'views' => 0,
                    'publish_app_at' => $request->publish_app_at
                ]);
                event(new StockMovement($product_color['stock'], $request->product_id, ATransactionTypes::PRODUCT, $store->id));
            }

            DB::commit();
            return response()->json([
                "data" => [],
                "message" => trans('messages.inventory.inventory_inserted_successfully')
            ]);
        } catch (\Exception $e) {
            Log::error('error in storeInventory of seller Inventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }

        DB::beginTransaction();
        foreach ($request->colors as $product_color) {
            ProductStore::query()->updateOrInsert([
                'product_id' => $request->product_id,
                'store_id' => $store->id,
                'stock' => $product_color['stock'],
                'available_stock' => $product_color['stock'],
                'reserved_stock' => 0,
                'sold' => 0,
                'size_id' => $product_color['size_id'],
                'color' => $product_color['color'],
                'returned' => 0,
                'views' => 0,
                'publish_app_at' => $request->publish_app_at
            ]);
            event(new StockMovement($product_color['stock'], $request->product_id, ATransactionTypes::PRODUCT, $store->id));
        }
        $data['ref_id'] = $request->product_id;
        $data['user_id'] = $request->seller_id;
        $data['action'] = Activities::INVENTORY_STORE;
        $data['type'] = ActivityType::PRODUCT;
        ActivitiesRepository::log($data);
        DB::commit();
        return response()->json([
            "data" => [],
            "message" => trans('messages.inventory.inventory_inserted_successfully')
        ]);
    }

    public function addDiscount(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'discount' => 'required|numeric',
                'discount_type' => 'required|numeric|in:1,2',
                'product_id' => 'required|numeric|exists:products,id'
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $storeId = Store::query()
                ->select('id')
                ->where('user_id', $request->user_id)
                ->first()->id;
            if (!AuthorizationHelper::isAuthorized('product_id', $request->product_id, 'store_id', $storeId, ProductStore::class)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.general.error'),
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }
            if (!ProductHelper::isActiveProduct($request->product_id)) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.inventory.not_active'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            $bundlesCount = Bundle::query()
                ->where('product_id', $request->product_id)
                ->count();
            if ($bundlesCount != 1) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.inventory.has_bundle'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            $productStore = ProductStore::query()
                ->where('store_id', $storeId)
                ->where('product_id', $request->product_id)
                ->first();

            if (!$productStore) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.general.error'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            $productStore->discount = $request->discount;
            $productStore->discount_type = $request->discount_type;
            $productStore->net_price = ProductHelper::calculateProductDiscount($request->discount_type, $request->discount, $productStore->price);
            $productStore->save();
            $data['ref_id'] = $request->product_id;
            $data['user_id'] = $request->seller_id;
            $data['action'] = Activities::ADD_DISCOUNT;
            $data['type'] = ActivityType::PRODUCT;
            ActivitiesRepository::log($data);
            return response()->json([
                'status' => true,
                'message' => trans('messages.inventory.discount_added'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in add discount of seller Inventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function increaseStock(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|numeric|exists:products,id',
                'quantity' => 'required|array',
                'quantity.*.stock' => 'required|numeric',
                'quantity.*.color_id' => 'required|numeric|exists:colors,id',
                'quantity.*.size_id' => 'required|numeric|exists:sizes,id'
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            DB::beginTransaction();
            $storeId = Store::query()
                ->select('id')
                ->where('user_id', $request->user_id)
                ->first()->id;
            if (!AuthorizationHelper::isAuthorized('product_id', $request->product_id, 'store_id', $storeId, ProductStore::class)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.general.error'),
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }
            if (!ProductHelper::isActiveProduct($request->product_id)) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.inventory.not_active'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }


            $productStoreId = ProductStore::query()
                ->select('id')
                ->where('store_id', $storeId)
                ->where('product_id', $request->product_id)
                ->first()->id;

            if (!$productStoreId) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.general.error'),
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            foreach ($request->quantity as $quantity) {
                $productStoreStock = ProductStoreStock::query()
                    ->where('product_store_id', $productStoreId)
                    ->where('size_id', $quantity['size_id'])
                    ->where('color_id', $quantity['color_id'])
                    ->first();

                $productStoreStock->stock += $quantity['stock'];
                $productStoreStock->available_stock += $quantity['stock'];
                $productStoreStock->save();
            }
            $data['ref_id'] = $request->product_id;
            $data['user_id'] = $request->seller_id;
            $data['action'] = Activities::INCREASE_STOCK;
            $data['type'] = ActivityType::PRODUCT;
            ActivitiesRepository::log($data);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => trans('messages.inventory.stock_updated'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in increaseStock of seller Inventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function toggleSwitchActivation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => [
                    'required',
                    'numeric',
                    'exists:products,id',
                ],
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $store = Store::query()
                ->select('id')
                ->where('user_id', $request->user_id)
                ->first();
            $product = ProductStore::query()
                ->where('product_id', $request['product_id'])
                ->where('store_id', $store->id)
                ->first();
            // check authorization
            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.product.not_valid'),
                    'data' => ''
                ], AResponseStatusCode::SUCCESS);
            }
            if ($product->activation) {
                $product->activation = false;
            } else {
                $product->activation = true;
            }
            $product->save();
            $message = trans('messages.product.not_active');
            if ($product->activation) {
                $message = trans('messages.product.active');
            }

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => ''
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in toggleSwitchActivation of seller Inventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    private function productInventoryForSupplier($productId, $storeId)
    {
        try {
            $productInfo = Product::query()
                ->select('products.id',
                    'products.name',
                    'products.description',
                    'products.brand_id',
                    'products.category_id',
                    'products.owner_id',
                    'products.policy_id',
                    'products.shipping_method_id',
                    'products.consumer_price',
                    'products.activation',
                    'products.reviewed',
                    'products.material_rate',
                    "materials.name_" . $this->lang . " as material_name",
                    "policies.name_" . $this->lang . " as policy_name",
                    "shipping_methods.name_" . $this->lang . " as shipping_method_name"
                )
                ->with(["brand:id,name_" . $this->lang . " as name", "category:id,name_" . $this->lang . " as name", 'owner'])
                ->leftJoin('materials', 'products.material_id', '=', 'materials.id')
                ->leftJoin('shipping_methods', 'products.shipping_method_id', '=', 'shipping_methods.id')
                ->leftJoin('policies', 'products.policy_id', '=', 'policies.id')
                ->where('products.id', $productId)
                ->first();

            $productStore = ProductStore::query()
                ->select('id',
                    'publish_app_at',
                    'price',
                    'net_price',
                    'discount',
                    'discount_type',
                    'activation'
                )
                ->where('product_id', $productId)
                ->where('store_id', $storeId)
                ->first();
            $productStoreId = $productStore->id;
            $attributes = BarcodeProduct::query()
                ->select('id', 'barcode', 'color_id')
                ->with(['color' => function ($q) {
                    $q->select('id', 'name_' . $this->lang . ' as name', 'hex');
                }])
                ->with(['images' => function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                }])
                ->with(['stock' => function ($q) use ($productStoreId) {
                    $q->where('product_store_id', $productStoreId);
                }])
                ->where('product_id', $productId)
                ->get();

            $stocks = [
                'stock' => 0,
                'available_stock' => 0,
                'reserved_stock' => 0,
                'sold' => 0,
                'returned' => 0,
            ];
            foreach ($attributes as $attribute) {
                foreach ($attribute->stock as $stock) {
                    $stocks['stock'] += $stock->stock;
                    $stocks['available_stock'] += $stock->available_stock;
                    $stocks['reserved_stock'] += $stock->reserved_stock;
                    $stocks['sold'] += $stock->sold;
                    $stocks['returned'] += $stock->returned;
                }
                foreach ($attribute->images as $attr) {
                    $attr->image = config('filesystems.aws_base_url') . $attr->image;
                }
            }
//            $productBundle = Bundle::query()
//                ->select('id', 'product_id', 'quantity', 'price')
//                ->where('product_id', $productId)
//                ->get();
            $packingUnitProductId = PackingUnitProduct::query()
                ->where('product_id', $productId)
                ->where('packing_unit_id', 1)
                ->first()->id;
            $package = PackingUnitProductAttribute::query()
                ->select('packing_unit_product_attributes.id', 'sizes.size', 'packing_unit_product_attributes.quantity')
                ->join('sizes', 'sizes.id', '=', 'packing_unit_product_attributes.size_id')
                ->where('packing_unit_product_id', $packingUnitProductId)
                ->get();
            $productStore->status = ProductHelper::productStatus($stocks['available_stock'], $productStore->publish_app_at, $productInfo->reviwed);
            $response = new \stdClass();
            $response->info = $productInfo;
            $response->store_details = $productStore;
//            $response->price = $productBundle;
            $response->global_stock = $stocks;
            $response->attributes = $attributes;
            $response->package = $package;
            $response->rate = $this->rateHelper->getAverageRate($productInfo->id, Product::class);
            return $response;
        } catch (\Exception $e) {
            Log::error('error in productInventoryForSupplier of seller Inventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    private function productInventoryForRetailer($productId, $storeId)
    {
        try {
            $productInfo = Product::query()
                ->select('products.id',
                    'products.name',
                    'products.description',
                    'products.brand_id',
                    'products.category_id',
                    'products.policy_id',
                    'products.shipping_method_id',
                    'products.owner_id',
                    'products.consumer_price',
                    'products.activation',
                    'products.reviewed',
                    'products.material_rate',
                    "materials.name_" . $this->lang . " as material_name",
                    "policies.name_" . $this->lang . " as policy_name",
                    "shipping_methods.name_" . $this->lang . " as shipping_method_name"

                )
                ->with(["brand:id,name_" . $this->lang . " as name", "category:id,name_" . $this->lang . " as name", 'owner'])
                ->leftJoin('shipping_methods', 'products.shipping_method_id', '=', 'shipping_methods.id')
                ->leftJoin('materials', 'products.material_id', '=', 'materials.id')
                ->leftJoin('policies', 'products.policy_id', '=', 'policies.id')
                ->where('products.id', $productId)
                ->first();
            $productStore = ProductStore::query()
                ->select('id',
                    'publish_app_at',
                    'price',
                    'net_price',
                    'discount',
                    'discount_type',
                    'activation'
                )
                ->where('product_id', $productId)
                ->where('store_id', $storeId)
                ->first();
            $productStoreId = $productStore->id;
            $attributes = BarcodeProduct::query()
                ->select('id', 'barcode', 'color_id')
                ->with(['color' => function ($q) {
                    $q->select('id', 'name_' . $this->lang . ' as name', 'hex');
                }])
                ->with(['images' => function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                }])
                ->with(['stock' => function ($q) use ($productStoreId) {
                    $q->where('product_store_id', $productStoreId);
                }])
                ->where('product_id', $productId)
                ->get();
            $stocks = [
                'stock' => 0,
                'available_stock' => 0,
                'reserved_stock' => 0,
                'sold' => 0,
                'returned' => 0,
            ];
            foreach ($attributes as $attribute) {
                foreach ($attribute->stock as $stock) {
                    $stocks['stock'] += $stock->stock;
                    $stocks['available_stock'] += $stock->available_stock;
                    $stocks['reserved_stock'] += $stock->reserved_stock;
                    $stocks['sold'] += $stock->sold;
                    $stocks['returned'] += $stock->returned;
                }
                foreach ($attribute->images as $attr) {
                    $attr->image = config('filesystems.aws_base_url') . $attr->image;
                }
            }
            $productStore->status = ProductHelper::productStatus($stocks['available_stock'], $productStore->publish_app_at, $productInfo->reviwed);

            $response = new \stdClass();
            $response->info = $productInfo;
            $response->store_details = $productStore;
            $response->global_stock = $stocks;
            $response->attributes = $attributes;
            $response->rate = $this->rateHelper->getAverageRate($productInfo->id, Product::class);
            return $response;
        } catch (\Exception $e) {
            Log::error('error in productInventoryForRetailer of seller Inventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getStatus(Request $request)
    {
        try {
            $status = [
                [
                    'id' => AProductStatus::AVAILABLE,
                    'status' => trans('messages.status.available'),
                ],
                [
                    'id' => AProductStatus::NOT_AVAILABLE,
                    'status' => trans('messages.status.not_available'),
                ],
                [
                    'id' => AProductStatus::IN_REVIEW,
                    'status' => trans('messages.status.in_review'),
                ],
                [
                    'id' => AProductStatus::SOON,
                    'status' => trans('messages.status.soon'),
                ],
            ];
            return response()->json([
                'status' => true,
                'message' => trans('messages.inventory.inventory_status'),
                'data' => $status
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getStatus of seller Inventory' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function toggleActiveProduct(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|numeric|exists:products,id',
                'store_id' => 'required|numeric|exists:stores,id'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $product = ProductStore::query()->where(['product_id' => $request->product_id, 'store_id' => $request->store_id])->first();

            if ($product->activation) {
                $product->activation = false;
                $product->save();
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.product.not_active'),
                    'data' => '',
                ], AResponseStatusCode::SUCCESS);
            } else {
                $product->activation = true;
                $product->save();
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.product.active'),
                    'data' => '',
                ], AResponseStatusCode::SUCCESS);
            }
        } catch (\Exception $e) {
            Log::error('error in toggleShowToConsumerProduct of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


}
