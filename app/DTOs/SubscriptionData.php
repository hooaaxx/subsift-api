<?php

namespace App\DTOs;

final class SubscriptionData
{
    public readonly string $initials;
    public readonly string $color;

    private const PALETTE = [
        '#6366f1', '#8b5cf6', '#06b6d4', '#10b981',
        '#f59e0b', '#ef4444', '#ec4899', '#14b8a6',
    ];

    public function __construct(
        public readonly int     $userId,
        public readonly string  $name,
        public readonly float   $cost,
        public readonly string  $billingCycle,
        public readonly string  $paymentMethod,
        public readonly string  $nextBillingDate,
        public readonly bool    $isTrial = false,
        public readonly ?string $trialEndsAt = null,
        public readonly bool    $alertEnabled = true,
        public readonly ?string $notes = null,
        public readonly string  $currency = 'USD',
    ) {
        $words = explode(' ', trim($name));
        $this->initials = count($words) >= 2
            ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
            : strtoupper(substr($words[0], 0, 2));

        $this->color = self::PALETTE[abs(crc32($name)) % count(self::PALETTE)];
    }

    public function toArray(): array
    {
        return [
            'user_id'           => $this->userId,
            'name'              => $this->name,
            'initials'          => $this->initials,
            'color'             => $this->color,
            'cost'              => $this->cost,
            'currency'          => $this->currency,
            'billing_cycle'     => $this->billingCycle,
            'payment_method'    => $this->paymentMethod,
            'next_billing_date' => $this->nextBillingDate,
            'is_trial'          => $this->isTrial,
            'trial_ends_at'     => $this->trialEndsAt,
            'alert_enabled'     => $this->alertEnabled,
            'notes'             => $this->notes,
        ];
    }

    public static function fromRequest(array $data, int $userId): self
    {
        return new self(
            userId:          $userId,
            name:            $data['name'],
            cost:            (float) $data['cost'],
            billingCycle:    $data['billing_cycle'],
            paymentMethod:   $data['payment_method'],
            nextBillingDate: $data['next_billing_date'],
            isTrial:         (bool) ($data['is_trial'] ?? false),
            trialEndsAt:     $data['trial_ends_at'] ?? null,
            alertEnabled:    (bool) ($data['alert_enabled'] ?? true),
            notes:           $data['notes'] ?? null,
            currency:        $data['currency'] ?? 'USD',
        );
    }
}
