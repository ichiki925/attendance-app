<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class BreakFunctionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_start_break()
    {
        $user = User::factory()->create(['role' => 'user']);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->format('H:i'),
            'status' => 'working',
        ]);

        $this->actingAs($user);
        $response = $this->post('/attendance/register', [
            'status' => 'on_break',
        ]);

        $response->assertRedirect('/attendance/register');
        $this->assertDatabaseHas('breaks', ['attendance_id' => $attendance->id]);
    }

    public function test_user_can_end_break()
    {
        $user = User::factory()->create(['role' => 'user']);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->format('H:i'),
            'status' => 'on_break',
        ]);

        $break = BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::now()->subMinutes(30)->format('H:i'), // H:i:s で保存
            'break_end' => null,
        ]);

        $this->actingAs($user);
        $response = $this->post('/attendance/register', [
            'status' => 'working',
        ])->assertRedirect('/attendance/register');

        $response = $this->get('/attendance/register');

        // 現在の時刻を H:i 形式に変換
        $expectedBreakEnd = Carbon::now()->format('H:i');


        // HTML に break_end の時間が含まれているか確認
        $response->assertSee($expectedBreakEnd);
    }

    public function test_user_can_see_break_button()
    {
        $user = User::factory()->create(['role' => 'user']);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->format('H:i'),
            'status' => 'working',
        ]);

        $this->actingAs($user);
        $response = $this->get('/attendance/register');

        $response->assertStatus(200);
        $response->assertSee('休憩入');
    }

    public function test_user_can_take_multiple_breaks()
    {
        $user = User::factory()->create(['role' => 'user']);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->format('H:i'),
            'status' => 'working',
        ]);

        $this->actingAs($user);
        $this->post('/attendance/register', ['status' => 'on_break']);

        $this->post('/attendance/register', ['status' => 'working_again']);

        $this->post('/attendance/register', ['status' => 'on_break']);

        $this->assertDatabaseCount('breaks', 2);
    }

    public function test_user_can_see_break_return_button()
    {
        $user = User::factory()->create(['role' => 'user']);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->format('H:i'),
            'status' => 'on_break',
        ]);

        $this->actingAs($user);
        $response = $this->get('/attendance/register');

        $response->assertStatus(200);
        $response->assertSee('休憩戻');
    }

    public function test_admin_can_see_break_time()
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->subHours(5)->format('H:i'),
            'end_time' => Carbon::now()->format('H:i'),
            'status' => 'completed',
        ]);

        $breakStart = Carbon::now()->subHours(2);
        $breakEnd = Carbon::now()->subHours(1);
        $breakTime = $breakEnd->diff($breakStart)->format('%H:%I'); // 差分を計算（H:i 形式）

        $break = BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => $breakStart->format('H:i'),
            'break_end' => $breakEnd->format('H:i'),
            'break_time' => $breakTime,
        ]);

        $this->actingAs($admin);
        $response = $this->get('/admin/attendance/list');

        $response->assertStatus(200);

        $response->assertSee($breakTime);
    }



}
