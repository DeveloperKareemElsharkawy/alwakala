<?php

namespace App\Repositories;

use App\Enums\UserTypes\UserType;
use App\Models\ProductStore;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class WarehousesRepository
{
    protected $model;

    public function __construct(Warehouse $model)
    {
        $this->model = $model;
    }

    public function list($data): array
    {
        $query = $this->model->newQuery()
            ->select('id', 'name_en', 'name_ar', 'address_en', 'address_ar', 'activation', 'city_id')
            ->with(['city', 'type' , 'products'])
            ->orderByRaw('updated_at DESC NULLS LAST');

        $warehouses = $this->prepareQueryFilters($data, $query);

        $warehouses = $warehouses->paginate(config('dashboard.pagination_limit'));

        return [
            'data' => $warehouses,
            'count' => $warehouses->count()
        ];
    }

    public function create($data)
    {
        return $this->model->newQuery()
            ->create($data);
    }

    public function add_products($data)
    {
        $warehouse = $this->model->with('products')->where('id' , $data['warehouse_id'])->first();

        $items = [];
        foreach ($data['products'] as $item) {
            $items[$item['id']] = ['amount' => $item['amount'] , 'size_id' => $item['size_id'] , 'color_id' => $item['color_id']];
        }

        $warehouse->products()->sync($items);

        return $warehouse;
    }

    public function accept_product($data)
    {
        $warehouse = $this->model->with('warehouse_products')->where('id' , $data['warehouse_id'])->first();
        foreach ($data['products'] as $product) {
            $product_warehouse = $warehouse->warehouse_products()->where('product_id' , $product['product_id'])->first();
            $product_warehouse->update([
                'accept' => $data['accept']
            ]);

            if ($data['accept'] == true) {
                $store = ProductStore::where('product_id' , $product['product_id'])->firstOrFail();
                $store->productStoreStock()->where('color_id' , $product_warehouse->color_id)->where('size_id' , $product_warehouse->size_id)->decrement('available_stock' , $product_warehouse->amount);
            }
        }

        return $warehouse;
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
            ->with(['city', 'type','user' ,'products'])
            ->first();
    }

    public function delete($id)
    {
        try {
            return $this->model->newQuery()->where('id', $id)->delete();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }


    public function prepareQueryFilters($data, $warehouses)
    {
        if ($data->filled('id')) {
            $warehouses->where('id', intval($data->query('id')));
        }
        if ($data->filled('name')) {
            $searchQuery = "%" . $data->name . "%";
            $warehouses->where('name_ar', "ilike", $searchQuery)
                ->orWhere('name_en', "ilike", $searchQuery);
        }

        if ($data->type_id == UserType::SELLER && $data->user_id) {
            $warehouses->where('user_id', $data->user_id);
        }

        if ($data->filled('sort_by_name_ar')) {
            $warehouses->orderBy('name_ar', $data->query('sort_by_name_ar'));
        }
        if ($data->filled('sort_by_name_en')) {
            $warehouses->orderBy('name_en', $data->query('sort_by_name_en'));
        }
        if ($data->filled('sort_by_address_ar')) {
            $warehouses->orderBy('address_ar', $data->query('sort_by_address_ar'));
        }
        if ($data->filled('sort_by_address_en')) {
            $warehouses->orderBy('address_en', $data->query('sort_by_address_en'));
        }

        if ($data->filled('sort_by_id')) {
            $warehouses->orderBy('id', $data->query('sort_by_id'));
        }

        if ($data->filled('type')) {
            $warehouses->where('store_type_id', $data->query('type'));
        }

        return $warehouses;
    }
}
