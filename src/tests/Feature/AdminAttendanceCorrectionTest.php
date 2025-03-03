<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use Carbon\Carbon;

class AdminAttendanceCorrectionTest extends TestCase
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

        $this->pendingRequest = AttendanceRequest::factory()->create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'reason' => '修正が必要です',
            'request_status' => 'pending',
        ]);

        $this->approvedRequest = AttendanceRequest::factory()->create([
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->user->id,
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
            'reason' => '承認されました',
            'request_status' => 'approved',
        ]);
    }


    public function test_admin_can_view_pending_corrections()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.applications.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertSee('修正が必要です');
        $response->assertSee('承認待ち');
    }


    public function test_admin_can_view_approved_corrections()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.applications.index', ['status' => 'approved']));

        $response->assertStatus(200);
        $response->assertSee('承認されました');
        $response->assertSee('承認済み');
    }


    public function test_admin_can_view_correction_detail()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.application.detail', ['id' => $this->pendingRequest->id]));

        $response->assertStatus(200);
        $response->assertSee('修正が必要です');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }


    public function test_admin_can_approve_correction()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.application.approve', ['id' => $this->pendingRequest->id]));

        $response->assertRedirect(route('admin.applications.index'));
        $this->assertDatabaseHas('attendance_requests', [
            'id' => $this->pendingRequest->id,
            'request_status' => 'approved',
        ]);
        $this->assertDatabaseHas('attendances', [
            'id' => $this->attendance->id,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);
    }
}