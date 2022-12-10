<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Exports\Dashboard\AppTvsExport;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Http\Requests\AppTv\CreateAppTvRequest;
use App\Http\Requests\AppTv\UpdateAppTvRequest;
use App\Http\Resources\Dashboard\AppTv\AppTvResource;
use App\Lib\Log\ServerError;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\AppTv;
use App\Models\AppTvType;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AppTvsController extends BaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $today = Carbon::today()->toDateString();
            $query = AppTv::query()
                ->with(['type', 'app', 'category', 'store'])
                ->orderBy('updated_at', 'desc');
            $offset = $request->query('offset') ? $request->query('offset') : 0;
            $limit = $request->query('limit') ? $request->query('limit') : config('dashboard.pagination_limit');
            if ($request->filled('expiry_date')) {
                if ($request->expiry_date == 'valid') {
                    $query->whereDate('expiry_date', '>=', Carbon::today()->toDateString());
                } elseif ($request->expiry_date == 'expired') {
                    $query->whereDate('expiry_date', '<', Carbon::today()->toDateString());
                }
            }
            if ($request->filled('app')) {
                $query->where('app_id', intval($request->app));
            }
            if ($request->filled('type')) {
                $query->where('item_type', intval($request->type));
            }
            if ($request->filled('category')) {
                $query->where('category_id', intval($request->category));
            }
            if ($request->filled('store')) {
                $query->where('store_id', intval($request->store));
            }
            if ($request->filled('home_section')) {
                $query->where('home_section_id', intval($request->home_section));
            }
            if ($request->filled('location')) {
                switch ($request->location) {
                    case 'category':
                        $query->whereNotNull('category_id');
                        break;
                    case 'home-section':
                        $query->whereNotNull('home-section');
                        break;
                    case 'store':
                        $query->whereNotNull('store_id');
                        break;
                    case 'home':
                        $query->whereNull(['category_id', 'store_id']);
                        break;
                }
            }

            if ($request->filled('sort_by_expiry')) {
                $query->orderBy('expiry_date', $request->sort_by_expiry);
            }
            if ($request->filled('sort_by_app')) {
                $query->orderBy('app_id', $request->sort_by_app);
            }
            if ($request->filled('sort_by_type')) {
                $query->orderBy('item_type', $request->sort_by_type);
            }
            if ($request->filled('sort_by_category')) {
                $query->orderBy('category_id', $request->sort_by_category);
            }
            if ($request->filled('sort_by_store')) {
                $query->orderBy('store_id', $request->sort_by_store);
            }
            $count = $query->count();
            $appTvs = $query->offset($offset)->limit($limit)->get();
            foreach ($appTvs as $appTv) {
//                $type = $appTv->type->type_en;
//                $app = $appTv->app->app_en;
//                unset($appTv->type);
//                unset($appTv->app);
//                $appTv->type = $type;
//                $appTv->app = $app;
                $appTv->image = config('filesystems.aws_base_url') . $appTv->image;
                $appTv->location = $this->checkLocation($appTv);
            }
            return response()->json([
                'status' => true,
                'message' => 'app tvs',
                'data' => $appTvs,
                'offset' => (int)$offset,
                'limit' => (int)$limit,
                'total' => $count,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard apps tvs ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreateAppTvRequest $request
     * @return JsonResponse
     */
    public function store(CreateAppTvRequest $request)
    {
        try {
            $appTv = new AppTv;
            $appTv->title_en = $request->title_en;
            $appTv->title_ar = $request->title_ar;
            $appTv->description_en = $request->description_en;
            $appTv->description_ar = $request->description_ar;
            $appTv->item_id = $request->item_id;
            $appTv->items_ids = json_encode($request->items_ids);
            $appTv->item_type = $request->item_type;
            $appTv->expiry_date = Carbon::createFromFormat('Y-m-d', $request->expiry_date)->endOfDay()->toDateTimeString();;
            $appTv->web_image = UploadImage::uploadImageToStorage($request->web_image, 'app-tv');
            $appTv->mobile_image = UploadImage::uploadImageToStorage($request->mobile_image, 'app-tv');
            $appTv->home_section_id = $request->home_section_id;
            $appTv->app_id = $request->app_id;
            $appTv->category_id = $request->category_id;
            $appTv->store_id = $request->store_id;
            $appTv->order = $request->order;
            $appTv->save();

            return response()->json([
                'status' => true,
                'message' => 'app tv added',
                'data' => $appTv
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard apps tvs ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $appTv = AppTv::query()
                ->with(['type', 'app'])
                ->find($id);
            if (!$appTv) {
                return response()->json([
                    'status' => false,
                    'message' => 'Not Found',
                    'data' => $appTv
                ], AResponseStatusCode::BAD_REQUEST);

            }
            switch ($appTv->item_type) {
                case 1:
                    $appTv->item = $appTv->product;
                    break;
                case 2:
                    $appTv->items_ids = json_decode(str_replace('"','',$appTv->items_ids));
                case 3:
                    $appTv->item = $appTv->category_item;
                    break;
                case 4:
                    $appTv->item = $appTv->store;
                    break;
                case 5:
                    $appTv->item = $appTv->seller->store;
                    break;
                default:
                    # code...
                    break;
            }
            $appTv->location = $this->checkLocation($appTv);
            if ($appTv->web_image)
                $appTv->web_image = config('filesystems.aws_base_url') . $appTv->web_image;
            if ($appTv->mobile_image)
                $appTv->mobile_image = config('filesystems.aws_base_url') . $appTv->mobile_image;
            return response()->json([
                'status' => true,
                'message' => 'app tv',
                'data' => $appTv
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard apps tvs ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateAppTvRequest $request
     * @return JsonResponse
     */
    public function update(UpdateAppTvRequest $request)
    {
        try {
            $appTv = AppTv::query()->find($request->id);
            $appTv->title_en = $request->title_en;
            $appTv->title_ar = $request->title_ar;
            $appTv->description_en = $request->description_en;
            $appTv->description_ar = $request->description_ar;
            $appTv->item_id = $request->item_id;
            $appTv->items_ids = json_encode($request->items_ids);
            $appTv->item_type = $request->item_type;
            $appTv->expiry_date = Carbon::createFromFormat('Y-m-d', $request->expiry_date)->endOfDay()->toDateTimeString();;
            $appTv->home_section_id = $request->home_section_id;
            $appTv->app_id = $request->app_id;
            $appTv->category_id = $request->category_id;
            $appTv->store_id = $request->store_id;
            $appTv->order = $request->order;
            if ($request->hasFile('web_image')) {
                Storage::disk('s3')->delete($appTv->web_image);
                $appTv->web_image = UploadImage::uploadImageToStorage($request->web_image, 'app-tv');
            }
            if ($request->hasFile('mobile_image')) {
                Storage::disk('s3')->delete($appTv->mobile_image);
                $appTv->mobile_image = UploadImage::uploadImageToStorage($request->mobile_image, 'app-tv');
            }
            $appTv->save();
            $logData['id'] = $appTv->id;
            $logData['user'] = $request->user;
            $logData['ref_name_ar'] = $request->title_ar;
            $logData['ref_name_en'] = $request->title_en;
            $logData['action'] = Activities::UPDATE_APP_TV;
            event(new DashboardLogs($logData, 'app_tvs'));
            return response()->json([
                'status' => true,
                'message' => 'app tv updated',
                'data' => $appTv
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            return $e;
            Log::error('error in update of dashboard apps tvs ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        try {
            $appTv = AppTv::query()->find($id);
            if (!$appTv) {
                return response()->json([
                    'status' => false,
                    'message' => 'id not found',
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            Storage::disk('s3')->delete($appTv->image);
            $logData['id'] = $appTv->id;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::DELETE_APP_TV;
            event(new DashboardLogs($logData, 'app_tvs'));
            $appTv->delete();
            return response()->json([
                'status' => true,
                'message' => 'app tv deleted',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard apps tvs ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    private function checkLocation($appTv): string
    {
        if ($appTv->category_id) {
            return 'category';
        } elseif ($appTv->store_id) {
            return 'store';
        }
        return 'home';
    }

    public function getAppTvTypes()
    {
        try {
            $appTvTypes = AppTvType::query()
                ->select('id', 'type_en as name')
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'app tv types',
                'data' => $appTvTypes
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getAppTvTypes of dashboard apps tvs ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getAppTvLocations()
    {
        try {
            $locations = ['home', 'category'];
            return response()->json([
                'status' => true,
                'message' => 'app tv locations',
                'data' => $locations
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getAppTvLocations of dashboard apps tvs ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new AppTvsExport($request), 'app-tv.xlsx');
        } catch (\Exception $e) {
            Log::error('error in AppTvsExport in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
