<?php

namespace Database\Seeders;

use App\Models\Policy;
use Illuminate\Database\Seeder;

class PoliciesSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createstatisPolicies();
    }

    /**
     * create dashboard roles
     */
    protected function createstatisPolicies()
    {
        Policy::query()->updateOrCreate(
            [
                'name_en' => "Wekala",
                'name_ar' => "منتج تابع الوكاله",
                'description_en' => 'Wekala Description',
                'description_ar' => "منتج تابع الوكاله",
                'activation' => 1
            ]
        );
        Policy::query()->updateOrCreate(
            [
                'name_en' => "Non Wekala",
                'name_ar' => "منتج خارج الوكاله",
                'activation' => 1
            ]
        );
    }
}
