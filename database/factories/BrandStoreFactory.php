<?php

namespace Database\Factories;

use App\Models\BrandStore;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandStoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BrandStore::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'brand_id' => $this->faker->randomElement(\App\Models\Brand::query()->get()->pluck('id'))
        ];
    }
}
