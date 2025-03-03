<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;

class DateTimeRetrievalTest extends TestCase
{
    use RefreshDatabase;


    public function test_attendance_register_displays_correct_datetime()
    {

        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $now = Carbon::now();

        $response = $this->get('/attendance/register');

        $response->assertStatus(200);

        $response->assertSee($now->locale('ja')->translatedFormat('Y年n月j日(D)'));

        $response->assertSee($now->format('H:i'));
    }
}

