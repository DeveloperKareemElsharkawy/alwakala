<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Warehouse\CheckWarehouseExistenceRequest;
use App\Http\Requests\Warehouse\FilterDataRequest;
use App\Http\Requests\Warehouse\CreateWareHouseRequest;
use App\Http\Requests\Warehouse\UpdateWareHousesRequest;
use App\Repositories\WarehousesRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WarehouseController extends BaseController
{
    /**
     * @var WarehousesRepository
     */
    private $warehousesRepository;

    /**
     * @param WarehousesRepository $warehousesRepository
     */
    public function __construct(WarehousesRepository $warehousesRepository)
    {
        $this->warehousesRepository = $warehousesRepository;
    }

     /**
     * @param FilterDataRequest $request
     * @return JsonResponse
     */
    public function index(FilterDataRequest $request)
    {
        try {
            $warehousesList = $this->warehousesRepository->list($request);

            return response()->json([
                'status' => true,
                'message' => 'WareHouses',
                'data' => $warehousesList['data'],
                'total' => $warehousesList['count'],
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard WareHouses ' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $warehouse = $this->warehousesRepository->showById($id);

            return $this->success([
                'success' => true,
                'message' => 'WareHouses',
                'data' => $warehouse
            ]);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard WareHouse' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreateWareHouseRequest $request
     * @return JsonResponse
     */
    public function store(CreateWareHouseRequest $request)
    {
        try {
            $data = $request->all();
            $data['user_id'] = $request['user_id'];
            $warehouse = $this->warehousesRepository->create($data);

            return response()->json([
                'success' => true,
                'message' => 'WareHouses',
                'data' => $warehouse
            ], AResponseStatusCode::CREATED);
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
            $data = $request->all();
            $data['user_id'] = $request['user_id'];
            $this->warehousesRepository->update($data);

            return response()->json([
                'success' => true,
                'message' => 'WareHouses Updated',
                'data' => []
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in update of dashboard WareHouses' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $this->warehousesRepository->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'WareHouses Deleted',
                'data' => []
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in delete of dashboard WareHouses' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
