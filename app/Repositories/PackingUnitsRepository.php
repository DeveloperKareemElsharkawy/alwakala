<?php

namespace App\Repositories;

use App\Models\PackingUnit;
use Illuminate\Support\Facades\DB;

class PackingUnitsRepository
{
    protected $model;

    public function __construct(PackingUnit $model)
    {
        $this->model = $model;
    }

    public function list($data): array
    {
        $offset = $data->query('offset') ? $data->query('offset') : 0;
        $limit = $data->query('limit') ? $data->query('limit') : config('dashboard.pagination_limit');

        $query = $this->model
            ->newQuery()
            ->select('id', 'name_en', 'name_ar')
            ->orderByRaw('updated_at DESC NULLS LAST');

        $listQuery = $this->prepareQueryFilters($data, $query);

        $list = $listQuery->offset($offset)->limit($limit)->get();

        return [
            'data' => $list,
            'count' => $listQuery->count(),
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
            ->where('id', $data['id'])
            ->update($data);
    }

    public function showById($id, $fields = ['*'])
    {
        return $this->model->newQuery()
            ->select($fields)
            ->where('id', $id)
            ->first();
    }


    public function getPackingUnitsForSelection($request)
    {
        $fields = ['id', 'name_ar', 'name_en'];
        $query = $this->model->newQuery()->select($fields);
        if ($request->filled('name')) {
            $searchQuery = "%" . $request->name . "%";
            $query->where('name_ar', "ilike", $searchQuery)
                ->orWhere('name_en', "ilike", $searchQuery);
        }
        return  $query->get();
    }

    public function destroy($id)
    {
        try {
            $this->model->newQuery()->where('id', $id)->delete();

            return ['status' => true, 'message' => 'Pcking Units Deleted'];

        } catch (\Exception $e) {
            DB::rollBack();
        }
    }


    public function prepareQueryFilters($data, $list)
    {
        if ($data->filled('id')) {
            $list->where('id', intval($data->query('id')));
        }
        if ($data->filled('name')) {
            $searchQuery = "%" . $data->name . "%";
            $list->where('name_ar', "ilike", $searchQuery)
                ->orWhere('name_en', "ilike", $searchQuery);
        }
        if ($data->filled('name_en')) {
            $searchQuery = "%" . $data->name_en . "%";
            $list->where('name_en', "ilike", $searchQuery);
        }
        if ($data->filled('name_ar')) {
            $searchQuery = "%" . $data->name_ar . "%";
            $list->where('name_ar', "ilike", $searchQuery);
        }
        if ($data->filled('sort_by_name_ar')) {
            $list->orderBy('name_ar', $data->query('sort_by_name_ar'));
        }
        if ($data->filled('sort_by_name_en')) {
            $list->orderBy('name_en', $data->query('sort_by_name_en'));
        }
        if ($data->filled('sort_by_id')) {
            $list->orderBy('id', $data->query('sort_by_id'));
        }
        return $list;
    }

}
