<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Exports\Dashboard\ShippingMethodsExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\ShippingMethod\CheckShippingMethodExistenceRequest;
use App\Http\Requests\ShippingMethod\CreateShippingMethodRequest;
use App\Http\Requests\ShippingMethod\FilterDataRequest;
use App\Http\Requests\ShippingMethod\UpdateShipmentRequest;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\ShippingMethod;
use App\Repositories\ShippingMethodsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ShippingMethodsController extends BaseController
{
    /**
     * @var ShippingMethodsRepository
     */
    private $shippingMethodsRepository;
    /**
     * @var string
     */
    private $lang;

    /**
     * @param ShippingMethodsRepository $shippingMethodsRepository
     * @param Request $request
     */
    public function __construct(ShippingMethodsRepository $shippingMethodsRepository, Request $request)
    {
        $this->shippingMethodsRepository = $shippingMethodsRepository;
        $this->lang = LangHelper::getDefaultLang($request);
    }

    /**
     * @param FilterDataRequest $request
     * @return JsonResponse
     */
    public function index(FilterDataRequest $request)
    {
        try {
            $shippingMethodsList = $this->shippingMethodsRepository->list($request);

            return response()->json([
                'status' => true,
                'message' => 'Shipping Methods',
                'data' => $shippingMethodsList['data'],
                'offset' => (int)$shippingMethodsList['offset'],
                'limit' => (int)$shippingMethodsList['limit'],
                'total' => $shippingMethodsList['count'],
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard Shipping Methods ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CheckShippingMethodExistenceRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function show(CheckShippingMethodExistenceRequest $request, $id)
    {
        try {
            $shippingMethod = $this->shippingMethodsRepository->showById($id);

            return $this->success([
                'success' => true,
                'message' => 'Shipping Method',
                'data' => $shippingMethod
            ]);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard Shipping Methods' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreateShippingMethodRequest $request
     * @return JsonResponse
     */
    public function store(CreateShippingMethodRequest $request)
    {
        try {
            $shippingMethod = $this->shippingMethodsRepository->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Shipping Method Created',
                'data' => $shippingMethod
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in store of dashboard Shipping Methods' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateShipmentRequest $request
     * @return JsonResponse
     */
    public function update(UpdateShipmentRequest $request)
    {
        try {
            $this->shippingMethodsRepository->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Shipping Method Updated',
                'data' => []
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in store of dashboard Shipping Methods' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     */
    public function destroy(CheckShippingMethodExistenceRequest $request, $id)
    {
        try {
            $this->shippingMethodsRepository->deleteShippingMethod($id);

            return response()->json([
                'success' => true,
                'message' => 'Shipping Method Deleted',
                'data' => []
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in store of dashboard Shipping Methods' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return  Excel::download(new ShippingMethodsExport($request), 'shipping-methods.xlsx');
        } catch (\Exception $e) {
            Log::error('error in shipping-methods in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getShipmentMethods(Request $request)
    {
        try {
            $ShipmentMethods = ShippingMethod::query()->select('id', 'name_' . $this->lang)->get();
            return response()->json([
                'status' => true,
                'message' => trans('messages.shipping.shipping_method'),
                'data' => $ShipmentMethods
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in getShipmentMethods of dashboard ShippingMethods' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
