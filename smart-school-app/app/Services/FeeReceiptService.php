<?php

namespace App\Services;

use App\Models\FeesTransaction;
use App\Models\Student;
use Illuminate\Http\Response;

/**
 * Fee Receipt Service
 * 
 * Prompt 427: Create Fee Receipt Service
 * 
 * Generates fee payment receipts for students.
 * Supports individual and bulk receipt generation.
 * 
 * Features:
 * - Generate individual fee receipt
 * - Generate bulk receipts for date range
 * - Include payment details and breakdown
 * - Support multiple payment methods
 * - Include school branding
 */
class FeeReceiptService
{
    protected PdfReportService $pdfService;

    public function __construct(PdfReportService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Generate receipt for a single transaction.
     *
     * @param int $transactionId
     * @return Response
     */
    public function generateReceipt(int $transactionId): Response
    {
        $transaction = FeesTransaction::with([
            'student.user',
            'student.schoolClass',
            'student.section',
            'feesType'
        ])->findOrFail($transactionId);

        $html = $this->buildReceiptHtml($transaction);
        $filename = "fee_receipt_{$transaction->receipt_number}";

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate receipts for multiple transactions.
     *
     * @param array $transactionIds
     * @return Response
     */
    public function generateBulkReceipts(array $transactionIds): Response
    {
        $transactions = FeesTransaction::with([
            'student.user',
            'student.schoolClass',
            'student.section',
            'feesType'
        ])->whereIn('id', $transactionIds)->get();

        $html = '';
        foreach ($transactions as $index => $transaction) {
            $html .= $this->buildReceiptHtml($transaction);
            
            if ($index < $transactions->count() - 1) {
                $html .= '<div style="page-break-after: always;"></div>';
            }
        }

        $filename = 'bulk_fee_receipts_' . now()->format('Y-m-d_His');

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate receipts for a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @param array $filters
     * @return Response
     */
    public function generateReceiptsByDateRange(string $startDate, string $endDate, array $filters = []): Response
    {
        $query = FeesTransaction::with([
            'student.user',
            'student.schoolClass',
            'student.section',
            'feesType'
        ])
        ->whereDate('payment_date', '>=', $startDate)
        ->whereDate('payment_date', '<=', $endDate)
        ->where('payment_status', 'completed');

        if (!empty($filters['class_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }

        $transactions = $query->orderBy('payment_date')->get();

        $html = '';
        foreach ($transactions as $index => $transaction) {
            $html .= $this->buildReceiptHtml($transaction);
            
            if ($index < $transactions->count() - 1) {
                $html .= '<div style="page-break-after: always;"></div>';
            }
        }

        $filename = "fee_receipts_{$startDate}_to_{$endDate}";

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Generate student fee statement.
     *
     * @param int $studentId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Response
     */
    public function generateStudentStatement(int $studentId, ?string $startDate = null, ?string $endDate = null): Response
    {
        $student = Student::with(['user', 'schoolClass', 'section', 'academicSession'])->findOrFail($studentId);
        
        $query = FeesTransaction::with(['feesType'])
            ->where('student_id', $studentId)
            ->orderBy('payment_date', 'desc');

        if ($startDate) {
            $query->whereDate('payment_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('payment_date', '<=', $endDate);
        }

        $transactions = $query->get();

        $html = $this->buildStatementHtml($student, $transactions, $startDate, $endDate);
        $filename = "fee_statement_{$student->admission_number}";

        return $this->pdfService->generateFromHtml($html, $filename, 'a4', 'portrait');
    }

    /**
     * Build receipt HTML.
     *
     * @param FeesTransaction $transaction
     * @return string
     */
    protected function buildReceiptHtml(FeesTransaction $transaction): string
    {
        $schoolName = config('app.name', 'Smart School');
        $student = $transaction->student;
        $studentName = $student?->user ? "{$student->user->first_name} {$student->user->last_name}" : '';
        $className = $student?->schoolClass?->name ?? '';
        $sectionName = $student?->section?->name ?? '';
        $receiptNo = $transaction->receipt_number ?? $transaction->id;
        $paymentDate = $transaction->payment_date?->format('F j, Y') ?? '';
        $feeType = $transaction->feesType?->name ?? 'Fee Payment';
        $amount = number_format($transaction->amount, 2);
        $paymentMethod = ucfirst($transaction->payment_method ?? 'Cash');
        $transactionId = $transaction->transaction_id ?? '-';
        $remarks = $transaction->remarks ?? '';
        $amountInWords = $this->numberToWords($transaction->amount);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fee Receipt - {$receiptNo}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; padding: 20px; }
        .receipt { border: 2px solid #4f46e5; padding: 20px; max-width: 600px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; margin-bottom: 5px; }
        .header h2 { font-size: 16px; color: #333; margin-top: 10px; }
        .receipt-no { text-align: right; margin-bottom: 15px; }
        .receipt-no span { background: #4f46e5; color: white; padding: 5px 15px; border-radius: 3px; font-weight: bold; }
        .info-section { margin-bottom: 20px; }
        .info-row { display: flex; margin-bottom: 10px; }
        .info-label { font-weight: bold; width: 150px; color: #666; }
        .info-value { color: #333; flex: 1; }
        .amount-section { background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; text-align: center; }
        .amount-label { font-size: 12px; color: #666; margin-bottom: 5px; }
        .amount-value { font-size: 28px; font-weight: bold; color: #4f46e5; }
        .amount-words { font-size: 10px; color: #666; margin-top: 5px; font-style: italic; }
        .payment-details { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .payment-details h3 { font-size: 12px; color: #4f46e5; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .detail-row { display: flex; justify-content: space-between; padding: 5px 0; }
        .signature-section { margin-top: 40px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 45%; }
        .signature-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; font-size: 10px; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #666; border-top: 1px dashed #ddd; padding-top: 15px; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 60px; color: rgba(79, 70, 229, 0.1); font-weight: bold; z-index: -1; }
        .status-paid { color: #28a745; font-weight: bold; }
        .status-pending { color: #ffc107; font-weight: bold; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="watermark">PAID</div>
        
        <div class="header">
            <h1>{$schoolName}</h1>
            <h2>FEE RECEIPT</h2>
        </div>

        <div class="receipt-no">
            <span>Receipt No: {$receiptNo}</span>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Student Name:</span>
                <span class="info-value">{$studentName}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Admission No:</span>
                <span class="info-value">{$student?->admission_number}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Class:</span>
                <span class="info-value">{$className} - {$sectionName}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value">{$paymentDate}</span>
            </div>
        </div>

        <div class="amount-section">
            <div class="amount-label">Amount Paid</div>
            <div class="amount-value">₹ {$amount}</div>
            <div class="amount-words">({$amountInWords} Only)</div>
        </div>

        <div class="payment-details">
            <h3>Payment Details</h3>
            <div class="detail-row">
                <span>Fee Type:</span>
                <span>{$feeType}</span>
            </div>
            <div class="detail-row">
                <span>Payment Method:</span>
                <span>{$paymentMethod}</span>
            </div>
            <div class="detail-row">
                <span>Transaction ID:</span>
                <span>{$transactionId}</span>
            </div>
            <div class="detail-row">
                <span>Status:</span>
                <span class="status-paid">PAID</span>
            </div>
            {$remarks}
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">Student/Parent Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Authorized Signature</div>
            </div>
        </div>

        <div class="footer">
            <p>This is a computer-generated receipt from {$schoolName} Management System</p>
            <p>For any queries, please contact the school office</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build student fee statement HTML.
     *
     * @param Student $student
     * @param \Illuminate\Support\Collection $transactions
     * @param string|null $startDate
     * @param string|null $endDate
     * @return string
     */
    protected function buildStatementHtml(Student $student, $transactions, ?string $startDate, ?string $endDate): string
    {
        $schoolName = config('app.name', 'Smart School');
        $studentName = $student->user ? "{$student->user->first_name} {$student->user->last_name}" : '';
        $className = $student->schoolClass?->name ?? '';
        $sectionName = $student->section?->name ?? '';
        $generatedAt = now()->format('F j, Y');
        $dateRange = $startDate && $endDate ? "{$startDate} to {$endDate}" : 'All Time';

        $totalPaid = $transactions->where('payment_status', 'completed')->sum('amount');
        $totalPending = $transactions->where('payment_status', 'pending')->sum('amount');

        $transactionRows = '';
        foreach ($transactions as $transaction) {
            $statusClass = $transaction->payment_status === 'completed' ? 'status-paid' : 'status-pending';
            $transactionRows .= <<<HTML
<tr>
    <td>{$transaction->payment_date?->format('Y-m-d')}</td>
    <td>{$transaction->receipt_number}</td>
    <td>{$transaction->feesType?->name}</td>
    <td class="text-right">₹ {$transaction->amount}</td>
    <td>{$transaction->payment_method}</td>
    <td class="{$statusClass}">{$transaction->payment_status}</td>
</tr>
HTML;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fee Statement - {$studentName}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; margin-bottom: 5px; }
        .header h2 { font-size: 16px; color: #333; margin-top: 10px; }
        .student-info { display: flex; justify-content: space-between; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .info-group { width: 48%; }
        .info-row { display: flex; margin-bottom: 8px; }
        .info-label { font-weight: bold; width: 120px; color: #666; }
        .info-value { color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #4f46e5; color: white; padding: 10px 5px; text-align: left; font-size: 10px; }
        td { padding: 8px 5px; border: 1px solid #ddd; font-size: 10px; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-right { text-align: right; }
        .status-paid { color: #28a745; font-weight: bold; }
        .status-pending { color: #ffc107; font-weight: bold; }
        .summary { margin-top: 20px; padding: 15px; background: #e8f4f8; border-radius: 5px; }
        .summary-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #ddd; }
        .summary-row:last-child { border-bottom: none; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <h2>FEE STATEMENT</h2>
        <p style="margin-top: 5px; color: #666;">Period: {$dateRange}</p>
    </div>

    <div class="student-info">
        <div class="info-group">
            <div class="info-row">
                <span class="info-label">Student Name:</span>
                <span class="info-value">{$studentName}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Admission No:</span>
                <span class="info-value">{$student->admission_number}</span>
            </div>
        </div>
        <div class="info-group">
            <div class="info-row">
                <span class="info-label">Class:</span>
                <span class="info-value">{$className} - {$sectionName}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Generated On:</span>
                <span class="info-value">{$generatedAt}</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Receipt No</th>
                <th>Fee Type</th>
                <th class="text-right">Amount</th>
                <th>Method</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            {$transactionRows}
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row">
            <span style="font-weight: bold;">Total Paid:</span>
            <span style="font-weight: bold; color: #28a745;">₹ {$totalPaid}</span>
        </div>
        <div class="summary-row">
            <span style="font-weight: bold;">Total Pending:</span>
            <span style="font-weight: bold; color: #ffc107;">₹ {$totalPending}</span>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer-generated statement from {$schoolName} Management System</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Convert number to words.
     *
     * @param float $number
     * @return string
     */
    protected function numberToWords(float $number): string
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
            'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $number = (int) $number;
        
        if ($number < 20) {
            return $ones[$number];
        }

        if ($number < 100) {
            return $tens[(int)($number / 10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
        }

        if ($number < 1000) {
            return $ones[(int)($number / 100)] . ' Hundred' . ($number % 100 ? ' ' . $this->numberToWords($number % 100) : '');
        }

        if ($number < 100000) {
            return $this->numberToWords((int)($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . $this->numberToWords($number % 1000) : '');
        }

        if ($number < 10000000) {
            return $this->numberToWords((int)($number / 100000)) . ' Lakh' . ($number % 100000 ? ' ' . $this->numberToWords($number % 100000) : '');
        }

        return $this->numberToWords((int)($number / 10000000)) . ' Crore' . ($number % 10000000 ? ' ' . $this->numberToWords($number % 10000000) : '');
    }

    /**
     * Get receipt data without generating PDF.
     *
     * @param int $transactionId
     * @return array
     */
    public function getReceiptData(int $transactionId): array
    {
        $transaction = FeesTransaction::with([
            'student.user',
            'student.schoolClass',
            'student.section',
            'feesType'
        ])->findOrFail($transactionId);

        return [
            'transaction' => $transaction,
            'student' => $transaction->student,
            'fee_type' => $transaction->feesType,
        ];
    }
}
