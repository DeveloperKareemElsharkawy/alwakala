<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Exports\Dashboard\OrdersExport;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Seller\Orders\OrdersCollection;
use App\Lib\Helpers\Address\AddressHelper;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Log\ServerError;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Dashboard\ParentOrderRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ParentOrdersController extends BaseController
{

    private $lang;
    private $parentOrderRepository;

    /**
     * OrdersController constructor.
     * @param Request $request
     * @param parentOrderRepository $orderRepository
     */
    public function __construct(Request $request, ParentOrderRepository $parentOrderRepository)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->parentOrderRepository = $parentOrderRepository;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return OrdersCollection|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $list = $this->parentOrderRepository->list($request);
            return response()->json([
                'status' => true,
                'message' => trans('messages.order.retrieved_all'),
                'data' => $list['data'],
                'offset' => (int)$list['offset'],
                'limit' => (int)$list['limit'],
                'total' => $list['count'],
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard orders' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param int $id
     */
    public function show(Request $request, $id)
    {
        try {
            $order = $this->parentOrderRepository->getOrderDetails($id, $this->lang);
            return response()->json([
                'status' => true,
                'message' => 'order',
                'data' => $order
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard orders' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }
}
