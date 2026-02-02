<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentProvider;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'student_id',
        'plan_id',
        'provider',
        'method',
        'amount_cents',
        'currency',
        'status',
        'description',
        'paid_at',
        'receipt_url',
        'stripe_payment_intent_id',
        'transaction_id',
    ];

    protected $casts = [
        'provider' => PaymentProvider::class,
        'method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
        'paid_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
