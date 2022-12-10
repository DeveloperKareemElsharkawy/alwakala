<?php

namespace App\Repositories;

use App\Models\Policy;
use Illuminate\Support\Facades\DB;

class PolicyRepository
{
    protected $model;

    public function __construct(Policy $model)
    {
        $this->model = $model;
    }

    public function list($data): array
    {
        $offset = $data->query('offset') ? $data->query('offset') : 0;
        $limit = $data->query('limit') ? $data->query('limit') : config('dashboard.pagination_limit');

        $query = $this->model
            ->newQuery()
            ->select('id', 'name_en', 'name_ar', 'activation')
            ->orderByRaw('updated_at DESC NULLS LAST');

        $policies = $this->prepareQueryFilters($data, $query);

        return [
            'data' => $policies->offset($offset)->limit($limit)->get(),
            'count' => $policies->count(),
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

    public function deletePolicy($id)
    {
        try {
            return $this->model->newQuery()->where('id', $id)->delete();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function prepareQueryFilters($data, $policies)
    {
        if ($data->filled('id')) {
            $policies->where('id', intval($data->query('id')));
        }
        if ($data->filled('name')) {
            $searchQuery = "%" . $data->name . "%";
            $policies->where('name_ar', "ilike", $searchQuery)
                ->orWhere('name_en', "ilike", $searchQuery);
        }

        if ($data->filled('sort_by_name_ar')) {
            $policies->orderBy('name_ar', $data->query('sort_by_name_ar'));
        }
        if ($data->filled('sort_by_name_en')) {
            $policies->orderBy('name_en', $data->query('sort_by_name_en'));
        }

        if ($data->filled('sort_by_id')) {
            $policies->orderBy('id', $data->query('sort_by_id'));
        }

        return $policies;
    }

}
