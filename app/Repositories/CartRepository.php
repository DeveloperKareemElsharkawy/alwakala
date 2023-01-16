<?php

namespace App\Repositories;

use App\Enums\Apps\AApps;
use App\Enums\StoreTypes\StoreType;
use App\Lib\Helpers\Coupon\CouponService;
use App\Lib\Helpers\Lang\LangHelper;
use App\Lib\Helpers\Product\ProductHelper;
use App\Lib\Helpers\UserId\UserId;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\PackingUnitProduct;
use App\Models\PackingUnitProductAttribute;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOrderUnitDetails;
use App\Models\ProductStore;
use App\Models\Size;
use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\ProductRepository;

class CartRepository
{
    protected $model;

    protected $lang;

    public $productsRepo;

    public function __construct(CartItem $model, ProductRepository $productRepository, Request $request)
    {
        $this->model = $model;
        $this->lang = LangHelper::getDefaultLang($request);
        $this->productsRepo = $productRepository;

    }

    public function recommendedProducts($request)
    {
        $request->merge(['store_id' => null, 'product_id' => null, 'color_id' => null]);
        $arrayOfParameters['pagination'] = 0;
        $arrayOfParameters['limit'] = 10;
        $arrayOfParameters['isStoreProfile'] = false;
        $arrayOfParameters['request'] = $request;
        $arrayOfParameters['userId'] = $request['user_id'];
        $arrayOfParameters['app'] = AApps::SELLER_APP;
        $arrayOfParameters['storeId'] = null;
        return $this->productsRepo->getProducts($arrayOfParameters);
    }

    public function getCartsByUserId($request): array
    {
        // TODO: Apply Coupon And Offers && Calculate Discounts

        $groupedCarts = $this->model->query()->with('store:id,name,logo')
            ->join('product_store', 'cart_items.product_store_id', '=', 'product_store.id')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->join('packing_unit_product', 'packing_unit_product.product_id', '=', 'products.id')
            ->join('colors', 'cart_items.color_id', '=', 'colors.id')
            ->select(
                'cart_items.*',
                'packing_unit_product.basic_unit_count as basic_unit_count',
                'products.id as product_id',
                'products.name as product_name',
                'product_store.net_price',
                'colors.name_' . $this->lang . ' as color_name',
                'colors.hex as color_code',
                'colors.id as color_id',
                'product_store.store_id as store_id'
            )
            ->where('cart_items.user_id', $request['user_id'])
            ->orderBy('cart_items.id', 'desc')
            ->get()
            ->groupBy('store_id');    //grouping Carts Response by store_id

        $cartsList = [];

        $cartsList['cart'] = Cart::query()->where('user_id', $request['user_id'])->first();   //get cart details

        $cartsList['stores'] = []; // Handle Empty Cart Bug (When User has no cart)

        $storeLoop = 0;
        $cartTotal = 0.00;
        $cartCount = 0;
        $itemCount = 0;

        foreach ($groupedCarts as $storeId => $carts) {

            $cartsList['stores'][$storeLoop] = $carts[0]['store'];

            foreach ($carts as $cart) {
                $unit_details_arr = [];
                $unit_details = PackingUnitProduct::query()->where('product_id', $cart->product_id)->first();
                foreach ($unit_details->attributes as $unit) {
                    $unit_details_arr[] = [
                        'size' => $unit->size->size,
                        'quantity' => $unit->quantity
                    ];
                }
                $cart['unit_details'] = $unit_details_arr;
                $productImage = ProductImage::query()
                    ->where([['product_id', $cart->product_id], ['color_id', $cart->color_id]])
                    ->first();

                $cart->image = $productImage ? $productImage->image : null;

                $cart->sub_total = (float)$cart['net_price'] * $cart['quantity'] * $cart->basic_unit_count;
                $cart->item_count = $cart['quantity'] * $cart->basic_unit_count;

                $cartTotal += $cart['sub_total'];

                $cartCount += $cart['quantity'];
                $itemCount += $cart['quantity'] * $cart->basic_unit_count;

                unset($cart->store);    // handle Recursion detected bug
            }

            $cartsList['stores'][$storeLoop]['total_quantity'] = $carts->map(function ($cart) {
                return $cart->quantity;
            })->sum();

            $cartsList['stores'][$storeLoop]['total_item_quantity'] = $carts->map(function ($cart) {
                return $cart->quantity * $cart->basic_unit_count;
            })->sum();

            $cartsList['stores'][$storeLoop]['total_Price'] = $carts->map(function ($cart) {
                return $cart->sub_total;
            })->sum();

            $cartsList['stores'][$storeLoop]['items'] = $carts;

            $storeLoop++;
        }

        $cartsList['cart_count'] = $cartCount;
        $cartsList['item_count'] = $itemCount;
        $cartsList['cart_total'] = $cartTotal;

        return $cartsList;
    }

    public function getCartByUserId($request)
    {
        return Cart::query()->where('user_id', $request['user_id'])->first();
    }

    public function getCount($request)
    {
        // TODO: Apply Coupon And Offers && Calculate Discounts

        $count = CartItem::where('cart_items.user_id', $request['user_id'])
            ->sum('quantity');

        return $count;
    }

    public function addCartItem($request): array
    {
        DB::beginTransaction();
        $cart = Cart::query()->firstOrCreate(['user_id' => $request['user_id']]);
        $store = Store::query()->find($request['store_id']);
        $quantity = $request['quantity'];
        $basicUnitCount = ProductHelper::getProductBasicUnitCount($request['product_id']);

        if ($store->store_type_id == StoreType::SUPPLIER) {
            $quantity = $quantity * $basicUnitCount; // multiply Quantity by Basic Unit Count if
        }

        $cartItem = $this->model->query()->where([
            ['cart_id', $cart['id']],
            ['user_id', $request['user_id']],
            ['product_id', $request['product_id']],
            ['color_id', $request['color_id']],
            ['store_id', $request['store_id']],
        ])->first();
        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            $productStore = ProductStore::query()->where([
                ['store_id', request()['store_id']], ['product_id', request()['product_id']]
            ])->first();
            $request->merge(['product_store_id' => $productStore->id]);
            $request->merge(['packing_unit_id' => $request['packing_unit_id']]);
            $request->merge(['cart_id' => $cart['id']]);
            $this->model->create($request->all());
        }
        // $unit = PackingUnitProduct::query()->where('packing_unit_id', $request->packing_unit_id)->first();
        // if ($unit) {
        //     $unit_attrs = PackingUnitProductAttribute::query()->where('packing_unit_product_id', $unit->id)->get();
        //     ProductOrderUnitDetails::query()->where('packing_unit_id', $request->packing_unit_id)->delete();
        //     foreach ($unit_attrs as $unit_attr) {
        //         $row = new ProductOrderUnitDetails();
        //         $row->packing_unit_id = $request->packing_unit_id;
        //         $row->size_id = $unit_attr->size_id;
        //         $row->quantity = $unit_attr->quantity;
        //         $row->save();
        //     }
        // }
        DB::commit();
        return $this->getCartsByUserId(['user_id' => $request['user_id']]);
    }

    public function applyCoupon($request, $coupon, $user_Cart)
    {
        DB::beginTransaction();
        $cart = Cart::where('user_id', $request['user_id'])->first();

        if ($coupon->brand_id == 0) {
            foreach ($cart->items() as $item) {
                if (!$coupon->coupon_products()->contains('product_id', $item->product_id)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Coupon isn\'t applicable for item ' . $item->product()->name_en,
                        'data' => '',
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
        } else {
            $products = Product::where('brand_id', $coupon->brand_id)->get();

            foreach ($cart->items() as $item) {
                if (!$products->contains('id', $item->product_id)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Coupon isn\'t applicable for item ' . $item->product()->name_en,
                        'data' => '',
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
        }

        if (!$coupon->percentage) {
            $request['discount'] = $coupon->amount;
        } else {
            $request['discount'] = ($coupon->percentage * $user_Cart['cart_total']) / 100;
        }

        $request['coupon_id'] = $coupon->id;
        $request['coupon_name'] = $coupon->name;
        $request['coupon_code'] = $coupon->code;

        if ($coupon->unlimited != 1) {
            $coupon->quantity = $coupon->quantity - 1;
            $coupon->update();
        }


        $cart->update($request->all());
        DB::commit();
        return $this->getCartsByUserId(['user_id' => $request['user_id']]);

        return $user_Cart;
    }

    public function changeCartItemQuantity($request)
    {
        $cartItem = $this->model->query()->find($request['cart_id']);

        $store = Store::query()->where('id', $cartItem['store_id'])->first();
        $quantity = $request['quantity'];
        $basicUnitCount = ProductHelper::getProductBasicUnitCount($cartItem['product_id']);

        if ($store->store_type_id == StoreType::SUPPLIER) {
            $quantity = $quantity * $basicUnitCount; // multiply Quantity by Basic Unit Count if
        }

        $this->model->query()->find($request['cart_id'])
            ->update(['quantity' => $quantity]);

        return $this->getCartsByUserId(['user_id' => $request['user_id']]);
    }

    public function removeCartItem($request): array
    {
        $cart = $this->model->query()->find($request['cart_id'])
            ->delete();

        return $this->getCartsByUserId(['user_id' => $request['user_id']]);
    }


    /**
     * get Cart Summary.
     * @param $request
     * @return array
     */
    public function getCartsSummaryByUserId($request): array
    {
        // TODO: Here Will be the Logic to get the Offer Discount For Cart Summary.

        $paymentMethod = PaymentMethod::query()
            ->find($request->payment_method_id);

        $address = Address::query()
            ->with('city.state')
            ->find($request->address_id);

        $this->UpdateCartData($request);

        $carts = $this->getCartsByUserId(['user_id' => $request['user_id']]);

        return [
            'carts' => $carts,
            'payment_method' => $paymentMethod,
            'address' => $address,
        ];
    }

    /**
     * get Cart Summary based on the Payment Method and Address
     * @param $request
     * @return void
     */
    public function updateCartData($request)
    {
        Cart::query()->where('user_id', $request['user_id'])
            ->update(['address_id' => $request['address_id'], 'payment_method_id' => $request['payment_method_id']]);
    }
}
