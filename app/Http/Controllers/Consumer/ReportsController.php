<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Consumer\Reports\ReportStoreRequest;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Report;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReportsController extends BaseController
{
    public function report(ReportStoreRequest $request)
    {
        try {

            $report = new Report;
            $report->user_id = $request->user_id;
            $report->item_id = $request->store_id;
            $report->item_type = Store::class;
            $report->details = $request->details;
            $report->save();

            return $this->success(['message' => trans('messages.reports.report_added')]);

        } catch (\Exception $e) {
            Log::error('error in report of seller reports ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
