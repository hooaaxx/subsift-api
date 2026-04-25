<?php

namespace App\Actions;

use App\Models\PushSubscription;
use App\Models\User;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class SendPushNotificationAction
{
    public function execute(User $user, string $title, string $message, string $url = '/dashboard'): void
    {
        if (! config('services.vapid.public_key')) {
            return;
        }

        $subscriptions = PushSubscription::where('user_id', $user->id)->get();
        if ($subscriptions->isEmpty()) {
            return;
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject'    => config('services.vapid.subject'),
                'publicKey'  => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ]);

        $payload = json_encode(['title' => $title, 'message' => $message, 'url' => $url], JSON_THROW_ON_ERROR);

        // Build id map before queuing so expired cleanup uses PK, not an unscoped endpoint query
        $endpointToId = $subscriptions->pluck('id', 'endpoint')->all();

        foreach ($subscriptions as $sub) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint'        => $sub->endpoint,
                    'contentEncoding' => 'aes128gcm',
                    'publicKey'       => $sub->public_key,
                    'authToken'       => $sub->auth_token,
                ]),
                $payload
            );
        }

        foreach ($webPush->flush() as $report) {
            // Only 410 Gone is the definitive "unsubscribed" signal per RFC 8030; 404 may be transient.
            // The library's isSubscriptionExpired() matches both 404 and 410 — we check status directly
            // to avoid deleting subscriptions on transient errors.
            if ($report->getResponse()?->getStatusCode() === 410) {
                $id = $endpointToId[$report->getEndpoint()] ?? null;
                if ($id) {
                    PushSubscription::destroy($id);
                }
            }
        }
    }
}
