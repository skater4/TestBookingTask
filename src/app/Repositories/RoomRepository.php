<?php

namespace App\Repositories;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property Room $model
 */
class RoomRepository extends BaseRepository
{
    protected function getModelClass(): string
    {
        return Room::class;
    }

    public function getAvailableBetween(Carbon $start, Carbon $end): Collection
    {
        $days = $start->diffInDays($end) + 1;

        return $this->model
            ->with('bookings')
            ->whereRaw('(
            SELECT COUNT(*) FROM bookings
            WHERE bookings.room_id = rooms.id
            AND booking_date BETWEEN ? AND ?
        ) < ?', [
                $start->toDateString(),
                $end->toDateString(),
                $days,
            ])
            ->orderBy('rooms.id')
            ->get();
    }
}
