<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        $startTime = Carbon::createFromTime(rand(8, 10), rand(0, 59), 0);
        $endTime = (clone $startTime)->addHours(rand(6, 9))->addMinutes(rand(0, 59));
        $workDuration = $startTime->diffInSeconds($endTime); // 秒単位で計算

        return [
            'user_id' => User::factory(),
            'date' => Carbon::now()->subDays(rand(0, 10))->toDateString(),
            'start_time' => $startTime->toTimeString(),
            'end_time' => $endTime->toTimeString(),
            'total_time' => gmdate("H:i:s", $workDuration), // 初期は休憩時間を考慮しない
            'status' => 'completed',
            'remarks' => $this->faker->sentence(),
        ];
    }

    
}
