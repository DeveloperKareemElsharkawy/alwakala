<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Exports\Dashboard\CategoriesExport;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Seller\CreateProductsController;
use App\Http\Requests\Categories\deleteCategoryRequest;
use App\Http\Requests\Categories\createCategoryRequest;
use App\Http\Requests\Categories\updateCategoryRequest;
use App\Jobs\Images\DeleteS3ImageJob;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Category;
use App\Repositories\ProductRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Category\CheckCategoryExistenceRequest;
use App\Http\Resources\Dashboard\Response\ApiDashboardResource;
use App\Http\Resources\Dashboard\Response\IndexResource;
use App\Repositories\CategoriesRepository;
use App\Services\Categories\CategoriesService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;


class CategoriesController extends BaseController
{
    private $service;
    private $repository;

    public function __construct(CategoriesService $service, CategoriesRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request)
    {
        try {
            $list = $this->repository->list($request);
            return response()->json(ApiDashboardResource::index($list), AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function getCategoriesAllWithTree()
    {
        try {
            $list = $this->repository->getCategoriesAllWithTree();
        // dd( $list );
        // dd( ['data'=>$list] );
            return response()->json(['data'=>$list], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return
            Log::error('error in index of dashboard categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CheckCategoryExistenceRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function show(CheckCategoryExistenceRequest $request, $id)
    {
        try {
            $object = $this->service->show($id);
            return response()->json(ApiDashboardResource::show(['object' => $object]), AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard Category' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(createCategoryRequest $request)
    {
        // if (!ProductRepository::checkIfCategoryIdIsValidParent($request->category_id)) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => trans('messages.category.un_valid_parent'),
        //         'data' => [],
        //     ], AResponseStatusCode::BAD_REQUEST);
        // }
        try {
            // $parent_id = Category::query()->where('id', $request->category_id)->first()->category_id ?? null;
            // if ($parent_id) {
            //     $validator = Validator::make($request->all(), [
            //         'packing_unit_id' => 'required|numeric|exists:packing_units,id|not in:1',
            //     ]);
            //     if ($validator->fails()) {
            //         return ValidationError::handle($validator);
            //     }
            // }
            // if (!$parent_id && $request->packing_unit_id) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => trans('messages.category.un_required_packing_unit'),
            //         'data' => ''
            //     ], AResponseStatusCode::BAD_REQUEST);
            // }
            $category = new Category;
            $category->name_ar = $request->name_ar;
            $category->name_en = $request->name_en;
            $category->description = $request->description;
            $category->activation = $request->activation;
            $category->is_seller = $request->is_seller;
            $category->is_consumer = $request->is_consumer;
            // $category->priority = $request->priority;
            $category->category_id = $request->category_id;
            if ($request->packing_unit_id) {
                $category->packing_unit_id = $request->packing_unit_id;
            }

            if ($request->has('image'))
                $category->image = UploadImage::uploadImageToStorage($request->image, 'categories');
            $category->save();
            return response()->json([
                'status' => true,
                'message' => 'Category created',
                'data' => $category
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getCategory($id)
    {
        try {
            $category = Category::query()->with('parent')->find($id);
            $category->image = config('filesystems.aws_base_url') . $category->image;

            return response()->json([
                'status' => true,
                'message' => 'List ' . $category->name_en,
                'data' => $category
            ], 200);
        } catch (\Exception $e) {
            Log::error('error in getCategory of dashboard categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param updateCategoryRequest $request
     * @return JsonResponse
     */
    public function update(updateCategoryRequest $request)
    {
        try {
            // $parent_id = Category::query()->where('id', $request->category_id)->first()->category_id ?? null;
            // if ($parent_id) {
            //     $validator = Validator::make($request->all(), [
            //         'packing_unit_id' => 'required|numeric|exists:packing_units,id|not in:1',
            //     ]);
            //     if ($validator->fails()) {
            //         return ValidationError::handle($validator);
            //     }
            // }
            // if (!$parent_id && $request->packing_unit_id) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => trans('messages.category.un_required_packing_unit'),
            //         'data' => ''
            //     ], AResponseStatusCode::BAD_REQUEST);
            // }
            $category = Category::query()->find($request->id);
            $category->name_ar = $request->name_ar;
            $category->name_en = $request->name_en;
            $category->description = $request->description;
            $category->activation = $request->activation;
            $category->is_seller = $request->is_seller;
            $category->is_consumer = $request->is_consumer;
            //            $category->priority = $request->priority;
            if ($request->packing_unit_id) {
                $category->packing_unit_id = $request->packing_unit_id;
            }
            $category->category_id = $request->category_id;
            if ($request->hasFile('image')) {
                $job = (new DeleteS3ImageJob($category->image))->delay(Carbon::now()->addSeconds(30));
                dispatch($job);
                $category->image = UploadImage::uploadImageToStorage($request->image, 'categories');
            }

            $category->save();
            return response()->json([
                'success' => true,
                'message' => 'Category Updated',
                'data' => $category
            ], 200);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param deleteCategoryRequest $request
     * @return JsonResponse
     */
    public function delete(deleteCategoryRequest $request)
    {
        try {
            $id = $request->get('id');
            $category = Category::query()->where('id', $id)->first();
            Storage::disk('s3')->delete($category->image);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            if ($e->errorInfo[0] == 23503) {
                return $this->error(['message' => trans('messages.category.delete_error', ['model'=>$this->handleDeleteException($e)])]);
            }
            Log::error('error in delete of dashboard categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function handleDeleteException($exception)
    {

        $test = ($exception->errorInfo[2]);
        $test = strrchr($test, 'from table "');
        $matches = array();
        preg_match('/"(.+?)"/', $test, $matches);
        return $matches[1];
    }

    public function getCategoriesForSelection(Request $request)
    {
        try {
            $query = Category::query();

            if ($request->filled('is_parent')) {
                $query->where('category_id', null);
            }

            $categories = $query->select('id', 'name_ar', 'name_en')
                ->where('activation', true)
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'Categories Retrieved',
                'categories' => $categories
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getCategoriesForSelection of dashboard categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function selectCategories($type, Request $request)
    {
        try {
            $categories = '';
            if ($type == 'sub-sub') {
                $parentsId = Category::query()
                    ->where('category_id', null)
                    ->where('activation', true)
                    ->pluck('id')
                    ->toArray();
                $subChild = Category::query()
                    ->whereIn('category_id', $parentsId)
                    ->where('activation', true)
                    ->pluck('id')
                    ->toArray();
                $query = Category::query()
                    ->select('id', 'name_ar', 'name_en', 'category_id')
                    ->where('activation', true);
                if ($request->filled('id')) {
                    $query->where('category_id', $request->query('id'));
                } else {
                    $query->whereIn('category_id', $subChild);
                }
                if ($request->filled("name")) {
                    $searchQuery = "%" . $request->get("name") . "%";
                    $query->where('name_en', "ilike", $searchQuery);
                }
                $categories = $query->get();
            } elseif ($type == 'sub') {
                $parentsId = Category::query()
                    ->where('category_id', null)
                    ->where('activation', true)
                    ->pluck('id')
                    ->toArray();
                $query = Category::query()
                    ->select('id', 'name_ar', 'name_en', 'category_id')
                    ->where('activation', true);
                if ($request->filled('id')) {
                    $query->where('category_id', $request->query('id'));
                } else {
                    $query->whereIn('category_id', $parentsId);
                }
                if ($request->filled("name")) {
                    $searchQuery = "%" . $request->get("name") . "%";
                    $query->where('name_en', "ilike", $searchQuery);
                }
                $categories = $query->get();
            } elseif ($type == 'parents') {
                $query = Category::query()
                    ->select('id', 'name_ar', 'name_en', 'category_id')
                    ->where('activation', true)
                    ->whereNull('category_id');
                if ($request->filled("name")) {
                    $searchQuery = "%" . $request->get("name") . "%";
                    $query->where('name_en', "ilike", $searchQuery);
                }
                $categories = $query->get();
            }
            return response()->json([
                "success" => true,
                "message" => "categories retrieved successfully",
                "data" => $categories
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in selectCategories of dashboard categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getForSelection()
    {
        try {
            $categories = Category::select('id', 'name_ar as name')
                ->where('activation', true)
                ->where('category_id', null)
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'categories',
                'data' => $categories
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getForSelection of dashboard categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function selectParentAndSub(Request $request)
    {
        try {
            $offset = $request->query('offset') ? $request->query('offset') : 0;
            $limit = $request->query('limit') ? $request->query('limit') : config('dashboard.pagination_limit');

            $parentsId = Category::query()
                ->where('activation', true)
                ->whereNull('category_id')
                ->pluck('id')
                ->toArray();

            $subIds = Category::query()
                ->select('id', 'name_ar', 'name_en', 'category_id')
                ->where('activation', true)
                ->whereIn('category_id', $parentsId)
                ->pluck('id')
                ->toArray();

            $ids = array_merge($parentsId, $subIds);

            $query = Category::query()
                ->select('id', 'name_ar', 'name_en');
            if ($request->filled('name')) {
                $query->where('name_ar', 'ilike', '%' . $request->query('name') . '%')
                    ->orWhere('name_en', 'ilike', '%' . $request->query('name') . '%');
            }
            $count = $query->count();
            $categories = $query->whereIn('id', $ids)
                ->get();

            return response()->json([
                "success" => true,
                "message" => "categories retrieved successfully",
                "data" => $categories,
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'total' => $count,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in selectParentAndSub of dashboard categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getCategoriesForAdmin(Request $request)
    {
        try {
            $name = $request->query('name_en');
            $categories = DB::select("select id,name_ar,name_en,category_id,1 as level
            from categories as catg1
            where catg1.category_id is null
            and  catg1.name_en like ?
            union all(
            select id,name_ar,name_en,category_id,2 as level
            from categories as catg2
            where catg2.category_id in (
               select id
            from categories as catg1
            where catg1.category_id is null
            ) and catg2.name_en like ?
            )
          union all(
            select id,name_ar,name_en,category_id,3 as level
            from categories as catg3
            where catg3.category_id not in (
            select id
            from categories as catg4
            where catg4.category_id is null
            ) and catg3.name_en like ?
          )
            ", ["%$name%", "%$name%", "%$name%"]);

            return response()->json([
                "success" => true,
                "message" => "categories retrieved successfully",
                "data" => $categories,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getCategories of dashboard categories' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function searchForName($name, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['name_en'] === $name) {
                return $key;
            }
        }
        return null;
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new CategoriesExport($request), 'categories.xlsx');
        } catch (\Exception $e) {
            Log::error('error in Export Categories in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function deleteImage($id)
    {
        try {
            $category = Category::query()->where('id', $id)->first();
            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'not found',
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            Storage::disk('s3')->delete($category->image);
            $category->image = null;
            $category->save();
            return response()->json([
                'status' => true,
                'message' => 'image deleted',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in deleteImage of dashboard admins ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
