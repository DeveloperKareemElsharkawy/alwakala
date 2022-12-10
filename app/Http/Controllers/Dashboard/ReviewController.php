<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Reviews\ChangeShareReviewToFeedsStatus;
use App\Http\Requests\Reviews\ReviewProductRequest;
use App\Repositories\ReviewsRepository;
use Illuminate\Support\Facades\Log;


class ReviewController extends BaseController
{
    /**
     * @var ReviewsRepository
     */
    private $reviewsRepository;

    public function __construct(ReviewsRepository $reviewsRepository)
    {
        $this->reviewsRepository = $reviewsRepository;

    }

    public function addReview(ReviewProductRequest $request)
    {
        try {
            $this->reviewsRepository->saveRate($request);
            return response()->json([
                'status' => true,
                'message' => trans('messages.reviews.add'),
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in addReview of dashboard reviews' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function listAllReviews()
    {
        try {

            return response()->json([
                'status' => true,
                'message' => 'All Reviews',
                'data' => $this->reviewsRepository->listAllReviews()
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in listAllReviews of dashboard reviews' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }


    public function deleteReview($id)
    {
        try {
            $this->reviewsRepository->deleteRate($id);
            return response()->json([
                'status' => true,
                'message' => trans('messages.reviews.delete'),
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in deleteReview of dashboard reviews' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function changeShareReviewToFeedsStatus(ChangeShareReviewToFeedsStatus $request)
    {
        try {
            $this->reviewsRepository->changeShareReviewToFeedsStatus($request);
            return response()->json([
                'status' => true,
                'message' => trans('messages.reviews.status_changed'),
                'data' => []
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in change status of reviews of dashboard reviews' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
    public function listReviewsByProductId($id,$store_id)
    {
        try {

            return response()->json([
                'status' => true,
                'message' => "All Reviews",
                'data' =>  $this->reviewsRepository->listAllReviews($id,$store_id)
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in listReviewsByProductId of dashboard reviews' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
