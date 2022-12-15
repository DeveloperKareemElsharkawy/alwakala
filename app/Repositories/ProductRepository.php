<?php


namespace App\Repositories;


use App\Enums\AChannels\AChannels;
use App\Enums\Apps\AApps;
use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\Product\APolicyTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\Stock\ATransactionTypes;
use App\Enums\StoreTypes\StoreType;
use App\Enums\UserTypes\UserType;
use App\Events\Inventory\StockMovement;
use App\Http\Controllers\Controller;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Pagination\PaginationHelper;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Log\ServerError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\BarcodeProduct;
use App\Models\Color;
use App\Models\FollowedStore;
use App\Models\PackingUnit;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\ProductImage;
use App\Models\Product;
use App\Models\ProductRate;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\Store;
use App\Models\User;
use App\Models\View;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRepository extends Controller
{

    protected $model, $locale;

    public function __construct(Product $model)
    {
        $this->model = $model;
        $this->locale = app()->getLocale();
    }

    /**
     * @param $arrayOfParameters
     * @return LengthAwarePaginator|Builder[]|Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     * @throws Exception
     */
    public function getProducts($arrayOfParameters)
    {
        try {
            $query = self::prepareProductQuery($arrayOfParameters['request'], $arrayOfParameters['userId'], $arrayOfParameters['storeId'], null, $arrayOfParameters['isStoreProfile']);
            if ($arrayOfParameters['limit'] != 0) {
                $products = $query->limit($arrayOfParameters['limit'])->get();
            } elseif ($arrayOfParameters['pagination'] != 0) {
                $products = $query->paginate($arrayOfParameters['pagination']);
            } else {
                $products = $query->get();
            }
            $app = $arrayOfParameters['app'] ?? AApps::SELLER_APP;
            $isActive = self::checkIfUserActive($arrayOfParameters['userId']);
             return $this->prepareProductsResponse($products, $arrayOfParameters['userId'], $isActive, isset($arrayOfParameters['mostPopular']) ? true : false, $app);
        } catch (Exception $e) {
            throw new Exception($e);

        }
    }

    public function getProductImages($storeProducts)
    {
        foreach ($storeProducts as $product) {

            $image = ProductImage::query()
                ->where('product_id', $product->id)
                ->first();
            if (!is_null($image)) {
                $image->image = config('filesystems.aws_base_url') . 'products/' . $image->image;
                $product->image = $image->image;
            } else {
                $product->image = config('filesystems.aws_base_url') . 'product_place_holder.png';
            }
        }
        return $storeProducts;
    }

    public function adoptQuantities($data)
    {
        foreach ($data as $product_store) {
            if (request()->segment(2) == 'consumer-app') {
                $productStore = ProductStore::query()
                    ->where('store_id', $product_store['store_id'])
                    ->where('product_id', $product_store['product_id'])
                    ->first();
                $productStoreStock = ProductStoreStock::query()
                    ->where('product_store_id', $productStore->id)
                    ->where('color_id', $product_store['color_id'])
                    ->where('size_id', $product_store['size_id'])
                    ->first();
                $productStoreStock->adoptStock($productStore->product_id, $productStore->store_id);
                continue;
            }

            $packingUnitProduct = PackingUnitProduct::query()
                ->where('product_id', $product_store['product_id'])
                ->where('packing_unit_id', 1)
                ->first();

            $productAttrs = PackingUnitProductAttribute::query()
                ->where('packing_unit_product_id', $packingUnitProduct->id)
                ->get();

            $productStore = ProductStore::query()
                ->where('store_id', $product_store['pivot']['store_id'])
                ->where('product_id', $product_store['pivot']['product_id'])
                ->first();
            foreach ($productAttrs as $productAttr) {
                $productStoreStock = ProductStoreStock::query()
                    ->where('product_store_id', $productStore->id)
                    ->where('size_id', $productAttr['size_id'])
                    ->where('color_id', $product_store['color_id'])
                    ->first();

                $productStoreStock->adoptStock($product_store['product_id'], $product_store['store_id'], $packingUnitProduct->id);
            }
        }
    }

    public function checkQuantities($data, $store_id)
    {
        // check if the purchase unit is not basic unit
        $quantity = $data['purchased_item_count'];
        // Consumer check quantity

        // TODO add another check to make sure its consumer order
//        if ($data['basic_unit_count'] == 1) {
//
//            $productStore = ProductStore::query()
//                ->where('store_id', $store->id)
//                ->where('product_id', $data['product_id'])
//                ->first();
//            $productStoreStock = ProductStoreStock::query()
//                ->where('product_store_id', $productStore->id)
//                ->first();
//
//            if ($productStoreStock->available_stock < $quantity) {
//                return 0;
//            }
//            return 1;
//        }

        $productPackingUnit = PackingUnitProduct::query()
            ->where('product_id', $data['product_id'])
            ->first();

        $productAttrs = PackingUnitProductAttribute::query()
            ->where('packing_unit_product_id', $productPackingUnit->id)
            ->get();

        $productStore = ProductStore::query()
            ->where('store_id', $store_id)
            ->where('product_id', $data['product_id'])
            ->first();
        foreach ($productAttrs as $productAttr) {
            $productStoreStock = ProductStoreStock::query()
                ->where('product_store_id', $productStore->id)
                ->where('size_id', $productAttr['size_id'])
                ->where('color_id', $data['color_id'])
                ->first();
            if (is_null($productStoreStock)) {
                return 0;
            }

            if ($productStoreStock->available_stock < $quantity) {// * $productAttr['quantity']) {
                return 0;
            }
        }
        return 1;
    }

    public function increaseStockInStore($productData, $receiverId, $productAttrs, $storeID = null, $is_purchased = false)
    {

        $productDetails = Product::query()
            ->select(['consumer_price', 'owner_id'])
            ->find($productData['pivot']['product_id']);

        $storeID = $storeID ? $storeID : $productDetails->owner_id;

        if (request()->segment(2) == 'consumer-app') {
            $productStore = ProductStore::query()
                ->firstOrNew([
                    'store_id' => $receiverId,
                    'product_id' => $productData['pivot']['product_id']
                ]);

            $productStore->views += 0;
            $productStore->price = $this->checkConsumerPrice($productData->price, $storeID, $productDetails->consumer_price, $productStore->price);
            $productStore->net_price += 0;
            $productStore->discount = $productStore->discount ? $productStore->discount : 0;
            $productStore->discount_type = $productStore->discount_type ? $productStore->discount_type : DiscountTypes::AMOUNT;
            $productStore->publish_app_at = $productStore->publish_app_at ? $productStore->publish_app_at : Carbon::now()->toDateString();
            $productStore->save();
            $productStoreStock = ProductStoreStock::query()
                ->firstOrNew([
                    'product_store_id' => $productStore->id
                ]);
            $productStoreStock->stock += $productData['pivot']['purchased_item_count'];
            $productStoreStock->reserved_stock += 0;
            $productStoreStock->available_stock += $productData['pivot']['purchased_item_count'];
            $productStoreStock->sold += 0;
            $productStoreStock->color_id = $productData['color_id'];
            $productStoreStock->returned += 0;
            $productStoreStock->save();
            event(new StockMovement($productStoreStock->stock, $productData['pivot']['product_id'], ATransactionTypes::INVENTORY, $receiverId));
            return;
        }

        $productStore = ProductStore::query()
            ->firstOrNew([
                'store_id' => $receiverId,
                'product_id' => $productData['pivot']['product_id']
            ]);
        $store = Store::query()->where('id', $storeID)->first();

        $productStore->views += 0;

        $productStore->consumer_price = $this->checkConsumerPrice($productData->price, $storeID, $productData->consumer_price, $productStore->price);
        $productStore->price = $productData->price;
        $productStore->net_price = $productData->net_price;
        $productStore->is_purchased = true;
         $productStore->discount = $productStore->discount ? $productStore->discount : 0;
        $productStore->discount_type = $productStore->discount_type ? $productStore->discount_type : DiscountTypes::AMOUNT;
        $productStore->publish_app_at = $productStore->publish_app_at ? $productStore->publish_app_at : Carbon::now()->toDateString();
        $productStore->save();

        foreach ($productAttrs as $productAttr) {

            $productStoreStock = ProductStoreStock::query()
                ->firstOrNew([
                    'product_store_id' => $productStore->id,
                    'size_id' => $productAttr['size_id'], // comes rom attrs
                    'color_id' => $productData['color_id']
                ]);
            $productStoreStock->stock = $productStoreStock->stock ?? 0 + ($productAttr['quantity'] * $productData['pivot']['purchased_item_count']);
            $productStoreStock->reserved_stock += 0;
            $productStoreStock->available_stock += $productStoreStock->stock ?? 0 + ($productAttr['quantity'] * $productData['pivot']['purchased_item_count']);
            $productStoreStock->sold += 0;
            $productStoreStock->returned += 0;
            if ($is_purchased)
                $productStoreStock->approved = true;
            $productStoreStock->save();
            event(new StockMovement($productStoreStock->stock, $productData['pivot']['product_id'], ATransactionTypes::INVENTORY, $receiverId));
        }
    }

    public function decreaseStockInStore($productData, $productAttrs, $packingUnitProductId)
    {
        if (request()->segment(2) == 'consumer-app') {

            $productStore = ProductStore::query()->where('store_id', $productData->store_id)->first();
            $productStoreStock = ProductStoreStock::query()
                ->where('product_store_id', $productStore->id)
                ->first();
            $productStoreStock->stock -= $productData['pivot']['purchased_item_count'];
            $productStoreStock->sold += $productData['pivot']['purchased_item_count'];

            $productStoreStock->save();
            $productStoreStock->adoptStock();
            event(new StockMovement($productData['pivot']['purchased_item_count'], $productData['pivot']['product_id'], ATransactionTypes::INVENTORY, $productData['store_id']));
            return;
        }

        $productStore = ProductStore::query()
            ->where('store_id', $productData['pivot']['store_id'])
            ->where('product_id', $productData['pivot']['product_id']) // the issue was decreased from first matched product
            ->first();

        foreach ($productAttrs as $productAttr) {
            $productStoreStock = ProductStoreStock::query()
                ->where('product_store_id', $productStore->id)
                ->where('size_id', $productAttr['size_id'])
                ->where('color_id', $productData['color_id'])
                ->first();


            if ($productStoreStock) {
                $productStoreStock->stock -= $productData['pivot']['purchased_item_count'] * $productAttr['quantity'];
                $productStoreStock->sold += $productData['pivot']['purchased_item_count'] * $productAttr['quantity'];
                $productStoreStock->save();
                $productStoreStock->adoptStock($productData['pivot']['product_id'], $productData->store_id, $packingUnitProductId);
                //            event(new StockMovement($productData['pivot']['purchased_item_count'] * $productAttr['quantity'], $productData['product_id'], ATransactionTypes::INVENTORY, $productData['store_id']));
            }


        }
    }

//    public function storeConsumerPrice($product_id, $consumerPrice, $receiverId)
//    {
//        ProductConsumerPrice::query()
//            ->updateOrCreate([
//                'product_id' => $product_id,
//                'consumer_price' => $consumerPrice,
//                'store_id' => $receiverId
//            ]);
//    }

    public function checkConsumerPrice($price, $storeId, $consumerPrice, $oldPrice)
    {
        $store = Store::query()->where('id', $storeId)->first();

        if ($oldPrice) {
            return $oldPrice;
        } elseif ($store->store_type_id == StoreType::RETAILER && $consumerPrice != null) {
            return $consumerPrice;
        } else {
            return $price;
        }
    }

    public function checkProductsNotExistInSellerStore($orderProducts, $sellerStoreId, $requestedProducts)
    {
        $AllSellerProducts = ProductStore::query()->select(['product_id'])
            ->where('store_id', $sellerStoreId)->distinct('product_id')->get()
            ->pluck('product_id')->toArray();

        $notExistsProducts = [];
        $requestedProducts = collect($requestedProducts)->pluck('id')->toArray();
        foreach ($orderProducts as $orderProduct) {
            if (!in_array($orderProduct->id, $AllSellerProducts)
                && !in_array($orderProduct->id, $requestedProducts)) {
                $notExistsProducts [] = $orderProduct->id;
            }
        }

        return Product::query()->select(['id', 'name'])->whereIn('id', $notExistsProducts)->get();
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     * @todo  NonCompletedProducts for user when he was interrupted before he completed the product
     */
    public static function getNonCompletedProducts($request): \Illuminate\Http\JsonResponse
    {
        try {
            $storeId = Store::query()
                ->where('user_id', $request->user_id)
                ->first();

            $products = ProductStore::query()
                ->select('product_store.product_id as product_id',
                    'products.name as product_name',
                    'products.updated_at',
                    'products.created_at',
                    'product_store.publish_app_at',
                    'product_store.net_price',
                    'product_store.price',
                    'product_store.discount')
                ->leftJoin('products', 'products.id', '=', 'product_store.product_id')
                ->leftJoin('product_store_stock', 'product_store.id', '=', 'product_store_stock.product_store_id')
                ->where('product_store.store_id', $storeId->id)
                ->whereNull('product_store_stock.product_store_id')
                ->groupBy(['product_store.id', 'products.name', 'products.updated_at', 'products.created_at'])
                ->orderBy('products.updated_at', 'desc')
                ->paginate(10);
            if (is_null($storeId)) {
                return response()->json([
                    'status' => false,
                    'message' => 'inventory',
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            return response()->json([
                'status' => true,
                'message' => 'inventory',
                'data' => $products
            ], AResponseStatusCode::SUCCESS);
        } catch (Exception $e) {
            return ServerError::handle($e);
        }
    }

    /**
     * @param $productId
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function deleteNonCompletedProducts($productId, $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            // check store type
            $storeType = Store::query()
                ->select('store_type_id')
                ->where('user_id', $request->user_id)
                ->first()->store_type_id;
            //delete data
            ProductStore::query()->where('product_id', $productId)->delete();

            if ($storeType == StoreType::SUPPLIER) {
//                Bundle::query()->where('product_id', $productId)->delete();

                $packingUnitProductId = PackingUnitProduct::query()
                    ->select('id')
                    ->where('product_id', $productId)
                    ->where('packing_unit_id', 1)
                    ->first()->id;

                PackingUnitProductAttribute::query()->where('packing_unit_product_id', $packingUnitProductId)->delete();
            }
            PackingUnitProduct::query()
                ->select('id')
                ->where('product_id', $productId)
                ->delete();

            Product::query()->where('id', $productId)->delete();
            //return response
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Non completed product deleted',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (Exception $e) {
            DB::rollBack();
            return ServerError::handle($e);
        }

    }

    public static function checkIfCategoryIdIsValidParent($CategoryId): bool
    {
        $query = DB::select("SELECT tb2.category_id AS parent
         FROM categories AS tb1
         INNER JOIN categories AS tb2 ON tb1.category_id=tb2.id
         WHERE tb1.id=?", [$CategoryId]);
        if (count($query) > 0 && $query[0]->parent != null) {
            return false;
        }
        return true;


    }

    public static function getJoinsOfProduct($request, $userId, $getFavoriteProducts = null)
    {
        $query = Product::query()
            ->with('productImage')
            ->leftJoin('product_store', 'products.id', '=', 'product_store.product_id')
            ->leftJoin('product_store_stock', 'product_store.id', '=', 'product_store_stock.product_store_id')
            ->leftJoin('colors', 'colors.id', '=', 'product_store_stock.color_id')
            ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
            ->leftJoin('materials', 'products.material_id', '=', 'materials.id')
            ->leftJoin('seller_rates', function ($join) {
                $join->on('seller_rates.rated_id', '=', 'products.id')
                    ->where('seller_rates.rated_type', '=', Product::class);
            })
            ->leftJoin('stores', 'product_store.store_id', '=', 'stores.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('packing_unit_product', function ($join) {
                $join->on('products.id', '=', 'packing_unit_product.product_id')
                    ->where('packing_unit_product.packing_unit_id', 1);
            });
//        if ($request['type_id'] == UserType::SELLER) {
//            $query->leftJoin('bundles', function ($join) {
//                $join->on('products.id', '=', 'bundles.product_id');
//                $join->on('stores.id', '=', 'bundles.store_id');
//            });
//        }
        $query->leftJoin('seller_favorites', function ($join) use ($userId) {
            $join->on('products.id', 'seller_favorites.favorited_id')
                ->on('seller_favorites.store_id', 'stores.id')
                ->where('favoriter_type', User::class)
                ->where('favoriter_id', $userId)
                ->where('favorited_type', Product::class);
        });
        if ($request['type_id'] == UserType::SELLER) {
            $query->where('stores.store_type_id', '=', StoreType::SUPPLIER);
        }
        if ($request['type_id'] == UserType::CONSUMER) {
            $query->where('stores.store_type_id', '=', StoreType::RETAILER);
        }
        if ($request['just_for_you']) {
            $query->leftJoin('category_store', 'category_store.category_id', '=', 'products.category_id');
        }
        $query->where('products.reviewed', true);
        if ($getFavoriteProducts) {
            $query->where('seller_favorites.favoriter_id', $userId);
        }
        return $query;
    }

    public static function checkIfUserActive($userId)
    {
        $isActive = false;
        if ($userId) {
            $isActive = User::query()
                ->select('activation')
                ->where('id', $userId)
                ->first()->activation;
        }

        return $isActive;
    }

    public function prepareProductsResponse($products, $userId, $isActive, $mostPopular = false, $app = AApps::SELLER_APP)
    {
        foreach ($products as $key => $product) {
            $productStore = ProductStore::query()
                ->with('store')
                ->where('product_id', $product->id)
                ->where('store_id', $product->store_id)
                ->first();

            $colorsIds = ProductStoreStock::query()
                ->where('product_store_id', $productStore->id)
                ->pluck('color_id')->toArray();

            $colors = Color::query()
                ->whereIn('id', $colorsIds)
                ->select('id', 'name_' . $this->locale . ' as name', 'hex')
                ->get();

            $product['colors'] = $colors;
            $product['hex'] = $colors[0]['hex'];
//            $product['number_of_followers'] = FollowedStore::query()->select('id')
//                ->where('store_id', $product->store_id)->get()->count();
//            $product['number_of_views'] = View::query()->select('id')
//                ->where('item_type', '=', 'STORE')
//                ->where('item_id', $product->store_id)->get()->count();
//            if ($mostPopular && $product['number_of_views'] == 0) {
//                $products->forget($key);
//            }
//            $product->price = $app == AApps::SELLER_APP ? ProductHelper::canShowPrice($userId, $isActive, $product->price) : $product->price;
//            $product->net_price = $app == AApps::SELLER_APP ? ProductHelper::canShowPrice($userId, $isActive, $product->net_price) : $product->net_price;
//            $product->is_retailer_product = $productStore->store->store_type_id == 1;
//
//            $productStore->store_logo = null;
//
//            if ($productStore->store->logo) {
//                $product->store_logo = config('filesystems.aws_base_url') . $productStore->store->logo;
//            }
//
//            if ($product->discount != 0 && $product->price != '--') {
//                $product->has_discount = true;
////                if ($product->discount_type == DiscountTypes::AMOUNT) {
////                    $product->discount_type = 'amount';
////                } else {
//                $product->discount_type = 'percentage';
//                $product->discount = $product->discount . '%';
//
////                }
//            } else {
//                $product->has_discount = false;
//            }
//            if (count($product->SellerRate) > 0) {
//                $product->rate = $product->SellerRate[0]->rate;
//            } else {
//                $product->rate = 0;
//            }
            unset($product->SellerRate);
            $product->image = null;
            if ($product->productImage) {
                $product->image = config('filesystems.aws_base_url') . $product->productImage->image;
            }
            unset($product->productImage);
            unset($product->productStore);
        }
        return $products;
    }

    public static function prepareProductQuery($request, $userId, $storeId = null, $query = null, $isStoreProfile = null, $getFavoriteProducts = null)
    {
        $lang = LangHelper::getDefaultLang($request);
        $PQuery = ProductRepository::getJoinsOfProduct($request, $userId, $getFavoriteProducts);

        if ($isStoreProfile) {
            $PQuery->with('productStore')->whereHas('productStore.store', function ($PQuery) use ($request) {
                $PQuery->where("store_type_id", StoreType::SUPPLIER);
            });
        }

        if ($request->filled('city_id')) {
            $cities_ids = explode(",", $request->query('city_id'));
            $PQuery->whereIn('stores.city_id', $cities_ids);
        }
        if ($request->filled('brand_id')) {
            $brands_ids = explode(",", $request->brand_id);
            $PQuery->whereIn('products.brand_id', $brands_ids);
        }
        if ($request->filled('date')) {
            $PQuery->orderBy('products.created_at', $request->query('date'));
        }
        if ($request->filled('price')) {
            $PQuery->orderBy('price_range', $request->query('price'));
        }
        if ($request->filled('category_id')) {
            $categories_ids = explode(",", $request->query('category_id'));
            $PQuery->whereIn('products.category_id', $categories_ids);
        }
        if ($request['categories_ids']) {
            $PQuery->whereIn('products.category_id', $request->categories_ids);
        }
        if ($request['products_ids']) {
            $PQuery->whereIn('products.id', $request->products_ids);
        }
        if ($request['shipment_method_id']) {
            $PQuery->whereIn('products.shipment_method_id', $request->shipment_method_id);
        }
        if ($request['policy_id']) {
            $PQuery->whereIn('products.policy_id', $request->policy_id);
        }
        if ($request['products_ids']) {
            $PQuery->whereIn('products.id', $request->products_ids);
        }

        $latitude = $request->header('Latitude');
        $longitude = $request->header('Longitude');

        if ($request->header('latitude') && $request->header('longitude')) {
            // This will calculate the distance in km
            // if you want in miles use 3959 instead of 6371

            $PQuery->selectRaw(DB::raw('6371 * acos(cos(radians(' . $latitude . ')) * cos(radians(stores.latitude)) * cos(radians(stores.longitude) - radians(' . $longitude . ')) + sin(radians(' . $latitude . ')) * sin(radians(stores.latitude))) as distance'))
                ->orderByRaw('distance asc');
        }

        if ($request->filled('material_id')) {
            $material_ids = explode(",", $request->query('material_id'));
            $PQuery->where(function ($query) use ($material_ids) {
                $query->whereIn('products.material_id', $material_ids);
            });
        }
        if ($request->filled('color_id')) {
            $colors_ids = explode(",", $request->query('color_id'));
            $PQuery->whereIn('product_store_stock.color_id', $colors_ids);
        }
        if ($request->filled('size_id')) {
            $sizes_ids = explode(",", $request->query('size_id'));
            $PQuery->whereIn('product_store_stock.size_id', $sizes_ids);
        }
        if ($request->filled('price_to')) {
            $PQuery->where('product_store.net_price', '<=', $request->query('price_to'));
        }
        if ($request->filled('price_from')) {
            $PQuery->where('product_store.net_price', '>=', $request->query('price_from'));
        }
        if ($request->filled('discount')) {
            $PQuery->where('product_store.discount', '!=', 0);
        }
        if ($request->filled('sort_by_date')) {
            $PQuery->orderBy('products.created_at', $request->query('sort_by_date'));
        }
        if ($request->filled('sort_by_price')) {
            $PQuery->orderBy('price', $request->query('sort_by_price'));
        }
        if ($request->filled('sort_by_rate')) {
            $PQuery->orderBy('best_rated', $request->query('sort_by_rate'));
        }
        if ($request->filled('sort_by_most_selling')) {
            $PQuery->orderBy('best_selling', $request->query('sort_by_most_selling'));
        }
        if ($request->filled('discount')) {
            $PQuery->orderBy('product_store.discount', 'desc');
        }
        if ($request->filled('newArrivals') || $request['just_for_you']) {
            $PQuery->orderBy('products.created_at', 'desc');
        }
        if ($request->filled('product_id')) {
            $PQuery->where('products.id', '!=', $request->query('product_id'));
        }

        if ($isStoreProfile) {
            $PQuery->where('stores.id', '=', $storeId);
        } else {
            $PQuery->where('stores.id', '!=', $storeId);
        }
        if ($request->filled('channel')) {
            $PQuery->where('stores.store_type_id', '=', StoreType::RETAILER);
        }
        if ($query) {
            $PQuery->where(function ($result) use ($query) {
                $result->where('products.name', 'ILIKE', '%' . $query . '%');
//                        ->orWhere('barcode', 'ILIKE', '%' . $query . '%');
            });
        }
        $PQuery->where('product_store.activation', '=', true);
        $price = 'product_store.price as price';
//        if ($request['type_id'] == UserType::SELLER) {
//            $price = DB::raw("CASE WHEN min(bundles.price) != max(bundles.price) THEN CONCAT(min(bundles.price) , '-' , max(bundles.price)) WHEN min(bundles.price) IS NULL THEN '' ELSE min(bundles.price)::varchar END AS price_range");
//        }
        $PQuery->select(
            ['products.id',
                'products.name as product_name',
                'stores.id as store_id',
                'stores.name as store_name',
                'products.brand_id',
                'products.policy_id',
                DB::raw('CASE WHEN products.policy_id = '. APolicyTypes::WekalaPrime .' THEN true else false END as is_wekala_policy'),
                'products.consumer_price',
                "brands.name_$lang as brand_name",
                'product_store.discount',
                'product_store.free_shipping',
                'product_store.created_at',
                $price,
                'product_store.net_price',
                'stores.name as store_name',
                'products.created_at',
                'products.material_rate',
                "materials.name_$lang as material_name",
                'products.category_id',
                'packing_unit_product.basic_unit_count',
                DB::raw('CASE WHEN COUNT(favorited_id) > 0 THEN true else false END as is_favorited'),
                DB::raw('count(order_products.product_id) as best_selling'),
                DB::raw('avg(seller_rates.rate) as best_rated')
            ])->distinct()
            ->groupBy(['products.id',
                'stores.id',
                'stores.name',
                'product_store.discount',
                'brands.name_' . $lang,
                'product_store.price',
                'product_store.net_price',
                'packing_unit_product.basic_unit_count',
                'product_store.free_shipping',
                'product_store.activation',
                'product_store.created_at',
                'products.category_id',
                'products.material_rate',
                "materials.name_$lang",
            ])
            ->orderBy('product_store.created_at', 'desc');

        return $PQuery;
    }


    public static function productAttrs($packingUnitProduct_id)
    {
        return PackingUnitProductAttribute::query()
            ->where('packing_unit_product_id', $packingUnitProduct_id)
            ->get();
    }

    public function toggleSwitchActivation($id)
    {
        $product = Product::query()->findOrFail($id);
        if ($product->activation) {
            $product->activation = false;
        } else {
            $product->activation = true;
        }
        $product->save();
        return $product;
    }

    public static function feeds($storeId = null, $page = 1)
    {
        if (!$page) {
            $page = 1;
        }
        $offset = ($page - 1) * 10;
        $stores = DB::select("
        select stores.name as store_name,stores.id as store_id,stores.logo as store_logo ,stores.created_at as store_created_at
             ,product_store.product_id as product_id,product_store.price as product_price,product_store.discount as product_discount,
             product_store.net_price as product_net_price,product_store.created_at, products.name as product_name,
             product_images.image,products.category_id
        from stores
        left join  product_store on stores.id =product_store.store_id
        left join  products on product_store.product_id =products.id
        left join product_images on product_store.product_id =product_images.product_id
        where stores.id in (select distinct store_id from product_store LIMIT 10 OFFSET $offset )

        order by product_store.created_at desc
        ");
        $countStores = DB::select('select count( DISTINCT  store_id) from product_store');
        $images = [];
        $data = [];
        $products = [];
        $data[]['totalPages'] = round($countStores[0]->count / 10);
        foreach ($stores as $store) {
            if ($storeId) {
                if ($storeId != $store->store_id) {
                    continue;
                }
            }
            $data[$store->store_id]['store_id'] = $store->store_id;
            $data[$store->store_id]['store_name'] = $store->store_name;
            $data[$store->store_id]['store_created_at'] = $store->store_created_at;
            $data[$store->store_id]['logo'] = ($store->store_logo) ? config('filesystems.aws_base_url') . $store->store_logo : '';
            $products[$store->store_id]['products'][] = [
                'product_id' => $store->product_id,
                'product_price' => $store->product_price,
                'product_net_price' => $store->product_net_price,
                'created_at' => $store->created_at,
                'product_name' => $store->product_name,
                'category_id' => $store->category_id,
                'product_discount' => $store->product_discount . '%',
                'product_image' => ($store->image) ? config('filesystems.aws_base_url') . $store->image : null
            ];
            $data[$store->store_id]['products'] = array_values($products[$store->store_id]['products']);
            /*  if ($store->image) {
                  $images[$store->product_id][] = ($store->image) ? config('filesystems.aws_base_url') . $store->image : null;
                  $products[$store->store_id]['products'][$store->product_id]['images'] = $images[$store->product_id];

              } else {
                  $data[$store->store_id]['products'][$store->product_id]['images'] = [];
              }*/


        }
        return array_values($data);
    }

    public function getProductDetails($productId, $storeId)
    {
        $query = Product::query()
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('product_images', 'product_images.product_id', 'products.id')
            ->join('product_store', 'products.id', '=', 'product_store.product_id')
            ->join('product_store_stock', 'product_store.id', '=', 'product_store_stock.product_store_id')
            ->join('colors', 'colors.id', '=', 'product_store_stock.color_id')
            ->join('sizes', 'sizes.id', '=', 'product_store_stock.size_id')
            ->join('stores', 'stores.id', '=', 'product_store.store_id')
            ->where('product_store.product_id', $productId)
            ->where('product_store.store_id', $storeId);
        return $query->select(
            'products.id as product_id',
            'products.name as product_name',
            'products.description',
            'categories.name_' . $this->locale . ' as category_name',
            'categories.id as category_id',
            'product_store.price as price',
            'product_store.net_price as net_price',
            DB::raw('CASE WHEN product_store.discount > 0 THEN true else false END as has_discount'),
            'product_images.image as product_image',
            'product_images.id as product_image_id',
            'product_images.is_primary as is_primary',
            'product_images.color_id as image_color_id',
//            'colors.id as color_id',
//            'colors.name_' . $this->locale . ' as color_name',
//            'colors.hex as color_code',
            'sizes.id as size_id',
            'sizes.size as size_name',
            'product_store_stock.available_stock as available_stock',
            'product_store_stock.id as product_store_stock_id',
            'stores.name as store_name'
        )
            ->orderBy('product_images.color_id')->get();
    }

    public function getProductStore($productId, $storeId)
    {
        return ProductStore::query()->where('product_id', $productId)->where('store_id', $storeId)->first();
    }

    public function getProductReviews($productId, $limit = 0)
    {
        $query = ProductRate::query()->where('product_id', $productId)
            ->join('users', 'users.id', '=', 'product_ratings.user_id')
            ->select(
                'product_ratings.amount',
                'product_ratings.review',
                'product_ratings.image',
                'product_ratings.created_at',
                'users.name as user_name', 'users.image as user_image');
        if ($limit != 0) {
            return $query->limit($limit)->get();
        }
        return $query->paginate(20);
    }

    public function getProductRatings($productId)
    {
        return ProductRate::query()->where('product_id', $productId)
            ->select(DB::raw('SUM(amount) as rating'), DB::raw('COUNT(*) as reviews_count'))
            ->get();
    }

    public function syncBadges($data)
    {
        try {
            $product = $this->model->newQuery()->find($data['id']);

            $product->badges()->sync($data['badges']);

        } catch (Exception $e) {

            DB::rollBack();
        }
    }

    public function approveProduct(Product $product)
    {
        $product->reviewed = true;
        $product->save();
        if ($product->shipping_method_id == 1) {
            //approve product with stock 0
            $product->productStore->productStoreStock()->update([
                'available_stock' => 0
            ]);
        }
    }
}
