@extends('layouts.app')

@section('title', 'Promote Students')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Promote Students</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Promotions</a></li>
                    <li class="breadcrumb-item active">Promote</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.promotions.store') }}">
        @csrf

        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header bg-warning bg-opacity-10">
                        <i class="bi bi-box-arrow-right me-2"></i>From (Current)
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                                <select name="from_session_id" class="form-select" required>
                                    <option value="">Select Session</option>
                                    @foreach($academicSessions ?? [] as $session)
                                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Class <span class="text-danger">*</span></label>
                                <select name="from_class_id" class="form-select" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes ?? [] as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Section</label>
                                <select name="from_section_id" class="form-select">
                                    <option value="">All Sections</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header bg-success bg-opacity-10">
                        <i class="bi bi-box-arrow-in-right me-2"></i>To (Promoted)
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                                <select name="to_session_id" class="form-select" required>
                                    <option value="">Select Session</option>
                                    @foreach($academicSessions ?? [] as $session)
                                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Class <span class="text-danger">*</span></label>
                                <select name="to_class_id" class="form-select" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes ?? [] as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Section</label>
                                <select name="to_section_id" class="form-select">
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-people me-2"></i>Select Students to Promote
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Select the source class and session above to load students for promotion.
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Student</th>
                                <th>Admission No</th>
                                <th>Roll No</th>
                                <th>Result</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Select class and session to load students
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-arrow-up-circle me-1"></i> Promote Selected Students
            </button>
        </div>
    </form>
</div>
@endsection
