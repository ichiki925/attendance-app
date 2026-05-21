<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClosingDaySettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['closing_day' => 5,  'label' => '5日締め',  'is_active' => false],
            ['closing_day' => 15, 'label' => '15日締め', 'is_active' => false],
            ['closing_day' => 20, 'label' => '20日締め', 'is_active' => false],
            ['closing_day' => 31, 'label' => '末日締め', 'is_active' => true],
        ];

        DB::table('closing_day_settings')->insert($settings);
    }
}