<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetOfferUser extends Model
{
    use HasFactory;
    protected $table = 'target_offer_users';
    protected $fillable = [
        'target_offer_id','receiver_user_id','is_approved'
    ];
}
