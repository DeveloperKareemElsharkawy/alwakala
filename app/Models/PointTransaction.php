<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    use HasFactory;
    protected $table = 'points_transactions';
    protected $fillable = ['type_id', 'amount','transaction_owner_id','creator_type','creator_id'];
}
