<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminUserInformationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->user1 = User::factory()->create([
            'name' => '山田 太郎',
            'email' => 'yamada@example.com',
            'role' => 'user',
        ]);

        $this->user2 = User::factory()->create([
            'name' => '佐藤 花子',
            'email' => 'sato@example.com',
            'role' => 'user',
        ]);

        Attendance::factory()->create([
            'user_id' => $this->user1->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);

        Attendance::factory()->create([
            'user_id' => $this->user2->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);
    }


    public function test_admin_can_view_user_list()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.staff.list'));

        $response->assertStatus(200);
        $response->assertSee('山田 太郎');
        $response->assertSee('yamada@example.com');
        $response->assertSee('佐藤 花子');
        $response->assertSee('sato@example.com');
    }


    public function test_admin_can_view_user_attendance_list()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.staff.attendance.list', ['id' => $this->user1->id]));

        $response->assertStatus(200);
        $response->assertSee($this->user1->name);
        $response->assertSee('08:00');
        $response->assertSee('17:00');
    }


    public function test_admin_can_navigate_to_previous_month()
    {
        $this->actingAs($this->admin);

        $prevMonth = Carbon::now()->subMonth()->format('Y-m');
        $response = $this->get(route('admin.staff.attendance.list', ['id' => $this->user1->id, 'month' => $prevMonth]));

        $response->assertStatus(200);
        $response->assertSee($prevMonth);
    }


    public function test_admin_can_navigate_to_next_month()
    {
        $this->actingAs($this->admin);

        $nextMonth = Carbon::now()->addMonth()->format('Y-m');
        $response = $this->get(route('admin.staff.attendance.list', ['id' => $this->user1->id, 'month' => $nextMonth]));

        $response->assertStatus(200);
        $response->assertSee($nextMonth);
    }


    public function test_admin_can_view_attendance_detail()
    {
        $this->actingAs($this->admin);

        $attendance = Attendance::where('user_id', $this->user1->id)->first();
        $response = $this->get(route('admin.attendance.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee($this->user1->name);
        $response->assertSee('08:00');
        $response->assertSee('17:00');
    }
}
