<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\LaravelSettings\Settings;

class SellerAppSettings extends Settings
{
    // Social Links For Seller App
    public $facebook_url;
    public $instagram_url;
    public $twitter_url;
    public $linkedin_url;
    public $website_url;
    public $pinterest_url;
    public $youtube_url;

    public static function group(): string
    {
        return 'seller_app';
    }
}
