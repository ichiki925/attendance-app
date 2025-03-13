<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_detail_page_displays_correct_user_name()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
        ]);

        $response = $this->get(route('attendance.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_attendance_detail_page_displays_correct_date()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
        ]);

        $response = $this->get(route('attendance.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->date)->year . '年');
        $response->assertSee(Carbon::parse($attendance->date)->month . '月' . Carbon::parse($attendance->date)->day . '日');
    }

    public function test_attendance_detail_page_displays_correct_working_hours()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'start_time' => '08:30:00',
            'end_time' => '17:30:00',
            'date' => Carbon::now()->toDateString(),
        ]);

        $response = $this->get(route('attendance.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee('08:30');
        $response->assertSee('17:30');
    }

    public function test_attendance_detail_page_displays_correct_break_time()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'start_time' => '08:30:00',
            'end_time' => '17:30:00',
            'date' => Carbon::now()->toDateString(),
        ]);

        $break = BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00:00',
            'break_end' => '12:30:00',
        ]);

        $response = $this->get(route('attendance.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee('12:00');
        $response->assertSee('12:30');
    }
}
