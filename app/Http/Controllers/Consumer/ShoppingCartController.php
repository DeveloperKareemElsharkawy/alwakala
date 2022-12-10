<?php

namespace App\Http\Controllers\Consumer;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShoppingCart\AddProductToShoppingCart;
use App\Http\Requests\ShoppingCart\AddShoppingCartRequest;
use App\Http\Requests\ShoppingCart\CreateShopingCart;
use App\Http\Requests\ShoppingCart\DeleteProductFromShoppingCart;
use App\Http\Requests\ShoppingCart\EditProductInShoppingCart;
use App\Lib\Helpers\Address\AddressHelper;
use App\Lib\Helpers\Offers\OffersHelper;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\PackingUnit;
use App\Models\PackingUnitProduct;
use App\Models\Product;
use App\Models\ProductShoppingCart;
use App\Models\ProductStore;
use App\Models\ShoppingCart;
use App\Repositories\ShoppingCartRepository;
use App\Services\ShoppingCarts\ShoppingCartsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use function GuzzleHttp\Promise\all;

class ShoppingCartController extends BaseController
{

    public $shoppingCartsService;

    public function __construct(ShoppingCartsService $shoppingCartsService)
    {
        $this->shoppingCartsService = $shoppingCartsService;
    }

    public function addShoppingCart(AddShoppingCartRequest $request)
    {
        try {
            DB::beginTransaction();
            $salesCartIds = $this->shoppingCartsService->insertShoppingCartData($request);
            DB::commit();
            return response()->json([
                'data' => [
                    'shopping_cart_ids' => $salesCartIds
                ],
                'message' => ''
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('add Shopping error' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
