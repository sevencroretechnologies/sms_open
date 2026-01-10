@extends('layouts.app')

@section('title', 'AI Tools')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">AI Tools</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">AI Tools</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-graph-up-arrow text-primary fs-3"></i>
                    </div>
                    <h5 class="card-title">Performance Predictor</h5>
                    <p class="card-text text-muted small">Analyze grades, attendance, and behavior to predict at-risk students.</p>
                    <a href="{{ route('admin.ai-tools.performance-predictor') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-right me-1"></i> Open Tool
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-chat-quote text-success fs-3"></i>
                    </div>
                    <h5 class="card-title">Report Card Comments</h5>
                    <p class="card-text text-muted small">Generate personalized teacher comments for report cards.</p>
                    <a href="{{ route('admin.ai-tools.report-card-comments') }}" class="btn btn-success">
                        <i class="bi bi-arrow-right me-1"></i> Open Tool
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-envelope-paper text-info fs-3"></i>
                    </div>
                    <h5 class="card-title">Parent Communication</h5>
                    <p class="card-text text-muted small">Draft professional messages to parents for various purposes.</p>
                    <a href="{{ route('admin.ai-tools.parent-communication') }}" class="btn btn-info">
                        <i class="bi bi-arrow-right me-1"></i> Open Tool
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-pencil-square text-warning fs-3"></i>
                    </div>
                    <h5 class="card-title">Assignment Grader</h5>
                    <p class="card-text text-muted small">AI-assisted grading with detailed feedback for assignments.</p>
                    <a href="{{ route('admin.ai-tools.assignment-grader') }}" class="btn btn-warning">
                        <i class="bi bi-arrow-right me-1"></i> Open Tool
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-calendar-check text-danger fs-3"></i>
                    </div>
                    <h5 class="card-title">Study Plan Generator</h5>
                    <p class="card-text text-muted small">Create custom study plans based on student weaknesses.</p>
                    <a href="{{ route('admin.ai-tools.study-plan') }}" class="btn btn-danger">
                        <i class="bi bi-arrow-right me-1"></i> Open Tool
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-secondary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-question-circle text-secondary fs-3"></i>
                    </div>
                    <h5 class="card-title">Question Generator</h5>
                    <p class="card-text text-muted small">Generate questions for any subject, topic, and difficulty.</p>
                    <a href="{{ route('admin.ai-tools.question-generator') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-right me-1"></i> Open Tool
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-table text-primary fs-3"></i>
                    </div>
                    <h5 class="card-title">Timetable Optimizer</h5>
                    <p class="card-text text-muted small">AI-powered timetable generation avoiding conflicts.</p>
                    <a href="{{ route('admin.ai-tools.timetable-optimizer') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-right me-1"></i> Open Tool
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-briefcase text-success fs-3"></i>
                    </div>
                    <h5 class="card-title">Career Guidance</h5>
                    <p class="card-text text-muted small">Provide career guidance based on interests and performance.</p>
                    <a href="{{ route('admin.ai-tools.career-guidance') }}" class="btn btn-success">
                        <i class="bi bi-arrow-right me-1"></i> Open Tool
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-file-text text-info fs-3"></i>
                    </div>
                    <h5 class="card-title">Meeting Summary</h5>
                    <p class="card-text text-muted small">Summarize parent-teacher meeting notes automatically.</p>
                    <a href="{{ route('admin.ai-tools.meeting-summary') }}" class="btn btn-info">
                        <i class="bi bi-arrow-right me-1"></i> Open Tool
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-check2-square text-warning fs-3"></i>
                    </div>
                    <h5 class="card-title">Curriculum Checker</h5>
                    <p class="card-text text-muted small">Verify lesson plans align with CBSE/ICSE standards.</p>
                    <a href="{{ route('admin.ai-tools.curriculum-checker') }}" class="btn btn-warning">
                        <i class="bi bi-arrow-right me-1"></i> Open Tool
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
