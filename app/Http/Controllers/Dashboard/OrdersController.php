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
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Dashboard\OrderRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class OrdersController extends BaseController
{

    private $lang;
    private $orderRepository;

    /**
     * OrdersController constructor.
     * @param Request $request
     * @param OrderRepository $orderRepository
     */
    public function __construct(Request $request, OrderRepository $orderRepository)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->orderRepository = $orderRepository;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return OrdersCollection|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $list = $this->orderRepository->list($request);
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
            return $e;
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
            $order = $this->orderRepository->getOrderDetails($id, $this->lang);
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatuses()
    {
        try {
            $orderStatuses = OrderStatus::query()
                ->select('id', 'status')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'order statuses',
                'data' => $orderStatuses
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getStatuses of dashboard orders' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getPackage($productId)
    {
        try {
            $packingUnitProductId = PackingUnitProduct::query()
                ->select('id')
                ->where('packing_unit_id', 1)
                ->where('product_id', $productId)
                ->first()->id;
            $package = PackingUnitProductAttribute::query()
                ->select('sizes.size', 'packing_unit_product_attributes.quantity')
                ->join('sizes', 'sizes.id', '=', 'packing_unit_product_attributes.size_id')
                ->where('packing_unit_product_id', $packingUnitProductId)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'product package',
                'data' => $package
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getPackage of dashboard orders' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new OrdersExport($request), 'orders.xlsx');
        } catch (\Exception $e) {
            Log::error('error in ORders in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function changeStatusOfOrdersProducts(Request $request)
    {
        try {
            $this->orderRepository->changeStatusOfOrdersProducts($request);
            return response()->json([
                'status' => true,
                'message' => 'status changed',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in change status of orders products of dashboard orders' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function changeStatusOfOrder(Request $request)
    {
        try {
            $this->orderRepository->changeStatusOfOrder($request);
            return response()->json([
                'status' => true,
                'message' => 'status changed',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in change status of order of dashboard orders' . __LINE__ . $e);
            return $this->connectionError($e);
        }

    }

    public function getOrdersByStoreTypeForStoreRating(Request $request,$type)
    {
        try {

           // $this->orderRepository->changeStatusOfOrder($request);
            return response()->json([
                'status' => true,
                'message' => 'Stores Listing',
                'data' => $this->orderRepository->getOrdersByStoreTypeForStoreRating($type)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in change status of order of dashboard orders' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
