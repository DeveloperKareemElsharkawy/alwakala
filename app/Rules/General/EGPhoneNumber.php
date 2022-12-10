<?php

namespace App\Rules\General;

use Illuminate\Contracts\Validation\Rule;

class EGPhoneNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return in_array(substr($value, 0, 3), ['010', '011', '012', '015']) && strlen($value) == 11;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Wrong Mobile Number Format';
    }
}
