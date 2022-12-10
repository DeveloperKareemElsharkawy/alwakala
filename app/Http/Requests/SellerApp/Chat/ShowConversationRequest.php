<?php

namespace App\Http\Requests\SellerApp\Chat;

use App\Lib\Helpers\UserId\UserId;
use App\Models\PackingUnitProduct;
use App\Models\ProductStore;
use App\Models\Store;
use App\Rules\Seller\Cart\CheckCartItemOwnerShip;
use App\Rules\Seller\Cart\QuantityCheck;
use App\Rules\Seller\Cart\UnitCountCheck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ShowConversationRequest extends FormRequest
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
        return [
            'store_id' => 'required|exists:stores,id',
         ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'store_id' => (int)$this->route('store_id'),
         ]);
    }

}
