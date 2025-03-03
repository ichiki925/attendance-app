<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceFunctionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_attendance_button_when_off_duty()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get('/attendance/register');

        $response->assertStatus(200);
        $response->assertSee('出勤');
    }


    public function test_user_can_start_work()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->post('/attendance/register', [
            'status' => 'working'
        ]);

        $response->assertRedirect('/attendance/register');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'status' => 'working'
        ]);
    }


    public function test_user_cannot_work_twice_a_day()
    {

        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->subHours(8)->format('H:i:s'),
            'end_time' => Carbon::now()->format('H:i:s'),
            'status' => 'completed',
        ]);

        $response = $this->get('/attendance/register');

        $response->assertStatus(200);
        $response->assertDontSee('出勤');
    }


    public function test_admin_can_see_attendance_record()
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        // 2️⃣ 勤怠データを Factory で作成（出勤のみ、退勤なし）
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'status' => 'working',
            'start_time' => Carbon::now()->format('H:i:s'),
            'end_time' => null, // 退勤前
        ]);

        $this->actingAs($admin);
        $response = $this->get('/admin/attendance/list');

        // 4️⃣ 管理画面に出勤時刻が表示されていることを確認
        $response->assertStatus(200);

        // Blade の表示ロジックに合わせてフォーマットを統一
        $expectedStartTime = $attendance->start_time 
            ? Carbon::parse($attendance->start_time)->format('H:i') 
            : '-';

        // ここを追加！（管理画面に出勤時刻が表示されているか確認）
        $response->assertSee($expectedStartTime);

        // 5️⃣ DB に勤怠データが正しく登録されていることを確認
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'status' => 'working',
            'start_time' => $attendance->start_time,
            'end_time' => null, // 退勤前
        ]);
    }

}

