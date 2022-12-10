<?php

namespace Database\Factories;

use App\Models\ProductStore;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductStore::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $discount = $this->faker->randomElement([10, 20, 30]);
        $price = $this->faker->randomElement([200, 300, 400, 500]);
        return [
//        'product_id' => $faker->name,
//        'store_id' => $faker->name,
            'publish_app_at' => $this->faker->date('Y-m-d'),
            'views' => $this->faker->numberBetween(1, 10000),
            'price' => $price,
            'net_price' => $price - ($price * ($discount / 100)),
            'discount' => $discount,
            'discount_type' => $this->faker->randomElement([2]),
            'activation' => true,
            'free_shipping' => $this->faker->randomElement([true, false]),
        ];
    }
}

//
//
//<?php
//
//
//use Faker\Generator as Faker;
//
//$imageArr = [
//    '/images/seeders/ava1.png',
//    '/images/seeders/ava2.png',
//    '/images/seeders/ava3.png',
//];
//$factory->define(\App\Models\ProductStore::class, function (Faker $faker) {
//
//    $discount = $faker->randomElement([10, 20, 30]);
//    $price = $faker->randomElement([200, 300, 400, 500]);
//    return [
////        'product_id' => $faker->name,
////        'store_id' => $faker->name,
//        'publish_app_at' => $faker->date('Y-m-d'),
//        'views' => $faker->numberBetween(1, 10000),
//        'price' => $price,
//        'net_price' => $price - ($price * ($discount / 100)),
//        'discount' => $discount,
//        'discount_type' => $faker->randomElement([2]),
//        'activation' => true,
//        'free_shipping' => $faker->randomElement([true, false]),
//    ];
//});
//
//$factory->define(\App\Models\ProductStoreStock::class, function (Faker $faker) {
//    return [
////        'product_store_id' => $faker->name,
//        'stock' => 1000,
//        'reserved_stock' => 0,
//        'available_stock' => 1000,
//        'returned' => 0,
//        'sold' => 0,
//        'size_id' => $faker->randomElement(\App\Models\Size::query()->get()->pluck('id')),
//        'color_id' => $faker->randomElement(\App\Models\Color::query()->get()->pluck('id')),
//    ];
//});
//
//$factory->define(\App\Models\PackingUnitProduct::class, function (Faker $faker) {
//    return [
////        'product_id' => $faker->name,
//        'packing_unit_id' => 1,
//        'basic_unit_id' => 2,
//        'basic_unit_count' => 4,
//    ];
//});
//
//$factory->define(\App\Models\PackingUnitProductAttribute::class, function (Faker $faker) {
//    return [
////        'packing_unit_product_id' => $faker->name,
//        'size_id' => $faker->randomElement(\App\Models\Size::query()->get()->pluck('id')),
//        'quantity' => 1,
//    ];
//});
//
//$factory->define(\App\Models\Bundle::class, function (Faker $faker) {
//    return [
////        'store_id' => $faker->name,
////        'product_id' => $faker->name,
//        'quantity' => $faker->randomElement([50, 100, 150]),
//        'price' => $faker->randomElement([100, 150, 200, 250, 300, 350]),
//    ];
//});
//$factory->define(\App\Models\BarcodeProduct::class, function (Faker $faker) {
//    return [
////        'product_id' => $faker->name,
//        'barcode' => $faker->numberBetween(1000, 99999999),
//        'color_id' => $faker->randomElement(\App\Models\Color::query()->get()->pluck('id'))
//    ];
//});
//$factory->define(\App\Models\ProductImage::class, function (Faker $faker) use ($imageArr) {
//    return [
////        'product_id' => $faker->name,
//        'image' => $faker->randomElement($imageArr),
//        'color_id' => $faker->randomElement(\App\Models\Color::query()->get()->pluck('id'))
//    ];
//});
