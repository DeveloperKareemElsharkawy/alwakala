<?php

namespace App\Models;

use App\Enums\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    public $table = 'activities';

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
