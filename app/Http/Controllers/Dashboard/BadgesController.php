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
use App\Models\Badge;
use App\Repositories\BadgesRepository;
use App\Services\Badges\ProductService;
use App\Services\Badges\BadgesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;


class BadgesController extends BaseController
{
    private $badgesService;

    private $badgesRepository;

    public function __construct(BadgesService $badgesService, BadgesRepository $badgesRepository)
    {
        $this->badgesService = $badgesService;
        $this->badgesRepository = $badgesRepository;
    }

    /**
     * @param FilterDataRequest $request
     * @return JsonResponse
     */
    public function index(FilterDataRequest $request)
    {
        try {
            $badgesList = $this->badgesRepository->list($request);

            return response()->json([
                'status' => true,
                'message' => 'Badges',
                'data' => $badgesList['data'],
                'offset' => (int)$badgesList['offset'],
                'limit' => (int)$badgesList['limit'],
                'total' => $badgesList['count'],
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in index of dashboard Badge' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CheckBadgeExistenceRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function show(CheckBadgeExistenceRequest $request, $id)
    {
        try {
            $badge = $this->badgesService->showBadge($id);

            return $this->success([
                'message' => 'Badge',
                'data' => $badge
            ]);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard badge' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param CreateBadgeRequest $request
     * @return JsonResponse
     */
    public function store(CreateBadgeRequest $request)
    {
        try {
            $badge = $this->badgesService->createBadge($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Badge Created',
                'data' => $badge
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard Badge' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @param UpdateBadgeRequest $request
     * @return JsonResponse
     */
    public function update(UpdateBadgeRequest $request)
    {
        try {
            $this->badgesService->editBadge($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Badge Updated',
                'data' => []
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard Badge' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    /**
     * @return JsonResponse
     */
    public function destroy(CheckBadgeExistenceRequest $request, $id)
    {
        try {
            $badge = $this->badgesService->deleteBadge($request->validated());

            return response()->json([
                'success' => $badge['status'],
                'message' => $badge['message'],
                'data' => []
            ], AResponseStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('error in store of dashboard Badge' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new BadgesExport($request), 'badges.xlsx');
        } catch (\Exception $e) {
            Log::error('error in Export Badges in dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getAllForSelection(Request $request)
    {
        try {
            $badge = $this->badgesService->getAllForSelection($request);

            return $this->success([
                'message' => 'Badge',
                'data' => $badge
            ]);
        } catch (\Exception $e) {
            Log::error('error in show of dashboard badge' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
