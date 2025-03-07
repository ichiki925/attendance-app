<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceDetailTest extends TestCase
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


    public function test_admin_can_view_attendance_detail()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.attendance.detail', $this->attendance->id));

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee('08:00');
        $response->assertSee('17:00');
    }


    public function test_admin_cannot_set_start_time_after_end_time()
    {
        $this->actingAs($this->admin);

        $response = $this->put(route('admin.attendance.update', $this->attendance->id), [
            'start_time' => '18:00',
            'end_time' => '08:00',
            'remarks' => 'Test',
        ]);

        $response->assertSessionHasErrors(['start_time']);
    }


    public function test_admin_cannot_set_break_start_time_after_end_time()
    {
        $this->actingAs($this->admin);

        $response = $this->put(route('admin.attendance.update', $this->attendance->id), [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'breaks' => [
                ['break_start' => '18:00', 'break_end' => '19:00']
            ],
            'remarks' => 'Test',
        ]);

        $response->assertSessionHasErrors(['breaks.0.break_start']);
    }


    public function test_admin_cannot_set_break_end_time_after_end_time()
    {
        $this->actingAs($this->admin);

        $response = $this->put(route('admin.attendance.update', $this->attendance->id), [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'breaks' => [
                ['break_start' => '12:00', 'break_end' => '18:00']
            ],
            'remarks' => 'Test',
        ]);

        $response->assertSessionHasErrors(['breaks.0.break_end']);
    }


    public function test_admin_cannot_submit_empty_remarks()
    {
        $this->actingAs($this->admin);

        $response = $this->put(route('admin.attendance.update', $this->attendance->id), [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'remarks' => '',
        ]);

        $response->assertSessionHasErrors(['remarks']);
    }


    public function test_admin_can_update_attendance_successfully()
    {
        $this->actingAs($this->admin);

        $response = $this->put(route('admin.attendance.update', $this->attendance->id), [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'remarks' => 'Updated by admin',
        ]);

        $response->assertRedirect(route('admin.attendance.detail', $this->attendance->id));
        $this->assertDatabaseHas('attendances', [
            'id' => $this->attendance->id,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'remarks' => 'Updated by admin',
        ]);
    }
}
