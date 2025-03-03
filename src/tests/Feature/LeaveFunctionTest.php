<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class LeaveFunctionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_clock_out()
    {
        $user = User::factory()->create(['role' => 'user']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->subHours(5)->format('H:i'),
            'end_time' => null,
            'status' => 'working',
        ]);

        $this->actingAs($user);

        $response = $this->post('/attendance/register', [
            'status' => 'completed',
        ]);

        $response->assertRedirect('/attendance/register');

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'end_time' => Carbon::now()->format('H:i:s'),
            'status' => 'completed',
        ]);
    }


    public function test_admin_can_see_clock_out_time()
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

        $this->actingAs($admin);

        $response = $this->get('/admin/attendance/list');
        $response->assertStatus(200);
        $response->assertSee($attendance->end_time);
    }
}
