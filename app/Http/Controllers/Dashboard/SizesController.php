<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Exports\Dashboard\SizesExport;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Categories\CategoryHelper;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Category;
use App\Models\CategorySize;
use App\Models\PackingUnitProductAttribute;
use App\Models\ProductStoreStock;
use App\Models\Size;
use App\Models\SizeType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SizesController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $query = Size::query()
                ->select('id', 'size', 'size_type_id')
                ->with('sizeType')
                ->with('categories')
                ->orderByRaw('sizes.updated_at DESC NULLS LAST');
            $offset = $request->query('offset') ? $request->query('offset') : 0;
            $limit = $request->query('limit') ? $request->query('limit') : config('dashboard.pagination_limit');
            if ($request->filled('id')) {
                $query->where('id', intval($request->query('id')));
            }
            if ($request->filled('category_id')) {
                $query->whereHas('categories', function ($query) use ($request) {
                    $query->where("category_size.category_id", intval($request->query('category_id')));
                });
            }
            if ($request->filled('size')) {
                $size = "%" . $request->get("size") . "%";
                $query->where('size', "ilike", $size);
            }
            if ($request->filled('size_type_id')) {
                $query->where('size_type_id', intval($request->query('size_type_id')));
            }
            if ($request->filled('sort_by_id')) {
                $query->orderBy('id', $request->query('sort_by_id'));
            }

            if ($request->filled('sort_by_size')) {
                $query->orderBy('size', $request->sort_by_size);
            }
            if ($request->filled('sort_by_size_type_id')) {
                $query->orderBy('size_type_id', $request->query('sort_by_size_type_id'));
            }
            $count = $query->count();
            $sizes = $query->offset($offset)->limit($limit)->get();

            return response()->json([
                "status" => true,
                "message" => "sizes",
                "data" => $sizes,
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'total' => $count,
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard sizes' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function show($id)
    {
        try {
            $size = Size::query()
                ->with('sizeType')
                ->with('categories')
                ->where('id', $id)
                ->first();
            return response()->json([
                "status" => true,
                "message" => "sizes",
                "data" => $size
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard sizes' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'size' => 'required|string|max:255|unique:sizes,size',
                'category_ids' => 'required|array',
                'category_ids.*' => 'required|numeric|exists:categories,id',
                'size_type_id' => 'required|numeric|exists:size_types,id',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            foreach ($request->category_ids as $category_id) {

                if (!CategoryHelper::checkCategoryLevel('sub_sub_category', $category_id)) {
                    return response()->json([
                        'status' => false,
                        'message' => trans('messages.category.un_valid_parent'),
                        'data' => ''
                    ], AResponseStatusCode::BAD_REQUEST);
                }
            }

            $size = new Size;
            $size->size = $request->size;
            $size->size_type_id = $request->size_type_id;
            $size->save();

            $size->categories()->sync($request->category_ids);

            return response()->json([
                "status" => true,
                "message" => "size created",
                "data" => $size
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard sizes' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:sizes,id',
                'size' => 'required|string|max:255|unique:sizes,size,' . $request->id,
                'category_ids' => 'required|array',
                'category_ids.*' => 'required|numeric|exists:categories,id',
                'size_type_id' => 'required|numeric|exists:size_types,id',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            foreach ($request->category_ids as $category_id) {

                if (!CategoryHelper::checkCategoryLevel('sub_sub_category', $category_id)) {
                    return response()->json([
                        'status' => false,
                        'message' => trans('messages.category.un_valid_parent'),
                        'data' => ''
                    ], AResponseStatusCode::BAD_REQUEST);
                }
            }
            $size = Size::query()
                ->where('id', $request->id)
                ->first();
            $size->size = $request->size;
            $size->size_type_id = $request->size_type_id;
            $size->save();
            $size->categories()->sync($request->category_ids);

            return response()->json([
                "status" => true,
                "message" => "size updated",
                "data" => $size
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard sizes' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function delete($id)
    {
        try {
            $size = Size::query()->where('id', $id)->first();
            if (!$size) {
                return response()->json([
                    'status' => false,
                    'message' => 'size not found',
                    'data' => ''
                ], AResponseStatusCode::NOT_FOUNT);
            }
            $isUsedInPackingUnit = PackingUnitProductAttribute::query()->where('size_id', $id)->first();
            $isUsedInProductStoreStock = ProductStoreStock::query()->where('size_id', $id)->first();

            if ($isUsedInPackingUnit || $isUsedInProductStoreStock) {
                return response()->json([
                    'status' => false,
                    'message' => 'this size is in use',
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            CategorySize::query()->where('size_id', $id)->delete();
            $size->delete();

            return response()->json([
                "status" => true,
                "message" => "size deleted",
                "data" => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard sizes' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getSizeType()
    {
        try {
            $sizesTypes = SizeType::query()
                ->select('id', 'type_en', 'type_ar')
                ->get();

            return response()->json([
                "status" => true,
                "message" => "size types",
                "data" => $sizesTypes
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in getSizeType of dashboard sizes' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function categories()
    {
        try {
            $parents = Category::query()
                ->whereNull('category_id')
                ->where('activation', true)
                ->pluck('id')->toArray();
            $subChildes = Category::query()
                ->whereIn('category_id', $parents)
                ->where('activation', true)
                ->pluck('id')->toArray();
            $ids = array_merge($parents, $subChildes);

            $categories = Category::query()
                ->where('activation', true)
                ->whereIn('category_id', $ids)
                ->get();
            return response()->json([
                "status" => true,
                "message" => "child categories",
                "data" => $categories
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in categories of dashboard sizes' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new SizesExport($request), 'sizes.xlsx');
        } catch (\Exception $e) {
            Log::error('error in Sizes Categories in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
    public function getAllForSelection()
    {
        try {
            $sizes = Size::query()
                ->select('id', 'size')->get();
            return response()->json([
                "status" => true,
                "message" => "sizes",
                "data" => $sizes
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in Sizes getAllForSelection in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);

        }
    }

}
