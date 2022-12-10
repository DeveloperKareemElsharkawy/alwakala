<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedsVideos extends Model
{
    protected $table='feeds_videos';
    protected $fillable = [
        'video',
        'store_id',
        'verified'
    ];
}
