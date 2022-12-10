<?php

namespace App\Models;

use App\Enums\Apps\AApps;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    /**
     * Scope a query to only include users of a given type.
     *
     * @param $query
     * @return Builder
     */
    public function scopeWhereSlug($query, $slug): Builder
    {
        return $query->where([['app_id', AApps::SELLER_APP], ['slug', $slug]]);
    }
}
