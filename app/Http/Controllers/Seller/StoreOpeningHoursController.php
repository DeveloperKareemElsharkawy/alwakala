<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\DaysOfWeek;
use App\Models\Store;
use App\Models\StoreOpeningHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StoreOpeningHoursController extends BaseController
{
    private $lang;

    /**
     * OrdersController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function storeOpeningHours(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'data' => 'required|array|max:7|min:5',
                'data.*.open_time' => 'required',
                'data.*.close_time' => 'required',
                'data.*.days_of_week_id' => 'required|numeric|min:1|max:7|exists:days_of_week,id',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            $storeId = Store::query()->where('user_id', $request->user_id)->first()->id;
            DB::beginTransaction();
            $storeOpeningHours = new StoreOpeningHour;
            $storeOpeningHours->where('store_id', $storeId)->delete();
            $openDays = [];
            $newRequest = [];
            foreach ($request->data as $index => $openHour) {
                $openDays[] = $openHour['days_of_week_id'];
                $newRequest[$index]['store_id'] = $storeId;
                $newRequest[$index]['open_time'] = $openHour['open_time'];
                $newRequest[$index]['close_time'] = $openHour['close_time'];
                $newRequest[$index]['days_of_week_id'] = $openHour['days_of_week_id'];
                $newRequest[$index]['is_open'] = true;
                $newRequest[$index]['created_at'] = Carbon::now();
                $newRequest[$index]['updated_at'] = Carbon::now();
            }
            $weekDays = DaysOfWeek::query()->pluck('id')->toArray();
            $vacationDays = array_diff($weekDays, $openDays);
            foreach ($vacationDays as $index => $vacationDay) {
                $newRequest[$index]['store_id'] = $storeId;
                $newRequest[$index]['open_time'] = null;
                $newRequest[$index]['close_time'] = null;
                $newRequest[$index]['days_of_week_id'] = $vacationDay;
                $newRequest[$index]['is_open'] = false;
                $newRequest[$index]['created_at'] = Carbon::now();
                $newRequest[$index]['updated_at'] = Carbon::now();
            }
            StoreOpeningHour::insert($newRequest);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => trans('messages.general.success'),
                'data' => ''
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in storeOpeningHours of seller StoreOpeningHours' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getOpeningHours(Request $request, $storeId)
    {
        try {
            if (!Store::query()->where('id', $storeId)->first()) {
                return $this->notFound();
            }
            $openTime = StoreOpeningHour::query()
                ->select('store_opening_hours.id',
                    'store_opening_hours.days_of_week_id',
                    'store_opening_hours.open_time',
                    'store_opening_hours.close_time',
                    'store_opening_hours.is_open',
                    'days_of_week.name_' . $this->lang . ' as day'
                )
                ->join('days_of_week', 'days_of_week.id', '=', 'store_opening_hours.days_of_week_id')
                ->where('store_id', $storeId)
                ->get();

            return response()->json([
                'status' => true,
                'message' => trans('messages.stores.open_hours_added'),
                'data' => $openTime
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getOpeningHours of seller StoreOpeningHours' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getWeekDays(Request $request)
    {
        try {

            DaysOfWeek::query()->updateOrCreate(
                ['name_en' => 'Saturday', 'name_ar' => 'السبت'],
                ['name_en' => 'Saturday', 'name_ar' => 'السبت']
            );
            DaysOfWeek::query()->updateOrCreate(
                ['name_en' => 'Sunday', 'name_ar' => 'الأحد'],
                ['name_en' => 'Sunday', 'name_ar' => 'الأحد']
            );
            DaysOfWeek::query()->updateOrCreate(
                ['name_en' => 'Monday', 'name_ar' => 'الاثنين'],
                ['name_en' => 'Monday', 'name_ar' => 'الاثنين']
            );
            DaysOfWeek::query()->updateOrCreate(
                ['name_en' => 'Tuesday', 'name_ar' => 'الثلاثاء'],
                ['name_en' => 'Tuesday', 'name_ar' => 'الثلاثاء']
            );
            DaysOfWeek::query()->updateOrCreate(
                ['name_en' => 'Wednesday', 'name_ar' => 'الأربعاء'],
                ['name_en' => 'Wednesday', 'name_ar' => 'الأربعاء']
            );
            DaysOfWeek::query()->updateOrCreate(
                ['name_en' => 'Thursday', 'name_ar' => 'الخميس'],
                ['name_en' => 'Thursday', 'name_ar' => 'الخميس']
            );
            DaysOfWeek::query()->updateOrCreate(
                ['name_en' => 'Friday', 'name_ar' => 'الجمعه'],
                ['name_en' => 'Friday', 'name_ar' => 'الجمعه']
            );

            $weekDays = DaysOfWeek::query()
                ->select('id', 'name_' . $this->lang . ' as name')
                ->get();
            return response()->json([
                'status' => true,
                'message' => trans('messages.stores.week_days'),
                'data' => $weekDays
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getWeekDays of seller StoreOpeningHours' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function storeWorkingTime(Request $request, $storeId): \Illuminate\Http\JsonResponse
    {
        try {
            $store = Store::query()->with('openHours')->where('id', $storeId)->first();

            if (!$store) {
                return $this->error(['message' => trans('messages.general.not_found')]);
            }

            if (!$store->openHours->isNotEmpty()) {
                return $this->error(['message' => trans('messages.stores.open_hours_not_found')]);
            }

            $openTime = StoreOpeningHour::query()
                ->select('store_opening_hours.id',
                    'store_opening_hours.days_of_week_id',
                    'store_opening_hours.open_time',
                    'store_opening_hours.close_time',
                    'store_opening_hours.is_open',
                    'days_of_week.name_' . $this->lang . ' as day'
                )
                ->join('days_of_week', 'days_of_week.id', '=', 'store_opening_hours.days_of_week_id')
                ->where('store_id', $storeId)->get();

            $today = Carbon::parse(now())->format('l');

            $todayId = DaysOfWeek::query()->where('name_en', $today)->first()->id;

            $currentWorkingDay = $openTime->where('days_of_week_id', $todayId)->first();

            return response()->json([
                'status' => true,
                'message' => trans('messages.stores.week_days'),
                'data' => [
                    'open_time' => $currentWorkingDay->open_time,
                    'close_time' => $currentWorkingDay->close_time,
                    'vacations' => $openTime->where('is_open', false)->pluck('day'),
                    'days' => $openTime->where('is_open', true)->count()
                ]
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in getWeekDays of seller StoreOpeningHours' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
