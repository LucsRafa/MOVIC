<?php

namespace App\Services;

use App\Models\Payment;

class ReceiptPdfService
{
    public function generate(Payment $payment, array $lines = []): string
    {
        $contentLines = array_merge([
            'Comprovante de Pagamento',
            'Aluno: ' . ($payment->student?->name ?? ''),
            'Data: ' . ($payment->paid_at?->format('d/m/Y') ?? ''),
            'Método: ' . ($payment->method?->value ?? ''),
            'Valor: R$ ' . number_format($payment->amount_cents / 100, 2, ',', '.'),
            'Status: ' . ($payment->status?->value ?? ''),
            'Transação: ' . ($payment->transaction_id ?? '-'),
        ], $lines);

        $y = 720;
        $text = "";
        foreach ($contentLines as $line) {
            $safe = str_replace(['(', ')'], ['\\(', '\\)'], $line);
            $text .= "BT /F1 12 Tf 72 {$y} Td ({$safe}) Tj ET\n";
            $y -= 18;
        }

        $objects = [];
        $objects[] = "1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj";
        $objects[] = "2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj";
        $objects[] = "3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>endobj";
        $objects[] = "4 0 obj<< /Length " . strlen($text) . " >>stream\n{$text}endstream\nendobj";
        $objects[] = "5 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj . "\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 " . count($offsets) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i < count($offsets); $i++) {
            $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }
        $pdf .= "trailer<< /Size " . count($offsets) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefPosition}\n%%EOF";

        return $pdf;
    }
}
