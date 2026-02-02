<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StudentDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function show(StudentDashboardService $service): JsonResponse
    {
        $data = $service->build(Auth::id());

        return response()->json($data);
    }
}
