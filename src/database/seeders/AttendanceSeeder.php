<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        // 一般ユーザーを取得
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            // 直近10日間の勤怠データを作成
            for ($i = 0; $i < 10; $i++) {
                $date = Carbon::now()->subDays($i);
                $startTime = Carbon::createFromTime(rand(8, 10), rand(0, 59), 0);
                $endTime = (clone $startTime)->addHours(rand(6, 9)); // 6～9時間勤務
                $totalTime = $startTime->diff($endTime)->format('%H:%I:%S');

                Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date->toDateString(),
                    'start_time' => $startTime->toTimeString(),
                    'end_time' => $endTime->toTimeString(),
                    'total_time' => $totalTime,
                    'status' => 'completed',
                    'remarks' => 'ダミーデータ',
                ]);
            }
        }
    }
}
