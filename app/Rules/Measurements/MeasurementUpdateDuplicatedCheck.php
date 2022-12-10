<?php

namespace App\Rules\Measurements;

use App\Models\Store;
use App\Models\StoreCategoriesMeasurement;
use Illuminate\Contracts\Validation\Rule;

class MeasurementUpdateDuplicatedCheck implements Rule
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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $size_id)
    {

        $store_id = Store::where('user_id',request()->user_id)->first()->id;

        $storeCategoriesMeasurementCheck = StoreCategoriesMeasurement::where([
            ['store_id',$store_id],
            ['id','!=',request()->id],
            ['size_id',$size_id],
            ['category_id',request()->category_id],
        ])->first();

        if($storeCategoriesMeasurementCheck){ // if has other Measurements With Same Size && Category return validation
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('messages.measurements.already_exist');
    }
}
