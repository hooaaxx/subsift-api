<?php

namespace App\Jobs;

use App\Actions\SendRenewalAlertAction;
use App\Models\Subscription;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendRenewalAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Subscription $subscription) {}

    public function handle(NotificationRepositoryInterface $notifications): void
    {
        Log::info('─────────────────────────────────────────');
        Log::info("SendRenewalAlertJob: processing subscription #{$this->subscription->id} ({$this->subscription->name})");

        DB::transaction(function () use ($notifications) {
            $action = new SendRenewalAlertAction($notifications);
            $action->execute($this->subscription);
        });

        Log::info("SendRenewalAlertJob: completed for subscription #{$this->subscription->id}");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendRenewalAlertJob: FAILED for subscription #{$this->subscription->id} — {$exception->getMessage()}");
    }
}
