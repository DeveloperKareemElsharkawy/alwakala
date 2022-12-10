<?php

namespace Database\Seeders;

use App\Models\DaysOfWeek;
use Illuminate\Database\Seeder;

class MainDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createWeekDays();
    }

    public function createWeekDays()
    {
        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Saturday', 'name_ar' => 'السبت'],
            ['name_en' => 'Saturday', 'name_ar' => 'السبت']
        );
        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Sunday', 'name_ar' => 'الأحد'],
            ['name_en' => 'Sunday', 'name_ar' => 'الأحد']
        );
        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Monday', 'name_ar' => 'الاثنين'],
            ['name_en' => 'Monday', 'name_ar' => 'الاثنين']
        );
        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Tuesday', 'name_ar' => 'الثلاثاء'],
            ['name_en' => 'Tuesday', 'name_ar' => 'الثلاثاء']
        );
        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Wednesday', 'name_ar' => 'الأربعاء'],
            ['name_en' => 'Wednesday', 'name_ar' => 'الأربعاء']
        );
        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Thursday', 'name_ar' => 'الخميس'],
            ['name_en' => 'Thursday', 'name_ar' => 'الخميس']
        );
        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Friday', 'name_ar' => 'الجمعه'],
            ['name_en' => 'Friday', 'name_ar' => 'الجمعه']
        );
    }
}
