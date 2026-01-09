<?php

namespace App\Services;

use App\Models\FeesTransaction;
use App\Models\FeesAllotment;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Fee Export Service
 * 
 * Prompt 423: Create Fee Export Service
 * 
 * Handles exporting fee data to various formats with filtering options.
 * Supports date range, class, section, and payment status filters.
 * 
 * Features:
 * - Export fee transactions
 * - Export fee collection summary
 * - Export pending fees report
 * - Export student fee statements
 * - Support PDF, Excel, CSV formats
 */
class FeeExportService
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Export fee transactions.
     *
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportTransactions(array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getTransactionData($filters);
        $filename = 'fee_transactions_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Fee Transactions Report');
    }

    /**
     * Export fee collection summary.
     *
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportCollectionSummary(array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getCollectionSummaryData($filters);
        $filename = 'fee_collection_summary_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Fee Collection Summary');
    }

    /**
     * Export pending fees report.
     *
     * @param array $filters
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportPendingFees(array $filters = [], string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getPendingFeesData($filters);
        $filename = 'pending_fees_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Pending Fees Report');
    }

    /**
     * Export student fee statement.
     *
     * @param int $studentId
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportStudentStatement(int $studentId, string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getStudentStatementData($studentId);
        $filename = 'student_fee_statement_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Student Fee Statement');
    }

    /**
     * Export daily collection report.
     *
     * @param string $date
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportDailyCollection(string $date, string $format = 'xlsx'): Response|StreamedResponse
    {
        $filters = ['date' => $date];
        $data = $this->getTransactionData($filters);
        $filename = 'daily_collection_' . $date;
        
        return $this->exportService->export($data, $format, $filename, "Daily Collection Report - {$date}");
    }

    /**
     * Export class-wise fee status.
     *
     * @param int $classId
     * @param int|null $sectionId
     * @param string $format
     * @return Response|StreamedResponse
     */
    public function exportClassFeeStatus(int $classId, ?int $sectionId = null, string $format = 'xlsx'): Response|StreamedResponse
    {
        $data = $this->getClassFeeStatusData($classId, $sectionId);
        $filename = 'class_fee_status_' . now()->format('Y-m-d_His');
        
        return $this->exportService->export($data, $format, $filename, 'Class Fee Status Report');
    }

    /**
     * Get transaction data for export.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getTransactionData(array $filters = []): Collection
    {
        $query = FeesTransaction::query()
            ->with(['student.user', 'student.schoolClass', 'student.section', 'feesType']);

        $this->applyFilters($query, $filters);

        return $query->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'Transaction ID' => $transaction->transaction_id ?? '',
                    'Receipt No' => $transaction->receipt_number ?? '',
                    'Admission No' => $transaction->student?->admission_number ?? '',
                    'Student Name' => $transaction->student?->user 
                        ? "{$transaction->student->user->first_name} {$transaction->student->user->last_name}" 
                        : '',
                    'Class' => $transaction->student?->schoolClass?->name ?? '',
                    'Section' => $transaction->student?->section?->name ?? '',
                    'Fee Type' => $transaction->feesType?->name ?? '',
                    'Amount' => number_format($transaction->amount, 2),
                    'Payment Method' => ucfirst($transaction->payment_method ?? ''),
                    'Payment Status' => ucfirst($transaction->payment_status ?? ''),
                    'Payment Date' => $transaction->payment_date?->format('Y-m-d') ?? '',
                    'Remarks' => $transaction->remarks ?? '',
                ];
            });
    }

    /**
     * Get collection summary data.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getCollectionSummaryData(array $filters = []): Collection
    {
        $query = FeesTransaction::query()
            ->with(['feesType'])
            ->where('payment_status', 'completed');

        if (!empty($filters['date_from'])) {
            $query->whereDate('payment_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('payment_date', '<=', $filters['date_to']);
        }

        $transactions = $query->get();

        $summary = $transactions->groupBy('fees_type_id')->map(function ($group) {
            $feeType = $group->first()->feesType;
            return [
                'Fee Type' => $feeType?->name ?? 'Unknown',
                'Total Transactions' => $group->count(),
                'Cash' => number_format($group->where('payment_method', 'cash')->sum('amount'), 2),
                'Cheque' => number_format($group->where('payment_method', 'cheque')->sum('amount'), 2),
                'Online' => number_format($group->where('payment_method', 'online')->sum('amount'), 2),
                'DD' => number_format($group->where('payment_method', 'dd')->sum('amount'), 2),
                'Total Amount' => number_format($group->sum('amount'), 2),
            ];
        });

        return $summary->values();
    }

    /**
     * Get pending fees data.
     *
     * @param array $filters
     * @return Collection
     */
    protected function getPendingFeesData(array $filters = []): Collection
    {
        $query = Student::query()
            ->with(['user', 'schoolClass', 'section', 'feesAllotments.feesMaster.feesType'])
            ->where('is_active', true);

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        if (!empty($filters['academic_session_id'])) {
            $query->where('academic_session_id', $filters['academic_session_id']);
        }

        $students = $query->orderBy('admission_number')->get();

        return $students->map(function ($student) {
            $totalAllotted = $student->feesAllotments->sum('amount');
            
            $totalPaid = FeesTransaction::where('student_id', $student->id)
                ->where('payment_status', 'completed')
                ->sum('amount');
            
            $pending = $totalAllotted - $totalPaid;

            if ($pending <= 0) {
                return null;
            }

            return [
                'Admission No' => $student->admission_number,
                'Student Name' => $student->user 
                    ? "{$student->user->first_name} {$student->user->last_name}" 
                    : '',
                'Class' => $student->schoolClass?->name ?? '',
                'Section' => $student->section?->name ?? '',
                'Father Name' => $student->father_name ?? '',
                'Father Phone' => $student->father_phone ?? '',
                'Total Fees' => number_format($totalAllotted, 2),
                'Paid Amount' => number_format($totalPaid, 2),
                'Pending Amount' => number_format($pending, 2),
            ];
        })->filter()->values();
    }

    /**
     * Get student statement data.
     *
     * @param int $studentId
     * @return Collection
     */
    protected function getStudentStatementData(int $studentId): Collection
    {
        $transactions = FeesTransaction::where('student_id', $studentId)
            ->with(['feesType'])
            ->orderBy('payment_date', 'desc')
            ->get();

        return $transactions->map(function ($transaction) {
            return [
                'Date' => $transaction->payment_date?->format('Y-m-d') ?? '',
                'Receipt No' => $transaction->receipt_number ?? '',
                'Fee Type' => $transaction->feesType?->name ?? '',
                'Amount' => number_format($transaction->amount, 2),
                'Payment Method' => ucfirst($transaction->payment_method ?? ''),
                'Status' => ucfirst($transaction->payment_status ?? ''),
                'Remarks' => $transaction->remarks ?? '',
            ];
        });
    }

    /**
     * Get class fee status data.
     *
     * @param int $classId
     * @param int|null $sectionId
     * @return Collection
     */
    protected function getClassFeeStatusData(int $classId, ?int $sectionId = null): Collection
    {
        $query = Student::query()
            ->with(['user', 'section', 'feesAllotments'])
            ->where('class_id', $classId)
            ->where('is_active', true);

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $students = $query->orderBy('admission_number')->get();

        return $students->map(function ($student) {
            $totalAllotted = $student->feesAllotments->sum('amount');
            
            $totalPaid = FeesTransaction::where('student_id', $student->id)
                ->where('payment_status', 'completed')
                ->sum('amount');
            
            $pending = max(0, $totalAllotted - $totalPaid);
            $status = $pending <= 0 ? 'Paid' : ($totalPaid > 0 ? 'Partial' : 'Unpaid');

            return [
                'Admission No' => $student->admission_number,
                'Student Name' => $student->user 
                    ? "{$student->user->first_name} {$student->user->last_name}" 
                    : '',
                'Section' => $student->section?->name ?? '',
                'Total Fees' => number_format($totalAllotted, 2),
                'Paid' => number_format($totalPaid, 2),
                'Pending' => number_format($pending, 2),
                'Status' => $status,
            ];
        });
    }

    /**
     * Apply filters to the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyFilters($query, array $filters): void
    {
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['fees_type_id'])) {
            $query->where('fees_type_id', $filters['fees_type_id']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('payment_date', $filters['date']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('payment_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('payment_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['class_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }

        if (!empty($filters['section_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('section_id', $filters['section_id']);
            });
        }
    }

    /**
     * Get export statistics.
     *
     * @param array $filters
     * @return array
     */
    public function getExportStatistics(array $filters = []): array
    {
        $query = FeesTransaction::query();
        $this->applyFilters($query, $filters);

        $total = $query->count();
        $completed = (clone $query)->where('payment_status', 'completed')->count();
        $totalAmount = (clone $query)->where('payment_status', 'completed')->sum('amount');

        return [
            'total_transactions' => $total,
            'completed' => $completed,
            'total_amount' => number_format($totalAmount, 2),
        ];
    }
}
