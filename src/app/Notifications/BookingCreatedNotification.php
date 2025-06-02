<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Бронирование подтверждено')
            ->greeting('Здравствуйте!')
            ->line('Ваш номер успешно забронирован.')
            ->line('Дата: '.$this->booking->booking_date->format('Y-m-d'))
            ->line('Номер: '.$this->booking->room->name)
            ->line('Спасибо за использование нашего сервиса!');
    }
}
