<?php

namespace App\Http\Requests\SellerApp\Chat;

use App\Lib\Helpers\StoreId\StoreId;
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

class SendMessageRequest extends FormRequest
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
            'message' => 'required_without_all:image,video,record',
            'image' => 'required_without_all:video,message,record|image|mimes:jpg,png',
            'video' => 'required_without_all:message,image,record|mimes:mp4,mov|max:20000',
            'record' => 'required_without_all:message,image,video|mimes:application/octet-stream,audio/mpeg,mpga,mp3,wav,mp4,audio/mpeg,m4a',
            'store_receiver_id' => 'required|exists:stores,id',
            'receiver_id' => 'required|exists:users,id',
            'store_sender_id' => 'required|exists:stores,id',
            'sender_id' => 'required|exists:users,id',
            'parent_id' => ['nullable', Rule::exists('messages', 'id')->where(function ($query) {
                return $query->where('store_receiver_id', $this->input('store_sender_id'))->orWhere('store_sender_id', $this->input('store_sender_id'));
            })],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'store_receiver_id' => (int)$this->route('store_id'),
            'receiver_id' => UserId::GetUserIdFromStore($this->route('store_id')),
            'sender_id' => request()->user('api')->id,
            'store_sender_id' => StoreId::getStoreID(request()),
        ]);
    }

}
