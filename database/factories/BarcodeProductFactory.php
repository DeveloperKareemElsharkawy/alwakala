<?php

namespace Database\Factories;

use App\Models\BarcodeProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarcodeProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BarcodeProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'barcode' => $this->faker->ean13,
        ];
    }
}
