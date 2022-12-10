<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Log\ServerError;
use App\Repositories\ActivitiesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogsController extends BaseController
{
    public function getLogs(Request $request)
    {
        try {

            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => "logs retrieved successfully",
                "data" => array_values(ActivitiesRepository::getDashboardLogs())
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getLogs of dashboard logs' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getLogsByRefId($colloction,$refId)
    {
        try {

            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => "logs retrieved successfully",
                "data" => array_values(ActivitiesRepository::getDashboardLogsByRefId($colloction,$refId))
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getLogsByRefId of dashboard logs' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getLogsByColloction($colloction)
    {
        try {

            return response()->json([
                "status" => AResponseStatusCode::SUCCESS,
                "message" => "logs retrieved successfully",
                "data" => array_values(ActivitiesRepository::getDashboardLogsByRefId($colloction))
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getLogsByColloction of dashboard logs' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
