<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Request as RequestAlias;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            RequestAlias::METHOD_POST => [
                'room_id' => ['required', 'integer', 'exists:rooms,id'],
                'booking_date' => ['required', 'date', 'after_or_equal:today', 'date_format:Y-m-d'],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'room_id.required' => 'Не указан ID комнаты.',
            'room_id.exists' => 'Комната не найдена.',
            'booking_date.required' => 'Дата бронирования обязательна.',
            'booking_date.date' => 'Неверный формат даты.',
            'booking_date.after_or_equal' => 'Дата бронирования не может быть в прошлом.',
        ];
    }
}
