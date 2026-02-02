<?php

namespace App\Services;

use App\Models\WorkoutPlan;
use Illuminate\Support\Facades\DB;

class WorkoutPlanService
{
    public function activatePlan(WorkoutPlan $plan): WorkoutPlan
    {
        return DB::transaction(function () use ($plan) {
            WorkoutPlan::where('student_id', $plan->student_id)
                ->where('id', '!=', $plan->id)
                ->update(['is_active' => false]);

            $plan->update(['is_active' => true]);

            return $plan;
        });
    }
}
