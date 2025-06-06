<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'name' => 'Room '.$this->faker->unique()->numberBetween(100, 999),
            'description' => $this->faker->sentence(10),
        ];
    }
}
