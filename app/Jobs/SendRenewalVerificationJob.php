<?php

namespace App\Jobs;

use App\Actions\SendPushNotificationAction;
use App\Actions\SendRenewalVerificationAction;
use App\Models\Subscription;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendRenewalVerificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Subscription $subscription) {}

    public function handle(NotificationRepositoryInterface $notifications, SendPushNotificationAction $push): void
    {
        Log::info('─────────────────────────────────────────');
        Log::info("SendRenewalVerificationJob: processing subscription #{$this->subscription->id} ({$this->subscription->name})");

        DB::transaction(function () use ($notifications, $push) {
            $action = new SendRenewalVerificationAction($notifications, $push);
            $action->execute($this->subscription);
        });

        Log::info("SendRenewalVerificationJob: completed for subscription #{$this->subscription->id}");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendRenewalVerificationJob: FAILED for subscription #{$this->subscription->id} — {$exception->getMessage()}");
    }
}
