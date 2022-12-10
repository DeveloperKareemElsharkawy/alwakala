<?php

namespace App\Http\Requests\Policies;

use App\Models\Policy;
use Illuminate\Foundation\Http\FormRequest;

class CheckPolicyExistenceRequest extends FormRequest
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
            'id' => 'required|numeric|exists:policies,id'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'id' => request()->id,
        ]);
    }
}
