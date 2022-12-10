<?php
namespace Database\Seeders;

use App\Models\Policy;
use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodsSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createstatisShippingMethods();
    }

    /**
     * create dashboard roles
     */
    protected function createstatisShippingMethods()
    {
        ShippingMethod::query()->updateOrCreate(
            [
                'name_en' => "Ready for shipping",
                'name_ar' => "جاهز للشحن",
                'activation' => 1
            ]
        );
        ShippingMethod::query()->updateOrCreate(
            [
                'name_en' => "Ship upon order",
                'name_ar' => "يشحن عند الطلب",
                'activation' => 1
            ]
        );
    }
}
