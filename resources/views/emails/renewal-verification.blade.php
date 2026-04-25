<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Renewal Verification</title>
</head>
<body style="font-family: sans-serif; background: #f4f4f8; padding: 32px; margin: 0;">
    <div style="max-width: 520px; margin: 0 auto; background: white; border-radius: 12px; padding: 32px; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
        <h2 style="color: #6366f1; margin-top: 0;">SubSift</h2>
        @php
            $symbols = [
                'USD'=>'$','CAD'=>'$','AUD'=>'$','SGD'=>'$','HKD'=>'$','NZD'=>'$','MXN'=>'$','TWD'=>'$',
                'EUR'=>'€','GBP'=>'£','JPY'=>'¥','CNY'=>'¥',
                'PHP'=>'₱','INR'=>'₹','KRW'=>'₩','THB'=>'฿',
                'CHF'=>'Fr ','SEK'=>'kr ','NOK'=>'kr ','DKK'=>'kr ',
                'MYR'=>'RM ','IDR'=>'Rp ','BRL'=>'R$','ZAR'=>'R ','AED'=>'AED ',
            ];
            $sym = $symbols[$subscription->currency] ?? $subscription->currency.' ';
            $cost = $sym . number_format($subscription->cost, 2);
        @endphp
        <p>Hi {{ $subscription->user->name }},</p>
        <p>
            Your <strong>{{ $subscription->name }}</strong> subscription was scheduled to renew
            on <strong>{{ $subscription->next_billing_date->format('M d, Y') }}</strong>
            for <strong>{{ $cost }}</strong>/{{ $subscription->billing_cycle }}.
        </p>
        <p style="font-weight: 600;">What happened?</p>

        <a href="{{ url('/api/v1/renewal/verify/' . $tokens['renewed']->token) }}"
           style="display: inline-block; margin: 6px 6px 6px 0; padding: 12px 20px; background: #6366f1; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px;">
            ✓ Yes, it renewed
        </a>

        <a href="{{ url('/api/v1/renewal/verify/' . $tokens['cancelled']->token) }}"
           style="display: inline-block; margin: 6px 6px 6px 0; padding: 12px 20px; background: #ef4444; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px;">
            ✕ I cancelled it
        </a>

        <a href="{{ url('/api/v1/renewal/verify/' . $tokens['price_changed']->token) }}"
           style="display: inline-block; margin: 6px 0; padding: 12px 20px; background: #f59e0b; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px;">
            ↑ Renewed, but price changed
        </a>

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
        <p style="color: #9ca3af; font-size: 12px; margin: 0;">
            These links expire in 7 days. You're receiving this because SubSift tracks your {{ $subscription->name }} subscription.
        </p>
    </div>
</body>
</html>
