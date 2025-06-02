<?php

namespace App\Repositories;

use App\Models\Booking;

/**
 * @property Booking $model
 */
class BookingRepository extends BaseRepository
{

    protected function getModelClass(): string
    {
        return Booking::class;
    }

    public function existsForRoomAndDate(int $roomId, string $date): bool
    {
        return $this->model
            ->where('room_id', $roomId)
            ->where('booking_date', $date)
            ->exists();
    }
}
