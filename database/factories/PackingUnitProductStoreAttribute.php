<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\PackingUnitProductAttribute::class, function (Faker $faker) {
    $products = \App\Models\Product::query()->get()->pluck('id');
    $stores = \App\Models\Store::query()->get()->pluck('id');
    $stock =  $faker->numberBetween(1,900);
    $available_stock =  $faker->numberBetween(1,900);
    $sales_price = $faker->numberBetween(1, 10000);
    return [
        'product_id' => $faker->randomElement($products),
        'store_id' => $faker->randomElement($stores),
        'stock' => $stock,
        'available_stock' => $available_stock,
        'reserved_stock' => $stock - $available_stock,
        'sales_price' => $sales_price,
        'purchase_price' => $faker->numberBetween($sales_price-10 , $sales_price)
    ];
});
