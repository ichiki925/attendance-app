<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BreakTime;
use App\Models\Attendance;
use Carbon\Carbon;

class BreakTimeFactory extends Factory
{
    protected $model = BreakTime::class;

    public function definition()
    {
        $breakStart = Carbon::createFromTime(rand(12, 14), rand(0, 59), 0);
        $breakEnd = (clone $breakStart)->addMinutes(rand(30, 60));
        $breakDuration = $breakStart->diffInSeconds($breakEnd);

        return [
            'attendance_id' => Attendance::factory(), // 関連する Attendance を作成
            'break_start' => $breakStart->toTimeString(),
            'break_end' => $breakEnd->toTimeString(),
            'break_time' => gmdate("H:i:s", $breakDuration),
        ];
    }
}
