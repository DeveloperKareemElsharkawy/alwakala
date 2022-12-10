<?php

namespace App\Http\Requests\SellerApp\Offer;

use App\Models\Policy;
use Illuminate\Foundation\Http\FormRequest;

class ApproveOrRejectOfferRequest extends FormRequest
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
     * @param $table
     * @return array
     */
    public function rules(): array
    {
        return [
            'offer_id' => 'required|numeric|exists:offers,id,deleted_at,NULL',
            'status' => 'required|in:approved,rejected'
        ];
    }


}
