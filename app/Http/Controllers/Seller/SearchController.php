<?php

namespace App\Http\Controllers\Seller;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Enums\UserTypes\UserType;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Seller\Store\StoreOpeningHoursResource;
use App\Http\Resources\Seller\Store\StoreProfileResource;
use App\Http\Resources\Seller\StoreResource;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SearchController extends BaseController
{
    public function getNearByStores(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
//                'longitude' => 'required|string|max:255',
//                'latitude' => 'required|string|max:255',
//                'distance' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $longitude = (float)$request->longitude;
            $latitude = (float)$request->latitude;
            $distance = (int)$request->distance;

            $query = Store::query()
                ->select('id', 'name', 'logo');
            if ($request->filled('latitude')) {
                $query->whereRaw("
                ST_DistanceSphere( ST_MakePoint($longitude, $latitude),
                    ST_MakePoint(
                        cast(stores.longitude as double precision),
                        cast(stores.latitude as double precision)
                    )
                ) <= $distance * 1000
                ");
            }

            $stores = $query->paginate(10);

            foreach ($stores as $store) {
                if ($store->logo)
                    $store->logo = config('filesystems.aws_base_url') . $store->logo;
            }
            return response()->json([
                'status' => true,
                'message' => trans('messages.stores.near_by_stores'),
                'data' => $stores
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getNearByStores of seller search ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function searchProducts(Request $request)
    {
        $lang = LangHelper::getDefaultLang($request);

        try {
            $userId = UserId::UserId($request);
            $storeId = null;
            if ($userId) {
                $store = Store::query()->where('user_id', $userId)->first();
                $storeId = $store->id;
            }
            $query = trim($request->input('query'));

            $products = Product::query()
                ->with('productImage')
                ->leftJoin('product_store', 'products.id', '=', 'product_store.product_id')
                ->leftJoin('stores', 'product_store.store_id', '=', 'stores.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('barcode_product', 'barcode_product.product_id', '=', 'products.id')
                ->leftJoin('packing_unit_product', function ($join) {
                    $join->on('products.id', '=', 'packing_unit_product.product_id')
                        ->where('packing_unit_product.packing_unit_id', 1);
                })
                ->leftJoin('seller_favorites', function ($join) use ($userId) {
                    $join->on('products.id', 'seller_favorites.favorited_id')
                        ->on('seller_favorites.store_id', 'stores.id')
                        ->where('favoriter_type', User::class)
                        ->where('favoriter_id', $userId)
                        ->where('favorited_type', Product::class);
                });
            if (request()->segment(2) == 'consumer-app') {
                $products->where('stores.store_type_id', StoreType::RETAILER);
            } else {
                $products->where('stores.store_type_id', StoreType::SUPPLIER);
            }

            $products->where('products.reviewed', true)
                ->where('products.name', 'ILIKE', '%' . $query . '%')
                ->orWhere('barcode_product.barcode', $query);

            if ($request->filled('longitude') && $request->filled('latitude')) {
                $longitude = (float)$request->longitude;
                $latitude = (float)$request->latitude;
                $distance = (int)$request->distance;
                $products->whereRaw("
                ST_DistanceSphere( ST_MakePoint($longitude, $latitude),
                    ST_MakePoint(
                        cast(stores.longitude as double precision),
                        cast(stores.latitude as double precision)
                    )
                ) <= $distance * 1000
                ");
            }
            if ($request->filled('city_id')) {
                $products->where('stores.city_id', $request->query('city_id'));
            }
            if ($request->filled('brand_id')) {
                $products->whereIn('products.brand_id', $request->query('brand_id'));
            }
            if ($request->filled('category_id')) {
                $products->where('products.category_id', $request->query('category_id'));
            }
            if ($request->filled('date')) {
                $products->orderBy('products.created_at', $request->query('date'));
            }
            if ($request->filled('price')) {
                $products->orderBy('product_store.net_price', $request->query('price'));
            }
            $products->where('stores.id', '!=', $storeId);
            $products->where('product_store.activation', '=', true);
            $products->select(
                ['products.id',
                    'products.name as product_name',
                    'stores.id as store_id',
                    'stores.name as store_name',
                    'products.brand_id',
                    "brands.name_$lang as brand_name",
                    'product_store.discount',
                    'product_store.free_shipping',
                    'product_store.barcode',
                    'product_store.barcode_text',
                    'product_store.price',
                    'product_store.net_price',
                    'product_store.created_at',
//                    DB::raw("CASE WHEN min(bundles.price) != max(bundles.price) THEN CONCAT(min(bundles.price) , '-' , max(bundles.price)) WHEN min(bundles.price) IS NULL THEN '' ELSE min(bundles.price)::varchar END AS price_range"),
                    "stores.name as store_name",
                    'products.created_at',
                    'packing_unit_product.basic_unit_count',
                    DB::raw('CASE WHEN COUNT(favorited_id) > 0 THEN true else false END as is_favorited')
                ])->distinct()
                ->groupBy(['products.id',
                    'stores.id',
                    'stores.name',
                    'product_store.discount',
                    'product_store.barcode',
                    'product_store.barcode_text',
                    'brands.name_' . $lang,
                    'product_store.price',
                    'product_store.created_at',
                    'product_store.net_price',
                    'product_store.free_shipping',
                    'packing_unit_product.basic_unit_count'])
                ->orderBy('product_store.created_at', 'desc');

            $products = $products->paginate(10);

            $isActive = false;
            if ($userId) {
                $isActive = User::query()
                    ->select('activation')
                    ->where('id', $userId)
                    ->first()->activation;
            }
            foreach ($products as $product) {
                $product->price = ProductHelper::canShowPrice($userId, $isActive, $product->price);
                $product->net_price = ProductHelper::canShowPrice($userId, $isActive, $product->net_price);
                if ($product->discount != 0 && $userId && $isActive) {
                    $product->has_discount = true;
//                    if ($product->discount_type == DiscountTypes::AMOUNT) {
//                        $product->discount_type = 'amount';
//                    } else {
                    $product->discount_type = 'percentage';
                    $product->discount = $product->discount . '%';
//                    }
                } else {
                    $product->has_discount = false;
                }
                if (count($product->SellerRate) > 0) {
                    $product->rate = $product->SellerRate[0]->rate;
                } else {
                    $product->rate = 0;
                }
                unset($product->SellerRate);
                $product->image = null;
                if ($product->productImage) {
                    $product->image = config('filesystems.aws_base_url') . $product->productImage->image;
                }

                if ($product->barcode) {
                    $product->barcode = config('filesystems.aws_base_url') . $product->barcode;
                }

                unset($product->productImage);
            }


            return response()->json([
                'status' => true,
                'message' => trans('messages.stores.search_products'),
                'data' => [
                    'stores' => StoreProfileResource::collection($this->searchStores($request, true)),
                    'products' => $products
                ]
            ], AResponseStatusCode::SUCCESS);


        } catch
        (\Exception $e) {
            Log::error('error in searchProducts of seller search ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function searchStores(Request $request, $inline = false)
    {

        try {
            $userId = UserId::UserId($request);
            $storeId = null;
            if ($userId) {
                $store = Store::query()->where('user_id', $userId)->first();
                $storeId = $store->id;
            }
            $query = trim($request->input('query'));

            $q = Store::query()->whereHas('products')
                ->select('stores.id', 'stores.name', 'stores.logo', 'stores.store_type_id',
                    DB::raw('CASE WHEN COUNT(followed_stores.store_id) > 0 THEN true else false END as is_followed'))
                ->leftJoin('category_store', 'category_store.store_id', '=', 'stores.id')
                ->leftJoin('followed_stores', function ($join) use ($userId) {
                    $join->on('stores.id', 'followed_stores.store_id')
                        ->where('followed_stores.user_id', $userId);
                })
//                ->with(['products' => function ($query) {
//                    $query->where('reviewed', true);
//                }])
                ->with('SellerRate')
                ->where('stores.name', 'ILIKE', '%' . $query . '%');
            if (request()->segment(2) == 'consumer-app') {
                $q->where('stores.store_type_id', StoreType::RETAILER);
            }

            $q->where('stores.id', '!=', $storeId);

            if ($request->filled('longitude') && $request->filled('latitude')) {
                $longitude = (float)$request->longitude;
                $latitude = (float)$request->latitude;
                $distance = (int)$request->distance;
                $q->whereRaw("
                ST_DistanceSphere( ST_MakePoint($longitude, $latitude),
                    ST_MakePoint(
                        cast(stores.longitude as double precision),
                        cast(stores.latitude as double precision)
                    )
                ) <= $distance * 1000
                ");
            }
            if ($request->filled('category_id')) {
                $q->where('category_store.category_id', $request->query('category_id'));
            }
            if ($request->filled('city_id')) {
                $q->where('stores.city_id', $request->query('city_id'));
            }

            $q->groupBy('stores.id');

            $stores = $q->paginate(10);

            if ($inline){
                $stores = $q->limit(3)->get();
            }

            $isActive = false;
            if ($userId) {
                $isActive = User::query()
                    ->select('activation')
                    ->where('id', $userId)
                    ->first()->activation;
            }
            foreach ($stores as $store) {
                if (count($store->SellerRate) > 0) {
                    $store->rate = $store->SellerRate[0]->rate;
                } else {
                    $store->rate = 0;
                }
                unset($store->SellerRate);

                foreach ($store->products as $product) {
                    if ($product->productImage) {
                        $product->image = $product->productImage->image;
                    } else {
                        $product->image = null;
                    }
                    $product->price = ProductHelper::canShowPrice($userId, $isActive, $product->price);
                    $product->net_price = ProductHelper::canShowPrice($userId, $isActive, $product->net_price);
//                    unset($product->productPrice);
                    unset($product->productImage);
                    unset($product->pivot);
                    if ($product->image)
                        $product->image = config('filesystems.aws_base_url') . $product->image;
                }
                if ($store->logo)
                    $store->logo = config('filesystems.aws_base_url') . $store->logo;
            }

            if ($inline){
                return $stores;
            }

            return response()->json([
                'status' => true,
                'message' => trans('messages.stores.search_stores'),
                'data' => $stores
            ], AResponseStatusCode::SUCCESS);


        } catch
        (\Exception $e) {
            Log::error('error in searchStores of seller search ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
