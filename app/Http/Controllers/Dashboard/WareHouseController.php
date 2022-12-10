<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Dashboard\Warehouses\AcceptProductRequest;
use App\Http\Requests\Warehouse\CheckWarehouseExistenceRequest;
use App\Http\Requests\Warehouse\FilterDataRequest;
use App\Http\Requests\Warehouse\CreateWareHouseRequest;
use App\Http\Requests\Warehouse\UpdateWareHousesRequest;
use App\Models\ProductStore;
use App\Models\Warehouse;
use App\Repositories\WarehousesRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WareHouseController extends BaseController
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
     * @param CheckWarehouseExistenceRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function show(CheckWarehouseExistenceRequest $request, $id)
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
            $warehouse = $this->warehousesRepository->create($request->validated());

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
            $this->warehousesRepository->update($request->validated());

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
    public function destroy(CheckWarehouseExistenceRequest $request, $id)
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

    /**
     * Accept to add products to warehouse or refuse it
     *
     * @param AcceptProductRequest $request
     * @return Response
     */
    public function accept_product(AcceptProductRequest $request)
    {
        try {
            $warehouse = Warehouse::findOrFail($request->warehouse_id);

            if (!$warehouse)
                return $this->error(['message' => trans('messages.warehouse.not_found')]);

            foreach ($request['products'] as $product) {
                $product_warehouse = $warehouse->warehouse_products()->where('product_id' , $product['product_id'])->first();
                if ($request['accept'] == true) {
                    $store = ProductStore::where('product_id' , $product['product_id'])->firstOrFail();
                    if (!$store->productStoreStock()->where('color_id' , $product_warehouse->color_id)->where('size_id' , $product_warehouse->size_id)->exists()) {

                        return response()->json([
                            'status' => false,
                            'message' => 'هذا المنتج غير متاح بتلك المواصفات',
                            'data' => '',
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }

                }
            }

            $data = $this->warehousesRepository->accept_product($request);

            return $this->success([
                'success' => true,
                'message' => 'Accept products in WareHouses',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('error in accepting products in warehouse' . __LINE__ . $e);
            dd($e->getMessage());
            return $this->connectionError($e);
        }
    }
}
