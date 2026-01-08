{{-- Student Fees View --}}
{{-- Prompt 148: Student fees view with payment history and fee status --}}

@extends('layouts.app')

@section('title', 'Student Fees - ' . ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))

@section('content')
<div x-data="studentFees()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Student Fees</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.show', $student->id ?? 0) }}">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</a></li>
                    <li class="breadcrumb-item active">Fees</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('students.show', $student->id ?? 0) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Profile
            </a>
            <button type="button" class="btn btn-outline-primary" @click="exportFees()">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button type="button" class="btn btn-outline-info" @click="sendReminder()">
                <i class="bi bi-bell me-1"></i> Send Reminder
            </button>
            @can('collect_fees')
            <button type="button" class="btn btn-success" @click="showCollectModal = true">
                <i class="bi bi-currency-rupee me-1"></i> Collect Fee
            </button>
            @endcan
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <img 
                    src="{{ $student->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) . '&background=4f46e5&color=fff&size=60' }}"
                    alt="{{ $student->first_name ?? '' }}"
                    class="rounded-circle me-3"
                    style="width: 60px; height: 60px; object-fit: cover;"
                >
                <div>
                    <h5 class="mb-1">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</h5>
                    <p class="text-muted mb-0">
                        <span class="badge bg-light text-dark me-2">{{ $student->admission_number ?? 'N/A' }}</span>
                        <span class="badge bg-primary">{{ $student->class->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">Rs. {{ number_format($totalFees ?? 0) }}</h3>
                            <small class="text-white-50">Total Fees</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">Rs. {{ number_format($paidAmount ?? 0) }}</h3>
                            <small class="text-white-50">Paid Amount</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">Rs. {{ number_format($pendingAmount ?? 0) }}</h3>
                            <small>Pending Amount</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white bg-opacity-25 p-3 me-3">
                            <i class="bi bi-exclamation-triangle fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">Rs. {{ number_format($overdueAmount ?? 0) }}</h3>
                            <small class="text-white-50">Overdue Amount</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Allotments -->
    <x-card title="Fee Allotments" icon="bi-list-check">
        <x-slot name="actions">
            <select class="form-select form-select-sm" style="width: auto;" x-model="filterFeeStatus">
                <option value="">All Status</option>
                <option value="paid">Paid</option>
                <option value="pending">Pending</option>
                <option value="partial">Partial</option>
                <option value="overdue">Overdue</option>
            </select>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" @change="toggleAllFees($event)">
                        </th>
                        <th>Fee Type</th>
                        <th>Fee Group</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Fine</th>
                        <th class="text-end">Net Amount</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="fee in filteredFeeAllotments" :key="fee.id">
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input" :value="fee.id" x-model="selectedFees" :disabled="fee.status === 'paid'">
                            </td>
                            <td x-text="fee.fee_type"></td>
                            <td x-text="fee.fee_group || '-'"></td>
                            <td class="text-end" x-text="'Rs. ' + formatNumber(fee.amount)"></td>
                            <td class="text-end text-success" x-text="fee.discount ? 'Rs. ' + formatNumber(fee.discount) : '-'"></td>
                            <td class="text-end text-danger" x-text="fee.fine ? 'Rs. ' + formatNumber(fee.fine) : '-'"></td>
                            <td class="text-end fw-bold" x-text="'Rs. ' + formatNumber(fee.net_amount)"></td>
                            <td class="text-end text-success" x-text="'Rs. ' + formatNumber(fee.paid_amount)"></td>
                            <td class="text-end" :class="{ 'text-danger fw-bold': fee.balance > 0 }" x-text="'Rs. ' + formatNumber(fee.balance)"></td>
                            <td x-text="formatDate(fee.due_date)"></td>
                            <td>
                                <span class="badge" :class="{
                                    'bg-success': fee.status === 'paid',
                                    'bg-warning': fee.status === 'pending',
                                    'bg-info': fee.status === 'partial',
                                    'bg-danger': fee.status === 'overdue'
                                }" x-text="fee.status.charAt(0).toUpperCase() + fee.status.slice(1)"></span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-success" @click="collectSingleFee(fee)" :disabled="fee.status === 'paid'" title="Collect">
                                        <i class="bi bi-currency-rupee"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" @click="viewFeeDetails(fee)" title="Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredFeeAllotments.length === 0">
                        <td colspan="12" class="text-center py-4 text-muted">
                            <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                            No fee allotments found
                        </td>
                    </tr>
                </tbody>
                <tfoot class="table-light" x-show="filteredFeeAllotments.length > 0">
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th class="text-end" x-text="'Rs. ' + formatNumber(totalAmount)"></th>
                        <th class="text-end text-success" x-text="'Rs. ' + formatNumber(totalDiscount)"></th>
                        <th class="text-end text-danger" x-text="'Rs. ' + formatNumber(totalFine)"></th>
                        <th class="text-end fw-bold" x-text="'Rs. ' + formatNumber(totalNetAmount)"></th>
                        <th class="text-end text-success" x-text="'Rs. ' + formatNumber(totalPaid)"></th>
                        <th class="text-end text-danger fw-bold" x-text="'Rs. ' + formatNumber(totalBalance)"></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Bulk Actions -->
        <div class="mt-3 pt-3 border-top" x-show="selectedFees.length > 0">
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted" x-text="selectedFees.length + ' fee(s) selected'"></span>
                <button type="button" class="btn btn-success btn-sm" @click="collectSelectedFees()">
                    <i class="bi bi-currency-rupee me-1"></i> Collect Selected (Rs. <span x-text="formatNumber(selectedFeesTotal)"></span>)
                </button>
            </div>
        </div>
    </x-card>

    <!-- Payment History -->
    <x-card title="Payment History" icon="bi-clock-history" class="mt-4">
        <x-slot name="actions">
            <div class="input-group" style="width: 200px;">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="Search..." x-model="searchPayment">
            </div>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Transaction ID</th>
                        <th>Fee Type</th>
                        <th class="text-end">Amount</th>
                        <th>Payment Date</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="payment in filteredPayments" :key="payment.id">
                        <tr>
                            <td>
                                <span class="font-monospace" x-text="payment.transaction_id"></span>
                            </td>
                            <td x-text="payment.fee_type"></td>
                            <td class="text-end fw-bold" x-text="'Rs. ' + formatNumber(payment.amount)"></td>
                            <td x-text="formatDate(payment.payment_date)"></td>
                            <td>
                                <span class="badge bg-light text-dark" x-text="payment.payment_method.toUpperCase()"></span>
                            </td>
                            <td>
                                <span class="badge" :class="{
                                    'bg-success': payment.status === 'completed',
                                    'bg-warning': payment.status === 'pending',
                                    'bg-danger': payment.status === 'failed',
                                    'bg-info': payment.status === 'refunded'
                                }" x-text="payment.status.charAt(0).toUpperCase() + payment.status.slice(1)"></span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="printReceipt(payment)" title="Print Receipt">
                                    <i class="bi bi-printer"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredPayments.length === 0">
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-receipt-cutoff fs-1 d-block mb-2"></i>
                            No payment records found
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Collect Fee Modal -->
    <div class="modal fade" :class="{ 'show d-block': showCollectModal }" tabindex="-1" x-show="showCollectModal" x-transition>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-currency-rupee me-2"></i>Collect Fee</h5>
                    <button type="button" class="btn-close" @click="showCollectModal = false"></button>
                </div>
                <form @submit.prevent="processPayment">
                    <div class="modal-body">
                        <!-- Selected Fees Summary -->
                        <div class="alert alert-info mb-3" x-show="collectForm.fees.length > 0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><strong x-text="collectForm.fees.length"></strong> fee(s) selected</span>
                                <span class="fw-bold">Total: Rs. <span x-text="formatNumber(collectForm.total_amount)"></span></span>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Amount to Pay <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" x-model="collectForm.amount" required min="1" :max="collectForm.total_amount">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select" x-model="collectForm.payment_method" required>
                                    <option value="">Select method</option>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="dd">Demand Draft</option>
                                    <option value="online">Online</option>
                                    <option value="upi">UPI</option>
                                    <option value="card">Card</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" x-model="collectForm.payment_date" required>
                            </div>
                            <div class="col-md-6" x-show="['cheque', 'dd'].includes(collectForm.payment_method)">
                                <label class="form-label">Reference Number</label>
                                <input type="text" class="form-control" x-model="collectForm.reference_number" placeholder="Cheque/DD number">
                            </div>
                            <div class="col-md-6" x-show="['cheque', 'dd'].includes(collectForm.payment_method)">
                                <label class="form-label">Bank Name</label>
                                <input type="text" class="form-control" x-model="collectForm.bank_name" placeholder="Enter bank name">
                            </div>
                            <div class="col-md-6" x-show="['online', 'upi', 'card'].includes(collectForm.payment_method)">
                                <label class="form-label">Transaction ID</label>
                                <input type="text" class="form-control" x-model="collectForm.transaction_ref" placeholder="Transaction reference">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control" rows="2" x-model="collectForm.remarks" placeholder="Add any remarks..."></textarea>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="border rounded p-3 mt-3 bg-light">
                            <h6 class="mb-3">Payment Summary</h6>
                            <div class="row g-2">
                                <div class="col-6">Total Due:</div>
                                <div class="col-6 text-end" x-text="'Rs. ' + formatNumber(collectForm.total_amount)"></div>
                                <div class="col-6">Amount Paying:</div>
                                <div class="col-6 text-end text-success fw-bold" x-text="'Rs. ' + formatNumber(collectForm.amount || 0)"></div>
                                <div class="col-6">Balance After Payment:</div>
                                <div class="col-6 text-end" :class="{ 'text-danger': (collectForm.total_amount - (collectForm.amount || 0)) > 0 }" x-text="'Rs. ' + formatNumber(collectForm.total_amount - (collectForm.amount || 0))"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="form-check me-auto">
                            <input type="checkbox" class="form-check-input" id="printReceipt" x-model="collectForm.print_receipt">
                            <label class="form-check-label" for="printReceipt">Print receipt after payment</label>
                        </div>
                        <button type="button" class="btn btn-secondary" @click="showCollectModal = false">Cancel</button>
                        <button type="submit" class="btn btn-success" :disabled="processing">
                            <span x-show="!processing"><i class="bi bi-check me-1"></i> Process Payment</span>
                            <span x-show="processing"><span class="spinner-border spinner-border-sm me-1"></span> Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" x-show="showCollectModal" x-transition></div>

    <!-- Fee Details Modal -->
    <div class="modal fade" :class="{ 'show d-block': showDetailsModal }" tabindex="-1" x-show="showDetailsModal" x-transition>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Fee Details</h5>
                    <button type="button" class="btn-close" @click="showDetailsModal = false"></button>
                </div>
                <div class="modal-body" x-show="selectedFeeDetails">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small text-muted mb-0">Fee Type</label>
                            <p class="mb-2 fw-medium" x-text="selectedFeeDetails?.fee_type"></p>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-0">Fee Group</label>
                            <p class="mb-2 fw-medium" x-text="selectedFeeDetails?.fee_group || '-'"></p>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-0">Amount</label>
                            <p class="mb-2 fw-medium" x-text="'Rs. ' + formatNumber(selectedFeeDetails?.amount)"></p>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-0">Discount</label>
                            <p class="mb-2 fw-medium text-success" x-text="selectedFeeDetails?.discount ? 'Rs. ' + formatNumber(selectedFeeDetails?.discount) : '-'"></p>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-0">Fine</label>
                            <p class="mb-2 fw-medium text-danger" x-text="selectedFeeDetails?.fine ? 'Rs. ' + formatNumber(selectedFeeDetails?.fine) : '-'"></p>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-0">Net Amount</label>
                            <p class="mb-2 fw-bold" x-text="'Rs. ' + formatNumber(selectedFeeDetails?.net_amount)"></p>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-0">Paid Amount</label>
                            <p class="mb-2 fw-medium text-success" x-text="'Rs. ' + formatNumber(selectedFeeDetails?.paid_amount)"></p>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-0">Balance</label>
                            <p class="mb-2 fw-bold" :class="{ 'text-danger': selectedFeeDetails?.balance > 0 }" x-text="'Rs. ' + formatNumber(selectedFeeDetails?.balance)"></p>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-0">Due Date</label>
                            <p class="mb-2 fw-medium" x-text="formatDate(selectedFeeDetails?.due_date)"></p>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-0">Status</label>
                            <p class="mb-2">
                                <span class="badge" :class="{
                                    'bg-success': selectedFeeDetails?.status === 'paid',
                                    'bg-warning': selectedFeeDetails?.status === 'pending',
                                    'bg-info': selectedFeeDetails?.status === 'partial',
                                    'bg-danger': selectedFeeDetails?.status === 'overdue'
                                }" x-text="selectedFeeDetails?.status?.charAt(0).toUpperCase() + selectedFeeDetails?.status?.slice(1)"></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showDetailsModal = false">Close</button>
                    <button type="button" class="btn btn-success" @click="collectSingleFee(selectedFeeDetails)" :disabled="selectedFeeDetails?.status === 'paid'">
                        <i class="bi bi-currency-rupee me-1"></i> Collect Fee
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" x-show="showDetailsModal" x-transition></div>
</div>

@push('scripts')
<script>
function studentFees() {
    return {
        feeAllotments: @json($feeAllotments ?? []),
        payments: @json($payments ?? []),
        
        selectedFees: [],
        filterFeeStatus: '',
        searchPayment: '',
        
        showCollectModal: false,
        showDetailsModal: false,
        selectedFeeDetails: null,
        
        collectForm: {
            fees: [],
            total_amount: 0,
            amount: 0,
            payment_method: '',
            payment_date: new Date().toISOString().split('T')[0],
            reference_number: '',
            bank_name: '',
            transaction_ref: '',
            remarks: '',
            print_receipt: true
        },
        processing: false,
        
        get filteredFeeAllotments() {
            if (!this.filterFeeStatus) return this.feeAllotments;
            return this.feeAllotments.filter(f => f.status === this.filterFeeStatus);
        },
        
        get filteredPayments() {
            if (!this.searchPayment) return this.payments;
            const query = this.searchPayment.toLowerCase();
            return this.payments.filter(p => 
                (p.transaction_id || '').toLowerCase().includes(query) ||
                (p.fee_type || '').toLowerCase().includes(query)
            );
        },
        
        get totalAmount() {
            return this.filteredFeeAllotments.reduce((sum, f) => sum + (f.amount || 0), 0);
        },
        
        get totalDiscount() {
            return this.filteredFeeAllotments.reduce((sum, f) => sum + (f.discount || 0), 0);
        },
        
        get totalFine() {
            return this.filteredFeeAllotments.reduce((sum, f) => sum + (f.fine || 0), 0);
        },
        
        get totalNetAmount() {
            return this.filteredFeeAllotments.reduce((sum, f) => sum + (f.net_amount || 0), 0);
        },
        
        get totalPaid() {
            return this.filteredFeeAllotments.reduce((sum, f) => sum + (f.paid_amount || 0), 0);
        },
        
        get totalBalance() {
            return this.filteredFeeAllotments.reduce((sum, f) => sum + (f.balance || 0), 0);
        },
        
        get selectedFeesTotal() {
            return this.feeAllotments
                .filter(f => this.selectedFees.includes(f.id))
                .reduce((sum, f) => sum + (f.balance || 0), 0);
        },
        
        formatNumber(num) {
            return (num || 0).toLocaleString('en-IN');
        },
        
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
        },
        
        toggleAllFees(event) {
            if (event.target.checked) {
                this.selectedFees = this.filteredFeeAllotments
                    .filter(f => f.status !== 'paid')
                    .map(f => f.id);
            } else {
                this.selectedFees = [];
            }
        },
        
        collectSingleFee(fee) {
            this.collectForm.fees = [fee.id];
            this.collectForm.total_amount = fee.balance;
            this.collectForm.amount = fee.balance;
            this.showDetailsModal = false;
            this.showCollectModal = true;
        },
        
        collectSelectedFees() {
            const selectedFeeObjects = this.feeAllotments.filter(f => this.selectedFees.includes(f.id));
            this.collectForm.fees = this.selectedFees;
            this.collectForm.total_amount = this.selectedFeesTotal;
            this.collectForm.amount = this.selectedFeesTotal;
            this.showCollectModal = true;
        },
        
        viewFeeDetails(fee) {
            this.selectedFeeDetails = fee;
            this.showDetailsModal = true;
        },
        
        async processPayment() {
            if (!this.collectForm.amount || !this.collectForm.payment_method) return;
            
            this.processing = true;
            
            try {
                const response = await fetch('{{ route("students.fees.pay", $student->id ?? 0) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.collectForm)
                });
                
                if (response.ok) {
                    const result = await response.json();
                    
                    // Update fee allotments
                    if (result.updated_fees) {
                        result.updated_fees.forEach(updatedFee => {
                            const index = this.feeAllotments.findIndex(f => f.id === updatedFee.id);
                            if (index !== -1) {
                                this.feeAllotments[index] = updatedFee;
                            }
                        });
                    }
                    
                    // Add new payment to history
                    if (result.payment) {
                        this.payments.unshift(result.payment);
                    }
                    
                    this.showCollectModal = false;
                    this.selectedFees = [];
                    this.resetCollectForm();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Successful!',
                        text: 'Fee payment has been processed.',
                        confirmButtonText: this.collectForm.print_receipt ? 'Print Receipt' : 'OK'
                    }).then((res) => {
                        if (res.isConfirmed && this.collectForm.print_receipt && result.payment) {
                            this.printReceipt(result.payment);
                        }
                    });
                } else {
                    throw new Error('Failed to process payment');
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to process payment. Please try again.' });
            } finally {
                this.processing = false;
            }
        },
        
        resetCollectForm() {
            this.collectForm = {
                fees: [],
                total_amount: 0,
                amount: 0,
                payment_method: '',
                payment_date: new Date().toISOString().split('T')[0],
                reference_number: '',
                bank_name: '',
                transaction_ref: '',
                remarks: '',
                print_receipt: true
            };
        },
        
        printReceipt(payment) {
            window.open(`/students/{{ $student->id ?? 0 }}/fees/receipt/${payment.id}`, '_blank');
        },
        
        exportFees() {
            window.location.href = '{{ route("students.fees.export", $student->id ?? 0) }}';
        },
        
        sendReminder() {
            Swal.fire({
                title: 'Send Fee Reminder',
                html: `
                    <select id="reminderType" class="form-select mb-3">
                        <option value="sms">SMS</option>
                        <option value="email">Email</option>
                        <option value="both">Both</option>
                    </select>
                    <textarea id="reminderMessage" class="form-control" rows="4" placeholder="Custom message (optional)..."></textarea>
                `,
                showCancelButton: true,
                confirmButtonText: 'Send Reminder',
                preConfirm: () => {
                    return {
                        type: document.getElementById('reminderType').value,
                        message: document.getElementById('reminderMessage').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Sent!', 'Fee reminder has been sent.', 'success');
                }
            });
        }
    };
}
</script>
@endpush
@endsection
