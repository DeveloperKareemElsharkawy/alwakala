<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Country::class, function (Faker $faker) {

    return [
        'iso' => $faker->isbn13,
        'name_en' => $faker->country,
        'name_ar' => $faker->country,
        'country_code' => $faker->countryCode,
        'phone_code' => $faker->countryCode,
        'activation' => 1,
    ];
});
