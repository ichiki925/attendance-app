<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Models\AttendanceRequest;
use App\Models\User;
use App\Models\Attendance;

class AttendanceRequestFactory extends Factory
{
    protected $model = AttendanceRequest::class;

    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'user_id' => User::factory(),
            'start_time' => Carbon::parse('08:00')->format('H:i'),
            'end_time' => Carbon::parse('17:00')->format('H:i'),
            'reason' => $this->faker->sentence,
            'request_status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
