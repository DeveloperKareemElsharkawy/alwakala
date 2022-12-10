<?php

namespace App\Repositories;

use App\Http\Resources\Dashboard\Stock\StockResource;
use App\Models\ProductStoreStock;
use Illuminate\Support\Facades\DB;

class StockRepository
{
    protected $model;

    public function __construct(ProductStoreStock $model)
    {
        $this->model = $model;
    }

    public function list($data): array
    {
        $offset = $data->query('offset') ? $data->query('offset') : 0;
        $limit = $data->query('limit') ? $data->query('limit') : config('dashboard.pagination_limit');

        $query = $this->model->newQuery()
            // ->select('id', 'name_en', 'name_ar', 'address_en', 'address_ar', 'activation', 'city_id')
            ->with(['product_store.store', 'product_store.product'])
            ->orderBy('id','desc');

        $stock = $this->prepareQueryFilters($data, $query);
        $count =  $stock->count();
        $data = $stock->offset($offset)->limit($limit)->get();

        return [
            // 'data' =>$data,
            'data' => StockResource::collection($data),
            'count' => $count,
            'offset' => $offset,
            'limit' => $limit,
        ];
    }

    public function prepareQueryFilters($data, $stock)
    {
        if ($data->filled('approved')) {
            $stock->where('approved', $data->query('approved'));
        }

        if ($data->filled('id')) {
            $stock->where('id', $data->query('id'));
        }

        if ($data->filled('stock')) {
            $stock->where('stock', $data->query('stock'));
        }

        if ($data->filled('sold')) {
            $stock->where('sold', $data->query('sold'));
        }

        if ($data->filled('available_stock')) {
            $stock->where('available_stock', $data->query('available_stock'));
        }
        

        if ($data->filled('product')) {
            $stock->whereHas('product_store', function ($q) use ($data) {
                $q->where('product_id',  $data->query('product'));
            });
        }

        if ($data->filled('store')) {
            $stock->whereHas('product_store', function ($q) use ($data) {
                $q->where('store_id',  $data->query('store'));
            });
        }

        if ($data->filled('type')) {
            $stock->whereHas('product_store.store', function ($q) use ($data) {
                $q->where('store_type_id',  $data->query('type'));
            });
        }

        $stock->whereHas('product_store.product', function ($q) {
            $q->where('reviewed',  1);
        });

        $stock->whereHas('product_store.product', function ($q) {
            $q->where('shipping_method_id',  1);
        });
        return $stock;
    }

    public function approveStock(ProductStoreStock $stock){
        $stock->approved=true;
        $stock->available_stock=$stock->stock;
        $stock->save();
    }

    public function rejectStock(ProductStoreStock $stock){
        $stock->approved=null;
        $stock->save();
    }
}
