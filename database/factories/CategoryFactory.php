<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Category::class, function (Faker $faker) {
    return [
        'name_en' => $faker->name,
        'name_ar' => $faker->name,
        'description' => $faker->paragraph,
        'activation' => 1,
        'priority' => 1,
        'category_id' => $faker->randomElement(\App\Models\Category::query()->get()->pluck('id')),
        'image' => '/storage/brands/download.png'
    ];
});
