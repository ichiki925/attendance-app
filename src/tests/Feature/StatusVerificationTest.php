<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class StatusVerificationTest extends TestCase
{
    use RefreshDatabase;


    public function test_displays_status_off_duty_correctly()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);


        $response = $this->get('/attendance/register');

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }


    public function test_displays_status_working_correctly()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);


        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->format('H:i'),
            'status' => 'working',
        ]);

        $response = $this->get('/attendance/register');

        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }


    public function test_displays_status_on_break_correctly()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);


        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->subHours(1)->format('H:i'),
            'status' => 'on_break',
        ]);

        $response = $this->get('/attendance/register');

        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }


    public function test_displays_status_completed_correctly()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);


        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->subHours(8)->format('H:i'),
            'end_time' => Carbon::now()->format('H:i:s'),
            'status' => 'completed',
        ]);

        $response = $this->get('/attendance/register');

        $response->assertStatus(200);
        $response->assertSee('退勤済');
        $response->assertSee('お疲れ様でした。');
    }
}