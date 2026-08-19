<?php

namespace App\Notifications\Channels;

use App\Notifications\Messages\SmsMessage;
use App\Services\SmsGatewayService;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function __construct(private SmsGatewayService $smsGateway)
    {
    }

    public function send(mixed $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);
        if (!$message instanceof SmsMessage) {
            return;
        }

        $phone = $message->to ?? $notifiable->routeNotificationFor('sms', $notification);
        if (!is_string($phone) || trim($phone) === '') {
            return;
        }

        $this->smsGateway->send($phone, $message->content, $message->meta);
    }
}
