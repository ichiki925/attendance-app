<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoundingSettingSeeder extends Seeder
{
    public function run()
    {
        DB::table('rounding_settings')->insert([
            [
                'round_minutes' => 1,
                'round_type'    => 'floor',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}