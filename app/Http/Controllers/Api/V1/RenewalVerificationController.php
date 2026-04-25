<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\RenewalVerificationToken;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RenewalVerificationController extends Controller
{
    private string $frontendUrl;

    public function __construct()
    {
        $this->frontendUrl = config('app.frontend_url');
    }

    public function verify(string $token): RedirectResponse
    {
        $record = RenewalVerificationToken::where('token', $token)
            ->with('subscription')
            ->first();

        if (! $record) {
            abort(404);
        }

        if ($record->used_at || $record->expires_at->isPast()) {
            return redirect("{$this->frontendUrl}/renewal/confirmed?expired=1");
        }

        $subscription = $record->subscription;

        if ($record->action === 'price_changed') {
            return redirect("{$this->frontendUrl}/renewal/price-changed?token={$token}");
        }

        if ($record->action === 'renewed') {
            $subscription->update([
                'next_billing_date' => $this->advanceBillingDate($subscription),
            ]);
        } elseif ($record->action === 'cancelled') {
            $subscription->update(['status' => 'cancelled']);
        }

        $record->update(['used_at' => now()]);

        $name = urlencode($subscription->name);
        return redirect("{$this->frontendUrl}/renewal/confirmed?name={$name}&action={$record->action}");
    }

    public function updatePrice(Request $request, string $token): JsonResponse
    {
        $request->validate(['new_cost' => ['required', 'numeric', 'min:0.01']]);

        $record = RenewalVerificationToken::where('token', $token)
            ->where('action', 'price_changed')
            ->unused()
            ->notExpired()
            ->with('subscription')
            ->first();

        if (! $record) {
            return response()->json(['message' => 'This link has expired or has already been used.'], 422);
        }

        $subscription = $record->subscription;
        $subscription->update([
            'cost'              => $request->new_cost,
            'next_billing_date' => $this->advanceBillingDate($subscription),
        ]);
        $record->update(['used_at' => now()]);

        return response()->json(['success' => true, 'name' => $subscription->name]);
    }

    private function advanceBillingDate($subscription): string
    {
        $date = Carbon::parse($subscription->next_billing_date);
        return $subscription->billing_cycle === 'monthly'
            ? $date->addMonth()->toDateString()
            : $date->addYear()->toDateString();
    }
}
