<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        $name   = $this->faker->company();
        $words  = explode(' ', $name);
        $initials = count($words) >= 2
            ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
            : strtoupper(substr($words[0], 0, 2));
        $colors = ['#6366f1', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'];

        return [
            'user_id'           => User::factory(),
            'name'              => $name,
            'initials'          => $initials,
            'color'             => $this->faker->randomElement($colors),
            'cost'              => $this->faker->randomFloat(2, 1, 100),
            'currency'          => 'USD',
            'billing_cycle'     => $this->faker->randomElement(['monthly', 'yearly']),
            'payment_method'    => $this->faker->randomElement(['credit_card', 'app_store', 'carrier_billing']),
            'next_billing_date' => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'is_trial'          => false,
            'trial_ends_at'     => null,
            'alert_enabled'     => true,
            'status'            => 'active',
            'notes'             => null,
        ];
    }

    public function trial(): static
    {
        return $this->state(fn () => [
            'is_trial'      => true,
            'trial_ends_at' => now()->addDays(7)->format('Y-m-d'),
        ]);
    }
}
