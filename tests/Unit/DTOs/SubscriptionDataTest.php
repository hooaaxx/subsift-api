<?php

namespace Tests\Unit\DTOs;

use App\DTOs\SubscriptionData;
use Tests\TestCase;

class SubscriptionDataTest extends TestCase
{
    public function test_initials_are_derived_from_name(): void
    {
        $dto = SubscriptionData::fromRequest([
            'name'              => 'Netflix Premium',
            'cost'              => 15.99,
            'billing_cycle'     => 'monthly',
            'payment_method'    => 'credit_card',
            'next_billing_date' => '2026-05-01',
        ], userId: 1);

        $this->assertEquals('NP', $dto->initials);
    }

    public function test_color_is_deterministic_for_same_name(): void
    {
        $dto1 = SubscriptionData::fromRequest([
            'name'              => 'Spotify',
            'cost'              => 9.99,
            'billing_cycle'     => 'monthly',
            'payment_method'    => 'credit_card',
            'next_billing_date' => '2026-05-01',
        ], userId: 1);

        $dto2 = SubscriptionData::fromRequest([
            'name'              => 'Spotify',
            'cost'              => 9.99,
            'billing_cycle'     => 'monthly',
            'payment_method'    => 'credit_card',
            'next_billing_date' => '2026-06-01',
        ], userId: 2);

        $this->assertEquals($dto1->color, $dto2->color);
    }

    public function test_to_array_contains_all_fields(): void
    {
        $dto = SubscriptionData::fromRequest([
            'name'              => 'GitHub Pro',
            'cost'              => 4.00,
            'billing_cycle'     => 'monthly',
            'payment_method'    => 'credit_card',
            'next_billing_date' => '2026-05-01',
        ], userId: 1);

        $array = $dto->toArray();

        $this->assertArrayHasKey('initials', $array);
        $this->assertArrayHasKey('color', $array);
        $this->assertArrayHasKey('user_id', $array);
    }
}
