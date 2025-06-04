<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Notifications\BookingCreatedNotification;

class SendBookingNotification
{
    public function handle(BookingCreated $event): void
    {
        $booking = $event->booking;
        $booking->user->notify(new BookingCreatedNotification($booking));
    }
}
