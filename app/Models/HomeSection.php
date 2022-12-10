<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    public function slides(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AppTv::class)->orderBy('order');
    }
}
