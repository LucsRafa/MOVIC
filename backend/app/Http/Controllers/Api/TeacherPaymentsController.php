<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentProvider;
use App\Enums\PaymentStatus;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\TeacherPaymentRegisterRequest;
use App\Models\Payment;
use App\Models\StudentProfile;
use App\Models\TeacherStudent;
use App\Models\User;
use App\Services\ReceiptPdfService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TeacherPaymentsController extends Controller
{
    public function index(): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $studentIds = TeacherStudent::where('teacher_id', $teacher->id)->pluck('student_id');
        $students = User::whereIn('id', $studentIds)->get();

        $month = Carbon::now()->format('Y-m');
        $paidMap = Payment::whereIn('student_id', $studentIds)
            ->where('status', PaymentStatus::Paid->value)
            ->whereRaw("DATE_FORMAT(paid_at, '%Y-%m') = ?", [$month])
            ->get()
            ->groupBy('student_id');

        $latestPaid = Payment::whereIn('student_id', $studentIds)
            ->where('status', PaymentStatus::Paid->value)
            ->orderByDesc('paid_at')
            ->get()
            ->groupBy('student_id');

        $data = $students->map(function ($student) use ($paidMap, $latestPaid) {
            $status = $paidMap->has($student->id) ? 'paid' : 'pending';
            $lastPaidAt = $latestPaid->get($student->id)?->first()?->paid_at?->toDateString();

            return [
                'student_id' => $student->id,
                'name' => $student->name,
                'avatar_url' => $student->avatar_url,
                'amount_cents' => 15000,
                'status' => $status,
                'last_paid_at' => $lastPaidAt,
            ];
        });

        return response()->json([
            'status' => 'success',
            'payments' => $data,
        ]);
    }

    public function register(TeacherPaymentRegisterRequest $request): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $data = $request->validated();
        $linked = TeacherStudent::where('teacher_id', $teacher->id)
            ->where('student_id', $data['student_id'])
            ->exists();

        if (!$linked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aluno nao pertence a este professor.',
            ], 403);
        }

        $payment = Payment::create([
            'student_id' => $data['student_id'],
            'plan_id' => $data['plan_id'] ?? null,
            'provider' => PaymentProvider::Manual,
            'method' => $data['method'],
            'amount_cents' => $data['amount_cents'],
            'currency' => 'BRL',
            'status' => PaymentStatus::Paid->value,
            'paid_at' => Carbon::now(),
            'description' => $data['description'] ?? 'Mensalidade registrada',
            'transaction_id' => $data['transaction_id'] ?? null,
        ]);

        StudentProfile::where('user_id', $data['student_id'])
            ->whereIn('status', [StudentStatus::PendingPayment, StudentStatus::Trial])
            ->update(['status' => StudentStatus::Active]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pagamento registrado com sucesso.',
            'payment' => $payment,
        ], 201);
    }

    public function receiptPdf(int $paymentId, ReceiptPdfService $pdfService)
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $payment = Payment::with('student')->findOrFail($paymentId);
        $linked = TeacherStudent::where('teacher_id', $teacher->id)
            ->where('student_id', $payment->student_id)
            ->exists();

        if (!$linked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pagamento nao pertence a este professor.',
            ], 403);
        }

        $pdf = $pdfService->generate($payment);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="comprovante.pdf"',
        ]);
    }

    public function sendReceipt(int $paymentId, ReceiptPdfService $pdfService): JsonResponse
    {
        $teacher = Auth::user();
        if ($teacher->role !== UserRole::Teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso permitido apenas para professores.',
            ], 403);
        }

        $payment = Payment::with('student')->findOrFail($paymentId);
        $linked = TeacherStudent::where('teacher_id', $teacher->id)
            ->where('student_id', $payment->student_id)
            ->exists();

        if (!$linked) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pagamento nao pertence a este professor.',
            ], 403);
        }

        $email = request()->input('email') ?: $payment->student?->email;
        $pdf = $pdfService->generate($payment);

        Mail::raw('Segue o comprovante de pagamento.', function ($message) use ($email, $pdf) {
            $message->to($email)
                ->subject('Comprovante de Pagamento')
                ->attachData($pdf, 'comprovante.pdf', ['mime' => 'application/pdf']);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Comprovante enviado por email.',
        ]);
    }
}
