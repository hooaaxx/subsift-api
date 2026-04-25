<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'initials'          => $this->initials,
            'color'             => $this->color,
            'cost'              => $this->cost,
            'currency'          => $this->currency,
            'billing_cycle'     => $this->billing_cycle,
            'payment_method'    => $this->payment_method,
            'next_billing_date' => $this->next_billing_date?->toDateString(),
            'is_trial'          => $this->is_trial,
            'trial_ends_at'     => $this->trial_ends_at?->toDateString(),
            'status'            => $this->status,
            'alert_enabled'     => $this->alert_enabled,
            'notes'             => $this->notes,
            'created_at'        => $this->created_at?->toISOString(),
        ];
    }
}
