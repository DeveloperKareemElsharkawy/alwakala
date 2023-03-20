<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Lib\Helpers\UserId\UserId;
use App\Models\Message;
use App\Models\Settings\SellerAppSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends BaseController
{
    public function getSocial(SellerAppSettings $settings)
    {
        try {
            return $this->success([
                'message' => trans('messages.general.listed'),
                'data' => [
                    'facebook_url' => $settings->facebook_url,
                    'instagram_url' => $settings->instagram_url,
                    'twitter_url' => $settings->twitter_url,
                    'linkedin_url' => $settings->linkedin_url
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('error in addComplaints of seller Complaints' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function getGlobal(Request $request)
    {
        try {

            $userId = UserId::UserId($request);

            return $this->success([
                'message' => trans('messages.general.listed'),
                'data' => [
                    'messages_count' => count(Message::query()->where('receiver_id',$userId)->get()->unique('sender_id')),

                ]
            ]);
        } catch (\Exception $e) {
            Log::error('error in addComplaints of seller Complaints' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

}
