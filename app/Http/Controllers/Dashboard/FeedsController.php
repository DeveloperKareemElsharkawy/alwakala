<?php

namespace App\Http\Controllers\Dashboard;


use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Feeds\FeedsVideosChangeStatusRequest;
use App\Http\Requests\Feeds\OrderFeedsRequest;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Order;
use App\Repositories\FeedsRepository;
use App\Repositories\FeedsVideosRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedsController extends BaseController
{
    /**
     * @var FeedsRepository
     */
    private $feedsRepository;
    /**
     * @var FeedsVideosRepository
     */
    private $feedsVideosRepository;

    public function __construct(FeedsRepository $feedsRepository, FeedsVideosRepository $feedsVideosRepository)
    {
        $this->feedsRepository = $feedsRepository;
        $this->feedsVideosRepository = $feedsVideosRepository;
    }

    public function feeds(Request $request)
    {
        try {

            return response()->json([
                'status' => true,
                'message' => 'feeds',
                'data' => $this->feedsRepository->showAllFeeds($request)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in feeds of dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function orderProductsInFeeds(OrderFeedsRequest $request)
    {
        try {
            $this->feedsRepository->orderProductsInFeeds($request);
            return response()->json([
                'status' => true,
                'message' => 'feeds ordered successfully.',
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in orderProductsInFeeds of dashboard' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function changeFeedsVideoStatus(FeedsVideosChangeStatusRequest $request)
    {
        $this->feedsVideosRepository->changeVideoStatus($request);
        return response()->json([
            'status' => true,
            'message' => 'Status Changed Successfully',
            'data' => []
        ], AResponseStatusCode::SUCCESS);
    }

    public function getAllVideos()
    {
        return response()->json([
            'status' => true,
            'message' => 'all videos',
            'data' => $this->feedsVideosRepository->listAllVideos()
        ], AResponseStatusCode::SUCCESS);
    }

}
