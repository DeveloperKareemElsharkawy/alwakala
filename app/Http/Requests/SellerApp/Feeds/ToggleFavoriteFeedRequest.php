<?php

namespace App\Http\Requests\SellerApp\Feeds;

use App\Lib\Helpers\StoreId\StoreId;
use App\Rules\Seller\Feed\YoutubeURLRule;
use Illuminate\Foundation\Http\FormRequest;

class ToggleFavoriteFeedRequest extends FormRequest
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
            'feed_id' => 'required|exists:feeds,id',
        ];
    }

}
