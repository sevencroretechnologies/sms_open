{{-- Income Create View --}}
{{-- Prompt 263: Income entry form with category, amount, payment method, receipt upload --}}

@extends('layouts.app')

@section('title', 'Add Income')

@section('content')
<div x-data="incomeForm()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Add Income</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('income.index') }}">Income</a></li>
                    <li class="breadcrumb-item active">Add</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('income.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <x-alert type="success" :dismissible="true">
            {{ session('success') }}
        </x-alert>
    @endif

    @if(session('error'))
        <x-alert type="danger" :dismissible="true">
            {{ session('error') }}
        </x-alert>
    @endif

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <form action="{{ route('income.store') }}" method="POST" enctype="multipart/form-data" @submit="handleSubmit">
                @csrf
                
                <x-card>
                    <x-slot name="header">
                        <i class="bi bi-cash-stack me-2"></i>
                        Income Details
                    </x-slot>

                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-8 mb-4">
                            <label for="title" class="form-label fw-medium">
                                Title <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('title') is-invalid @enderror" 
                                id="title" 
                                name="title" 
                                x-model="title"
                                value="{{ old('title') }}"
                                placeholder="e.g., Fee Collection - January"
                                required
                            >
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Income Date -->
                        <div class="col-md-4 mb-4">
                            <label for="income_date" class="form-label fw-medium">
                                Date <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="date" 
                                class="form-control @error('income_date') is-invalid @enderror" 
                                id="income_date" 
                                name="income_date" 
                                x-model="incomeDate"
                                value="{{ old('income_date', date('Y-m-d')) }}"
                                required
                            >
                            @error('income_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Category -->
                        <div class="col-md-6 mb-4">
                            <label for="category_id" class="form-label fw-medium">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select @error('category_id') is-invalid @enderror" 
                                id="category_id" 
                                name="category_id"
                                x-model="categoryId"
                                required
                            >
                                <option value="">-- Select Category --</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="mt-1">
                                <a href="{{ route('income-categories.create') }}" class="small text-primary">
                                    <i class="bi bi-plus-lg me-1"></i> Add New Category
                                </a>
                            </div>
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6 mb-4">
                            <label for="amount" class="form-label fw-medium">
                                Amount <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">$</span>
                                <input 
                                    type="number" 
                                    class="form-control @error('amount') is-invalid @enderror" 
                                    id="amount" 
                                    name="amount" 
                                    x-model="amount"
                                    value="{{ old('amount') }}"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0.01"
                                    required
                                >
                            </div>
                            @error('amount')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-medium">Description</label>
                        <textarea 
                            class="form-control @error('description') is-invalid @enderror" 
                            id="description" 
                            name="description" 
                            rows="3"
                            x-model="description"
                            placeholder="Detailed description of the income..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Payment Method -->
                        <div class="col-md-6 mb-4">
                            <label for="payment_method" class="form-label fw-medium">
                                Payment Method <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select @error('payment_method') is-invalid @enderror" 
                                id="payment_method" 
                                name="payment_method"
                                x-model="paymentMethod"
                                required
                            >
                                <option value="">-- Select Method --</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reference Number -->
                        <div class="col-md-6 mb-4">
                            <label for="reference_number" class="form-label fw-medium">Reference Number</label>
                            <input 
                                type="text" 
                                class="form-control @error('reference_number') is-invalid @enderror" 
                                id="reference_number" 
                                name="reference_number" 
                                x-model="referenceNumber"
                                value="{{ old('reference_number') }}"
                                placeholder="e.g., REC-001, TXN-123"
                            >
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Source/Payer -->
                    <div class="mb-4">
                        <label for="source" class="form-label fw-medium">Source/Payer</label>
                        <input 
                            type="text" 
                            class="form-control @error('source') is-invalid @enderror" 
                            id="source" 
                            name="source" 
                            x-model="source"
                            value="{{ old('source') }}"
                            placeholder="Name of payer or income source"
                        >
                        @error('source')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Receipt Upload -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Receipt/Document</label>
                        <div 
                            class="border rounded p-3 text-center bg-light"
                            :class="{'border-primary': isDragging}"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop($event)"
                        >
                            <div x-show="!receipt">
                                <i class="bi bi-receipt fs-3 text-muted mb-2 d-block"></i>
                                <p class="mb-2 small">Drag and drop a receipt here, or click to browse</p>
                                <input 
                                    type="file" 
                                    class="form-control form-control-sm" 
                                    id="receipt" 
                                    name="receipt"
                                    @change="handleFileSelect($event)"
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                >
                                <small class="text-muted d-block mt-2">
                                    Supported: PDF, JPG, PNG, DOC (Max: 5MB)
                                </small>
                            </div>
                            
                            <div x-show="receipt" class="text-start">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark fs-4 text-success"></i>
                                    <div class="flex-grow-1">
                                        <span class="fw-medium" x-text="receipt?.name"></span>
                                        <br>
                                        <small class="text-muted" x-text="formatFileSize(receipt?.size)"></small>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm" @click="removeReceipt()">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @error('receipt')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('income.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" name="action" value="save_add" class="btn btn-outline-primary" :disabled="isSubmitting">
                                <i class="bi bi-plus-lg me-1"></i> Save & Add Another
                            </button>
                            <button type="submit" name="action" value="save" class="btn btn-primary" :disabled="isSubmitting">
                                <span x-show="!isSubmitting">
                                    <i class="bi bi-check-lg me-1"></i> Save Income
                                </span>
                                <span x-show="isSubmitting">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                                </span>
                            </button>
                        </div>
                    </x-slot>
                </x-card>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Preview Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-eye me-2"></i>
                    Preview
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-circle bg-success bg-opacity-10 text-success">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0" x-text="title || 'Income Title'"></h6>
                            <small class="text-muted" x-text="incomeDate || 'Date'"></small>
                        </div>
                    </div>
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Amount:</span>
                            <span class="fw-bold text-success" x-text="'$' + parseFloat(amount || 0).toFixed(2)"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Category:</span>
                            <span x-text="getCategoryName() || 'Not selected'"></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Payment:</span>
                            <span x-text="paymentMethod ? paymentMethod.replace('_', ' ') : 'Not selected'"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Income -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-clock-history me-2"></i>
                    Recent Income
                </div>
                <div class="list-group list-group-flush">
                    @forelse(($recentIncomes ?? []) as $income)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-medium">{{ Str::limit($income->title ?? '', 25) }}</span>
                                    <br>
                                    <small class="text-muted">{{ $income->category->name ?? 'Uncategorized' }}</small>
                                </div>
                                <span class="text-success fw-medium">${{ number_format($income->amount ?? 0, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-3">
                            No recent income
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-graph-up me-2"></i>
                    Quick Stats
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Today:</span>
                        <span class="fw-medium text-success">${{ number_format($stats['today'] ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">This Week:</span>
                        <span class="fw-medium text-success">${{ number_format($stats['this_week'] ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">This Month:</span>
                        <span class="fw-medium text-success">${{ number_format($stats['this_month'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-lightbulb me-2"></i>
                    Tips
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Always attach receipts for audit trail
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Use reference numbers for tracking
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Categorize income correctly
                        </li>
                        <li>
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Record source/payer information
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function incomeForm() {
    return {
        title: '{{ old('title', '') }}',
        incomeDate: '{{ old('income_date', date('Y-m-d')) }}',
        categoryId: '{{ old('category_id', '') }}',
        amount: '{{ old('amount', '') }}',
        description: '{{ old('description', '') }}',
        paymentMethod: '{{ old('payment_method', '') }}',
        referenceNumber: '{{ old('reference_number', '') }}',
        source: '{{ old('source', '') }}',
        receipt: null,
        isDragging: false,
        isSubmitting: false,
        
        categories: @json($categories ?? []),
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.validateAndSetFile(file);
            }
        },
        
        handleDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            if (file) {
                this.validateAndSetFile(file);
                const input = document.getElementById('receipt');
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
            }
        },
        
        validateAndSetFile(file) {
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
            const ext = file.name.split('.').pop().toLowerCase();
            
            if (file.size > maxSize) {
                alert('File size exceeds 5MB limit');
                return;
            }
            
            if (!allowedTypes.includes(ext)) {
                alert('File type not supported');
                return;
            }
            
            this.receipt = file;
        },
        
        removeReceipt() {
            this.receipt = null;
            document.getElementById('receipt').value = '';
        },
        
        formatFileSize(bytes) {
            if (!bytes) return '';
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        },
        
        getCategoryName() {
            if (!this.categoryId) return null;
            const category = this.categories.find(c => c.id == this.categoryId);
            return category ? category.name : null;
        },
        
        handleSubmit(event) {
            if (!this.title || !this.categoryId || !this.amount || !this.paymentMethod) {
                event.preventDefault();
                alert('Please fill in all required fields');
                return;
            }
            
            this.isSubmitting = true;
        }
    };
}
</script>
@endpush

@push('styles')
<style>
.avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.border-primary {
    border-color: var(--bs-primary) !important;
    border-width: 2px !important;
}
</style>
@endpush
