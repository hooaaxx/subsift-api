<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RenewalVerificationToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'token',
        'action',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopeUnused($query)
    {
        return $query->whereNull('used_at');
    }

    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    public static function generateFor(Subscription $subscription, string $action): self
    {
        return self::create([
            'subscription_id' => $subscription->id,
            'token'           => Str::random(64),
            'action'          => $action,
            'expires_at'      => now()->addDays(7),
        ]);
    }
}
