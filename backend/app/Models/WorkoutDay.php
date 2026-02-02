<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutDay extends Model
{
    protected $fillable = [
        'workout_plan_id',
        'weekday',
        'title',
        'notes',
    ];

    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class);
    }

    public function items()
    {
        return $this->hasMany(WorkoutItem::class);
    }
}
