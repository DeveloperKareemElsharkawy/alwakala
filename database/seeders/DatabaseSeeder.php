<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $this->call(CountriesSeeder::class);
        $this->call(AuthorizationSeeder::class);
        $this->call(LookUpsSeeder::class);
        $this->call(LookUpsSeeder::class);
//        $this->call(PoliciesSeeder::class);
//        $this->call(ShippingMethodsSeeder::class);
    }
}
