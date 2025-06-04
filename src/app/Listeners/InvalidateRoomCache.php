<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use Cache;

class InvalidateRoomCache
{
    public function handle(BookingCreated $event): void
    {
        $date = $event->booking->booking_date->toDateString();

        Cache::forget("available_rooms_{$date}");
    }
}
