<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionAuthorizedOwner extends Model
{
    use HasFactory;
    protected $table = 'transaction_authorized_owners';
    protected $fillable = ['transaction_id', 'transaction_type','stores_owner_id'];
}
