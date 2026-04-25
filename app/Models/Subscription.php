<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'initials',
        'color',
        'cost',
        'currency',
        'billing_cycle',
        'payment_method',
        'next_billing_date',
        'is_trial',
        'trial_ends_at',
        'alert_enabled',
        'status',
        'notes',
    ];

    protected $casts = [
        'cost'              => 'float',
        'is_trial'          => 'boolean',
        'alert_enabled'     => 'boolean',
        'next_billing_date' => 'date',
        'trial_ends_at'     => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function renewalVerificationTokens(): HasMany
    {
        return $this->hasMany(RenewalVerificationToken::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
