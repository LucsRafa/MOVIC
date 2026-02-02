<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutItem extends Model
{
    protected $fillable = [
        'workout_day_id',
        'exercise_id',
        'item_order',
        'sets',
        'reps',
        'rest_seconds',
        'notes',
    ];

    public function workoutDay()
    {
        return $this->belongsTo(WorkoutDay::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function checks()
    {
        return $this->hasMany(WorkoutItemCheck::class);
    }
}
