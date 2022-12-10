<?php


namespace App\Repositories;


use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\Category\CategoryDetailsResource;
use App\Http\Resources\Dashboard\Category\CategoryResource;
use App\Http\Resources\Dashboard\Category\CategorySelectAllResource;
use App\Models\Category;

class CategoriesRepository extends Controller
{

    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function list($request): array
    {
        $offset = $request->query('offset') ? $request->query('offset') : 0;
        $limit = $request->query('limit') ? $request->query('limit') : config('dashboard.pagination_limit');
        $query = $this->model
            ->newQuery()
            ->with('parent.parent','packing_unit')
            ->orderByRaw('updated_at DESC NULLS LAST');
        $query_after_filter = $this->prepareQueryFilters($request, $query);
        $count = $query_after_filter->count();
        $list = $query_after_filter->offset($offset)->limit($limit)->get();
        return [
            'data' => CategoryResource::collection($list),
            'count' => $count,
            'offset' => $offset,
            'limit' => $limit,
            'message' => 'Categories Retrieved Successfully'
        ];
    }

    public function getCategoriesAllWithTree()
    {
        $list = $this->model
            ->newQuery()
            // ->with('categories.categories')
            ->whereNull('category_id')
            ->where('activation', true)
            ->orderBy('name_en')
            ->get();
        return CategorySelectAllResource::collection($list);
    }

    public function showById($id)
    {
        return new CategoryDetailsResource($this->model->newQuery()
            ->where('id', $id)
            ->first());
    }

    public function getParents($lang = 'ar')
    {
        $categories = $this->model->newQuery()
            ->select('id', 'name_' . $lang . ' as name', 'image')
            ->where('activation', true)
            ->whereNull('category_id')
            ->get();
        foreach ($categories as $category) {
            $category->image = $category->image ? config('filesystems.aws_base_url') . $category->image : null;
        }
        return $categories;
    }

    public function getChildes($ids = [], $lang = 'ar')
    {
        $query = $this->model->newQuery()
            ->select('id', 'name_' . $lang . ' as name', 'category_id')
            ->where('activation', true);
        if ($ids) {
            $query->whereIn('category_id', $ids);
        }
        $categories = $query->get();

        foreach ($categories as $category) {
            $category->image = $category->image ? config('filesystems.aws_base_url') . $category->image : null;
        }
        return $categories;
    }

    public function getParentsIds($ids = null): array
    {
        $query = $this->model->newQuery()
            ->where('activation', true);
        if ($ids) {
            $query->wherein('category_id', $ids);
        } else {
            $query->whereNull('category_id');
        }
        return $query->pluck('id')->toArray();
    }

    public function prepareQueryFilters($request, $query)
    {
        if ($request->filled('level')) {
            switch ($request->query('level')) {
                case '1':
                    $query->whereNull('category_id');
                    break;
                case '2':
                    $query->whereHas('parent', function ($q) {
                        $q->whereNull('category_id');
                    });
                    break;
                case '3':
                    $query->whereHas('parent.parent');
                    break;
            }
        }
        if ($request->filled('id')) {
            $query->where('id', intval($request->query('id')));
        }
        if ($request->filled('id')) {
            $query->where('id', intval($request->query('id')));
        }
        if ($request->filled('name_ar')) {
            $query->where('name_ar', 'ilike', '%' . $request->query('name_ar') . '%');
        }
        if ($request->filled('name_en')) {
            $query->where('name_en', 'ilike', '%' . $request->query('name_en') . '%');
        }
        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('name_ar', 'ilike', '%' . $request->query('name') . '%')
                    ->orWhere('name_en', 'ilike', '%' . $request->query('name') . '%');
            });
        }
        if ($request->filled('parent_name')) {
            $query->where('category_id', $request->query('parent_name'));
        }
        if ($request->filled('packing_unit')) {
            $query->where('packing_unit_id', $request->query('packing_unit'));
        }
        if ($request->filled('parent_parent_name')) {
            $query->whereHas('parent', function ($q) use ($request) {
                $q->where('category_id', $request->query('parent_parent_name'));
            });
        }
        if ($request->filled('activation')) {
            $query->where('activation', $request->query('activation'));
        }
        if ($request->filled('is_seller')) {
            $query->where('is_seller', $request->query('is_seller'));
        }
        if ($request->filled('is_consumer')) {
            $query->where('is_consumer', $request->query('is_consumer'));
        }
        if ($request->filled('category')) {
            $query->where('category_id', intval($request->category));
        }
        if ($request->filled('sort_by_name_ar')) {
            $query->orderBy('name_ar', $request->query('sort_by_name_ar'));
        }
        if ($request->filled('sort_by_name_en')) {
            $query->orderBy('name_en', $request->query('sort_by_name_en'));
        }
        if ($request->filled('sort_by_id')) {
            $query->orderBy('id', $request->query('sort_by_id'));
        }
        if ($request->filled('sort_by_activation')) {
            $query->orderBy('activation', $request->query('sort_by_activation'));
        }
        if ($request->filled('sort_by_category')) {
            $query->orderBy('category_id', $request->sort_by_category);
        }
        return $query;
    }
}
