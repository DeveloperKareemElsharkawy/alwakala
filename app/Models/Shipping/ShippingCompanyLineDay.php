<?php

namespace App\Models\Shipping;

use App\Models\DaysOfWeek;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingCompanyLineDay extends Model
{
    use SoftDeletes;

    protected $fillable = ['day_of_week_id', 'shipping_company_line_id'];

    public function dayName()
    {
        return $this->belongsTo(DaysOfWeek::class, 'day_of_week_id');
    }
}
