<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreCategoriesMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
     'store_id',
     'size_id',
     'category_id',
     'length',
     'shoulder',
     'chest',
     'waist',
     'hem',
     'arm',
     'biceps',
     's_l',
    ];


    public function size()
    {
        return $this->belongsTo(Size::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
