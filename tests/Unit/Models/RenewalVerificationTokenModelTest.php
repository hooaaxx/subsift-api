<?php

namespace Tests\Unit\Models;

use App\Models\RenewalVerificationToken;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenewalVerificationTokenModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_unused_scope_excludes_tokens_with_used_at(): void
    {
        $user = User::factory()->create();
        $sub  = Subscription::factory()->create(['user_id' => $user->id]);

        RenewalVerificationToken::factory()->create(['subscription_id' => $sub->id, 'used_at' => null]);
        RenewalVerificationToken::factory()->create(['subscription_id' => $sub->id, 'used_at' => now()]);

        $this->assertCount(1, RenewalVerificationToken::unused()->get());
    }

    public function test_not_expired_scope_excludes_past_expiry(): void
    {
        $user = User::factory()->create();
        $sub  = Subscription::factory()->create(['user_id' => $user->id]);

        RenewalVerificationToken::factory()->create(['subscription_id' => $sub->id, 'expires_at' => now()->addDays(7)]);
        RenewalVerificationToken::factory()->create(['subscription_id' => $sub->id, 'expires_at' => now()->subDay()]);

        $this->assertCount(1, RenewalVerificationToken::notExpired()->get());
    }

    public function test_generate_for_creates_token_with_correct_fields(): void
    {
        $user = User::factory()->create();
        $sub  = Subscription::factory()->create(['user_id' => $user->id]);

        $token = RenewalVerificationToken::generateFor($sub, 'renewed');

        $this->assertDatabaseHas('renewal_verification_tokens', [
            'subscription_id' => $sub->id,
            'action'          => 'renewed',
        ]);
        $this->assertNotNull($token->token);
        $this->assertNull($token->used_at);
        $this->assertTrue($token->expires_at->isFuture());
    }

    public function test_subscription_active_scope_filters_cancelled(): void
    {
        $user = User::factory()->create();
        Subscription::factory()->create(['user_id' => $user->id, 'status' => 'active']);
        Subscription::factory()->create(['user_id' => $user->id, 'status' => 'cancelled']);

        $this->assertCount(1, Subscription::active()->where('user_id', $user->id)->get());
    }
}
