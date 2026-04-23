<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'         => User::factory(),
            'subscription_id' => null,
            'type'            => $this->faker->randomElement(['renewal_alert', 'trial_expiry']),
            'title'           => $this->faker->sentence(4),
            'message'         => $this->faker->sentence(10),
            'channel'         => 'in_app',
            'read_at'         => null,
            'sent_at'         => now(),
        ];
    }

    public function read(): static
    {
        return $this->state(fn () => ['read_at' => now()]);
    }
}
