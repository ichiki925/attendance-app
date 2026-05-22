<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkPatternSeeder extends Seeder
{
    public function run()
    {
        $patterns = [
            ['name' => '通常勤務', 'start_time' => '09:00', 'end_time' => '18:00', 'break_minutes' => 60],
            ['name' => '早番',     'start_time' => '07:00', 'end_time' => '16:00', 'break_minutes' => 60],
            ['name' => '遅番',     'start_time' => '13:00', 'end_time' => '22:00', 'break_minutes' => 60],
            ['name' => '夜勤',     'start_time' => '22:00', 'end_time' => '07:00', 'break_minutes' => 60],
        ];

        foreach ($patterns as $pattern) {
            DB::table('work_patterns')->insertOrIgnore(array_merge($pattern, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}