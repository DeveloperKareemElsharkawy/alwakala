<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetOffer extends Model
{
    use HasFactory;
    protected $table = 'target_offers';
    protected $fillable = [
        'name_ar','name_en','description','start_date','end_date', 'is_active', 'owner_user_id'
    ];

    public function milestones()
    {
        return $this->hasMany(TargetOfferMilestone::class)->orderBy('targeted_price');
    }


}
