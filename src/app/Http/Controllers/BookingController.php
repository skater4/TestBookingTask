<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Services\BookingService;
use App\Transformers\BookingResource;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService
    ) {}

    public function store(BookingRequest $request): BookingResource
    {
        $user = auth()->user();
        $validated = $request->validated();

        $booking = $this->bookingService->bookRoom(
            user: $user,
            roomId: $validated['room_id'],
            date: $validated['booking_date']
        );

        return new BookingResource($booking);
    }
}
