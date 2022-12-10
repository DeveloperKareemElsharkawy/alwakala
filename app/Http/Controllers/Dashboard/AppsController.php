<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Log\ServerError;
use App\Models\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AppsController extends BaseController
{
    public function getForSelection()
    {
        try {
            $apps = App::select('id', 'app_ar as name')
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'apps',
                'data' => $apps
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getForSelection of dashboard apps ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
