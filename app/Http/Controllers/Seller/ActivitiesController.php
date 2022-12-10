<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Seller\ActivitiesCollection;
use App\Http\Resources\Seller\ActivitiesResource;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Activity;
use App\Models\Seller;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ActivitiesController extends BaseController
{
    private $lang;

    public function __construct(Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function index(Request $request)
    {
        try {
            $store = Store::query()
                ->select('id')
                ->where('user_id', $request->user_id)
                ->first();
            $sellersIds = Seller::query()
                ->where('store_id', $store->id)
                ->pluck('user_id')
                ->toArray();
            $activities = Activity::query()
                ->select(
                    'activities.id',
                    'activities.action',
                    'activities.ref_id',
                    'users.name',
                    'activities.user_id',
                    'activities.type',
                    'activities.created_at'
                )
                ->join('users', 'users.id', '=', 'activities.user_id')
                ->whereIn('user_id', $sellersIds)
                ->paginate(10);
            foreach ($activities as $activity) {
                $activity->action = trans('messages.actions.' . $activity->action);
                $activity->date = Carbon::parse($activity->created_at)->format('Y-m-d H:i:s');
                unset($activity->created_at);
            }
            return response()->json([
                "status" => true,
                "message" => trans('messages.general.listed'),
                "data" => $activities
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in validateFirstScreen of seller auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function userActivity(Request $request, $userId): \Illuminate\Http\JsonResponse
    {
        try {
            $store = Store::query()
                ->select('id')
                ->where('user_id', $request->user_id)
                ->first();
            if (!$store) {
                return response()->json([
                    "status" => false,
                    "message" => trans('messages.general.not_found'),
                    "data" => ''
                ], AResponseStatusCode::NOT_FOUNT);
            }

            $sellersIds = Seller::query()
                ->where('store_id', $store->id)
                ->pluck('user_id')
                ->toArray();
            if (!in_array($userId, $sellersIds)) {
                return response()->json([
                    "status" => false,
                    "message" => trans('messages.general.forbidden'),
                    "data" => ''
                ], AResponseStatusCode::FORBIDDEN);
            }

            $activities = Activity::query()
                ->select(
                    'activities.id',
                    'activities.action',
                    'activities.ref_id',
                    'users.name',
                    'activities.user_id',
                    'activities.type',
                    'activities.created_at'
                )
                ->join('users', 'users.id', '=', 'activities.user_id')
                ->where('user_id', $userId)
                ->paginate(10);
            foreach ($activities as $activity) {
                $activity->action = trans('messages.actions.' . $activity->action);
                $activity->date = Carbon::parse($activity->created_at)->format('Y-m-d H:i:s');
                unset($activity->created_at);
            }
            return response()->json([
                "status" => true,
                "message" => trans('messages.general.listed'),
                "data" => $activities
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in validateFirstScreen of seller auth' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


}
