<?php

namespace App\Models;

use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'trial_ends_at',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'status' => StudentStatus::class,
        'trial_ends_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
