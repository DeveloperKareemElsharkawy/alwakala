<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyTransaction extends Model
{
    use HasFactory;
    protected $table = 'money_transactions';
    protected $fillable = ['type_id', 'amount','transaction_owner_id','creator_type','creator_id'];
}
