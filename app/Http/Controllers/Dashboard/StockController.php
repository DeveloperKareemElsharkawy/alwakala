<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Stock\CheckStockExistenceRequest;
use App\Http\Requests\Stock\FilterDataRequest;
use App\Models\ProductStoreStock;
use App\Repositories\StockRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class StockController extends BaseController
{
    /**
     * @var StockRepository
     */
    private $stockRepository;


    /**
     * @param StockRepository $stockRepository
     */
    public function __construct(StockRepository $stockRepository)
    {
        $this->stockRepository = $stockRepository;
    }

    /**
     * @param FilterDataRequest $request
     * @return JsonResponse
     */
    public function index(FilterDataRequest $request)
    {
        try {
            $stockList = $this->stockRepository->list($request);

            return response()->json([
                'status' => true,
                'message' => 'Stock',
                'data' => $stockList['data'],
                'offset' => (int)$stockList['offset'],
                'limit' => (int)$stockList['limit'],
                'total' => $stockList['count'],
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard Stock ' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function approvePendingStock(CheckStockExistenceRequest $request)
    {
        try {
            $stock = ProductStoreStock::query()->where('id', $request->id)->first();
            $this->stockRepository->approveStock($stock);
            return response()->json([
                'status' => true,
                'message' => trans('messages.product.reviewed'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in approvePendingStock of dashboard stock' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function editStock(CheckStockExistenceRequest $request)
    {
        try {
            $stock = ProductStoreStock::query()->where('id', $request->id)->first();
            if ($request->stock)
                $stock->stock =  $request->stock;
            if ($request->available_stock)
                $stock->available_stock =  $request->available_stock;
            if ($request->sold)
                $stock->sold =  $request->sold;
            $stock->save();
            return response()->json([
                'status' => true,
                'message' => trans('messages.product.reviewed'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in approvePendingStock of dashboard stock' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    public function rejectPendingStock(CheckStockExistenceRequest $request)
    {
        try {
            $stock = ProductStoreStock::query()->where('id', $request->id)->first();
            $this->stockRepository->rejectStock($stock);
            return response()->json([
                'status' => true,
                'message' => trans('messages.product.reviewed'),
                'data' => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in rejectPendingStock of dashboard stock' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }


}
