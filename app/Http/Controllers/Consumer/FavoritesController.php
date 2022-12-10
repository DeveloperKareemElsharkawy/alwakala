<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\StoreTypes\StoreType;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Favourites\AddProductToFavRequest;
use App\Http\Requests\Favourites\AddSellerToFavRequest;
use App\Http\Requests\Favourites\GetFavProducts;
use App\Lib\Helpers\UserId\UserId;
use App\Lib\Log\ServerError;
use App\Models\FavouriteProduct;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FavoritesController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFavoriteProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $userId = UserId::UserId($request);
            $query=ProductRepository::prepareProductQuery($request,$userId,null,null,null,true);
            $products = $query->paginate(10);
            foreach ($products as $product) {
                if ($product->discount != 0) {
                    $product->has_discount = true;
                    if ($product->discount_type == DiscountTypes::AMOUNT) {
                        $product->discount_type = 'amount';
                    } else {
                        $product->discount_type = 'percentage';
                        $product->discount = $product->discount . '%';
                    }
                } else {
                    $product->has_discount = false;
                }
                if (count($product->SellerRate) > 0) {
                    $product->rate = $product->SellerRate[0]->rate;
                } else {
                    $product->rate = 0;
                }
                unset($product->SellerRate);
                $product->image = null;
                if ($product->productImage) {
                    $product->image = config('filesystems.aws_base_url') . $product->productImage->image;
                }
                unset($product->productImage);
            }
            return response()->json([
                'status' => true,
                'message' => 'Favorite products',
                'data' => $products
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in getFavoriteProducts of seller  Favorites' . __LINE__ . $e);
            return $this->connectionError($e);

        }
    }

   }
