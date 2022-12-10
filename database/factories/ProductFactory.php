<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Brand;
use App\Models\User;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $parentsId = Category::query()
            ->where('category_id', null)
            ->where('activation', true)
            ->pluck('id')
            ->toArray();
        $subChild = Category::query()
            ->whereIn('category_id', $parentsId)
            ->where('activation', true)
            ->pluck('id')
            ->toArray();
        $categories = Category::query()
            ->where('activation', true)
            ->whereIn('category_id', $subChild)
            ->pluck('id')
            ->toArray();
        $brands = Brand::query()->get()->pluck('id')->toArray();
//        $sellers = User::query()
//            ->where('type_id', 2)
//            ->where('activation', true)
//            ->get()
//            ->pluck('id')->toArray();
        $materials = Material::query()->get()->pluck('id')->toArray();

        return [
            'name' => $this->faker->name,
            'description' => $this->faker->name,
            'activation' => 1,
            'brand_id' => $this->faker->randomElement($brands),
            'category_id' => $this->faker->randomElement($categories),
//            'owner_id' => $this->faker->randomElement($sellers),
            'channel' => 'faker',
            'consumer_price' => $this->faker->randomElement([100, 150, 200, 250, 300, 350]),
            'reviewed' => 1,
            'material_id' => $this->faker->randomElement($materials),
            'material_rate' => 100,
        ];
    }
}
