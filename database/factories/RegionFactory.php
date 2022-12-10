<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Region::class, function (Faker $faker) {
    $name= $faker->country;
    $countries = \App\Models\Country::query()->get()->pluck('id');
    return [
        'name_en' => $name,
        'name_ar' => $name,
        'country_id' => $faker->randomElement($countries),
    ];
});
