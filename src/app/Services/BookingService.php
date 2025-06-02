<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingCreatedNotification;
use App\Repositories\BookingRepository;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BookingService
{
    public function __construct(
        private readonly BookingRepository $bookingRepository
    ) {}

    public function bookRoom(User $user, int $roomId, string $date): Booking
    {
        $bookingDate = Carbon::parse($date)->toDateString();

        if ($this->bookingRepository->existsForRoomAndDate($roomId, $bookingDate)) {
            abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, 'Этот номер уже забронирован на указанную дату.');
        }

        $booking = $this->bookingRepository->create([
            'user_id' => $user->id,
            'room_id' => $roomId,
            'booking_date' => $bookingDate,
        ]);

        $booking->load('room');

        $user->notify(new BookingCreatedNotification($booking));

        return $booking;
    }
}
