@extends('layouts.app')

@section('title', __('Add Language'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ __('Add Language') }}</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.settings.languages.index') }}">{{ __('Languages') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Add') }}</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.settings.languages.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Language Details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.languages.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Language Code') }} <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="en, ar, fr, etc." required maxlength="5">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">{{ __('ISO 639-1 language code (e.g., en, ar, fr)') }}</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Language Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="English" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Native Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="native_name" class="form-control @error('native_name') is-invalid @enderror" value="{{ old('native_name') }}" placeholder="English" required>
                                @error('native_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">{{ __('Name in the native language (e.g., العربية for Arabic)') }}</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Flag Code') }}</label>
                                <input type="text" name="flag_code" class="form-control @error('flag_code') is-invalid @enderror" value="{{ old('flag_code') }}" placeholder="us, gb, sa, etc." maxlength="5">
                                @error('flag_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">{{ __('ISO 3166-1 country code for flag icon') }}</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_rtl" class="form-check-input" id="is_rtl" value="1" {{ old('is_rtl') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_rtl">{{ __('Right-to-Left (RTL)') }}</label>
                                </div>
                                <small class="text-muted">{{ __('Enable for Arabic, Hebrew, Urdu, etc.') }}</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                </div>
                                <small class="text-muted">{{ __('Make this language available to users') }}</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.settings.languages.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> {{ __('Save Language') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Common Languages') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">{{ __('Click to auto-fill language details:') }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary lang-preset" data-code="en" data-name="English" data-native="English" data-flag="us" data-rtl="0">English</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary lang-preset" data-code="ar" data-name="Arabic" data-native="العربية" data-flag="sa" data-rtl="1">Arabic</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary lang-preset" data-code="fr" data-name="French" data-native="Français" data-flag="fr" data-rtl="0">French</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary lang-preset" data-code="es" data-name="Spanish" data-native="Español" data-flag="es" data-rtl="0">Spanish</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary lang-preset" data-code="de" data-name="German" data-native="Deutsch" data-flag="de" data-rtl="0">German</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary lang-preset" data-code="hi" data-name="Hindi" data-native="हिन्दी" data-flag="in" data-rtl="0">Hindi</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary lang-preset" data-code="ur" data-name="Urdu" data-native="اردو" data-flag="pk" data-rtl="1">Urdu</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary lang-preset" data-code="zh" data-name="Chinese" data-native="中文" data-flag="cn" data-rtl="0">Chinese</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary lang-preset" data-code="ja" data-name="Japanese" data-native="日本語" data-flag="jp" data-rtl="0">Japanese</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary lang-preset" data-code="pt" data-name="Portuguese" data-native="Português" data-flag="pt" data-rtl="0">Portuguese</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary lang-preset" data-code="ru" data-name="Russian" data-native="Русский" data-flag="ru" data-rtl="0">Russian</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary lang-preset" data-code="he" data-name="Hebrew" data-native="עברית" data-flag="il" data-rtl="1">Hebrew</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.lang-preset').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelector('input[name="code"]').value = this.dataset.code;
        document.querySelector('input[name="name"]').value = this.dataset.name;
        document.querySelector('input[name="native_name"]').value = this.dataset.native;
        document.querySelector('input[name="flag_code"]').value = this.dataset.flag;
        document.querySelector('input[name="is_rtl"]').checked = this.dataset.rtl === '1';
    });
});
</script>
@endpush
@endsection
