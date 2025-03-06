<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run()
    {


        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {

            for ($i = 0; $i < 10; $i++) {
                $date = Carbon::now()->subDays($i);
                $startTime = Carbon::createFromTime(rand(8, 10), rand(0, 59), 0);
                $endTime = (clone $startTime)->addHours(rand(6, 9));
                $totalTime = $startTime->diff($endTime)->format('%H:%I:%S');


                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date->toDateString(),
                    'start_time' => $startTime->toTimeString(),
                    'end_time' => $endTime->toTimeString(),
                    'total_time' => $totalTime,
                    'status' => 'completed',
                    'remarks' => 'ダミーデータ',
                ]);


                if (rand(0, 1)) {
                    $breakStart = (clone $startTime)->addHours(rand(2, 4))->addMinutes(rand(0, 30));
                    $breakEnd = (clone $breakStart)->addMinutes(rand(15, 45));

                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => $breakStart->format('H:i'),
                        'break_end' => $breakEnd->format('H:i'),
                        'break_time' => $breakStart->diff($breakEnd)->format('%H:%I'),
                    ]);
                }
            }
        }
    }
}
