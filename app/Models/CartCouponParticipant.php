<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartCouponParticipant extends Model
{
    protected $fillable =[
        'coupon_id',
        'user_id',
        'cart_id',
        'main_participant_id',
        'share_coupon_code',
    ];
    use HasFactory;
}
