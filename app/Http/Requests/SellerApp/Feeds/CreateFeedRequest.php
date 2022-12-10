<?php

namespace App\Http\Requests\SellerApp\Feeds;

use App\Lib\Helpers\StoreId\StoreId;
use App\Rules\Seller\Feed\YoutubeURLRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateFeedRequest extends FormRequest
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
            'images' => 'required_without:youtube_url|prohibited_unless:youtube_url,null|array',
            'images.*' => 'image|mimes:jpg,jpeg,png',
            'youtube_url' => ['required_without:images','prohibited_unless:images,null', new YoutubeURLRule()],
            'products' => 'required|array',
            'products.*' => 'required|numeric|exists:products,id,owner_id,' . $this->user_id,
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'images.prohibited_unless' => trans('messages.feed.validation.only_images_or_youtube_url'),
            'youtube_url.prohibited_unless' => trans('messages.feed.validation.only_images_or_youtube_url'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'store_id' => StoreId::getStoreID(request()),
        ]);
    }

}
