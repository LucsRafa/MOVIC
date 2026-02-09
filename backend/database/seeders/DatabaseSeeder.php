<?php

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Enums\PaymentProvider;
use App\Enums\PaymentStatus;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Enums\WorkoutSessionStatus;
use App\Models\Exercise;
use App\Models\Payment;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\TeacherStudent;
use App\Models\User;
use App\Models\WorkoutDay;
use App\Models\WorkoutItem;
use App\Models\WorkoutItemCheck;
use App\Models\WorkoutPlan;
use App\Models\WorkoutSession;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'role' => UserRole::Teacher,
                'password' => bcrypt('password'),
            ]
        );

        TeacherProfile::firstOrCreate(['user_id' => $teacher->id]);

        $studentsData = [
            ['name' => 'Joao Silva', 'email' => 'joao@example.com', 'status' => StudentStatus::Active],
            ['name' => 'Maria Santos', 'email' => 'maria@example.com', 'status' => StudentStatus::Trial],
            ['name' => 'Pedro Costa', 'email' => 'pedro@example.com', 'status' => StudentStatus::PendingPayment],
            ['name' => 'Ana Paula', 'email' => 'ana@example.com', 'status' => StudentStatus::Inactive],
        ];

        $students = [];
        foreach ($studentsData as $data) {
            $student = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => UserRole::Student,
                    'password' => bcrypt('password'),
                ]
            );

            StudentProfile::updateOrCreate(
                ['user_id' => $student->id],
                [
                    'status' => $data['status'],
                    'trial_ends_at' => $data['status'] === StudentStatus::Trial
                        ? Carbon::now()->addDays(7)
                        : null,
                    'approved_at' => Carbon::now(),
                ]
            );

            TeacherStudent::updateOrCreate(
                ['student_id' => $student->id],
                ['teacher_id' => $teacher->id]
            );

            $students[] = $student;
        }

        $requested = [
            ['name' => 'Carlos Mendes', 'email' => 'carlos@example.com'],
            ['name' => 'Juliana Alves', 'email' => 'juliana@example.com'],
        ];

        foreach ($requested as $data) {
            $student = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => UserRole::Student,
                    'password' => bcrypt('password'),
                ]
            );

            StudentProfile::updateOrCreate(
                ['user_id' => $student->id],
                ['status' => StudentStatus::Requested]
            );
        }

        $exerciseNames = [
            ['Supino Reto', 'Peito'],
            ['Supino Inclinado', 'Peito'],
            ['Crucifixo', 'Peito'],
            ['Triceps Pulley', 'Triceps'],
            ['Agachamento Livre', 'Pernas'],
            ['Remada Curvada', 'Costas'],
        ];

        $exercises = [];
        foreach ($exerciseNames as $entry) {
            $exercise = Exercise::firstOrCreate(
                ['teacher_id' => $teacher->id, 'name' => $entry[0]],
                [
                    'category' => $entry[1],
                    'video_url' => 'https://example.com/video/' . Str::slug($entry[0]),
                    'is_active' => true,
                ]
            );
            $exercises[] = $exercise;
        }

        foreach ($students as $student) {
            if ($student->studentProfile?->status === StudentStatus::Inactive) {
                continue;
            }

            $plan = WorkoutPlan::firstOrCreate(
                ['student_id' => $student->id, 'is_active' => true],
                ['teacher_id' => $teacher->id, 'title' => 'Plano do aluno']
            );

            $weekdays = [1, 3, 5];
            $days = [];
            foreach ($weekdays as $weekday) {
                $day = WorkoutDay::firstOrCreate(
                    ['workout_plan_id' => $plan->id, 'weekday' => $weekday],
                    ['title' => 'Treino ' . chr(64 + $weekday), 'notes' => 'Treino do dia']
                );
                $days[] = $day;
            }

            foreach ($days as $day) {
                foreach (array_slice($exercises, 0, 4) as $index => $exercise) {
                    WorkoutItem::firstOrCreate(
                        ['workout_day_id' => $day->id, 'exercise_id' => $exercise->id, 'item_order' => $index + 1],
                        ['sets' => 4, 'reps' => '12', 'rest_seconds' => 60]
                    );
                }
            }

            $sessionDate = Carbon::now()->subDays(1);
            $session = WorkoutSession::firstOrCreate(
                ['student_id' => $student->id, 'session_date' => $sessionDate->toDateString()],
                [
                    'teacher_id' => $teacher->id,
                    'workout_plan_id' => $plan->id,
                    'workout_day_id' => $days[0]->id,
                    'status' => WorkoutSessionStatus::Completed,
                    'started_at' => $sessionDate->copy()->setTime(9, 0),
                    'finished_at' => $sessionDate->copy()->setTime(10, 0),
                ]
            );

            $items = $days[0]->items;
            foreach ($items as $item) {
                WorkoutItemCheck::firstOrCreate(
                    ['workout_session_id' => $session->id, 'workout_item_id' => $item->id],
                    ['is_checked' => true, 'checked_at' => $sessionDate->copy()->setTime(9, 30)]
                );
            }

            if ($student->studentProfile?->status === StudentStatus::Active) {
                Payment::create([
                    'student_id' => $student->id,
                    'provider' => PaymentProvider::Manual,
                    'method' => PaymentMethod::Pix,
                    'amount_cents' => 15000,
                    'currency' => 'BRL',
                    'status' => PaymentStatus::Paid,
                    'paid_at' => Carbon::now()->startOfMonth()->addDays(2),
                    'description' => 'Mensalidade',
                    'transaction_id' => 'TX-' . Str::upper(Str::random(6)),
                ]);
            }
        }
    }
}
