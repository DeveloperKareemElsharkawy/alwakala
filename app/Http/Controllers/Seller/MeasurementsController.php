<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Measurements\DeleteMeasurementRequest;
use App\Http\Requests\Measurements\StoreMeasurementsRequest;
use App\Http\Requests\Measurements\UpdateMeasurementRequest;
use App\Lib\Helpers\Authorization\AuthorizationHelper;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\Store;
use App\Models\StoreCategoriesMeasurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Support\Facades\Validator;

class MeasurementsController extends BaseController
{

    private $lang;

    /**
     * OrdersController constructor.
     * @param ShoppingCartRepository $shoppingCartRepo
     * @param ProductRepository $productRepository
     * @param Request $request
     */
    public function __construct(Request $request)
    {

        $this->lang = LangHelper::getDefaultLang($request);
    }

    public function store(StoreMeasurementsRequest $request)
    {

        try {

            $store_id = Store::where('user_id', request()->user_id)->first()->id;

            $StoreCategoriesMeasurement = StoreCategoriesMeasurement::create([
                'store_id' => $store_id,
                'size_id' => $request->size_id,
                'category_id' => $request->category_id,
                'length' => $request->length,
                'shoulder' => $request->shoulder,
                'chest' => $request->chest,
                'waist' => $request->waist,
                'hem' => $request->hem,
                'arm' => $request->arm,
                'biceps' => $request->biceps,
                's_l' => $request->s_l,
            ]);

            return response()->json([
                'status' => true,
                'message' => trans('messages.measurements.save'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in StoreCategoriesMeasurement for seller ' . __LINE__ . $e);
            return $this->connectionError($e);
        }


    }


    public function storeMeasurements()
    {
        try {

            $store = Store::where('user_id', request()->user_id)->first();

            $storeMeasurements = StoreCategoriesMeasurement::
            query()->join('sizes', 'sizes.id', 'store_categories_measurements.size_id')
                ->select('sizes.id', 'sizes.size', 'store_categories_measurements.*')
                ->where('store_id', $store->id)->get();

            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $storeMeasurements
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in StoreCategoriesMeasurement for seller ' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function update(UpdateMeasurementRequest $request,$storeMeasurements_id)
    {
        try {
            $store = Store::where('user_id', request()->user_id)->first();

            $storeMeasurement = StoreCategoriesMeasurement::query()->where([['store_id',$store->id],['id',$storeMeasurements_id]])->first();

            if (!$storeMeasurement) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.measurements.not_found'),
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }

            $storeMeasurement->update([
                'size_id' => $request->size_id,
                'category_id' => $request->category_id,
                'length' => $request->length,
                'shoulder' => $request->shoulder,
                'chest' => $request->chest,
                'waist' => $request->waist,
                'hem' => $request->hem,
                'arm' => $request->arm,
                'biceps' => $request->biceps,
                's_l' => $request->s_l,
            ]);

            return response()->json([
                'status' => true,
                'message' => trans('messages.measurements.updated'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in StoreCategoriesMeasurement for seller ' . __LINE__ . $e);
            return $this->connectionError($e);
        }


    }

    public function destroy(Request $request,$storeMeasurements_id)
    {
        try {

            $store = Store::where('user_id', request()->user_id)->first();

            $storeMeasurement = StoreCategoriesMeasurement::query()->where([['store_id',$store->id],['id',$storeMeasurements_id]])->first();

            if (!$storeMeasurement) {
                return response()->json([
                    'status' => false,
                    'message' => trans('messages.measurements.not_found'),
                    'data' => ''
                ], AResponseStatusCode::FORBIDDEN);
            }

            $storeMeasurement->delete();
            return response()->json([
                'status' => true,
                'message' => trans('messages.measurements.deleted'),
                'data' => '',
            ]);

        } catch (\Exception $exception) {
            return ServerError::handle($exception);
        }
    }

}
