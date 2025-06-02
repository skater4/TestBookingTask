<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomRequest;
use App\Services\RoomService;
use App\Transformers\RoomResource;
use Cache;
use Illuminate\Http\Request;
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

        $availableRooms = Cache::remember("available_rooms_{$date}", now()->addMinutes(5), function () use ($date) {
            return $this->roomService->getAvailableRooms(
                date: $date
            );
        });

        return RoomResource::collection($availableRooms);
    }
}
