<?php

namespace App\Http\Requests\Feeds;

use App\Models\FeedsOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeedsVideosRequest extends FormRequest
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
        $selected_orders = FeedsOrder::query()->get()->pluck('order')->toArray();
        return [
            'video' => 'required|mimes:m4v,avi,flv,mp4,mov|max:10000',
            'store_id' => 'required|exists:stores,id'
        ];
    }
}
