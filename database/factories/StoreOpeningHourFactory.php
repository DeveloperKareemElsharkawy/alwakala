<?php

namespace Database\Factories;

use App\Models\StoreOpeningHour;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreOpeningHourFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StoreOpeningHour::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'days_of_week_id' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7]),
            'open_time' => $this->faker->time('H:i:s'),
            'close_time' => $this->faker->time('H:i:s'),
            'is_open' => $this->faker->randomElement([true, false]),
        ];
    }
}


