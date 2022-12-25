<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use App\Services\Stores\StoresService;
use Carbon\Carbon;

class ProductService
{
    private $productRepository, $storeRepository, $storesService;

    public function __construct(ProductRepository $productRepository, StoreRepository $storeRepository, StoresService $storesService)
    {
        $this->productRepository = $productRepository;
        $this->storeRepository = $storeRepository;
        $this->storesService = $storesService;
    }

    public function getProductDetails($productId, $storeId)
    {
        $productDetails = $this->productRepository->getProductDetails($productId, $storeId);
        $mappedData = $this->mapProductDetailsResponse($productDetails);
        $formattedData = $this->formatMappedProductData($mappedData, $storeId, $productId);
        return $formattedData;
    }

    public function getProductDetailsv2($productId, $storeId)
    {
        return Product::query()->whereHas('productStore', function ($q) use ($productId, $storeId) {
            $q->where('product_id', $productId);
            $q->where('store_id', $storeId);
        })->with(['category', 'brand', 'material', 'shipping', 'images'])->first();
    }


    private function mapProductDetailsResponse($productDetails)
    {
        $formattedData = [];
        foreach ($productDetails as $productDetail) {
            $formattedData['product_id'] = $productDetail->product_id;
            $formattedData['product_name'] = $productDetail->product_name;
            $formattedData['product_description'] = $productDetail->description;
            $formattedData['category'] = $productDetail->category_name;
            $formattedData['category_id'] = $productDetail->category_id;
            $formattedData['price'] = $productDetail->price;
            $formattedData['net_price'] = $productDetail->net_price;
            $formattedData['store_name'] = $productDetail->store_name;
            $formattedData['has_discount'] = $productDetail->has_discount;
            $formattedData['images'][$productDetail->product_image_id] = config('filesystems.aws_base_url') . $productDetail->product_image;
            if (isset($formattedData['colors'][$productDetail->color_id]) && $formattedData['colors'][$productDetail->color_id]['available'] == false) {
                $formattedData['colors'][$productDetail->color_id]['available'] = $productDetail->available_stock > 0 ? true : false;
            } else {
                $formattedData['colors'][$productDetail->color_id]['available'] = $productDetail->available_stock > 0 ? true : false;
                $formattedData['colors'][$productDetail->color_id]['color_id'] = $productDetail->color_id;
                $formattedData['colors'][$productDetail->color_id]['color_name'] = $productDetail->color_name;
                $formattedData['colors'][$productDetail->color_id]['color_code'] = $productDetail->color_code;
            }
            $formattedData['colors'][$productDetail->color_id]['sizes'][$productDetail->product_store_stock_id]['size_id'] = $productDetail->size_id;
            $formattedData['colors'][$productDetail->color_id]['sizes'][$productDetail->product_store_stock_id]['size'] = $productDetail->size_name;
            $formattedData['colors'][$productDetail->color_id]['sizes'][$productDetail->product_store_stock_id]['stock'] = $productDetail->available_stock;
            if ($productDetail->color_id == $productDetail->image_color_id) {
                $formattedData['colors'][$productDetail->color_id]['images'][$productDetail->product_image_id]['image'] = config('filesystems.aws_base_url') . $productDetail->product_image;
                $formattedData['colors'][$productDetail->color_id]['images'][$productDetail->product_image_id]['is_primary'] = $productDetail->is_primary;
            }
        }
        return $formattedData;
    }

    private function formatMappedProductData($mappedData, $storeId, $productId)
    {
        $formattedData = [];
        $formattedData['product_id'] = $mappedData['product_id'];
        $formattedData['product_name'] = $mappedData['product_name'];
        $formattedData['product_description'] = $mappedData['product_description'];
        $formattedData['category'] = $mappedData['category'];
        $formattedData['category_id'] = $mappedData['category_id'];
        $formattedData['price'] = $mappedData['price'];
        $formattedData['net_price'] = $mappedData['net_price'];
        $formattedData['has_discount'] = $mappedData['has_discount'];
        $formattedData['rating'] = $this->getProductRating($productId);
        $formattedData['store']['name'] = $mappedData['store_name'];
        $formattedData['store']['followers_count'] = $this->storeRepository->getStoreFollowersCount($storeId);
        $formattedData['store']['rating'] = $this->storesService->getStoreRating($storeId);
        foreach ($mappedData['images'] as $image) {
            $formattedData['images'][] = $image;
        }
        foreach ($mappedData['colors'] as $color) {
            $toBeInsertedColor = $color;
            unset($toBeInsertedColor['sizes']);
            unset($toBeInsertedColor['images']);
            foreach ($color['sizes'] as $size) {
                $toBeInsertedColor['sizes'][] = $size;
            }
            foreach ($color['images'] as $image) {
                $toBeInsertedColor['images'][] = $image;
            }
            $formattedData['colors'][] = $toBeInsertedColor;
        }
        $formattedData['reviews'] = $this->getProductReviews($productId, 3);
        return $formattedData;
    }

    public function getProductReviews($productId, $limit = 0)
    {
        $reviews = $this->productRepository->getProductReviews($productId, $limit);
        if (count($reviews)) {
            foreach ($reviews as $formattedReview) {
                $formattedReview->image = $formattedReview->image ? config('filesystems.aws_base_url') . $formattedReview->image : null;
                $formattedReview->user_image = $formattedReview->user_image ? config('filesystems.aws_base_url') . $formattedReview->user_image : null;
            }
        }
        return $reviews;
    }

    public function getPaginatedProductReviews($productId)
    {
        $reviews = $this->productRepository->getProductReviews($productId);
        if (count($reviews)) {
            foreach ($reviews->items() as $formattedReview) {
                $formattedReview->image = $formattedReview->image ? config('filesystems.aws_base_url') . $formattedReview->image : null;
                $formattedReview->user_image = $formattedReview->user_image ? config('filesystems.aws_base_url') . $formattedReview->user_image : null;
            }
        }
        return $reviews;
    }

    public function getProductRating($productId)
    {
        $ratingData = $this->productRepository->getProductRatings($productId)[0];
        if ($ratingData['rating'] == null) {
            return 5;
        }
        return round($ratingData['rating'] / $ratingData['reviews_count']);
    }
}
