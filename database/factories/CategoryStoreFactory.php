<?php

namespace Database\Factories;

use App\Models\CategoryStore;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryStoreFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CategoryStore::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_id' => $this->faker->randomElement([1, 11, 24])
        ];
    }
}
