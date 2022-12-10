<?php

namespace App\Http\Requests\Orders;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ApproveOrders extends FormRequest
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
        return [
            'id' => 'required|exists:orders,id',
//            'delivery_date' => 'required|date|date_format:Y-m-d|after_or_equal:' . Carbon::today()
        ];
    }
}
