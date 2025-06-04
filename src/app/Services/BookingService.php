<?php

namespace App\Services;

use App\Events\BookingCreated;
use App\Exceptions\BookingException;
use App\Models\Booking;
use App\Models\User;
use App\Repositories\BookingRepository;
use Carbon\Carbon;

readonly class BookingService
{
    public function __construct(
        private BookingRepository $bookingRepository
    ) {}

    public function bookRoom(User $user, int $roomId, string $date): Booking
    {
        $bookingDate = Carbon::parse($date)->toDateString();

        if ($this->bookingRepository->existsForRoomAndDate($roomId, $bookingDate)) {
            throw new BookingException('Этот номер уже забронирован на указанную дату.');
        }

        $booking = $this->bookingRepository->create([
            'user_id' => $user->id,
            'room_id' => $roomId,
            'booking_date' => $bookingDate,
        ]);

        $booking->load('room');

        event(new BookingCreated($booking));

        return $booking;
    }
}
