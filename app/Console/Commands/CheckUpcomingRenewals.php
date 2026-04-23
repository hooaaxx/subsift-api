<?php

namespace App\Console\Commands;

use App\Jobs\SendRenewalAlertJob;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckUpcomingRenewals extends Command
{
    protected $signature   = 'subsift:check-renewals';
    protected $description = 'Dispatch renewal alert jobs for subscriptions due in 48–72 hours';

    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        Log::info('═════════════════════════════════════════');
        Log::info('subsift:check-renewals started at ' . now()->toDateTimeString());

        $due = $this->subscriptions->dueForAlert();

        Log::info("Found {$due->count()} subscription(s) due for alert.");

        $due->each(fn ($s) => SendRenewalAlertJob::dispatch($s));

        Log::info('subsift:check-renewals completed.');

        return self::SUCCESS;
    }
}
