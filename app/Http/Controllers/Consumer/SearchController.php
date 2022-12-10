<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Enums\UserTypes\UserType;
use App\Http\Controllers\BaseController;
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
                'message' => 'Near By Stores',
                'data' => $stores
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getNearByStores of seller search ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function searchProducts(Request $request)
    {

        try {
            $userId = UserId::UserId($request);
            $query = trim($request->input('query'));
            $lang = LangHelper::getDefaultLang($request);
            $products = Product::query()
                ->with('productImage')
                ->leftJoin('product_store', 'products.id', '=', 'product_store.product_id')
                ->leftJoin('stores', 'product_store.store_id', '=', 'stores.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('barcode_product', 'barcode_product.product_id', '=', 'products.id')
//                ->leftJoin('bundles', function ($join) {
//                    $join->on('products.id', '=', 'bundles.product_id');
//                    $join->on('stores.id', '=', 'bundles.store_id');
//                })
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
                $products->where('products.brand_id', $request->query('brand_id'));
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
            $products->where('product_store.activation', '=', true);
            $products->select(
                ['products.id',
                    'products.name as product_name',
                    'stores.id as store_id',
                    'stores.name as store_name',
                    'products.brand_id',
                    "brands.name_$lang as brand_name",
                    'product_store.discount',
                    'product_store.price',
                    'product_store.net_price',
                    'product_store.created_at',
//                    DB::raw("CASE WHEN min(bundles.price) != max(bundles.price) THEN CONCAT(min(bundles.price) , '-' , max(bundles.price)) WHEN min(bundles.price) IS NULL THEN '' ELSE min(bundles.price)::varchar END AS price_range"),
                    'stores.name as store_name',
                    'products.created_at',
                    DB::raw('CASE WHEN COUNT(favorited_id) > 0 THEN true else false END as is_favorited')
                ])->distinct()
                ->groupBy(['products.id',
                    'stores.id',
                    'stores.name',
                    'product_store.discount',
                    "brands.name_$lang",
                    'product_store.price',
                    'product_store.created_at',
                    'product_store.net_price'])
                ->orderBy('product_store.created_at', 'desc');

            $products = $products->paginate(10);

            foreach ($products as $product) {
                if ($product->discount != 0) {
                    $product->has_discount = true;
                    $product->discount_type = 'percentage';
                    $product->discount = $product->discount . '%';
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
                unset($product->productImage);
            }
            return response()->json([
                'status' => true,
                'message' => 'search products',
                'data' => $products
            ], AResponseStatusCode::SUCCESS);


        } catch
        (\Exception $e) {
            Log::error('error in searchProducts of seller search ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function searchStores(Request $request)
    {
        try {
            $userId = UserId::UserId($request);
            $query = trim($request->input('query'));
            $q = Store::query()
                ->select('stores.id', 'stores.name', 'stores.logo',
                    DB::raw('CASE WHEN COUNT(followed_stores.store_id) > 0 THEN true else false END as is_followed'))
                ->leftJoin('category_store', 'category_store.store_id', '=', 'stores.id')
                ->leftJoin('followed_stores', function ($join) use ($userId) {
                    $join->on('stores.id', 'followed_stores.store_id')
                        ->where('followed_stores.user_id', $userId);
                })
                ->join('users', 'users.id', '=', 'stores.user_id')
                ->with(['products' => function ($query) {
                    $query->where('reviewed', true);
                }])
                ->with('SellerRate')
                ->where('stores.name', 'ILIKE', '%' . $query . '%')
                ->where('stores.is_verified', true)
                ->where('users.activation', true)
                ->where('stores.store_type_id', StoreType::RETAILER);

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
                    if ($product->discount != 0) {
                        $product->has_discount = true;
                        $product->discount = $product->discount . '%';
                    } else {
                        $product->has_discount = false;
                    }
                    unset($product->productImage);
                    unset($product->pivot);
                    if ($product->image)
                        $product->image = config('filesystems.aws_base_url') . $product->image;
                }
                if ($store->logo)
                    $store->logo = config('filesystems.aws_base_url') . $store->logo;
            }

            return response()->json([
                'status' => true,
                'message' => 'search stores',
                'data' => $stores
            ], AResponseStatusCode::SUCCESS);


        } catch
        (\Exception $e) {
            Log::error('error in searchStores of seller search ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
