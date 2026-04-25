<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RenewalVerificationTokenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'token'           => Str::random(64),
            'action'          => $this->faker->randomElement(['renewed', 'cancelled', 'price_changed']),
            'expires_at'      => now()->addDays(7),
            'used_at'         => null,
        ];
    }
}
