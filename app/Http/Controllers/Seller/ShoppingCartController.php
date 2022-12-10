<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Http\Controllers\BaseController;
use App\Http\Requests\ShoppingCart\AddCartDetailsRequest;
use App\Http\Requests\ShoppingCart\AddShoppingCartRequest;
use App\Lib\Helpers\Address\AddressHelper;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Offers\OffersHelper;
use App\Lib\Log\ServerError;
use App\Lib\Log\ValidationError;
use App\Models\PackingUnitProduct;
use App\Models\ProductImage;
use App\Models\ProductShoppingCart;
use App\Models\ProductStore;
use App\Models\ProductStoreStock;
use App\Models\ShoppingCart;
use App\Models\Store;
use App\Repositories\ShoppingCartRepository;
use App\Services\ShoppingCarts\ShoppingCartsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ShoppingCartController extends BaseController
{
    public $shoppingCartRepo;
    public $shoppingCartService;
    public $lang;

    public function __construct(ShoppingCartRepository $shoppingCartRepo, ShoppingCartsService $shoppingCartService,Request $request)
    {
        $this->lang = LangHelper::getDefaultLang($request);
        $this->shoppingCartRepo = $shoppingCartRepo;
        $this->shoppingCartService = $shoppingCartService;
    }

    public function getShoppingCart(Request $request)
    {
        try {
            $carts = ShoppingCart::query()
                ->select('id', 'cart_price', 'total_price', 'store_id')
                ->where('user_id', $request->user_id)
                ->get();

            $data = new \stdClass();
            $data->net_price = 0;
            $data->total_price = 0;
            $data->total_items = 0;
            $cartProducts = Store::query()
                ->select('id', 'name', 'logo')
                ->with(['shoppingCartProduct' => function ($q) use ($carts) {
                    $q->whereIn('shopping_cart_id', $carts->pluck('id')->toArray());
                }, 'shoppingCart'])
                ->whereIn('id', $carts->pluck('store_id')->toArray())
                ->get();
            foreach ($cartProducts as $cartProduct) {
                if ($cartProduct->logo) {
                    $cartProduct->logo = config('filesystems.aws_base_url') . $cartProduct->logo;
                }
                foreach ($cartProduct->shoppingCartProduct as $product) {
                    $product->color = $this->lang == 'ar' ? $product->color_ar : $product->color_en;
                    $image = ProductImage::query()->select('image')->where('product_id', $product->product_id)->first();
                    $product->image = $image ? config('filesystems.aws_base_url') . $image->image : null;
                    unset($product->color_ar);
                    unset($product->color_en);
                    unset($product->store_id);
                    $cartProduct->total_item_count += $product->purchased_item_count * $product->basic_unit_count;
                    $cartProduct->total_price += $product->total_price;
                }
                $data->total_items += $cartProduct->total_item_count;
                $cartProduct->net_price = intval($cartProduct->shoppingCart[0]->cart_price);
                $cartProduct->discount = intval($cartProduct->shoppingCart[0]->discount) .'%';
                $data->net_price += $cartProduct->net_price;
                $data->total_price += intval($cartProduct->shoppingCart[0]->total_price);
                unset($cartProduct->shoppingCart);
            }

            $data->srores = $cartProducts;
            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('error in getShoppingCart of seller ShoppingCart' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function editShoppingCartAttributes(AddCartDetailsRequest $request)
    {
        try {
            $currentShoppingCart = $this->shoppingCartRepo->getCurrentShoppingCarts($request->seller_id);
            $currentShoppingCart->payment_method_id = $request->payment_method_id;
            $currentShoppingCart->shipment_method_id = $request->shipment_method_id;
            $currentShoppingCart->seller_address_id = $request->seller_address_id;

            $currentShoppingCart->save();
            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $this->shoppingCartRepo->getCurrentShoppingCartForApp($request->seller_id)
            ]);
        } catch (\Exception $e) {
            Log::error('error in editShoppingCartAttributes of seller ShoppingCart' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function addShoppingCart(AddShoppingCartRequest $request)
    {
        try {
            DB::beginTransaction();
            $shoppingCartIds = $this->shoppingCartService->insertShoppingCartData($request);
            DB::commit();
            return response()->json([
                'status' => true,
                'data' => $shoppingCartIds,
                'message' => trans('messages.cart.shopping_cart_added')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error in addShoppingCart of seller ShoppingCart' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function checkCartAvailableQuantities(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'store_id' => 'required|numeric|exists:stores,id',
                'product_id' => 'required|numeric|exists:products,id',
                'quantity' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $productStoreId = ProductStore::query()
                ->select('id')
                ->where('product_id', $request->product_id)
                ->where('store_id', $request->store_id)
                ->first()->id;

            $availableStock = ProductStoreStock::query()
                ->select('available_stock')
                ->where('product_store_id', $productStoreId)
                ->first()->available_stock;
            $available = false;
            if ($availableStock >= $request->quantity) {
                $available = true;
            }
            $availableMessage = trans('messages.cart.available');
            $notAvailableMessage = trans('messages.cart.not_available');
            return response()->json([
                "status" => true,
                "message" => $available ? $availableMessage : $notAvailableMessage,
                "data" => $available
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in checkCartAvailableQuantities of seller ShoppingCart' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }

    public function validateProducts(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'store_id' => 'required|numeric|exists:stores,id',
                'product_id' => 'required|numeric|exists:products,id',
            ]);
            if ($validator->fails()) {
                return ValidationError::handle($validator);
            }

            $store = Store::query()
                ->select('id')
                ->where('user_id', $request->user_id)
                ->first();
            $product = ProductStore::query()
                ->where('product_id', $request->product_id)
                ->where('store_id', $request->store_id)
                ->first();
            if (!$product) {
                return response()->json([
                    "status" => false,
                    "message" => trans('messages.cart.product_not_found'),
                    "data" => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }

            if ($store->id == $product->store_id) {
                return response()->json([
                    "status" => false,
                    "message" => trans('messages.cart.invalid_cart'),
                    "data" => ''
                ], AResponseStatusCode::BAD_REQUEST);
            }
            return response()->json([
                "status" => true,
                "message" => trans('messages.cart.valid'),
                "data" => ''
            ], AResponseStatusCode::SUCCESS);
        } catch (\Exception $e) {
            Log::error('error in validateProducts of seller ShoppingCart' . __LINE__ . $e);
            return $this->connectionError($e);
        }
    }
}
