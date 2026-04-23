<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'type'            => $this->type,
            'title'           => $this->title,
            'message'         => $this->message,
            'channel'         => $this->channel,
            'read_at'         => $this->read_at?->toISOString(),
            'sent_at'         => $this->sent_at?->toISOString(),
            'subscription_id' => $this->subscription_id,
            'created_at'      => $this->created_at?->toISOString(),
        ];
    }
}
