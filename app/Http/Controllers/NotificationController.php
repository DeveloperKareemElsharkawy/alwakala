<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Lib\Helpers\Lang\LangHelper;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends BaseController
{

    private $lang;

    public function __construct(Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function listNotifications(Request $request)
    {
        try {

            return response()->json([
                'status' => true,
                'message' => 'Notifications List',
                'data' => NotificationRepository::listNotifications($request->user_id)
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in NotificationController in listNotifications' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function makeAllRead(Request $request)
    {
        try {
            NotificationRepository::makeAllRead($request->user_id);
            return response()->json([
                'status' => true,
                'message' => trans('messages.notifications.make_read'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in NotificationController in makeAllRead' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function makeReadById($id)
    {
        try {
            NotificationRepository::makeReadById($id);
            return response()->json([
                'status' => true,
                'message' => trans('messages.notifications.make_read'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in NotificationController in makeAllReadById' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function unReadCount(Request $request)
    {
        try {

            return response()->json([
                'status' => true,
                'message' => 'Notifications unReadCount',
                'data' => NotificationRepository::unReadCount($request->user_id)
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in NotificationController in unReadCount' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


}
