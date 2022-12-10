<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    private $imageArr = [
        '/images/sellers/ava1.png',
        '/images/sellers/ava2.png',
        '/images/sellers/ava3.png',
    ];
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => bcrypt('123456'),
            'mobile' => '012' . $this->faker->numberBetween(10000000, 99999999),
            'image' => $this->faker->randomElement($this->imageArr),
            'type_id' => 2,
            'activation' => true,
        ];
    }
}
