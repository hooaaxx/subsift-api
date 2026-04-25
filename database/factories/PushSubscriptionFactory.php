<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PushSubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'endpoint'   => 'https://fcm.googleapis.com/fcm/send/' . $this->faker->uuid(),
            'public_key' => base64_encode($this->faker->sha256()),
            'auth_token' => base64_encode($this->faker->sha256()),
        ];
    }
}
