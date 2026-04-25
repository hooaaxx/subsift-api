<?php

namespace App\Actions;

use App\DTOs\NotificationData;
use App\Mail\RenewalAlertMail;
use App\Models\Subscription;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Support\Facades\Mail;

class SendRenewalAlertAction
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notifications
    ) {}

    public function execute(Subscription $subscription): void
    {
        $isTrial = $subscription->is_trial;
        $date    = $isTrial
            ? $subscription->trial_ends_at?->format('M d, Y')
            : $subscription->next_billing_date->format('M d, Y');

        $title   = $isTrial
            ? "{$subscription->name} trial ends on {$date}"
            : "{$subscription->name} renews on {$date}";

        $message = $isTrial
            ? "Your free trial for {$subscription->name} expires on {$date}."
            : "Your {$subscription->name} subscription will renew on {$date} for \${$subscription->cost}.";

        $type = $isTrial ? 'trial_expiry' : 'renewal_alert';

        $inAppData = new NotificationData(
            userId:         $subscription->user_id,
            type:           $type,
            title:          $title,
            message:        $message,
            channel:        'in_app',
            subscriptionId: $subscription->id,
        );

        $emailData = new NotificationData(
            userId:         $subscription->user_id,
            type:           $type,
            title:          $title,
            message:        $message,
            channel:        'email',
            subscriptionId: $subscription->id,
        );

        $this->notifications->create($inAppData->toArray());
        $this->notifications->create($emailData->toArray());

        Mail::send(new RenewalAlertMail($subscription));

        $user = $subscription->user;
        if ($user) {
            (new SendPushNotificationAction())->execute($user, $title, $message);
        }
    }
}
