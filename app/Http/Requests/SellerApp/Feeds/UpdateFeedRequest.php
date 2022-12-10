<?php

namespace App\Http\Requests\SellerApp\Feeds;

use App\Lib\Helpers\StoreId\StoreId;
use App\Models\Feed;
use App\Rules\Seller\Feed\YoutubeURLRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFeedRequest extends FormRequest
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
        $rules = [
            'feedId' => 'required|exists:feeds,id',
            'store_id' => 'required|exists:stores,id',
            'products' => 'required|array',
            'products.*' => 'required|numeric|exists:products,id,owner_id,' . $this->user_id,
        ];

        if ($feed = Feed::query()->find($this->input('feedId'))) {
            if ($feed->images) {
                $rules['images'] = 'array';
                $rules['images.*'] = 'image|mimes:jpg,jpeg,png';
            } else if ($feed->youtube_url) {
                $rules['youtube_url'] = ['required', new YoutubeURLRule()];
            }
        }


        return $rules;
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
            'feedId' => (int)$this->route('feedId'),
        ]);
    }

}
