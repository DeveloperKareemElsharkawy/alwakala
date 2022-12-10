<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Logs\DashboardLogs;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\RegisterAdmin;
use App\Http\Requests\Dashboard\System\NewSystemSetupRequest;
use App\Lib\Log\ServerError;
use App\Models\SystemSetup;
use App\Repositories\StoreRepository;
use App\Repositories\SystemSetupRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SystemSetupController extends BaseController
{
    public function create(NewSystemSetupRequest $request)
    {

        try {
            SystemSetupRepository::save($request);
            $logData['id'] = $request->id;
            $logData['ref_name_ar'] = $request->title;
            $logData['ref_name_en'] =  $request->title;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::CREATE_SYSTEM_SETUP;
            event(new DashboardLogs($logData, 'systems'));
            return response()->json([
                'status' => true,
                'message' => trans('messages.system.created'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in create of dashboard SystemSetup' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function update(NewSystemSetupRequest $request)
    {
        try {
            $system = SystemSetup::query()->findOrFail($request['id']);
            SystemSetupRepository::update($request);
            $logData['id'] = $request->id;
            $logData['ref_name_ar'] = $system->title;
            $logData['ref_name_en'] =  $system->title;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::UPDATE_SYSTEM_SETUP;
            event(new DashboardLogs($logData, 'systems'));
            return response()->json([
                'status' => true,
                'message' => trans('messages.system.updated'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard SystemSetup' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function delete(Request $request,$id)
    {
        try {
            SystemSetupRepository::delete($id);
            $logData['id'] = $id;
            $logData['ref_name_ar'] = $request->title;
            $logData['ref_name_en'] =  $request->title;
            $logData['user'] = $request->user;
            $logData['action'] = Activities::DELETE_SYSTEM_SETUP;
            event(new DashboardLogs($logData, 'systems'));
            return response()->json([
                'status' => true,
                'message' => trans('messages.system.deleted'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard SystemSetup' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function view()
    {
        try {
            $data = SystemSetupRepository::view();
            return response()->json([
                'status' => true,
                'message' => 'view',
                'data' => $data
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in view of dashboard SystemSetup' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }
}
