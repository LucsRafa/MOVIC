<?php

namespace App\Models;

use App\Enums\BillingInterval;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price_cents',
        'currency',
        'billing_interval',
        'trial_days',
        'stripe_price_id',
        'is_active',
    ];

    protected $casts = [
        'billing_interval' => BillingInterval::class,
        'is_active' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
