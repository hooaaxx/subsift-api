<?php

namespace App\DTOs;

final class NotificationData
{
    public function __construct(
        public readonly int     $userId,
        public readonly string  $type,
        public readonly string  $title,
        public readonly string  $message,
        public readonly string  $channel,
        public readonly ?int    $subscriptionId = null,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id'         => $this->userId,
            'subscription_id' => $this->subscriptionId,
            'type'            => $this->type,
            'title'           => $this->title,
            'message'         => $this->message,
            'channel'         => $this->channel,
            'sent_at'         => now(),
        ];
    }
}
