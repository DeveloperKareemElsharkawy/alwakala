<?php


namespace App\Repositories;


use App\Enums\Orders\AOrders;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Services\ImageUploader\UploadImage;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Review;
use Illuminate\Support\Facades\DB;


class ReviewsRepository
{

    public function saveRate($request)
    {
        $userId = UserId::UserId($request);
        $image = null;
        if ($request->image) {
            $image = UploadImage::uploadImageToStorage($request->image, 'products/' . $request->product_id);
        }
        Review::query()->updateOrCreate(
            ['user_id' => $userId, 'product_id' => $request->product_id, 'store_id' => $request->store_id],
            ['review' => $request->review, 'image' => $image, 'status' => 0]);
    }

    public function deleteRate($id)
    {
        Review::query()->where('id', $id)->delete();
    }

    public function changeShareReviewToFeedsStatus($request)
    {
        $review = Review::query()->where('id', $request->id)->first();
        $review->share_to_feeds = $request->status;
        $review->save();
    }


    public function checkIfUserCanReviewProduct($request): bool
    {
        $products = OrderProduct::query()->where(['product_id' => $request->product_id, 'status_id' => AOrders::RECEIVED])->get();
        foreach ($products as $product) {
            $order = Order::query()->where(['id'=> $product->order_id, 'user_id' => UserId::UserId($request), 'status_id'=> AOrders::RECEIVED])->first();
            if ($order) {
                return true;
            }
        }

        return false;
    }

    public function listAllReviews($product_id = null, $store_id = null)
    {
        // dd(request()->user_id);
        $query = Review::query()->select(
            'reviews.id',
            'products.id as product_id',
            'products.name as product_name',
            'reviews.review',
            'users.name as user_name',
            'users.id as user_id',
            'reviews.image',
            'stores.name as store_name',
            'stores.logo',
            'stores.store_type_id',
            'reviews.created_at as created_at'
        )
            ->leftJoin('stores', 'stores.id', '=', 'reviews.store_id')
            ->leftJoin('products', 'products.id', '=', 'reviews.product_id')
            ->leftJoin('users', 'users.id', '=', 'reviews.user_id')
            ->groupBy(
                'reviews.id',
                'products.id',
                'products.name',
                'users.name',
                'users.id',
                'reviews.review',
                'reviews.image',
                'stores.name',
                'stores.store_type_id',
                'stores.logo',
                'stores.id')
            ->orderBy('reviews.created_at', 'desc');
        if ($product_id) {
            $query->where(['product_id' => $product_id, 'store_id' => $store_id]);
        }
        $reviews = $query->paginate(10);
        foreach ($reviews as $review) {
            $review->image = ($review->image) ? config('filesystems.aws_base_url') . $review->image : null;
            $review->logo = ($review->logo) ? config('filesystems.aws_base_url') . $review->logo : null;
            $review->is_owner = $review->user_id == request()->user_id ? true : false;
        }

        return $reviews;
    }

}
