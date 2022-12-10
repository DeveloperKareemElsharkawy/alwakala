<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreOpeningHour extends Model
{
    use HasFactory;
    protected $fillable = ['store_id', 'days_of_week_id', 'open_time', 'close_time'];

    public function day()
    {
        return $this->hasOne(DaysOfWeek::class, 'id', 'days_of_week_id');
    }
}
