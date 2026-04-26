<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentProvider;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\PaymentManualRequest;
use App\Models\Payment;
use App\Services\ReceiptPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class StudentPaymentController extends Controller
{
    public function index(): JsonResponse
    {
        $payments = Payment::where('student_id', Auth::id())->get();

        return response()->json([
            'status' => 'success',
            'payments' => $payments,
        ]);
    }

    public function manual(PaymentManualRequest $request): JsonResponse
    {
        $data = $request->validated();

        $payment = Payment::create([
            'student_id' => Auth::id(),
            'plan_id' => $data['plan_id'] ?? null,
            'provider' => PaymentProvider::Manual,
            'method' => $data['method'],
            'amount_cents' => $data['amount_cents'],
            'currency' => 'BRL',
            'status' => 'pending',
            'receipt_url' => $data['receipt_url'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pagamento registrado com sucesso.',
            'payment' => $payment,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $payment = Payment::where('student_id', Auth::id())->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'payment' => $payment,
        ]);
    }

    public function pdf(int $id, ReceiptPdfService $pdfService)
    {
        $payment = Payment::where('student_id', Auth::id())->with('student')->findOrFail($id);
        $pdf = $pdfService->generate($payment);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="comprovante-'.$payment->id.'.pdf"',
        ]);
    }

    public function email(Request $request, int $id, ReceiptPdfService $pdfService): JsonResponse
    {
        $payment = Payment::where('student_id', Auth::id())->with('student')->findOrFail($id);
        $data = $request->validate([
            'email' => ['nullable', 'email'],
        ]);
        $to = $data['email'] ?? Auth::user()->email;

        $pdf = $pdfService->generate($payment);

        Mail::raw('Segue o comprovante de pagamento.', function ($message) use ($to, $pdf, $payment) {
            $message->to($to)
                ->subject('Comprovante de Pagamento')
                ->attachData($pdf, 'comprovante-'.$payment->id.'.pdf', ['mime' => 'application/pdf']);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Email enviado com sucesso.',
        ]);
    }
}
