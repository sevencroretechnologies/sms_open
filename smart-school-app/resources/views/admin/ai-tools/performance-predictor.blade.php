@extends('layouts.app')

@section('title', 'Student Performance Predictor')

@section('content')
<div class="container-fluid" x-data="performancePredictor()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Student Performance Predictor</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ai-tools.index') }}">AI Tools</a></li>
                    <li class="breadcrumb-item active">Performance Predictor</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-sliders me-2"></i>Prediction Settings
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Select Student</label>
                        <select class="form-select" x-model="studentId">
                            <option value="">Choose a student...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->user->name ?? 'Unknown' }} ({{ $student->schoolClass->name ?? '' }} - {{ $student->section->name ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" x-model="includeAttendance" id="includeAttendance">
                            <label class="form-check-label" for="includeAttendance">Include Attendance Data</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" x-model="includeGrades" id="includeGrades">
                            <label class="form-check-label" for="includeGrades">Include Grades Data</label>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100" @click="predict()" :disabled="!studentId || isLoading">
                        <span x-show="!isLoading"><i class="bi bi-graph-up me-2"></i>Predict Performance</span>
                        <span x-show="isLoading"><i class="bi bi-hourglass-split me-2"></i>Analyzing...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" x-show="result">
                <div class="card-header">
                    <i class="bi bi-clipboard-data me-2"></i>Prediction Results
                </div>
                <div class="card-body">
                    <template x-if="result">
                        <div>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="text-center p-3 rounded" :class="getRiskClass(result.prediction?.risk_level)">
                                        <h6 class="text-uppercase small mb-1">Risk Level</h6>
                                        <h3 class="mb-0 text-capitalize" x-text="result.prediction?.risk_level || 'N/A'"></h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h6 class="text-uppercase small mb-1">Confidence Score</h6>
                                        <h3 class="mb-0" x-text="(result.prediction?.confidence_score || 0) + '%'"></h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h6 class="text-uppercase small mb-1">Avg. Percentage</h6>
                                        <h3 class="mb-0" x-text="(result.student?.average_percentage || 0) + '%'"></h3>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6><i class="bi bi-lightbulb me-2"></i>Predicted Outcome</h6>
                                <p class="text-muted" x-text="result.prediction?.predicted_outcome"></p>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6><i class="bi bi-exclamation-triangle text-warning me-2"></i>Areas of Concern</h6>
                                    <ul class="list-unstyled">
                                        <template x-for="concern in result.prediction?.areas_of_concern || []">
                                            <li class="mb-1"><i class="bi bi-dot"></i><span x-text="concern"></span></li>
                                        </template>
                                    </ul>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6><i class="bi bi-star text-success me-2"></i>Strengths</h6>
                                    <ul class="list-unstyled">
                                        <template x-for="strength in result.prediction?.strengths || []">
                                            <li class="mb-1"><i class="bi bi-dot"></i><span x-text="strength"></span></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6><i class="bi bi-check2-circle text-primary me-2"></i>Recommendations</h6>
                                <ul class="list-group list-group-flush">
                                    <template x-for="rec in result.prediction?.recommendations || []">
                                        <li class="list-group-item" x-text="rec"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="card border-0 shadow-sm" x-show="!result && !isLoading">
                <div class="card-body text-center py-5">
                    <i class="bi bi-graph-up-arrow text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Select a Student to Predict Performance</h5>
                    <p class="text-muted">Choose a student from the dropdown and click "Predict Performance" to analyze their academic risk.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function performancePredictor() {
    return {
        studentId: '',
        includeAttendance: true,
        includeGrades: true,
        isLoading: false,
        result: null,

        getRiskClass(level) {
            const classes = {
                'low': 'bg-success bg-opacity-10 text-success',
                'medium': 'bg-warning bg-opacity-10 text-warning',
                'high': 'bg-danger bg-opacity-10 text-danger'
            };
            return classes[level] || 'bg-secondary bg-opacity-10';
        },

        async predict() {
            if (!this.studentId) return;
            
            this.isLoading = true;
            this.result = null;

            try {
                const response = await fetch('/api/v1/ai/predict-performance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        student_id: parseInt(this.studentId),
                        include_attendance: this.includeAttendance,
                        include_grades: this.includeGrades
                    })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    this.result = data.data;
                } else {
                    alert('Error: ' + (data.message || 'Failed to predict performance'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while predicting performance');
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endpush
