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
            for ($i = 1; $i <= 20; $i++) {
                $date = Carbon::now()->subDays($i);
                // 通常・残業・深夜をランダムに混在
                $pattern = rand(1, 5);
                if ($pattern === 1) {
                    // 深夜パターン
                    $startTime = Carbon::createFromTime(22, rand(0, 30), 0);
                    $endTime = (clone $startTime)->addHours(rand(6, 8));
                } elseif ($pattern === 2) {
                    // 残業パターン
                    $startTime = Carbon::createFromTime(9, rand(0, 30), 0);
                    $endTime = (clone $startTime)->addHours(rand(9, 11));
                } else {
                    // 通常パターン
                    $startTime = Carbon::createFromTime(rand(8, 10), rand(0, 59), 0);
                    $endTime = (clone $startTime)->addHours(rand(7, 8));
                }

                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date->toDateString(),
                    'start_time' => $startTime->toTimeString(),
                    'end_time' => $endTime->toTimeString(),
                    'status' => 'completed',
                ]);

                // 休憩を必ず1つ作成
                $breakStart = (clone $startTime)->addHours(rand(2, 4))->addMinutes(rand(0, 30));
                $breakEnd = (clone $breakStart)->addMinutes(rand(30, 60));
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $breakStart->format('H:i'),
                    'break_end' => $breakEnd->format('H:i'),
                    'break_time' => $breakStart->diff($breakEnd)->format('%H:%I'),
                ]);

                // calculateTimesで合計・残業・深夜を計算
                $attendance->load('breaks');
                $attendance->calculateTimes();
                $attendance->save();
            }
        }
    }
}