<?php

namespace App\Console\Commands;

use App\Jobs\SendRenewalVerificationJob;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckRenewalVerifications extends Command
{
    protected $signature   = 'subsift:check-renewal-verifications';
    protected $description = 'Dispatch renewal verification emails for subscriptions that renewed yesterday';

    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        Log::info('═════════════════════════════════════════');
        Log::info('subsift:check-renewal-verifications started at ' . now()->toDateTimeString());

        $due = $this->subscriptions->dueForVerification();

        Log::info("Found {$due->count()} subscription(s) due for verification.");

        $due->each(fn ($s) => SendRenewalVerificationJob::dispatch($s));

        Log::info('subsift:check-renewal-verifications completed.');

        return self::SUCCESS;
    }
}
