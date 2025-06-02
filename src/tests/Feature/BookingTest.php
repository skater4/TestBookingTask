<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_booking(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $date = now()->addDay()->startOfDay();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/bookings', [
            'room_id' => $room->id,
            'booking_date' => $date->toDateString(),
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.room_id', $room->id)
            ->assertJsonPath('data.booking_date', $date->toDateString());

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'room_id' => $room->id,
            'booking_date' => $date->toDateTimeString(),
        ]);
    }

    public function test_prevents_double_booking(): void
    {
        $room = Room::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $date = now()->addDay()->startOfDay();

        Booking::factory()->create([
            'room_id' => $room->id,
            'user_id' => $user1->id,
            'booking_date' => $date->toDateTimeString(),
        ]);

        Sanctum::actingAs($user2);

        $response = $this->postJson('/api/v1/bookings', [
            'room_id' => $room->id,
            'booking_date' => $date->toDateString(),
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Этот номер уже забронирован на указанную дату.');
    }

    public function test_requires_authentication(): void
    {
        $room = Room::factory()->create();
        $date = now()->addDay()->toDateString();

        $response = $this->postJson('/api/v1/bookings', [
            'room_id' => $room->id,
            'booking_date' => $date,
        ]);

        $response->assertUnauthorized();
    }
}
