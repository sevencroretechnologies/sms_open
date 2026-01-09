@extends('layouts.app')

@section('title', __('Edit Language'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ __('Edit Language') }}: {{ $language->name ?? '' }}</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.settings.languages.index') }}">{{ __('Languages') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Edit') }}</li>
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
                    <form action="{{ route('admin.settings.languages.update', $language ?? 0) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Language Code') }}</label>
                                <input type="text" class="form-control" value="{{ $language->code ?? '' }}" readonly disabled>
                                <small class="text-muted">{{ __('Language code cannot be changed') }}</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Language Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $language->name ?? '') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Native Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="native_name" class="form-control @error('native_name') is-invalid @enderror" value="{{ old('native_name', $language->native_name ?? '') }}" required>
                                @error('native_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Flag Code') }}</label>
                                <input type="text" name="flag_code" class="form-control @error('flag_code') is-invalid @enderror" value="{{ old('flag_code', $language->flag_code ?? '') }}" maxlength="5">
                                @error('flag_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_rtl" class="form-check-input" id="is_rtl" value="1" {{ old('is_rtl', $language->is_rtl ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_rtl">{{ __('Right-to-Left (RTL)') }}</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $language->is_active ?? true) ? 'checked' : '' }} {{ ($language->code ?? '') === config('app.locale') ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                </div>
                                @if(($language->code ?? '') === config('app.locale'))
                                <small class="text-muted">{{ __('Default language cannot be deactivated') }}</small>
                                @endif
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.settings.languages.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> {{ __('Update Language') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Translation Statistics') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('Total Keys') }}</span>
                            <strong>{{ $translationCount ?? 0 }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('Translated') }}</span>
                            <strong class="text-success">{{ $translatedCount ?? 0 }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('Missing') }}</span>
                            <strong class="text-danger">{{ $missingCount ?? 0 }}</strong>
                        </li>
                    </ul>
                    <div class="mt-3">
                        <a href="{{ route('admin.settings.languages.translations', $language ?? 0) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-translate me-1"></i> {{ __('Manage Translations') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
