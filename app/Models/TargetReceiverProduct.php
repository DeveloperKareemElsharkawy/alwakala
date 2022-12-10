<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetReceiverProduct extends Model
{
    use HasFactory;
    protected $table = 'target_receiver_products';
    protected $fillable = [
        'product_id','receiver_user_id', 'stock'
    ];
}
