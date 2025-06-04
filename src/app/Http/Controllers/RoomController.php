<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomRequest;
use App\Http\Resources\RoomResource;
use App\Services\RoomService;
use Cache;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoomController extends Controller
{
    public function __construct(
        private readonly RoomService $roomService
    ) {}

    public function index(RoomRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();
        $date = $validated['date'] ?? null;
        $cacheKey = $date ? "available_rooms_$date" : 'available_rooms_default';

        $availableRooms = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($date) {
            return $this->roomService->getAvailableRooms(
                date: $date
            );
        });

        return RoomResource::collection($availableRooms);
    }
}
