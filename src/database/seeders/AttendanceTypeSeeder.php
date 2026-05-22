<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => '通常出勤', 'color' => '#66bb6a', 'is_paid' => false, 'is_holiday' => false],
            ['name' => '有給休暇', 'color' => '#64b5f6', 'is_paid' => true,  'is_holiday' => true],
            ['name' => '欠勤',     'color' => '#ef9a9a', 'is_paid' => false, 'is_holiday' => true],
            ['name' => '休日出勤', 'color' => '#ffb74d', 'is_paid' => false, 'is_holiday' => false],
            ['name' => '振替休日', 'color' => '#b39ddb', 'is_paid' => false, 'is_holiday' => true],
        ];


        foreach ($types as $type) {
            DB::table('attendance_types')->insertOrIgnore(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}