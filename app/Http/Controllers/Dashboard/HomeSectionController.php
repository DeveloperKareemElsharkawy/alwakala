<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Dashboard\HomeSections\CreateHomeSectionRequest;
use App\Http\Requests\Dashboard\HomeSections\UpdateHomeSectionRequest;
use App\Lib\Log\ServerError;
use App\Models\HomeSection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HomeSectionController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = HomeSection::query()
                ->select('id', 'name_en', 'name_ar','order','item_type', 'activation', 'app_type', 'image', 'created_at')
                ->orderBy('updated_at', 'desc');;
            $sections = $query->get();
            foreach ($sections as $section) {
                if ($section->image) {
                    $section->image = config('filesystems.aws_base_url') . $section->image;
                }
                $section->item_type= ($section->item_type==1)?'Product':'Store';
                $section->app_type= ($section->type==1)?'Consumer':'Seller';
            }
            return response()->json([
                'status' => true,
                'message' => 'sections retrieved ',
                'data' => $sections
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard home section' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreateHomeSectionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateHomeSectionRequest $request)
    {
        try {
            $section = new HomeSection;
            $section->name_ar = $request->name_ar;
            $section->name_en = $request->name_en;
            $section->item_type = $request->item_type;
            $section->image = UploadImage::uploadImageToStorage($request->image, 'home-sections');
            $section->activation = $request->activation;
            $section->app_type = $request->app_type;
            $section->order = $request->order;
            $section->items_ids = $request->items_ids;
            $section->save();
            $logData['id'] = $section->id;
            $logData['ref_name_ar'] = $section->name_ar;
            $logData['ref_name_en'] =  $section->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_SECTION;
            event(new DashboardLogs($logData, 'sections'));
            return response()->json([
                'status' => true,
                'message' => 'home section added',
                'data' => $section
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard home section' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $section = HomeSection::query()->find($id);
            $section->items_ids=array_map('intval',explode(",",$section->items_ids));
            $section->selected_orders=array_values(HomeSection::query()->select('order')->where('activation',true)->where('app_type',$section->app_type)->get()->pluck('order')->toArray());

            if ($section->image) {
                $section->image = config('filesystems.aws_base_url') . $section->image;
            }
            if (!$section) {
                return response()->json([
                    'status' => false,
                    'message' => 'Not Found',
                    'data' => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            return response()->json([
                'status' => true,
                'message' => 'home section retrieved',
                'data' => $section
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard home section' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateHomeSectionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateHomeSectionRequest $request)
    {
        try {
            $section = HomeSection::query()->find($request->id);
            $section->name_ar = $request->name_ar;
            $section->name_en = $request->name_en;
            $section->item_type = $request->item_type;
            $section->activation = $request->activation;
            $section->app_type = $request->app_type;
            $section->order = $request->order;
            $section->items_ids = $request->items_ids;
            if ($request->hasFile('image')) {
                Storage::disk('s3')->delete($section->image);
                $section->image = UploadImage::uploadImageToStorage($request->image, 'home-sections');
            }
            $section->save();
            $logData['id'] = $section->id;
            $logData['ref_name_ar'] = $section->name_ar;
            $logData['ref_name_en'] =  $section->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_SECTION;
            event(new DashboardLogs($logData, 'sections'));
            return response()->json([
                'status' => true,
                'message' => 'home section retrieved',
                'data' => $section
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard home section' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request,$id)
    {
        try {
            $section = HomeSection::query()->find($id);
            Storage::disk('s3')->delete($section->image);
            $logData['id'] = $section->id;
            $logData['ref_name_ar'] = $section->name_ar;
            $logData['ref_name_en'] =  $section->name_en;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::DELETE_SECTION;
            event(new DashboardLogs($logData, 'sections'));
            $section->delete();
            return response()->json([
                'status' => true,
                'message' => 'section deleted successfully',
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard home section' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
