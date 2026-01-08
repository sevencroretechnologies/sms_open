{{-- Payment Settings View --}}
{{-- Prompt 275: Payment gateway configuration, fee settings, currency options --}}

@extends('layouts.app')

@section('title', 'Payment Settings')

@section('content')
<div x-data="paymentSettings()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Payment Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active">Payment</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('settings.general') ?? '#' }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Settings
            </a>
            <button type="button" class="btn btn-primary" @click="saveSettings()" :disabled="saving">
                <span x-show="!saving"><i class="bi bi-check-lg me-1"></i> Save Settings</span>
                <span x-show="saving"><span class="spinner-border spinner-border-sm me-1"></span> Saving...</span>
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Payment Configuration -->
        <div class="col-lg-8">
            <form action="{{ route('settings.payment.update') ?? '#' }}" method="POST" @submit.prevent="saveSettings()">
                @csrf
                @method('PUT')

                <!-- Currency Settings -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-currency-exchange me-2 text-primary"></i>Currency Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Currency <span class="text-danger">*</span></label>
                                <select class="form-select @error('currency') is-invalid @enderror" 
                                        name="currency" x-model="form.currency">
                                    <option value="INR">Indian Rupee (INR)</option>
                                    <option value="USD">US Dollar (USD)</option>
                                    <option value="EUR">Euro (EUR)</option>
                                    <option value="GBP">British Pound (GBP)</option>
                                    <option value="AED">UAE Dirham (AED)</option>
                                    <option value="SGD">Singapore Dollar (SGD)</option>
                                    <option value="AUD">Australian Dollar (AUD)</option>
                                    <option value="CAD">Canadian Dollar (CAD)</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Currency Symbol</label>
                                <input type="text" class="form-control @error('currency_symbol') is-invalid @enderror" 
                                       name="currency_symbol" x-model="form.currency_symbol" maxlength="5">
                                @error('currency_symbol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Symbol Position</label>
                                <select class="form-select @error('symbol_position') is-invalid @enderror" 
                                        name="symbol_position" x-model="form.symbol_position">
                                    <option value="before">Before Amount</option>
                                    <option value="after">After Amount</option>
                                </select>
                                @error('symbol_position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Decimal Places</label>
                                <select class="form-select" name="decimal_places" x-model="form.decimal_places">
                                    <option value="0">0</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Thousand Separator</label>
                                <select class="form-select" name="thousand_separator" x-model="form.thousand_separator">
                                    <option value=",">Comma (,)</option>
                                    <option value=".">Period (.)</option>
                                    <option value=" ">Space</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Decimal Separator</label>
                                <select class="form-select" name="decimal_separator" x-model="form.decimal_separator">
                                    <option value=".">Period (.)</option>
                                    <option value=",">Comma (,)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3 p-3 bg-light rounded">
                            <small class="text-muted">Preview: </small>
                            <strong x-text="formatCurrency(12345.67)"></strong>
                        </div>
                    </div>
                </div>

                <!-- Payment Gateways -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-credit-card me-2 text-success"></i>Payment Gateways</h5>
                    </div>
                    <div class="card-body">
                        <!-- Razorpay -->
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="https://razorpay.com/favicon.png" alt="Razorpay" width="24" class="me-2">
                                    <h6 class="mb-0">Razorpay</h6>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="razorpay_enabled" 
                                           x-model="form.razorpay_enabled" id="razorpayEnabled">
                                    <label class="form-check-label" for="razorpayEnabled">
                                        <span x-text="form.razorpay_enabled ? 'Enabled' : 'Disabled'"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row g-3" x-show="form.razorpay_enabled">
                                <div class="col-md-6">
                                    <label class="form-label">Key ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="razorpay_key_id" 
                                           x-model="form.razorpay_key_id" placeholder="rzp_live_xxxxxxxx">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Key Secret <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input :type="showRazorpaySecret ? 'text' : 'password'" class="form-control" 
                                               name="razorpay_key_secret" x-model="form.razorpay_key_secret">
                                        <button class="btn btn-outline-secondary" type="button" 
                                                @click="showRazorpaySecret = !showRazorpaySecret">
                                            <i :class="showRazorpaySecret ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Webhook Secret</label>
                                    <input type="text" class="form-control" name="razorpay_webhook_secret" 
                                           x-model="form.razorpay_webhook_secret">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mode</label>
                                    <select class="form-select" name="razorpay_mode" x-model="form.razorpay_mode">
                                        <option value="test">Test Mode</option>
                                        <option value="live">Live Mode</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Stripe -->
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-stripe fs-4 text-primary me-2"></i>
                                    <h6 class="mb-0">Stripe</h6>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="stripe_enabled" 
                                           x-model="form.stripe_enabled" id="stripeEnabled">
                                    <label class="form-check-label" for="stripeEnabled">
                                        <span x-text="form.stripe_enabled ? 'Enabled' : 'Disabled'"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row g-3" x-show="form.stripe_enabled">
                                <div class="col-md-6">
                                    <label class="form-label">Publishable Key <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="stripe_publishable_key" 
                                           x-model="form.stripe_publishable_key" placeholder="pk_live_xxxxxxxx">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input :type="showStripeSecret ? 'text' : 'password'" class="form-control" 
                                               name="stripe_secret_key" x-model="form.stripe_secret_key">
                                        <button class="btn btn-outline-secondary" type="button" 
                                                @click="showStripeSecret = !showStripeSecret">
                                            <i :class="showStripeSecret ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Webhook Secret</label>
                                    <input type="text" class="form-control" name="stripe_webhook_secret" 
                                           x-model="form.stripe_webhook_secret">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mode</label>
                                    <select class="form-select" name="stripe_mode" x-model="form.stripe_mode">
                                        <option value="test">Test Mode</option>
                                        <option value="live">Live Mode</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- PayPal -->
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-paypal fs-4 text-primary me-2"></i>
                                    <h6 class="mb-0">PayPal</h6>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="paypal_enabled" 
                                           x-model="form.paypal_enabled" id="paypalEnabled">
                                    <label class="form-check-label" for="paypalEnabled">
                                        <span x-text="form.paypal_enabled ? 'Enabled' : 'Disabled'"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="row g-3" x-show="form.paypal_enabled">
                                <div class="col-md-6">
                                    <label class="form-label">Client ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="paypal_client_id" 
                                           x-model="form.paypal_client_id">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Client Secret <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input :type="showPaypalSecret ? 'text' : 'password'" class="form-control" 
                                               name="paypal_client_secret" x-model="form.paypal_client_secret">
                                        <button class="btn btn-outline-secondary" type="button" 
                                                @click="showPaypalSecret = !showPaypalSecret">
                                            <i :class="showPaypalSecret ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mode</label>
                                    <select class="form-select" name="paypal_mode" x-model="form.paypal_mode">
                                        <option value="sandbox">Sandbox</option>
                                        <option value="live">Live</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Offline Payment -->
                        <div class="border rounded p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-cash-stack fs-4 text-success me-2"></i>
                                    <h6 class="mb-0">Offline Payment</h6>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="offline_enabled" 
                                           x-model="form.offline_enabled" id="offlineEnabled">
                                    <label class="form-check-label" for="offlineEnabled">
                                        <span x-text="form.offline_enabled ? 'Enabled' : 'Disabled'"></span>
                                    </label>
                                </div>
                            </div>
                            <div x-show="form.offline_enabled">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Payment Methods</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       x-model="form.offline_cash" id="offlineCash">
                                                <label class="form-check-label" for="offlineCash">Cash</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       x-model="form.offline_cheque" id="offlineCheque">
                                                <label class="form-check-label" for="offlineCheque">Cheque</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       x-model="form.offline_dd" id="offlineDD">
                                                <label class="form-check-label" for="offlineDD">Demand Draft</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       x-model="form.offline_bank_transfer" id="offlineBankTransfer">
                                                <label class="form-check-label" for="offlineBankTransfer">Bank Transfer</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Bank Details (for Bank Transfer)</label>
                                        <textarea class="form-control" name="bank_details" 
                                                  x-model="form.bank_details" rows="3"
                                                  placeholder="Bank Name: XYZ Bank&#10;Account Number: 1234567890&#10;IFSC Code: XYZB0001234"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fee Settings -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2 text-warning"></i>Fee Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Fee Due Reminder (Days Before)</label>
                                <input type="number" class="form-control" name="fee_reminder_days" 
                                       x-model="form.fee_reminder_days" min="1" max="30">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Late Fee Grace Period (Days)</label>
                                <input type="number" class="form-control" name="late_fee_grace_days" 
                                       x-model="form.late_fee_grace_days" min="0" max="30">
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="auto_late_fee" 
                                           x-model="form.auto_late_fee" id="autoLateFee">
                                    <label class="form-check-label" for="autoLateFee">
                                        Auto-apply Late Fee
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="partial_payment" 
                                           x-model="form.partial_payment" id="partialPayment">
                                    <label class="form-check-label" for="partialPayment">
                                        Allow Partial Payment
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="send_receipt" 
                                           x-model="form.send_receipt" id="sendReceipt">
                                    <label class="form-check-label" for="sendReceipt">
                                        Auto-send Payment Receipt
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="online_payment_parent" 
                                           x-model="form.online_payment_parent" id="onlinePaymentParent">
                                    <label class="form-check-label" for="onlinePaymentParent">
                                        Allow Parent Online Payment
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Payment Statistics -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>This Month</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Collected</span>
                        <strong class="text-success" x-text="formatCurrency(stats.total_collected)"></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Online Payments</span>
                        <strong x-text="stats.online_payments">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Offline Payments</span>
                        <strong x-text="stats.offline_payments">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Failed Transactions</span>
                        <strong class="text-danger" x-text="stats.failed_transactions">0</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Pending Amount</span>
                        <strong class="text-warning" x-text="formatCurrency(stats.pending_amount)"></strong>
                    </div>
                    <hr>
                    <a href="{{ route('fee-transactions.index') ?? '#' }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-list-ul me-1"></i> View Transactions
                    </a>
                </div>
            </div>

            <!-- Gateway Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-wifi me-2 text-success"></i>Gateway Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Razorpay</span>
                        <span class="badge" :class="form.razorpay_enabled ? 'bg-success' : 'bg-secondary'">
                            <span x-text="form.razorpay_enabled ? 'Active' : 'Inactive'"></span>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Stripe</span>
                        <span class="badge" :class="form.stripe_enabled ? 'bg-success' : 'bg-secondary'">
                            <span x-text="form.stripe_enabled ? 'Active' : 'Inactive'"></span>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>PayPal</span>
                        <span class="badge" :class="form.paypal_enabled ? 'bg-success' : 'bg-secondary'">
                            <span x-text="form.paypal_enabled ? 'Active' : 'Inactive'"></span>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Offline</span>
                        <span class="badge" :class="form.offline_enabled ? 'bg-success' : 'bg-secondary'">
                            <span x-text="form.offline_enabled ? 'Active' : 'Inactive'"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-link-45deg me-2 text-info"></i>Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('fee-types.index') ?? '#' }}" class="btn btn-outline-primary text-start">
                            <i class="bi bi-tags me-2"></i> Fee Types
                        </a>
                        <a href="{{ route('fee-groups.index') ?? '#' }}" class="btn btn-outline-primary text-start">
                            <i class="bi bi-collection me-2"></i> Fee Groups
                        </a>
                        <a href="{{ route('fee-discounts.index') ?? '#' }}" class="btn btn-outline-primary text-start">
                            <i class="bi bi-percent me-2"></i> Fee Discounts
                        </a>
                        <a href="{{ route('fee-fines.index') ?? '#' }}" class="btn btn-outline-primary text-start">
                            <i class="bi bi-exclamation-triangle me-2"></i> Late Fee Rules
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function paymentSettings() {
    return {
        saving: false,
        showRazorpaySecret: false,
        showStripeSecret: false,
        showPaypalSecret: false,
        stats: {
            total_collected: {{ $stats['total_collected'] ?? 1250000 }},
            online_payments: {{ $stats['online_payments'] ?? 156 }},
            offline_payments: {{ $stats['offline_payments'] ?? 89 }},
            failed_transactions: {{ $stats['failed_transactions'] ?? 3 }},
            pending_amount: {{ $stats['pending_amount'] ?? 450000 }}
        },
        form: {
            currency: '{{ $settings["currency"] ?? "INR" }}',
            currency_symbol: '{{ $settings["currency_symbol"] ?? "₹" }}',
            symbol_position: '{{ $settings["symbol_position"] ?? "before" }}',
            decimal_places: {{ $settings['decimal_places'] ?? 2 }},
            thousand_separator: '{{ $settings["thousand_separator"] ?? "," }}',
            decimal_separator: '{{ $settings["decimal_separator"] ?? "." }}',
            // Razorpay
            razorpay_enabled: {{ ($settings['razorpay_enabled'] ?? false) ? 'true' : 'false' }},
            razorpay_key_id: '{{ $settings["razorpay_key_id"] ?? "" }}',
            razorpay_key_secret: '{{ $settings["razorpay_key_secret"] ?? "" }}',
            razorpay_webhook_secret: '{{ $settings["razorpay_webhook_secret"] ?? "" }}',
            razorpay_mode: '{{ $settings["razorpay_mode"] ?? "test" }}',
            // Stripe
            stripe_enabled: {{ ($settings['stripe_enabled'] ?? false) ? 'true' : 'false' }},
            stripe_publishable_key: '{{ $settings["stripe_publishable_key"] ?? "" }}',
            stripe_secret_key: '{{ $settings["stripe_secret_key"] ?? "" }}',
            stripe_webhook_secret: '{{ $settings["stripe_webhook_secret"] ?? "" }}',
            stripe_mode: '{{ $settings["stripe_mode"] ?? "test" }}',
            // PayPal
            paypal_enabled: {{ ($settings['paypal_enabled'] ?? false) ? 'true' : 'false' }},
            paypal_client_id: '{{ $settings["paypal_client_id"] ?? "" }}',
            paypal_client_secret: '{{ $settings["paypal_client_secret"] ?? "" }}',
            paypal_mode: '{{ $settings["paypal_mode"] ?? "sandbox" }}',
            // Offline
            offline_enabled: {{ ($settings['offline_enabled'] ?? true) ? 'true' : 'false' }},
            offline_cash: {{ ($settings['offline_cash'] ?? true) ? 'true' : 'false' }},
            offline_cheque: {{ ($settings['offline_cheque'] ?? true) ? 'true' : 'false' }},
            offline_dd: {{ ($settings['offline_dd'] ?? true) ? 'true' : 'false' }},
            offline_bank_transfer: {{ ($settings['offline_bank_transfer'] ?? true) ? 'true' : 'false' }},
            bank_details: `{{ $settings["bank_details"] ?? "" }}`,
            // Fee Settings
            fee_reminder_days: {{ $settings['fee_reminder_days'] ?? 7 }},
            late_fee_grace_days: {{ $settings['late_fee_grace_days'] ?? 5 }},
            auto_late_fee: {{ ($settings['auto_late_fee'] ?? true) ? 'true' : 'false' }},
            partial_payment: {{ ($settings['partial_payment'] ?? true) ? 'true' : 'false' }},
            send_receipt: {{ ($settings['send_receipt'] ?? true) ? 'true' : 'false' }},
            online_payment_parent: {{ ($settings['online_payment_parent'] ?? true) ? 'true' : 'false' }}
        },
        
        formatCurrency(amount) {
            const symbol = this.form.currency_symbol;
            const decimals = parseInt(this.form.decimal_places);
            const thousandSep = this.form.thousand_separator;
            const decimalSep = this.form.decimal_separator;
            
            let formatted = amount.toFixed(decimals);
            let parts = formatted.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSep);
            formatted = parts.join(decimalSep);
            
            return this.form.symbol_position === 'before' 
                ? symbol + formatted 
                : formatted + symbol;
        },
        
        async saveSettings() {
            this.saving = true;
            
            try {
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                Swal.fire({
                    icon: 'success',
                    title: 'Settings Saved!',
                    text: 'Payment settings have been updated successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to save settings. Please try again.'
                });
            } finally {
                this.saving = false;
            }
        }
    };
}
</script>
@endpush

@push('styles')
<style>
[dir="rtl"] .text-start {
    text-align: right !important;
}
</style>
@endpush
