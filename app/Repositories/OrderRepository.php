<?php

namespace App\Repositories;

use App\Enums\Orders\AOrders;
use App\Enums\Orders\OrdersTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\Roles\ARoles;
use App\Enums\StoreTypes\StoreType;
use App\Enums\UserTypes\UserType;
use App\Http\Resources\Seller\Orders\OrderDetailsResource;
use App\Http\Resources\Seller\Orders\OrderDetailsStoreResource;
use App\Http\Resources\Seller\Orders\OrdersStoresResource;
use App\Http\Resources\Seller\Orders\OrdersCollection;
use App\Http\Resources\Seller\Orders\OrdersResource;
use App\Lib\Helpers\Address\AddressHelper;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\StoreId\StoreId;
use App\Lib\Helpers\UserId\UserId;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ParentOrder;
use App\Models\ProductOrderUnitDetails;
use App\Models\ProductStore;
use App\Models\SellerRate;
use App\Models\Size;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public function insertOrderProducts($orderProducts)
    {
        OrderProduct::query()->insert($orderProducts);

    }

    public function getOrdersByStatus($request)
    {
        $lang = LangHelper::getDefaultLang($request);
        $pageSize = $request->pageSize ? $request->pageSize : 10;
        $store_id = null;
        $store = Store::query()->where('user_id', $request->user_id)->first();
        if ($store) {
            $store_id = $store->id;
        }
        $query = Order::query()
            ->join('order_products', 'orders.id', '=', 'order_products.order_id');
        $query->join('users', 'orders.user_id', '=', 'users.id');
        $query->join('stores', function ($join) {
            $join->on('orders.store_id', '=', 'stores.id');
        });
        if ($request->order_type == OrdersTypes::FROM_CONSUMER) {
            $query->where('users.type_id', '=', UserType::CONSUMER);
        }
        if ($request->order_type == OrdersTypes::FROM_SELLER) {
            $query->where('users.type_id', '=', UserType::SELLER);
            $query->where('stores.store_type_id', '=', StoreType::SUPPLIER);

        }
        if ($request->order_type == OrdersTypes::TO_SELLER) {
            $query->where('orders.user_id', '=', $request->user_id);
        }
        if ($request->order_type == OrdersTypes::FROM_STORE) {
            $query->where('users.type_id', '=', UserType::SELLER);
            $query->where('stores.store_type_id', '=', StoreType::RETAILER);

        }
        if ($store_id && ($request->order_type != OrdersTypes::TO_SELLER && request()->segment(2) == 'seller-app')) {
            $query->where('stores.id', '=', $store_id);
        }
        $query->join('order_statuses', 'orders.status_id', '=', 'order_statuses.id');

        $query->join('cities', 'stores.city_id', '=', 'cities.id')
            ->select(
                'orders.id',
                "users.name as user_name",
                "users.mobile as user_mobile",
                "stores.mobile as store_mobile",
                "stores.name as store_name",
                "cities.name_" . $lang . " as city_name",
                "order_statuses.status_" . $lang . " as status",
                "order_statuses.status_en as color_key",
                'orders.total_price',
                'orders.number',
                'orders.delivery_date',
                DB::raw('count(order_products.product_id) as product_count'),
                DB::raw('SUM(order_products.purchased_item_count * order_products.basic_unit_count)  as item_count')
            )->groupBy('orders.id', 'stores.mobile', 'users.name', 'users.mobile', 'stores.name', 'cities.name_ar', 'orders.delivery_date', 'order_statuses.status_en', 'order_statuses.status_ar', 'cities.name_' . $lang);
        if ($request->filled("id")) {
            $query->where('orders.id', intval($request->id));
        }
        if ($request->filled("store")) {
            $query->where('orders.store_id', intval($request->store));
        }
        if ($request->filled("client")) {
            $query->where('orders.user_id', intval($request->client));
        }
        if ($request->filled("status")) {
            $query->where('orders.status_id', intval($request->status));
        }
        if ($request->filled("number")) {
            $query->where('orders.number', intval($request->number));
        }
        if ($request->filled("sort_by_id")) {
            $query->orderBy('orders.id', $request->sort_by_id);
        }
        if ($request->filled("sort_by_store")) {
            $query->orderBy('orders.store_id', $request->sort_by_store);
        }
        if ($request->filled("sort_by_client")) {
            $query->orderBy('orders.user_id', $request->sort_by_client);
        }
        if ($request->filled("sort_by_status")) {
            $query->orderBy('orders.status_id', $request->sort_by_status);
        }
        if ($request->filled("sort_by_number")) {
            $query->orderBy('orders.number', $request->sort_by_number);
        }
        if ($request->filled('filter_by_today') && $request->filter_by_today == 1) {
            $query->whereDate('orders.created_at', '=', date('Y-m-d'));
        }
        if ($request->filled('filter_by_date')) {
            $query->whereDate('orders.created_at', Carbon::parse($request->filter_by_date)->format('Y-m-d'));
        }
        if ($request->filled('filter_by_delivery_date')) {
            $query->where('orders.delivery_date', Carbon::parse($request->filter_by_delivery_date)->format('Y-m-d'));
        }
        if ($request->filled('filter_by_city_filter')) {
            $query->where('city_id', $request->filter_by_city_filter);
        }
        if ($request->filled('sort_by_total_price') && in_array($request->sort_by_total_price, ['asc', 'desc'])) {
            $query->OrderBy('total_price', $request->sort_by_total_price);
        }
        return $query->paginate($pageSize);
    }

    public function changeStatusOfOrdersProducts($data)
    {
        $product = OrderProduct::query()->where('id', $data['id'])->first();

        $product->status_id = $data['status_id'];
        $product->delivery_date = $data['delivery_date'];
        $product->save();
    }

    public function getOrderDetails($id, $lang, $parent_order = false)
    {
        $order = Order::query()
            ->select(
                'id',
                'total_price',
                'user_id',
                'status_id',
                'created_at',
                'number'
            )
            ->with('client', 'last_status', 'order_address')
            ->where('id', $id)
            ->first();
        $order_ids = [$order->id];
        $order->products = OrderProduct::query()
            ->select('order_products.id',
                'order_products.packing_unit_id',
                'product_id',
                'purchased_item_count',
                'order_products.size_id',
                'item_price',
                "order_statuses.status_" . $lang . " as status",
                'color_id',
                'order_products.total_price',
                'order_products.status_id',
                'stores.id as store_id',
                'stores.name as store_name',
                'order_products.basic_unit_count'
            // 'sizes.size',
            //'product_order_unit_details.quantity'
            )->leftJoin('order_statuses', 'order_products.status_id', '=', 'order_statuses.id')
            ->leftJoin('orders', 'order_products.order_id', '=', 'orders.id')
            ->leftJoin('stores', 'stores.id', '=', 'orders.store_id')
            //->leftJoin('product_order_unit_details','product_order_unit_details.packing_unit_id','=','order_products.packing_unit_id')
            //->leftJoin('sizes','product_order_unit_details.size_id','=','sizes.id')
            ->whereIn('order_id', $order_ids)
            ->with(['product', 'color', 'size'])
            ->get();
        $total_items = 0;
        $required_price = 0;
        $paid_price = 0;
        if (!$parent_order) {
            $order->required_price = $required_price;
            $order->paid_price = $paid_price;
        }

        foreach ($order->products as $product) {
            $unit_details_arr = [];
            $unit_details = ProductOrderUnitDetails::query()->where('packing_unit_id', $product->packing_unit_id)->get();
            foreach ($unit_details as $unit) {
                $size = Size::query()->where('id', $unit->size_id)->first()->size;
                $unit_details_arr[] = [$size => $unit->quantity];
            }
            $product['unit_details'] = $unit_details_arr;
            $product->total_count = $product->purchased_item_count * $product->basic_unit_count;
            $product->image = config('filesystems.aws_base_url') . $product->product->productImage->image;
            unset($product->product->productImage);
            if (!$parent_order) {
                $order->total_items += $product->total_count;
                if ($product->status_id == AOrders::SHIPPING) {
                    $order->required_price += $product->total_price;
                }
                if ($product->status_id == AOrders::RECEIVED) {
                    $order->paid_price += $product->total_price;
                }
            } else {
                $total_items += $product->total_count;
                if ($product->status_id == AOrders::SHIPPING) {
                    $required_price += $product->total_price;
                }
                if ($product->status_id == AOrders::RECEIVED) {
                    $paid_price += $product->total_price;
                }
            }


        }
        if ($parent_order) {
            return ['products' => $order->products, 'total_items' => $total_items, 'required_price' => $required_price, 'paid_price' => $paid_price];
        }
        return $order;
    }

    public function changeStatusOfOrder($data)
    {
        DB::beginTransaction();
        $order = Order::query()->where('id', $data['id'])->first();
        $order->status_id = $data['status'];
        $order->save();
        $product = OrderProduct::query()->where('order_id', $order->id)->first();
        $product->status_id = $data['status'];
        // $product->delivery_date = $data['delivery_date'];
        $product->save();
        DB::commit();
    }

    public function getRequestedOrdersByUser($data, $user_id)
    {
        $objects = Order::query()
            ->where('user_id', '=', $user_id)
            ->orderBy('id', 'desc')
            ->when($data->query('filter_by_status'), function ($q) use ($data) {
                $q->where('status_id', $data->query('filter_by_status'));
            })
            ->paginate(config('dashboard.pagination_limit'));
        return OrdersResource::collection($objects)->response()->getData(true);
    }

    public function getOrdersByUser($data)
    {
        $store = Store::query()
            ->select('id')
            ->where('user_id', $data->user_id)
            ->first();

        // $offset = $data->query('offset') ? $data->query('offset') : 0;
        // $limit = $data->query('limit') ? $data->query('limit') : config('dashboard.pagination_limit');
        $objects = Order::query()
            ->whereHas('products', function ($q) use ($store) {
                $q->where('store_id', $store->id);
            })
            ->with('products', function ($q) use ($store) {
                $q->where('store_id', $store->id);
            })
            ->when($data->query('filter_by_status'), function ($q) use ($data) {
                $q->where('status_id', $data->query('filter_by_status'));
            })->orderBy('id', 'desc')
            ->paginate(config('dashboard.pagination_limit'));

        return OrdersStoresResource::collection($objects);
    }

    public function getRequestedOrderByUser($data, $user_id)
    {
        return $object = Order::query()
            ->where('user_id', $user_id)
            ->where('id', $data->query('id'))
            ->first();
    }

    public function getOrderByUser($data)
    {
        $storeId = StoreId::getStoreID($data);
        return $object = Order::query()
            ->whereHas('products', function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->where('id', $data->query('id'))
            ->first();
    }


    public function getRequestedOrdersByUserOld($user_id)
    {
        $sub_orders = Order::query()->where('user_id', '=', $user_id)->get();
        return ParentOrder::query()
            ->select('parent_orders.id', 'parent_orders.created_at', 'parent_orders.order_price',
                DB::raw('count(order_products.product_id) as product_count'),
                DB::raw('SUM(order_products.purchased_item_count * order_products.basic_unit_count)  as item_count'))
            ->join('orders', 'parent_orders.id', '=', 'orders.parent_order_id')
            ->join('order_products', 'order_products.order_id', 'orders.id')
            ->groupBy('parent_orders.id')
            ->whereIn('parent_orders.id', $sub_orders->pluck('parent_order_id')->toArray())->get();
    }

    /**
     * List of stores by type to admin to rate its.
     * @param $type
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getOrdersByStoreTypeForStoreRating($type)
    {
        $stores = Store::query()->select('stores.name', 'stores.id',
            DB::raw('SUM(CASE
            WHEN orders.status_id = 1 then 1 else 0 end ) completed_orders'
            ),
            DB::raw('SUM(CASE
            WHEN orders.status_id = 4 then 1 else 0 end ) canceled_orders'
            ),
            DB::raw('SUM(CASE
            WHEN orders.status_id = 1 then total_price else 0 end) as sales_price'
            )
        )
            ->where('stores.store_type_id', $type)
            ->leftJoin('orders', 'orders.store_id', '=', 'stores.id')
            ->groupBy(['stores.name', 'stores.id'])->distinct()->paginate(10);

        foreach ($stores as $store) {
            $store->rate = round(SellerRate::query()->where(['rated_id' => $store->id, 'rated_type' => 'App\Models\Product'])->avg('rate'), 1);
            $store->num_of_products = ProductStore::query()->where('store_id', $store->id)->count('id');
        }

        return $stores;
    }
}
