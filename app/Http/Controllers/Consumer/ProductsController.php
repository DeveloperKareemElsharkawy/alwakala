<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Events\Product\FavoriteProduct;
use App\Http\Controllers\BaseController;
use App\Http\Requests\ConsumerApp\Product\GetProductByBarcodeRequest;
use App\Http\Requests\ConsumerApp\Product\ListProductReviews;
use App\Http\Requests\ConsumerApp\Product\ShowProductRequest;
use App\Http\Resources\Consumer\Product\ProductDetailsResource;
use App\Http\Resources\Consumer\Product\ProductResource;
use App\Lib\Log\ValidationError;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductStore;
use App\Models\SellerFavorite;
use App\Models\Store;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Repositories\StoreRepository;
use App\Services\Product\ProductService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class ProductsController extends BaseController
{
    public $storeRepository, $productService, $productRepository;

    public function __construct(StoreRepository $storeRepository, ProductService $productService, ProductRepository $productRepository)
    {
        $this->storeRepository = $storeRepository;
        $this->productService = $productService;
        $this->productRepository = $productRepository;
    }

    public function toggleFavoriteProduct(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'product_id' => 'required|numeric|exists:products,id',
                'store_id' => 'required|numeric|exists:stores,id'
            ]);

            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }
            if (!$this->storeRepository->ifAllowTofollow($request)) {
                return response()->json([
                    'status' => true,
                    'message' => trans('messages.stores.follow_denied'),
                    'data' => []
                ]);
            }

            $findFavoritedProduct = SellerFavorite::query()
                ->where('favoriter_type', '=', User::class)
                ->where('favoriter_id', '=', $request->user_id)
                ->where('favorited_type', '=', Product::class)
                ->where('favorited_id', '=', $request->product_id)
                ->where('store_id', '=', $request->store_id)
                ->first();


            if (is_null($findFavoritedProduct)) {

                $favorite = new SellerFavorite();
                $favorite->favoriter_type = User::class;
                $favorite->favoriter_id = $request->user_id;
                $favorite->favorited_type = Product::class;
                $favorite->favorited_id = $request->product_id;
                $favorite->store_id = $request->store_id;
                $favorite->save();
                $store = Store::query()->where('id', $request->store_id)->first();
                $product_image = ProductImage::query()->where('product_id', $request->product_id)->first();
                event(new FavoriteProduct([$store->user_id], $request->product_id, $product_image->image));

                return response()->json([
                    'status' => true,
                    'message' => trans('messages.product.favorite'),
                    'data' => '',
                ], AResponseStatusCode::SUCCESS);

            } else {

                $findFavoritedProduct->delete();
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.product.unfavourite'),
                    'data' => '',
                ], AResponseStatusCode::SUCCESS);

            }

        } catch (\Exception $e) {
            Log::error('error in toggleFavoriteProduct of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function show(ShowProductRequest $request)
    {
        try {
            $productId = $request->product_id;
            $storeId = $request->store_id;
            $productStore = $this->productRepository->getProductStore($productId, $storeId);

            if (!$productStore) {
                return response()->json([
                    "status" => AResponseStatusCode::FORBIDDEN,
                    "message" => "Store doesn't have this product",
                    "data" => []
                ], AResponseStatusCode::FORBIDDEN);
            }

            $productDetails = $this->productService->getProductDetails($productId, $storeId);

            if (!$productDetails){
                return $this->error(['message' => "Product is not active"]);
            }

            $suggestedProducts = $this->productService->suggestedProducts($productDetails->category_id, null, 0, $request);
            $justForYou = $this->productService->suggestedProducts($productDetails->category_id, $request->product_id, 0, $request);

            return response()->json([
                'success' => true,
                'message' => "",
                'data' => [
                    'product' => new ProductDetailsResource($productDetails),
                    'suggested_products' => ProductResource::collection($suggestedProducts),
                    'jus_for_you' => ProductResource::collection($justForYou),

                ]
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in showProductFromConsumerSide of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getProductByBarcode(GetProductByBarcodeRequest $request)
    {
        try {

            $productStore = ProductStore::where('barcode_text', $request->barcode)->first();

            $productId = $productStore->product_id;
            $storeId = $productStore->store_id;

            $productStore = $this->productRepository->getProductStore($productId, $storeId);

            if (!$productStore) {
                return $this->error(['message' => "Store doesn't have this product"]);
            }

            $productDetails = $this->productService->getProductDetails($productId, $storeId);

            if (!$productDetails){
                return $this->error(['message' => "Product is not active"]);
            }

            $suggestedProducts = $this->productService->suggestedProducts($productDetails->category_id, null, 0, $request);
            $justForYou = $this->productService->suggestedProducts($productDetails->category_id, $request->product_id, 0, $request);

            return response()->json([
                'success' => true,
                'message' => "",
                'data' => [
                    'product' => new ProductDetailsResource($productDetails),
                    'suggested_products' => ProductResource::collection($suggestedProducts),
                    'jus_for_you' => ProductResource::collection($justForYou),

                ]
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in showProductFromConsumerSide of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function smilerProducts(ShowProductRequest $request)
    {
        try {
            $productId = $request->product_id;
            $storeId = $request->store_id;
            $productStore = $this->productRepository->getProductStore($productId, $storeId);

            if (!$productStore) {
                return response()->json([
                    "status" => AResponseStatusCode::FORBIDDEN,
                    "message" => "Store doesn't have this product",
                    "data" => []
                ], AResponseStatusCode::FORBIDDEN);
            }

            $productDetails = $this->productService->getProductDetails($productId, $storeId);
            $suggestedProducts = $this->productService->suggestedProducts($productDetails->category_id);
            $justForYou = $this->productService->suggestedProducts(null, $request->product_id, 10);

            return $this->respondWithPagination(ProductResource::collection($justForYou));

        } catch (\Exception $e) {
            Log::error('error in showProductFromConsumerSide of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function suggestedProducts(Request $request)
    {
        try {
            $productId = $request->product_id;
            $storeId = $request->store_id;
            $productStore = $this->productRepository->getProductStore($productId, $storeId);

            $productDetails = $this->productService->getProductDetails($productId, $storeId);
            $justForYou = $this->productService->suggestedProducts(null, $request->product_id, 10, $request);

            return $this->respondWithPagination(ProductResource::collection($justForYou));

        } catch (\Exception $e) {
            Log::error('error in showProductFromConsumerSide of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function getProductReviews(ListProductReviews $request)
    {
        try {
            $productDetails = $this->productService->getPaginatedProductReviews($request->product_id);
            return response()->json([
                'success' => true,
                'message' => "",
                'data' => $productDetails,
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in showProductFromConsumerSide of seller products' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

}
