<?php

namespace App\Http\Requests\SellerApp\QRCode;

use App\Rules\Seller\Cart\QuantityCheck;
use Illuminate\Foundation\Http\FormRequest;

class AcceptOrderByBarcode extends FormRequest
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
    public function rules()
    {
        return [
            'order_number' => 'required|exists:orders,number',
        ];
    }
}
