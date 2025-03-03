<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;


    public function test_displays_the_attendance_list_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('attendance.list'));
        $response->assertStatus(200);
        $response->assertSee('勤怠一覧');
    }


    public function test_retrieves_attendance_data_for_the_selected_month()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $currentMonth = Carbon::now()->format('Y-m');
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
        ]);

        $response = $this->get(route('attendance.list', ['month' => $currentMonth]));

        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->date)->format('m/d'));
    }


    public function test_displays_previous_months_attendance_when_previous_button_is_clicked()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $prevMonth = Carbon::now()->subMonth()->format('Y-m');
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->subMonth()->toDateString(),
        ]);

        $response = $this->get(route('attendance.list', ['month' => $prevMonth]));

        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->date)->format('m/d'));
    }


    public function test_displays_next_months_attendance_when_next_button_is_clicked()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $nextMonth = Carbon::now()->addMonth()->format('Y-m');
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->addMonth()->toDateString(),
        ]);

        $response = $this->get(route('attendance.list', ['month' => $nextMonth]));

        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->date)->format('m/d'));
    }


    public function test_navigates_to_attendance_detail_page_when_detail_link_is_clicked()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
        ]);

        $response = $this->get(route('attendance.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee('詳細');
    }
}
