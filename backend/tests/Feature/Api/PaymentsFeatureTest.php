<?php

namespace Tests\Feature\Api;

use App\Enums\PaymentStatus;
use App\Enums\StudentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesMovicData;
use Tests\TestCase;

class PaymentsFeatureTest extends TestCase
{
    use CreatesMovicData;
    use RefreshDatabase;

    public function test_teacher_can_register_payment_for_linked_student_and_activate_profile(): void
    {
        $teacher = $this->actAsApiUser($this->createTeacher());
        $student = $this->createStudent([], StudentStatus::PendingPayment);
        $plan = $this->createSubscriptionPlan();

        $this->linkTeacherAndStudent($teacher, $student);

        $response = $this->postJson('/api/teacher/payments/register', [
            'student_id' => $student->id,
            'plan_id' => $plan->id,
            'amount_cents' => 15000,
            'method' => 'pix',
            'description' => 'Monthly fee',
            'transaction_id' => 'txn-100',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('payment.student_id', $student->id)
            ->assertJsonPath('payment.status', PaymentStatus::Paid->value);

        $this->assertDatabaseHas('payments', [
            'student_id' => $student->id,
            'plan_id' => $plan->id,
            'status' => PaymentStatus::Paid->value,
        ]);

        $this->assertDatabaseHas('student_profiles', [
            'user_id' => $student->id,
            'status' => StudentStatus::Active->value,
        ]);
    }

    public function test_student_can_create_manual_payment_and_only_access_their_own_payment(): void
    {
        $student = $this->actAsApiUser($this->createStudent([
            'email' => 'student-payments@example.com',
        ]));
        $otherStudent = $this->createStudent([
            'email' => 'other-student@example.com',
        ]);

        $manualResponse = $this->postJson('/api/student/payments/manual', [
            'amount_cents' => 9800,
            'method' => 'pix',
            'receipt_url' => 'https://example.com/receipt.png',
            'description' => 'Manual upload',
        ]);

        $manualResponse->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('payment.student_id', $student->id)
            ->assertJsonPath('payment.status', 'pending');

        $ownPaymentId = $manualResponse->json('payment.id');
        $otherPayment = $this->createPayment($otherStudent);

        $this->getJson("/api/student/payments/{$ownPaymentId}")
            ->assertOk()
            ->assertJsonPath('payment.id', $ownPaymentId);

        $this->getJson("/api/student/payments/{$otherPayment->id}")
            ->assertStatus(404);
    }
}
