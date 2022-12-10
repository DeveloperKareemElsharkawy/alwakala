<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReportsController extends BaseController
{
    public function report(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_type' => 'required|string',
                'item_id' => 'required|numeric',
                'details' => 'required',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $report = new Report;
            $report->user_id = $request->user_id;
            $report->item_id = $request->item_id;
            $report->item_type = $request->item_type;
            $report->details = $request->details;
            $report->save();


            return response()->json([
                'status' => true,
                'message' => trans('messages.reports.report_added'),
                'data' => $report
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in report of seller reports ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
