<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use Carbon\Carbon;

class AttendanceEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_error_when_start_time_is_after_end_time()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
        ]);

        $response = $this->put(route('attendance.update', $attendance->id), [
            'start_time' => '18:00',
            'end_time' => '08:00',
            'remarks' => 'Test',
        ]);

        $response->assertSessionHasErrors(['start_time']);
    }

    public function test_validation_error_when_break_start_is_after_end_time()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
        ]);

        $response = $this->put(route('attendance.update', $attendance->id), [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'breaks' => [
                ['start' => '18:00', 'end' => '19:00']
            ],
            'remarks' => 'Test',
        ]);

        $response->assertSessionHasErrors(['breaks.0.start']);
    }

    public function test_validation_error_when_break_end_is_after_end_time()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
        ]);

        $response = $this->put(route('attendance.update', $attendance->id), [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'breaks' => [
                ['start' => '12:00', 'end' => '18:00']
            ],
            'remarks' => 'Test',
        ]);

        $response->assertSessionHasErrors(['breaks.0.end']);
    }

    public function test_validation_error_when_remarks_is_empty()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
        ]);

        $response = $this->put(route('attendance.update', $attendance->id), [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'remarks' => '',
        ]);

        $response->assertSessionHasErrors(['remarks']);
    }

    public function test_correction_request_is_created_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
        ]);

        $response = $this->put(route('attendance.update', $attendance->id), [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'remarks' => 'Correction request',
        ]);

        $response->assertRedirect(route('applications.index', ['status' => 'pending']));
        $this->assertDatabaseHas('attendance_requests', [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'request_status' => 'pending',
        ]);
    }

    public function test_pending_requests_are_displayed_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = AttendanceRequest::factory()->create([
            'user_id' => $user->id,
            'request_status' => 'pending',
        ]);

        $response = $this->get(route('applications.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertSee('承認待ち');
        $response->assertSee($request->reason);
    }

    public function test_approved_requests_are_displayed_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = AttendanceRequest::factory()->create([
            'user_id' => $user->id,
            'request_status' => 'approved',
        ]);

        $response = $this->get(route('applications.index', ['status' => 'approved']));

        $response->assertStatus(200);
        $response->assertSee('承認済み');
        $response->assertSee($request->reason);
    }

    public function test_clicking_detail_button_navigates_to_request_detail_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = AttendanceRequest::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('applications.show', $request->id));

        $response->assertStatus(200);
        $response->assertSee('勤怠詳細');
        $response->assertSee($request->reason);
    }
}

