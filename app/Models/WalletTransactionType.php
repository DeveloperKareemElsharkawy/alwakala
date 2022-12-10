<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransactionType extends Model
{
    use HasFactory;
    protected $table = 'wallet_transactions_types';
    protected $fillable = ['name_ar', 'name_en'];
}
