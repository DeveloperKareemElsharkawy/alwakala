<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'details', 'complaint_topic_id', 'app_id'];
}
