<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use App\Services\Stores\StoresService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use LaravelIdea\Helper\App\Models\_IH_Product_C;
use LaravelIdea\Helper\App\Models\_IH_Product_QB;

class ProductService
{
    private $productRepository, $storeRepository, $storesService;

    public function __construct(ProductRepository $productRepository, StoreRepository $storeRepository, StoresService $storesService)
    {
        $this->productRepository = $productRepository;
        $this->storeRepository = $storeRepository;
        $this->storesService = $storesService;
    }

//    public function getProductDetailsOld($productId, $storeId): array
//    {
//        $productDetails = $this->productRepository->getProductDetails($productId, $storeId);
//        $mappedData = $this->mapProductDetailsResponse($productDetails);
//        $formattedData = $this->formatMappedProductData($mappedData, $storeId, $productId);
//        return $formattedData;
//    }

    public function getProductDetails($productId, $storeId): Model|_IH_Product_QB|Builder|Product|null
    {
        return Product::query()->whereHas('productStore', function ($q) use ($productId, $storeId) {
            $q->where([['product_id', $productId], ['store_id', $storeId]]);
        })->with(['category', 'brand', 'material', 'shipping', 'images', 'productStore.store', 'productStore.productStoreStock'])->first();
    }

    public function suggestedProducts($category_id = null, $productsSmilerTo = null, $paginated = 0, $request): _IH_Product_C|Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array
    {
        $productsSmiler = Product::where('id', $productsSmilerTo)->first();

        $products = Product::query()
            ->when($category_id != null, function ($q) use ($category_id) {
                return $q->where('category_id', $category_id);
            })
            ->when($productsSmilerTo != null, function ($q) use ($productsSmiler) {
                $q->where('category_id', $productsSmiler->category_id)->orWhere('brand_id', $productsSmiler->brand_id);
            })->with([
                'category',
                'brand',
                'material',
                'shipping',
                'images',
                'productStore.store',
                'productStore.productStoreStock'
            ]);

        if (!$paginated > 0) {
            return $products->limit(3)->get();
        }
        return $products->paginate($paginated);
    }


    public function productsByStore($storeId, $request, $limit = 0, $filterData = []): _IH_Product_C|Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array
    {

        $products = Product::query()
            ->whereHas('productStore', function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })
            ->with([
                'category',
                'brand',
                'material',
                'shipping',
                'images',
                'productStore.store',
                'productStore.productStoreStock',
                'orderProducts',
            ]);

        $products = $this->filterProducts($request, $products, $filterData);

        if ($limit > 0) {
            return $products->limit($limit)->get();
        }

        return $products->paginate(10);
    }

    public function filterProducts($request, $PQuery, $filterData = [])
    {
        if ($request->filled('filter_by_category_ids')) {
            $PQuery->whereIn('category_id', $request->filter_by_category_ids);
        }

        if ($request->filled('filter_by_materials_ids')) {
            $PQuery->whereIn('material_id', $request->query('filter_by_materials_ids'));
        }

        if ($request->filled('filter_by_category_ids')) {
            $PQuery->whereIn('category_id', $request->filter_by_category_ids);
        }

        if ($request->filled('filter_by_brands_ids')) {
            $PQuery->whereIn('brand_id', $request->query('filter_by_brands_ids'));
        }

        if ($request->filled('filter_by_price_from')) {
            $PQuery->whereHas('productStore', function ($q) use ($request) {
                $q->where('consumer_price', '>=', $request->query('filter_by_price_from'));
            });
        }

        if ($request->filled('filter_by_price_to')) {
            $PQuery->whereHas('productStore', function ($q) use ($request) {
                $q->where('consumer_price', '<=', $request->query('filter_by_price_to'));
            });
        }

        if ($request->filled('sort_by_price')) {
            $PQuery->whereHas('productStore', function ($q) use ($request) {
                $q->orderBy('consumer_price', $request->query('sort_by_price'));
            });
        }

        if ($request->filled('is_new_arrivals') || array_key_exists('is_new_arrivals', $filterData)) {
            $PQuery->orderBy('products.created_at', 'desc');
        }

        if ($request->filled('search_word')) {
            $PQuery->where('name', 'ILIKE', '%' . $request->search_word . '%');
        }

        if ($request->filled('filter_by_shipping_method_id')) {
            $PQuery->where('shipping_method_id', $request->filter_by_shipping_method_id);
        }

        if ($request->filled('filter_by_policy_id')) {
            $PQuery->where('policy_id', $request->filter_by_policy_id);
        }


        if ($request->filled('filter_by_colors_ids')) {
            $PQuery->whereHas('productStore.productStoreStock', function ($q) use ($request) {
                $q->whereIn('color_id', $request->query('filter_by_colors_ids'));
            });
        }

        if ($request->filled('filter_by_sizes_ids')) {
            $PQuery->whereHas('productStore.productStoreStock', function ($q) use ($request) {
                $q->whereIn('size_id', $request->query('filter_by_sizes_ids'));
            });
        }


        if ($request->filled('has_discount') || array_key_exists('has_discount', $filterData)) {
            $PQuery->whereHas('productStore', function ($q) use ($request) {
                $q->where('discount', '!=', 0);
            });
        }

        if ($request->filled('sort_by_most_selling') || array_key_exists('sort_by_most_selling', $filterData)) {
            $PQuery->withCount('orderProducts')
                ->orderBy('order_products_count', 'desc');
        }

        if ($request->filled('sort_by_rate')) {
            $PQuery->withCount(['SellerRate as average_rating' => function($query) {
                $query->select(DB::raw('coalesce(avg(rate),0)'));
            }])->orderByDesc('average_rating');
        }

//        Filter By Location
        $latitude = $request->header('Latitude');
        $longitude = $request->header('Longitude');

        if ($request->header('latitude') && $request->header('longitude')) {
            // This will calculate the distance in km
            // if you want in miles use 3959 instead of 6371

            $PQuery->selectRaw(DB::raw('6371 * acos(cos(radians(' . $latitude . ')) * cos(radians(stores.latitude)) * cos(radians(stores.longitude) - radians(' . $longitude . ')) + sin(radians(' . $latitude . ')) * sin(radians(stores.latitude))) as distance'))
                ->orderByRaw('distance asc');
        }

        return $PQuery;
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
