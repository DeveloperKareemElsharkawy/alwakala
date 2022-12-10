<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetOfferMilestone extends Model
{
    use HasFactory;
    protected $table = 'target_offer_milestones';
    protected $fillable = [
        'target_offer_id','targeted_price', 'reward_value', 'is_active'
    ];
}
