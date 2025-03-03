<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->attendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);
    }


    public function test_admin_can_view_all_users_attendance()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.attendance.list', ['date' => Carbon::today()->toDateString()]));

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee('08:00');
        $response->assertSee('17:00');
    }


    public function test_attendance_page_displays_correct_date()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.attendance.list', ['date' => Carbon::today()->toDateString()]));

        $response->assertStatus(200);
        $response->assertSee(Carbon::today()->format('Y年n月j日'));
    }


    public function test_previous_day_button_shows_previous_day_attendance()
    {
        $this->actingAs($this->admin);

        $previousDate = Carbon::yesterday()->toDateString();
        $previousAttendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'date' => $previousDate,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $response = $this->get(route('admin.attendance.list', ['date' => $previousDate]));

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }


    public function test_next_day_button_shows_next_day_attendance()
    {
        $this->actingAs($this->admin);

        $nextDate = Carbon::tomorrow()->toDateString();
        $nextAttendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'date' => $nextDate,
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
        ]);

        $response = $this->get(route('admin.attendance.list', ['date' => $nextDate]));

        $response->assertStatus(200);
        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }
}
