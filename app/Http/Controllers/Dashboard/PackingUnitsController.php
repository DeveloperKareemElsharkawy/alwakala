<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\Activity\Activities;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Logs\DashboardLogs;
use App\Exports\Dashboard\BadgesExport;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Dashboard\Badges\CheckBadgeExistenceRequest;
use App\Http\Requests\Dashboard\Badges\CreateBadgeRequest;
use App\Http\Requests\Dashboard\Badges\FilterDataRequest;
use App\Http\Requests\Dashboard\Badges\UpdateBadgeRequest;
use App\Http\Requests\Dashboard\PackingUnits\CheckPackingUnitsExistenceRequest;
use App\Http\Requests\Dashboard\PackingUnits\CreatePackingUnitsRequest;
use App\Http\Requests\Dashboard\PackingUnits\UpdatePackingUnitsRequest;
use App\Repositories\PackingUnitsRepository;
use App\Services\PackingUnits\PackingUnitsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;


class PackingUnitsController extends BaseController
{
    private $packingUnitsService;

    private $packingUnitsRepository;

    public function __construct(PackingUnitsService $packingUnitsService, PackingUnitsRepository $packingUnitsRepository)
    {
        $this->packingUnitsService = $packingUnitsService;
        $this->packingUnitsRepository = $packingUnitsRepository;
    }

    /**
     * @param FilterDataRequest $request
     * @return JsonResponse
     */
    public function index(FilterDataRequest $request)
    {
        try {
            $list = $this->packingUnitsRepository->list($request);
            return response()->json([
                'status' => true,
                'message' => 'Packing Unit',
                'data' => $list['data'],
                'offset' => (int)$list['offset'],
                'limit' => (int)$list['limit'],
                'total' => $list['count'],
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard Packing Unit' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    /**
     * @param CheckPackingUnitsExistenceRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function show(CheckPackingUnitsExistenceRequest $request, $id)
    {
        try {
            $object = $this->packingUnitsService->showPackingUnit($id);

            return $this->success([
                'message' => 'Packing Unit',
                'data' => $object
            ]);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard PackingUnits' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreateBadgeRequest $request
     * @return JsonResponse
     */
    public function store(CreatePackingUnitsRequest $request)
    {
        try {
            $object = $this->packingUnitsService->createPackingUnit($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'PackingUnit Created',
                'data' => $object
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard PackingUnit' . __LINE__ . $e);
            return $e;
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateBadgeRequest $request
     * @return JsonResponse
     */
    public function update(UpdatePackingUnitsRequest $request)
    {
        try {
            $this->packingUnitsService->editPackingUnit($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'PackingUnit Updated',
                'data' => []
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard PackingUnit' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     */
    public function destroy(CheckPackingUnitsExistenceRequest $request, $id)
    {
        try {
            $object = $this->packingUnitsService->deletePackingUnit($request->validated());

            return response()->json([
                'success' => $object['status'],
                'message' => $object['message'],
                'data' => []
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard PackingUnit' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new BadgesExport($request), 'packing-units.xlsx');
        } catch (\Exception $e) {
            Log::error('error in Export PackingUnit in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getAllForSelection(Request $request)
    {
        try {
            $object = $this->packingUnitsService->getAllForSelection($request);

            return $this->success([
                'message' => 'Packing Units',
                'data' => $object
            ]);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard PackingUnit' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
