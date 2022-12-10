<?php

namespace App\Repositories;

use App\Models\Badge;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;

class BadgesRepository
{
    protected $model;

    public function __construct(Badge $model)
    {
        $this->model = $model;
    }

    public function list($data): array
    {
        $offset = $data->query('offset') ? $data->query('offset') : 0;
        $limit = $data->query('limit') ? $data->query('limit') : config('dashboard.pagination_limit');

        $query = $this->model
            ->newQuery()
            ->select('id', 'name_en', 'name_ar', 'color_id', 'icon','is_product','is_seller','is_store','activation')
            ->with('color')
            ->orderByRaw('updated_at DESC NULLS LAST');

        $badgesQuery = $this->prepareQueryFilters($data, $query);

        $badges = $badgesQuery->offset($offset)->limit($limit)->get();

        foreach ($badges as $badge) {
            $badge->icon = $badge->icon ? config('filesystems.aws_base_url') . $badge->icon : null;
        }

        return [
            'data' => $badges,
            'count' => $badgesQuery->count(),
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
            ->with('color')
            ->select($fields)
            ->where('id', $id)
            ->first();
    }


    public function getBadgesForSelection($request)
    {
        $fields = ['id', 'name_ar', 'name_en'];
        $query = $this->model->newQuery()->select($fields);
        if ($request->filled('is_store')) {
            $query->where('is_store', true);
        }
        if ($request->filled('is_seller')) {
            $query->where('is_seller', true);
        }
        if ($request->filled('is_product')) {
            $query->where('is_product', true);
        }
        return  $query->get();
    }

    public function destroy($id)
    {
        try {
            $this->model->newQuery()->where('id', $id)->delete();

            return ['status' => true, 'message' => 'Badge Deleted'];

        } catch (\Exception $e) {
            DB::rollBack();
        }
    }


    public function prepareQueryFilters($data, $policies)
    {
        if ($data->filled('id')) {
            $policies->where('id', intval($data->query('id')));
        }
        if ($data->filled('color_name_en')) {
            $policies->where('color_id', intval($data->query('color_name_en')));
        }
        if ($data->filled('name')) {
            $searchQuery = "%" . $data->name . "%";
            $policies->where('name_ar', "ilike", $searchQuery)
                ->orWhere('name_en', "ilike", $searchQuery);
        }
        if ($data->filled('name_en')) {
            $searchQuery = "%" . $data->name_en . "%";
            $policies->where('name_en', "ilike", $searchQuery);
        }
        if ($data->filled('name_ar')) {
            $searchQuery = "%" . $data->name_ar . "%";
            $policies->where('name_ar', "ilike", $searchQuery);
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
        if ($data->filled('activation')) {
            $policies->where('activation', $data->query('activation'));
        }
        if ($data->filled('is_seller')) {
            $policies->where('is_seller', $data->query('is_seller'));
        }
        if ($data->filled('is_store')) {
            $policies->where('is_store', $data->query('is_store'));
        }
        if ($data->filled('is_product')) {
            $policies->where('is_product', $data->query('is_product'));
        }


        return $policies;
    }

}
