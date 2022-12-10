<?php

namespace App\Http\Requests\SellerApp\Feeds;

use App\Lib\Helpers\StoreId\StoreId;
use App\Rules\Seller\Feed\YoutubeURLRule;
use Illuminate\Foundation\Http\FormRequest;

class GetFeedRequest extends FormRequest
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
            'feedId' => 'required|exists:feeds,id',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'feedId' => (int)$this->route('feedId'),
        ]);
    }

}
