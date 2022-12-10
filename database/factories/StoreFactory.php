<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\City;

class StoreFactory extends Factory
{
    private $imageArr = [
        '/images/seeders/ava1.png',
        '/images/seeders/ava2.png',
        '/images/seeders/ava3.png',
        '/images/seeders/ava4.png',
        '/images/seeders/ava5.png',
        '/images/seeders/ava6.png',
    ];

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Store::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'logo' => $this->faker->randomElement($this->imageArr),
            'licence' => $this->faker->randomElement($this->imageArr),
            'address' => $this->faker->address,
            'landing_number' => $this->faker->phoneNumber,
            'mobile' => $this->faker->phoneNumber,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'building_no' => $this->faker->randomNumber(),
            'landmark' => $this->faker->name,
            'main_street' => $this->faker->name,
            'side_street' => $this->faker->name,
            'is_store_has_delivery' => $this->faker->boolean,
            'store_type_id' => 2,
            'city_id' => $this->faker->randomElement(City::query()->get()->pluck('id')),
//        'description' => $faker->text,
        ];
    }
}
