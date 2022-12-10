<?php

namespace App\Http\Controllers\Seller;

use App\Enums\Apps\AApps;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Complaint;
use App\Models\ComplaintTopic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ComplaintsController extends BaseController
{
    private $lang;

    public function __construct(Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function addComplaint(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'required|max:11',
                'details' => 'required',
                'complaint_topic_id' => 'required|exists:complaint_topics,id',
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $complaint = new Complaint;
            $complaint->name = $request->name;
            $complaint->email = $request->email;
            $complaint->phone = $request->phone;
            $complaint->details = $request->details;
            $complaint->complaint_topic_id = $request->complaint_topic_id;
            $complaint->app_id = AApps::SELLER_APP;
            $complaint->save();

            return response()->json([
                'success' => true,
                'message' => trans('messages.complaints.added'),
                'data' => $complaint
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in addComplaints of seller Complaints' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getTopics()
    {
        try {
            $topics = ComplaintTopic::query()
                ->select('id', 'name_' . $this->lang . ' as name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => trans('messages.complaints.topics'),
                'data' => $topics
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in getTopics of seller Complaints' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
