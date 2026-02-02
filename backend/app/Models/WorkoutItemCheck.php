<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutItemCheck extends Model
{
    protected $fillable = [
        'workout_session_id',
        'workout_item_id',
        'is_checked',
        'checked_at',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function workoutSession()
    {
        return $this->belongsTo(WorkoutSession::class);
    }

    public function workoutItem()
    {
        return $this->belongsTo(WorkoutItem::class);
    }
}
