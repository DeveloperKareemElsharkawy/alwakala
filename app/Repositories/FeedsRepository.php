<?php


namespace App\Repositories;


use App\Lib\Helpers\UserId\UserId;
use App\Models\FeedsOrder;
use App\Models\FeedsVideos;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class FeedsRepository
{

    public function showAllFeeds($request)
    {
        $query = Product::query()->select(
            'products.id',
            'products.name as product_name',
            'products.description',
            'products.consumer_price',
            'product_store.price',
            'product_store.net_price',
            'product_store.discount',
            'followed_stores.store_id',
            'stores.name as store_name',
            'seller_favorites.favoriter_id as is_store_favorited',
            'stores.logo',
            'stores.store_type_id',
            'followed_stores.store_id as is_store_followed',
            'stores.id as store_id',
            'feeds_order.order as order',
            'products.created_at as created_at',
            DB::raw('COUNT(views.id) as views'),
            DB::raw('AVG(seller_rates.rate) as rates')
        )->with('images')
            ->join('product_store', 'product_store.product_id', '=', 'products.id')
            ->leftJoin('stores', 'stores.id', '=', 'product_store.store_id')
            ->leftJoin('feeds_order', 'feeds_order.product_id', '=', 'products.id')
            ->leftJoin('followed_stores', function ($join) use ($request) {
                $user_id = UserId::UserId($request);
                if (!$user_id) {
                    $user_id = -1;
                }
                $join->on('followed_stores.store_id', '=', 'stores.id');
                $join->where('followed_stores.user_id', '=', $user_id);
            })
            ->leftJoin('views', function ($join) {
                $join->on('views.item_id', '=', 'products.id');
                $join->where('views.item_type', '=', 'PRODUCT');
            })
            ->leftJoin('seller_rates', function ($join) {
                $join->on('seller_rates.rated_id', '=', 'stores.id');
                $join->where('seller_rates.rated_type', '=', 'App\Models\Store');
            })
            ->leftJoin('seller_favorites', function ($join) use ($request) {
                $user_id = UserId::UserId($request);
                if (!$user_id) {
                    $user_id = -1;
                }
                $join->on('seller_favorites.favorited_id', '=', 'stores.id');
                $join->where('seller_favorites.favorited_type', '=', 'App\Models\Store');
                $join->where('seller_favorites.favoriter_id', '=', $user_id);
            })
            ->groupBy('products.id',
                'products.name',
                'feeds_order.order',
                'products.description',
                'products.consumer_price',
                'product_store.price',
                'product_store.net_price',
                'product_store.discount',
                'followed_stores.store_id',
                'stores.name',
                'stores.store_type_id',
                'stores.logo',
                'seller_favorites.favoriter_id',
                'followed_stores.store_id',
                'stores.id')
            ->orderBy('feeds_order.order', 'asc')
            ->orderBy('products.created_at', 'desc');
        $products = $query->paginate(10);
        $query2 = FeedsVideos::query()->select(
            'feeds_videos.id',
            'feeds_videos.video',
            'followed_stores.store_id',
            'stores.name as store_name',
            'seller_favorites.favoriter_id as is_store_favorited',
            'stores.logo',
            'stores.store_type_id',
            'feeds_videos.created_at as created_at',
            'followed_stores.store_id as is_store_followed',
            'stores.id as store_id',
            DB::raw('AVG(seller_rates.rate) as rates')
        )
            ->leftJoin('stores', 'stores.id', '=', 'feeds_videos.store_id')
            ->leftJoin('followed_stores', function ($join) use ($request) {
                $user_id = UserId::UserId($request);
                if (!$user_id) {
                    $user_id = -1;
                }
                $join->on('followed_stores.store_id', '=', 'stores.id');
                $join->where('followed_stores.user_id', '=', $user_id);
            })
            ->leftJoin('seller_rates', function ($join) {
                $join->on('seller_rates.rated_id', '=', 'stores.id');
                $join->where('seller_rates.rated_type', '=', 'App\Models\Store');
            })
            ->leftJoin('seller_favorites', function ($join) use ($request) {
                $user_id = UserId::UserId($request);
                if (!$user_id) {
                    $user_id = -1;
                }
                $join->on('seller_favorites.favorited_id', '=', 'stores.id');
                $join->where('seller_favorites.favorited_type', '=', 'App\Models\Store');
                $join->where('seller_favorites.favoriter_id', '=', $user_id);
            })
            ->groupBy(
                'feeds_videos.id',
                'feeds_videos.video',
                'followed_stores.store_id',
                'stores.name',
                'stores.store_type_id',
                'stores.logo',
                'seller_favorites.favoriter_id',
                'followed_stores.store_id',
                'stores.id')
            ->orderBy('feeds_videos.created_at', 'desc');
        $videos = $query2->paginate(10);
        $query3 = Review::query()->select(
            'reviews.id',
            'products.id as product_id',
            'products.name as product_name',
            'reviews.review',
            'users.name as user_name',
            'reviews.image',
            'followed_stores.store_id',
            'stores.name as store_name',
            'seller_favorites.favoriter_id as is_store_favorited',
            'stores.logo',
            'stores.store_type_id',
            'reviews.created_at as created_at',
            'followed_stores.store_id as is_store_followed',
            'stores.id as store_id',
            DB::raw('AVG(seller_rates.rate) as rates')
        )
            ->leftJoin('stores', 'stores.id', '=', 'reviews.store_id')
            ->leftJoin('products', 'products.id', '=', 'reviews.product_id')
            ->leftJoin('users', 'users.id', '=', 'reviews.user_id')
            ->leftJoin('followed_stores', function ($join) use ($request) {
                $user_id = UserId::UserId($request);
                if (!$user_id) {
                    $user_id = -1;
                }
                $join->on('followed_stores.store_id', '=', 'stores.id');
                $join->where('followed_stores.user_id', '=', $user_id);
            })
            ->leftJoin('seller_rates', function ($join) {
                $join->on('seller_rates.rated_id', '=', 'stores.id');
                $join->where('seller_rates.rated_type', '=', 'App\Models\Store');
            })
            ->leftJoin('seller_favorites', function ($join) use ($request) {
                $user_id = UserId::UserId($request);
                if (!$user_id) {
                    $user_id = -1;
                }
                $join->on('seller_favorites.favorited_id', '=', 'stores.id');
                $join->where('seller_favorites.favorited_type', '=', 'App\Models\Store');
                $join->where('seller_favorites.favoriter_id', '=', $user_id);
            })
            ->where('reviews.share_to_feeds', '=', 2)
            ->groupBy(
                'reviews.id',
                'products.id',
                'products.name',
                'reviews.review',
                'reviews.image',
                'users.name',
                'followed_stores.store_id',
                'stores.name',
                'stores.store_type_id',
                'stores.logo',
                'seller_favorites.favoriter_id',
                'followed_stores.store_id',
                'stores.id')
            ->orderBy('reviews.created_at', 'desc');

        $reviews = $query3->paginate(10);

        foreach ($products as $product) {
            $images = [];
            foreach ($product->images as $image) {
                $images[] = ($image) ? config('filesystems.aws_base_url') . $image->image : null;
            }
            unset($product->images);
            $product->images = $images;
            $product->is_store_followed = ($product->is_store_followed) ? 1 : 0;
            $product->is_store_favorited = ($product->is_store_favorited) ? 1 : 0;
            if (!$product->order) {
                $product->order = 99999999999;
            }
            $product->logo = ($product->logo) ? config('filesystems.aws_base_url') . $product->logo : null;
            $product->feed_type = 'product';
        }
        foreach ($videos as $video) {
            $video->video = config('filesystems.aws_base_url') . $video->video;
            $video->order = null;
            $video->is_store_followed = ($video->is_store_followed) ? 1 : 0;
            $video->is_store_favorited = ($video->is_store_favorited) ? 1 : 0;
            $video->logo = ($video->logo) ? config('filesystems.aws_base_url') . $video->logo : null;
            $video->feed_type = 'video';
            if (!$video->order) {
                $video->order = 99999999999;
            }
        }
        foreach ($reviews as $review) {
            $review->image = ($review->image) ? config('filesystems.aws_base_url') . $review->image : null;
            $review->order = null;
            $review->is_store_followed = ($review->is_store_followed) ? 1 : 0;
            $review->is_store_favorited = ($review->is_store_favorited) ? 1 : 0;
            $review->logo = ($review->logo) ? config('filesystems.aws_base_url') . $review->logo : null;
            $review->feed_type = 'review';
            if (!$review->order) {
                $review->order = 99999999999;
            }
        }
        $result = $products->merge($videos);
//        return $result->sortBy([
//        'order'=>'asc',
//        'created'=>'asc',
//        ]);
        $result = $result->merge($reviews);
        $orderedByDate = $result
            ->sortByDesc('created_at')->values();
        return $orderedByDate->sortBy('order')->values()->all();
    }

    public function orderProductsInFeeds($request)
    {
        $order = FeedsOrder::query()->where('product_id', $request->product_id)->first();
        if (!$order) {
            $order = new FeedsOrder();
        }
        $order->order = $request->order;
        $order->product_id = $request->product_id;
        $order->save();
    }
}
