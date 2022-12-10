<?php

namespace App\Repositories\Dashboard;

use App\Enums\Orders\AOrders;
use App\Enums\Orders\OrdersTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\Roles\ARoles;
use App\Enums\StoreTypes\StoreType;
use App\Enums\UserTypes\UserType;
use App\Http\Resources\Dashboard\ParentOrders\ParentOrderDetailsResource;
use App\Http\Resources\Dashboard\ParentOrders\ParentOrdersResource;
use App\Http\Resources\Seller\Orders\OrdersCollection;
use App\Lib\Helpers\Address\AddressHelper;
use App\Lib\Helpers\Lang\LangHelper;
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

class ParentOrderRepository
{
    protected $model;

    public function __construct(ParentOrder $model)
    {
        $this->model = $model;
    }

    public function list($data): array
    {
        $offset = $data->query('offset') ? $data->query('offset') : 0;
        $limit = $data->query('limit') ? $data->query('limit') : config('dashboard.pagination_limit');
        $query = $this->model->newQuery();
        $query = $this->prepareQueryFilters($data, $query);
        $count = $query->count();
        $objects = $query->offset($offset)->limit($limit)->get();
        return [
            'data' => ParentOrdersResource::collection($objects),
            'count' => $count,
            'offset' => $offset,
            'limit' => $limit,
        ];
    }

    public function prepareQueryFilters($data, $object)
    {
        if ($data->filled('id')) {
            $object->where('id', intval($data->query('id')));
        }
        if ($data->filled('type_id')) {
            $object->whereHas('user', function ($q) use ($data) {
                $q->where('type_id', $data->query('type_id'));
            });
        }
        if ($data->filled('sort_by_id')) {
            $object->orderBy('id', $data->query('sort_by_id'));
        }
        return $object;
    }

    public function getOrderDetails($id)
    {
        $order = ParentOrder::query()
            ->where('id', $id)
            ->first();
        return new ParentOrderDetailsResource($order);
    }
}
