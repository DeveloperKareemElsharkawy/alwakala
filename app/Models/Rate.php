<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    public function rater()
    {
        return $this->morphTo();
    }

    public function rated()
    {
        return $this->morphTo();
    }
}
