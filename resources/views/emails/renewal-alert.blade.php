<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Renewal Reminder</title>
</head>
<body style="font-family: sans-serif; background: #f4f4f8; padding: 32px; margin: 0;">
    <div style="max-width: 520px; margin: 0 auto; background: white; border-radius: 12px; padding: 32px; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
        <h2 style="color: #6366f1; margin-top: 0;">SubSift Reminder</h2>
        <p>Hi {{ $subscription->user->name }},</p>
        <p>
            Your subscription to <strong>{{ $subscription->name }}</strong> is scheduled to
            {{ $subscription->is_trial ? 'expire' : 'renew' }} on
            <strong>
                {{ $subscription->is_trial
                    ? $subscription->trial_ends_at?->format('M d, Y')
                    : $subscription->next_billing_date->format('M d, Y') }}
            </strong>.
        </p>
        <div style="background: #f8f7ff; border-radius: 8px; padding: 16px; margin: 16px 0;">
            <p style="margin: 0; color: #374151;">
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
                Cost: <strong>{{ $cost }}</strong>
                &mdash; {{ ucfirst($subscription->billing_cycle) }}
            </p>
        </div>
        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
        <p style="color: #9ca3af; font-size: 12px; margin: 0;">
            You're receiving this because alerts are enabled for this subscription in SubSift.
        </p>
    </div>
</body>
</html>
