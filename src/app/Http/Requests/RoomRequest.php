<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Request as RequestAlias;

class RoomRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            RequestAlias::METHOD_GET => [
                'date' => ['nullable', 'date', 'after_or_equal:today', 'date_format:Y-m-d'],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'date.date' => 'Некорректный формат даты.',
            'date.after_or_equal' => 'Дата должна быть сегодня или позже.',
        ];
    }
}
