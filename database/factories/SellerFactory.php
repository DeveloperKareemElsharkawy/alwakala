<?php

namespace Database\Factories;

use App\Enums\Roles\ARoles;
use App\Models\Role;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

class SellerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Seller::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'role_id' => Role::query()->where('role', ARoles::OWNER)->first()->id
        ];
    }
}
