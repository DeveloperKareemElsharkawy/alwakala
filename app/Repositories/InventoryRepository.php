<?php


namespace App\Repositories;


use App\Enums\Product\AProductStatus;
use App\Http\Controllers\Controller;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\ProductStore;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class InventoryRepository extends Controller
{
    /**
     * @param $request
     * @param $status
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getInventoryQuery($request, $status = null)
    {
        $pageSize = $request->pageSize ? $request->pageSize : 10;
        $lang = LangHelper::getDefaultLang($request);
        $storeId = StoreRepository::getStoreByUserId($request->user_id);
        $q = ProductStore::query()
            ->select([
                'product_store.product_id as product_id',
                'product_store.store_id as store_id',
                'products.name as product_name',
                'products.reviewed',
                'product_store.publish_app_at as publish_at_date',
                'product_store.created_at',
                'product_store.activation',
                'product_store.price',
                'product_store.net_price',
                'product_store.discount',
                'products.material_rate',
                'brands.name_' . $lang . ' as brand_name',
                'categories.name_' . $lang . ' as category_name',
                'materials.name_' . $lang . ' as material_name',
                DB::raw("SUM(product_store_stock.stock) as stock,
                    SUM(product_store_stock.available_stock) as available_stock,
                    SUM(product_store_stock.reserved_stock) as reserved_stock,
                    SUM(product_store_stock.sold) as sold"),

            ])->distinct(['product_store.product_id', 'product_store.created_at'])
            ->with('productImage')
            ->join('products', 'products.id', '=', 'product_store.product_id')
            ->join('product_store_stock', 'product_store_stock.product_store_id', '=', 'product_store.id')
            ->join('materials', 'products.material_id', '=', 'materials.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->orderBy('product_store.created_at', 'desc')
            ->groupBy(['product_store.id', 'products.reviewed', 'products.name', 'material_rate', 'materials.name_' . $lang, 'brands.name_' . $lang, 'categories.name_' . $lang])
            ->where('product_store.store_id', $storeId->id)
            ->where('product_store.activation', '=', true);
//                ->where('products.reviewed', true)
        if ($request->query && $request->query != '') {
            $q = $q->where('products.name', 'ILIKE', '%' . $request->get('query') . '%');
        }
        if ($request->category && $request->category != '') {
            $q = $q->where('products.category_id', $request->category);
        }
        if ($request->brand && $request->brand != '') {
            $q = $q->where('products.brand_id', $request->brand);
        }

        if ($request->has('products_ids')) {
            $q = $q->whereIn('products.id', $request['product_ids'] ?? []);
        }

        if ($status == AProductStatus::AVAILABLE) {
            $q->where('available_stock', '>', 0);
            $q->where('products.reviewed', '=', true);
            $q->where('product_store.publish_app_at', '<=', Carbon::today());
        } elseif ($status == AProductStatus::IN_REVIEW) {
            $q->where('products.reviewed', '=', false);
        } elseif ($status == AProductStatus::NOT_AVAILABLE) {
            $q->where('available_stock', '=', 0);
        } elseif ($status == AProductStatus::SOON) {
            $q->where('product_store.publish_app_at', '>', Carbon::now());
        }

        if ($request->filled('policy_id')) {
            $q->where('products.policy_id', (int)$request->policy_id);
        }

        return $q->paginate($pageSize);
    }

    /**
     * @param $request
     * @param $status
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getpurchasedProductsQuery($request, $productStoreIds = [])
    {
        $pageSize = $request->pageSize ? $request->pageSize : 10;
        $lang = LangHelper::getDefaultLang($request);
        $q = ProductStore::query()
            ->select([
                'product_store.id as product_store_id',
                'product_store.product_id as product_id',
                'product_store.store_id as store_id',
                'products.name as product_name',
                'products.policy_id',
                'products.reviewed',
                'product_store.publish_app_at as publish_at_date',
                'product_store.created_at',
                'product_store.activation',
                'product_store.price',
                'product_store.net_price',
                'product_store.discount',
                'products.material_rate',
                'brands.name_' . $lang . ' as brand_name',
                'categories.name_' . $lang . ' as category_name',
                'materials.name_' . $lang . ' as material_name',

            ])->distinct(['product_store.product_id', 'product_store.created_at'])
            ->with('productImage')
            ->join('products', 'products.id', '=', 'product_store.product_id')
            ->join('product_store_stock', 'product_store_stock.product_store_id', '=', 'product_store.id')
            ->join('materials', 'products.material_id', '=', 'materials.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->orderBy('product_store.created_at', 'desc')
            ->groupBy(['product_store.id', 'products.reviewed', 'products.name','products.policy_id', 'material_rate', 'materials.name_' . $lang, 'brands.name_' . $lang, 'categories.name_' . $lang])
            ->whereIn('product_store.id', $productStoreIds);

        return $q->paginate($pageSize);
    }

}
