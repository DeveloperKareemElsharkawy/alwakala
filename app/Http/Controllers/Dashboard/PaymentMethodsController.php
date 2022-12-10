<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\PaymentMethods\CheckPaymentMethodExistenceRequest;
use App\Http\Requests\PaymentMethods\FilterDataRequest;
use App\Http\Requests\PaymentMethods\CreatePaymentMethodRequest;
use App\Http\Requests\PaymentMethods\UpdateWareHousesRequest;
use App\Repositories\PaymentMethodRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentMethodsController extends BaseController
{
    /**
     * @var PaymentMethodRepository
     */
    private $paymentMethodRepository;

    /**
     * @param PaymentMethodRepository $paymentMethodRepository
     */
    public function __construct(PaymentMethodRepository $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * @param FilterDataRequest $request
     * @return JsonResponse
     */
    public function index(FilterDataRequest $request)
    {
        try {
            $paymentMethodsList = $this->paymentMethodRepository->list($request);

            return response()->json([
                'status' => true,
                'message' => trans('messages.general.listed'),
                'data' => $paymentMethodsList['data'],
                'offset' => (int)$paymentMethodsList['offset'],
                'limit' => (int)$paymentMethodsList['limit'],
                'total' => $paymentMethodsList['count'],
            ], AResponseStatusCode::SUCCESS);

        } catch (\Exception $e) {
            Log::error('error in index of dashboard WareHouses ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CheckPaymentMethodExistenceRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function show(CheckPaymentMethodExistenceRequest $request, $id)
    {
        try {
            $warehouse = $this->paymentMethodRepository->showById($id);

            return $this->success(['message' => trans('messages.general.listed'), 'data' => $warehouse]);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard WareHouse' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreatePaymentMethodRequest $request
     * @return JsonResponse
     */
    public function store(CreatePaymentMethodRequest $request)
    {
        try {
            $warehouse = $this->paymentMethodRepository->create($request->validated());

            return $this->success(['message' => trans('messages.payment_methods.created'), 'data' => $warehouse]);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard WareHouses ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateWareHousesRequest $request
     * @return JsonResponse
     */
    public function update(UpdateWareHousesRequest $request)
    {
        try {
            $this->paymentMethodRepository->update($request->validated());

            return $this->success(['message' => trans('messages.payment_methods.updated'), 'data' => []]);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard WareHouses' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     */
    public function destroy(CheckPaymentMethodExistenceRequest $request, $id)
    {
        try {
            $this->paymentMethodRepository->delete($id);

            return $this->success(['message' => trans('messages.payment_methods.deleted'), 'data' => []]);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard WareHouses' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


}
