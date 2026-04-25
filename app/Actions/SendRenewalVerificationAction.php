<?php

namespace App\Actions;

use App\DTOs\NotificationData;
use App\Mail\RenewalVerificationMail;
use App\Models\RenewalVerificationToken;
use App\Models\Subscription;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Support\Facades\Mail;

class SendRenewalVerificationAction
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notifications,
        private readonly SendPushNotificationAction $push,
    ) {}

    public function execute(Subscription $subscription): void
    {
        $tokens = [
            'renewed'       => RenewalVerificationToken::generateFor($subscription, 'renewed'),
            'cancelled'     => RenewalVerificationToken::generateFor($subscription, 'cancelled'),
            'price_changed' => RenewalVerificationToken::generateFor($subscription, 'price_changed'),
        ];

        Mail::send(new RenewalVerificationMail($subscription, $tokens));

        $this->notifications->create((new NotificationData(
            userId:         $subscription->user_id,
            type:           'renewal_verification',
            title:          "Did {$subscription->name} renew?",
            message:        "Confirm what happened with your {$subscription->name} subscription renewal.",
            channel:        'in_app',
            subscriptionId: $subscription->id,
        ))->toArray());

        $user = $subscription->user;
        if ($user) {
            $this->push->execute(
                $user,
                "Did {$subscription->name} renew?",
                "Confirm what happened with your {$subscription->name} subscription renewal."
            );
        }
    }
}
