<?php


namespace App\Http\Controllers\Seller;


use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Requests\Feeds\FeedsVideosRequest;
use App\Lib\Log\ValidationError;
use App\Repositories\FeedsVideosRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedsVideosController
{

    /**
     * @var FeedsVideosRepository
     */
    private $feedsVideosRepository;

    public function __construct(FeedsVideosRepository $feedsVideosRepository)
    {
        $this->feedsVideosRepository = $feedsVideosRepository;
    }

    public function uploadFeedsVideos(FeedsVideosRequest $request)
    {
        $this->feedsVideosRepository->uploadFeedsVideos($request);
        return response()->json([
            'status' => true,
            'message' => trans('messages.feeds.upload_video'),
            'data' => []
        ], AResponseStatusCode::SUCCESS);
    }

    public function deleteFeedsVideos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:feeds_videos,id',
        ]);
        if ($validator->fails()) {
            return ValidationError::handle($validator);
        }
        $this->feedsVideosRepository->deleteVideo($request->id);
        return response()->json([
            'status' => true,
            'message' => trans('messages.feeds.delete_video'),
            'data' => []
        ], AResponseStatusCode::SUCCESS);
    }

    public function listAllVideos($id)
    {
        return response()->json([
            'status' => true,
            'message' => 'all videos',
            'data' => $this->feedsVideosRepository->listAllVideosByStoreId($id)
        ], AResponseStatusCode::SUCCESS);
    }

}
