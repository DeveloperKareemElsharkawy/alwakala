<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateSellerAppSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('seller_app.facebook_url', 'https://www.facebook.com');
        $this->migrator->add('seller_app.instagram_url', 'https://www.instagram.com');
        $this->migrator->add('seller_app.twitter_url', 'https://twitter.com');
        $this->migrator->add('seller_app.linkedin_url', 'https://www.linkedin.com');
        $this->migrator->add('seller_app.website_url', 'https://elwekala.com');
        $this->migrator->add('seller_app.pinterest_url', 'https://www.pinterest.com');
        $this->migrator->add('seller_app.youtube_url', 'https://www.youtube.com');
    }
}
