<?php

namespace App\Http\Requests\SellerApp\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Rules\Seller\Cart\QuantityCheck;
use App\Rules\Seller\Cart\UnitCountCheck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeCartQuantityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $cartItem = CartItem::query()->find(request()['cart_id']);
        $productId = $cartItem->product_id ?? 0;
        $storeId = $cartItem->store_id ?? 0;
        $colorId = $cartItem->color_id ?? 0;

        return [
            'cart_id' => ['required', Rule::exists('cart_items', 'id')->where(function ($query) {
                return $query->where('user_id', request()['user_id']);
            })],
            'quantity' => [
                'required',
                new QuantityCheck($productId, $storeId, $colorId),
                // new UnitCountCheck($productId)
            ],
        ];
    }
}
