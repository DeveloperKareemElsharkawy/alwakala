<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Logs\DashboardLogs;
use App\Exports\Dashboard\BrandsExport;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Categories\CategoryHelper;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\Models\Category;
use App\Models\Role;
use App\Repositories\ActivitiesRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BrandsController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $offset = $request->query('offset') ? $request->query('offset') : 0;
            $limit = $request->query('limit') ? $request->query('limit') : config('dashboard.pagination_limit');
            $category = $request->query('category');
            $query = Brand::query()
                ->select('id', 'name_en', 'activation', 'image')
                ->orderByRaw('brands.updated_at DESC NULLS LAST')
                ->with('categoryBrand');

            if ($request->filled('category')) {
                $query->whereHas('categoryBrand', function ($q) use ($category) {
                    $q->where('brand_category.category_id', $category);
                });
            }
            if ($request->filled('name')) {
                $query->where('name_en', 'ilike', '%' . $request->query('name') . '%');
            }
            if ($request->filled('id')) {
                $query->where('id', intval($request->query('id')));
            }
            if ($request->filled('activation')) {
                $query->where('activation', $request->query('activation'));
            }
            if ($request->filled('sort_by_name')) {
                $query->orderBy('name', $request->query('sort_by_name'));
            }
            if ($request->filled('sort_by_id')) {
                $query->orderBy('id', $request->query('sort_by_id'));
            }
            if ($request->filled('sort_by_activation')) {
                $query->orderBy('activation', $request->query('sort_by_activation'));
            }
            $count = $query->count();
            $brands = $query->offset($offset)->limit($limit)->get();

            foreach ($brands as $brand) {
                if ($brand->image) {
                    $brand->image = config('filesystems.aws_base_url') . $brand->image;
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'Brands',
                'data' => $brands,
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'total' => $count,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard brands' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'activation' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
                'category_brand' => 'required',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            foreach (json_decode($request->category_brand) as $category_id) {

                if (!CategoryHelper::checkCategoryLevel('sub_sub_category', $category_id)) {
                    return response()->json([
                        'status' => false,
                        'message' => trans('messages.category.un_valid_parent'),
                        'data' => ''
                    ], AResponseStatusCode::BAD_REQUEST);
                }
            }

            DB::beginTransaction();
            $brand = new Brand;
            $brand->name_ar = $request->name_ar;
            $brand->name_en = $request->name_en;
            $brand->activation = $request->activation;
            $brand->image = UploadImage::uploadImageToStorage($request->image, 'brands');
            $brand->save();
            $categories = json_decode($request->category_brand);
            $brand->categoryBrand()->attach($categories);
            $logData['id'] = $brand->id;
            $logData['ref_name_ar'] = $brand->name;
            $logData['ref_name_en'] = $brand->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_BRAND;
            event(new DashboardLogs($logData, 'brands'));

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Brand Created',
                'data' => $brand
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in store of dashboard brands' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function show($id)
    {
        try {
            $brand = Brand::query()
                ->with('categoryBrand')
                ->find($id);
            if ($brand->image)
                $brand->image = config('filesystems.aws_base_url') . $brand->image;
            return response()->json([
                'success' => true,
                'message' => 'Brand',
                'data' => $brand
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard brands' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:brands,id',
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'activation' => 'required',
                'category_brand' => 'required',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            DB::beginTransaction();
            foreach (json_decode($request->category_brand) as $category_id) {

                if (!CategoryHelper::checkCategoryLevel('sub_sub_category', $category_id)) {
                    return response()->json([
                        'status' => false,
                        'message' => trans('messages.category.un_valid_parent'),
                        'data' => ''
                    ], AResponseStatusCode::BAD_REQUEST);
                }
            }
            $brand = Brand::query()->find($request->id);
            $brand->name_ar = $request->name_ar;
            $brand->name_en = $request->name_en;
            $brand->activation = $request->activation;
            if ($request->hasFile('image')) {
                Storage::disk('s3')->delete($brand->image);
                $brand->image = UploadImage::uploadImageToStorage($request->image, 'brands');
            }
            $brand->save();
            BrandCategory::query()->where('brand_id', $brand->id)->delete();
            $categories = json_decode($request->category_brand);
            $brand->categoryBrand()->attach($categories);
            $logData['id'] = $brand->id;
            $logData['ref_name_ar'] = $brand->name;
            $logData['ref_name_en'] = $brand->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_BRAND;
            event(new DashboardLogs($logData, 'brands'));
            Db::commit();
            return response()->json([
                'success' => true,
                'message' => 'Brand Updated',
                'data' => $brand
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Db::rollBack();
            Log::error('error in update of dashboard brands' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:brands,id'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            DB::beginTransaction();
            $brand = Brand::query()
                ->where('id', $request->id)
                ->first();
            $logData['id'] = $brand->id;
            $logData['ref_name_ar'] = $brand->name;
            $logData['ref_name_en'] = $brand->name;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::DELETE_BRAND;
            event(new DashboardLogs($logData, 'brands'));
            BrandCategory::query()->where('brand_id', $brand->id)->delete();
            $brand->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Brand deleted',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in delete of dashboard brands' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getBrands(Request $request)
    {
        try {
            $lang = LangHelper::getDefaultLang($request);
            $brand = Brand::query()
                ->select('id', 'name_'.$lang)
                ->where('activation', true)
                ->get();
            return response()->json([
                'success' => true,
                'message' => 'brands',
                'data' => $brand
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in get brands of dashboard brands' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new BrandsExport($request), 'brands.xlsx');
        } catch (\Exception $e) {
            Log::error('error in Sizes Categories in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
