<?php

namespace App\Models;

use App\Enums\WorkoutSessionStatus;
use Illuminate\Database\Eloquent\Model;

class WorkoutSession extends Model
{
    protected $fillable = [
        'student_id',
        'teacher_id',
        'workout_plan_id',
        'workout_day_id',
        'session_date',
        'status',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'session_date' => 'date',
        'status' => WorkoutSessionStatus::class,
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class);
    }

    public function workoutDay()
    {
        return $this->belongsTo(WorkoutDay::class);
    }

    public function itemChecks()
    {
        return $this->hasMany(WorkoutItemCheck::class);
    }
}
