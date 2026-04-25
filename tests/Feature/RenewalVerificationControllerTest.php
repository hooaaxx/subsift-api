<?php

namespace Tests\Feature;

use App\Models\RenewalVerificationToken;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenewalVerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeToken(array $subAttrs = [], array $tokenAttrs = []): RenewalVerificationToken
    {
        $user = User::factory()->create();
        $sub  = Subscription::factory()->create(array_merge(['user_id' => $user->id], $subAttrs));
        return RenewalVerificationToken::factory()->create(array_merge(['subscription_id' => $sub->id], $tokenAttrs));
    }

    public function test_verify_renewed_advances_monthly_billing_date(): void
    {
        $token = $this->makeToken(
            ['billing_cycle' => 'monthly', 'next_billing_date' => Carbon::yesterday()->toDateString()],
            ['action' => 'renewed', 'expires_at' => now()->addDays(7)]
        );

        $this->get("/api/v1/renewal/verify/{$token->token}")->assertRedirect();

        $this->assertEquals(
            Carbon::yesterday()->addMonth()->toDateString(),
            $token->subscription->fresh()->next_billing_date->toDateString()
        );
        $this->assertNotNull($token->fresh()->used_at);
    }

    public function test_verify_renewed_advances_yearly_billing_date(): void
    {
        $token = $this->makeToken(
            ['billing_cycle' => 'yearly', 'next_billing_date' => Carbon::yesterday()->toDateString()],
            ['action' => 'renewed', 'expires_at' => now()->addDays(7)]
        );

        $this->get("/api/v1/renewal/verify/{$token->token}")->assertRedirect();

        $this->assertEquals(
            Carbon::yesterday()->addYear()->toDateString(),
            $token->subscription->fresh()->next_billing_date->toDateString()
        );
    }

    public function test_verify_cancelled_marks_subscription_cancelled(): void
    {
        $token = $this->makeToken([], ['action' => 'cancelled', 'expires_at' => now()->addDays(7)]);

        $this->get("/api/v1/renewal/verify/{$token->token}")->assertRedirect();

        $this->assertEquals('cancelled', $token->subscription->fresh()->status);
        $this->assertNotNull($token->fresh()->used_at);
    }

    public function test_verify_price_changed_redirects_to_price_form_without_marking_used(): void
    {
        $token = $this->makeToken([], ['action' => 'price_changed', 'expires_at' => now()->addDays(7)]);

        $response = $this->get("/api/v1/renewal/verify/{$token->token}");

        $response->assertRedirect();
        $this->assertNull($token->fresh()->used_at);
    }

    public function test_verify_expired_token_redirects_to_confirmed_with_expired_flag(): void
    {
        $token = $this->makeToken([], ['action' => 'renewed', 'expires_at' => now()->subDay()]);

        $response = $this->get("/api/v1/renewal/verify/{$token->token}");

        $response->assertRedirect();
        $this->assertStringContainsString('expired=1', $response->headers->get('Location'));
    }

    public function test_verify_used_token_redirects_to_confirmed_with_expired_flag(): void
    {
        $token = $this->makeToken([], ['action' => 'renewed', 'used_at' => now()->subHour(), 'expires_at' => now()->addDays(7)]);

        $response = $this->get("/api/v1/renewal/verify/{$token->token}");

        $response->assertRedirect();
        $this->assertStringContainsString('expired=1', $response->headers->get('Location'));
    }

    public function test_verify_unknown_token_returns_404(): void
    {
        $this->get('/api/v1/renewal/verify/nonexistenttoken123')->assertStatus(404);
    }

    public function test_update_price_advances_billing_date_and_updates_cost(): void
    {
        $token = $this->makeToken(
            ['billing_cycle' => 'monthly', 'next_billing_date' => Carbon::yesterday()->toDateString(), 'cost' => 9.99],
            ['action' => 'price_changed', 'expires_at' => now()->addDays(7)]
        );

        $response = $this->postJson("/api/v1/renewal/price/{$token->token}", ['new_cost' => 12.99]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals(12.99, $token->subscription->fresh()->cost);
        $this->assertNotNull($token->fresh()->used_at);
    }

    public function test_update_price_returns_422_for_expired_token(): void
    {
        $token = $this->makeToken([], ['action' => 'price_changed', 'expires_at' => now()->subDay()]);

        $this->postJson("/api/v1/renewal/price/{$token->token}", ['new_cost' => 12.99])
             ->assertStatus(422);
    }

    public function test_update_price_returns_422_for_wrong_action_token(): void
    {
        $token = $this->makeToken([], ['action' => 'renewed', 'expires_at' => now()->addDays(7)]);

        $this->postJson("/api/v1/renewal/price/{$token->token}", ['new_cost' => 12.99])
             ->assertStatus(422);
    }
}
