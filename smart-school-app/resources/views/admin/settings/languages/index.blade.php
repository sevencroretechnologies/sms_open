@extends('layouts.app')

@section('title', __('Language Management'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ __('Language Management') }}</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="#">{{ __('Settings') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Languages') }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.settings.languages.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> {{ __('Add Language') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Available Languages') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Language') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Native Name') }}</th>
                                    <th>{{ __('Direction') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($languages ?? [] as $language)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="flag-icon flag-icon-{{ $language->flag_code ?? 'un' }} me-2"></span>
                                            {{ $language->name }}
                                        </div>
                                    </td>
                                    <td><code>{{ $language->code }}</code></td>
                                    <td>{{ $language->native_name }}</td>
                                    <td>
                                        @if($language->is_rtl)
                                            <span class="badge bg-info">RTL</span>
                                        @else
                                            <span class="badge bg-secondary">LTR</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($language->is_active)
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.settings.languages.edit', $language) }}" class="btn btn-outline-primary" title="{{ __('Edit') }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('admin.settings.languages.translations', $language) }}" class="btn btn-outline-info" title="{{ __('Translations') }}">
                                                <i class="bi bi-translate"></i>
                                            </a>
                                            @if($language->code !== config('app.locale'))
                                            <form action="{{ route('admin.settings.languages.toggle', $language) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-{{ $language->is_active ? 'warning' : 'success' }}" title="{{ $language->is_active ? __('Deactivate') : __('Activate') }}">
                                                    <i class="bi bi-{{ $language->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.settings.languages.destroy', $language) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this language?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="{{ __('Delete') }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-translate fs-1 d-block mb-2"></i>
                                        {{ __('No languages configured yet.') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Current Settings') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('Default Language') }}</span>
                            <strong>{{ config('app.locale', 'en') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('Fallback Language') }}</span>
                            <strong>{{ config('app.fallback_locale', 'en') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('Current Locale') }}</span>
                            <strong>{{ app()->getLocale() }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('Text Direction') }}</span>
                            <strong>{{ app()->getLocale() == 'ar' ? 'RTL' : 'LTR' }}</strong>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.settings.languages.export') }}" class="btn btn-outline-primary">
                            <i class="bi bi-download me-1"></i> {{ __('Export Translations') }}
                        </a>
                        <a href="{{ route('admin.settings.languages.import') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-upload me-1"></i> {{ __('Import Translations') }}
                        </a>
                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#syncModal">
                            <i class="bi bi-arrow-repeat me-1"></i> {{ __('Sync Missing Keys') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="syncModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Sync Missing Translation Keys') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('This will scan all language files and add missing keys to other languages using the default language values.') }}</p>
                <form action="{{ route('admin.settings.languages.sync') }}" method="POST" id="syncForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('Source Language') }}</label>
                        <select name="source_locale" class="form-select">
                            @foreach($languages ?? [] as $language)
                            <option value="{{ $language->code }}" {{ $language->code === config('app.locale') ? 'selected' : '' }}>
                                {{ $language->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" form="syncForm" class="btn btn-primary">{{ __('Sync Now') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection
