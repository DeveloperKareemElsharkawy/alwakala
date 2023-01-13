<?php

namespace App\Http\Requests\Consumer\Reports;

use App\Rules\Consumer\Cart\QuantityCheck;
use Illuminate\Foundation\Http\FormRequest;

class ReportStoreRequest extends FormRequest
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
            'details' => 'required',
        ];
    }

}
