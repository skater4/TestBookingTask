<?php

namespace App\Services;

use App\Repositories\RoomRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

readonly class RoomService
{
    public function __construct(
        private RoomRepository $roomRepository
    ) {}

    public function getAvailableRooms(?string $date): Collection
    {
        if ($date) {
            $start = Carbon::parse($date)->startOfDay();
            $end = $start->copy()->endOfDay();
        } else {
            $start = now()->startOfDay();
            $end = now()->addDays(config('project.default_booking_days_request') - 1)->endOfDay();
        }

        return $this->roomRepository->getAvailableBetween($start, $end);
    }
}
