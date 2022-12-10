<?php

namespace App\Http\Controllers\Seller;

use App\Enums\Orders\AOrders;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Log;

class StatisticsController extends BaseController
{

    /**
     * @return JsonResponse
     */
    public function productsStatistics(): \Illuminate\Http\JsonResponse
    {
        try {
            $storesId = Seller::query()->where('user_id', request()->user_id)->first()->store_id;

            $product_stores = ProductStore::query()->where('store_id', $storesId)->get();

            $products = Product::query()->whereIn('id', $product_stores->pluck('product_id')->toArray())->get();

            $stocks = ProductStoreStock::query()->whereIn('product_store_id', $product_stores->pluck('id')->toArray())->get(); // get stocks by product stores rel

            return $this->success(['message' => trans('messages.general.listed'), 'data' => [
                'products_count' => count($products),
                'instock' => $stocks->sum('stock'),
                'available_stock' => $stocks->sum('available_stock'),
                'sold' => $stocks->sum('sold'),
                'returned' => $stocks->sum('returned'),
            ]]);

        } catch (\Exception $e) {
            Log::error('error in Yearly Products Statics in seller Dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param $period
     * @return JsonResponse
     */
    public function productsStatisticsCharts($period): \Illuminate\Http\JsonResponse
    {
        try {
            switch ($period) {

                case 'yearly-statistics':
                    $statistics = Product::query()->where('owner_id', request()->user_id)
                        ->selectRaw("to_char(created_at, 'YYYY') as year, count(*) as products_count")->groupBy('year')->get();
                    break;

                case 'monthly-statistics':
                    $statistics = Product::query()->where('owner_id', request()->user_id)
                        ->selectRaw("to_char(created_at, 'YYYY-MM') as month, count(*) as products_count")->groupBy('month')->get();
                    break;

                case 'weekly-statistics':
                    $statistics = Product::query()->where('owner_id', request()->user_id)
                        ->selectRaw("date_trunc('week', created_at) as week_start,date_trunc('week', created_at) + INTERVAL '7 DAY' as week_end,  count(*) as products_count")
                        ->groupBy('week_start')->get();
                    break;

                case 'daily-statistics':
                    $statistics = Product::query()->where('owner_id', request()->user_id)
                        ->selectRaw("to_char(created_at, 'YYYY-MM-DD') as day, count(*) as products_count")->groupBy('day')->get();
                    break;
            }

            return $this->success(['message' => trans('messages.general.listed'), 'data' => $statistics ?? []]);
        } catch (\Exception $e) {
            Log::error('error in Yearly Products Statics in seller Dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     */
    public function orderStatistics(): JsonResponse
    {
        try {
            $storesId = Seller::query()->where('user_id', request()->user_id)->first()->store_id;

            $orders = Order::query()->where('store_id', $storesId)->get();

            return $this->success(['message' => trans('messages.general.listed'), 'data' => [
                'placed_orders' => count($orders->where('status_id', AOrders::ISSUED)),
                'processing_orders' => count($orders->where('status_id', AOrders::IN_PROGRESS)),
                'delivered_orders' => count($orders->where('status_id', AOrders::RECEIVED)),
                'canceled_orders' => count($orders->where('status_id', AOrders::CANCELED)),
                'rejected_orders' => count($orders->where('status_id', AOrders::REJECT)),
            ]]);
        } catch (\Exception $e) {
            Log::error('error in Yearly Orders Statics in seller Dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param $period
     * @return JsonResponse
     */
    public function ordersStatisticsCharts($period): \Illuminate\Http\JsonResponse
    {
        try {
            $storesId = Seller::query()->where('user_id', request()->user_id)->first()->store_id;

            switch ($period) {

                case 'yearly-statistics':
                    $statistics = Order::query()->where('store_id', $storesId)
                        ->selectRaw("to_char(created_at, 'YYYY') as year, count(*) as orders_count")->groupBy('year')->get();
                    break;

                case 'monthly-statistics':
                    $statistics = Order::query()->where('store_id', $storesId)
                        ->selectRaw("to_char(created_at, 'YYYY-MM') as month, count(*) as orders_count")->groupBy('month')->get();
                    break;

                case 'weekly-statistics':
                    $statistics = Order::query()->where('store_id', $storesId)
                        ->selectRaw("date_trunc('week', created_at) as week_start,date_trunc('week', created_at) + INTERVAL '7 DAY' as week_end,  count(*) as orders_count")
                        ->groupBy('week_start')->get();
                    break;

                case 'daily-statistics':
                    $statistics = Order::query()->where('store_id', $storesId)
                        ->selectRaw("to_char(created_at, 'YYYY-MM-DD') as day, count(*) as orders_count")->groupBy('day')->get();
                    break;
            }

            return $this->success(['message' => trans('messages.general.listed'), 'data' => $statistics ?? []]);
        } catch (\Exception $e) {
            Log::error('error in Yearly Products Statics in seller Dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
