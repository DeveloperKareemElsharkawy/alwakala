<?php

namespace App\Http\Requests\Feeds;

use App\Models\FeedsOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderFeedsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
        $selected_orders=FeedsOrder::query()->get()->pluck('order')->toArray();
        return [
           'product_id' => 'required|exists:products,id',
            'order' => 'required|min:1|'.Rule::notIn($selected_orders)
        ];
    }
}
