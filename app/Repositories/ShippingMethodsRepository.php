<?php

namespace App\Repositories;

use App\Models\Shipment;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\DB;

class ShippingMethodsRepository
{
    protected $model;

    public function __construct(ShippingMethod $model)
    {
        $this->model = $model;
    }

    public function list($data): array
    {
        $offset = $data->query('offset') ? $data->query('offset') : 0;
        $limit = $data->query('limit') ? $data->query('limit') : config('dashboard.pagination_limit');

        $query = $this->model->newQuery()
            ->select('id', 'name_en', 'name_ar', 'activation')
            ->orderByRaw('updated_at DESC NULLS LAST');

        $shippingMethods = $this->prepareQueryFilters($data, $query);

        return [
            'data' => $shippingMethods->offset($offset)->limit($limit)->get(),
            'count' => $shippingMethods->count(),
            'offset' => $offset,
            'limit' => $limit,
        ];
    }

    public function create($data)
    {
        return $this->model->newQuery()
            ->create($data);
    }

    public function update($data)
    {
        return $this->model->newQuery()
            ->where('id', $data['id'])->update($data);
    }

    public function showById($id, $fields = ['*'])
    {
        return $this->model->newQuery()
            ->select($fields)
            ->where('id', $id)
            ->first();
    }

    public function deleteShippingMethod($id)
    {
        try {
            return $this->model->newQuery()->where('id', $id)->delete();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }


    public function prepareQueryFilters($data, $shippingMethods)
    {
        if ($data->filled('id')) {
            $shippingMethods->where('id', intval($data->query('id')));
        }
        if ($data->filled('name')) {
            $searchQuery = "%" . $data->name . "%";
            $shippingMethods->where('name_ar', "ilike", $searchQuery)
                ->orWhere('name_en', "ilike", $searchQuery);
        }

        if ($data->filled('sort_by_name_ar')) {
            $shippingMethods->orderBy('name_ar', $data->query('sort_by_name_ar'));
        }
        if ($data->filled('sort_by_name_en')) {
            $shippingMethods->orderBy('name_en', $data->query('sort_by_name_en'));
        }

        if ($data->filled('sort_by_id')) {
            $shippingMethods->orderBy('id', $data->query('sort_by_id'));
        }

        return $shippingMethods;
    }
}
