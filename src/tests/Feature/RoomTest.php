<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_all_rooms_if_no_bookings(): void
    {
        Room::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/rooms');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_excludes_rooms_booked_on_given_date(): void
    {
        $room1 = Room::factory()->create();
        $room2 = Room::factory()->create();

        $date = now()->addDays(2)->toDateString();

        Booking::factory()->create([
            'room_id' => $room1->id,
            'booking_date' => $date,
        ]);

        $response = $this->getJson('/api/v1/rooms?date='.$date);

        $response->assertOk()
            ->assertJsonMissing(['id' => $room1->id])
            ->assertJsonFragment(['id' => $room2->id]);
    }

    public function test_defaults_to_next_seven_days_when_no_date_given(): void
    {
        Room::factory()->create();

        $response = $this->getJson('/api/v1/rooms');

        $response->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'description']]]);
    }
}
