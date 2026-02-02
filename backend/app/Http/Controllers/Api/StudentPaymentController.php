<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentProvider;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\PaymentManualRequest;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class StudentPaymentController extends Controller
{
    public function index(): JsonResponse
    {
        $payments = Payment::where('student_id', Auth::id())->get();

        return response()->json(['payments' => $payments]);
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
        ]);

        return response()->json(['payment' => $payment], 201);
    }

    public function show(int $id): JsonResponse
    {
        $payment = Payment::where('student_id', Auth::id())->findOrFail($id);

        return response()->json(['payment' => $payment]);
    }

    public function pdf(int $id)
    {
        $payment = Payment::where('student_id', Auth::id())->findOrFail($id);
        $pdf = $this->buildPdf($payment);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="comprovante-'.$payment->id.'.pdf"',
        ]);
    }

    public function email(Request $request, int $id): JsonResponse
    {
        $payment = Payment::where('student_id', Auth::id())->findOrFail($id);
        $to = $request->input('email') ?: Auth::user()->email;

        $pdf = $this->buildPdf($payment);

        Mail::raw('Segue o comprovante de pagamento.', function ($message) use ($to, $pdf, $payment) {
            $message->to($to)
                ->subject('Comprovante de Pagamento')
                ->attachData($pdf, 'comprovante-'.$payment->id.'.pdf', ['mime' => 'application/pdf']);
        });

        return response()->json(['message' => 'Email enviado']);
    }

    private function buildPdf(Payment $payment): string
    {
        $content = "Comprovante de Pagamento\n".
            "Data: ".($payment->paid_at?->format('d/m/Y') ?? '-')."\n".
            "Metodo: {$payment->method}\n".
            "Valor: {$payment->amount_cents} {$payment->currency}\n".
            "Status: {$payment->status}\n";

        $text = str_replace(["\\", "(", ")", "\r"], ["\\\\", "\\(", "\\)", ""], $content);

        return "%PDF-1.4\n".
            "1 0 obj<<>>endobj\n".
            "2 0 obj<< /Length 44 >>stream\n".
            "BT /F1 12 Tf 72 720 Td (".$text.") Tj ET\n".
            "endstream\nendobj\n".
            "3 0 obj<< /Type /Page /Parent 4 0 R /Contents 2 0 R >>endobj\n".
            "4 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj\n".
            "5 0 obj<< /Type /Catalog /Pages 4 0 R >>endobj\n".
            "trailer<< /Root 5 0 R >>\n".
            "%%EOF";
    }
}
