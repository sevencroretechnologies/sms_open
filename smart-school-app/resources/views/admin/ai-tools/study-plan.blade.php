@extends('layouts.app')

@section('title', 'Study Plan Generator')

@section('content')
<div class="container-fluid" x-data="studyPlanGenerator()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Personalized Study Plan Generator</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ai-tools.index') }}">AI Tools</a></li>
                    <li class="breadcrumb-item active">Study Plan</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-calendar-check me-2"></i>Study Plan Settings
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Target Exam</label>
                        <input type="text" class="form-control" x-model="targetExam" placeholder="e.g., Final Exams, Board Exams">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Weak Subjects (select multiple)</label>
                        <select class="form-select" x-model="weakSubjects" multiple size="4">
                            @foreach($subjects as $subj)
                                <option value="{{ $subj->name }}">{{ $subj->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hours/Day</label>
                            <input type="number" class="form-control" x-model="availableHours" min="1" max="12" placeholder="4">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Duration (Days)</label>
                            <input type="number" class="form-control" x-model="durationDays" min="7" max="90" placeholder="30">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Level</label>
                        <select class="form-select" x-model="currentLevel">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <button class="btn btn-danger w-100" @click="generate()" :disabled="!isValid() || isLoading">
                        <span x-show="!isLoading"><i class="bi bi-magic me-2"></i>Generate Study Plan</span>
                        <span x-show="isLoading"><i class="bi bi-hourglass-split me-2"></i>Generating...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" x-show="result">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-calendar3 me-2"></i>Your Study Plan</span>
                    <span class="badge bg-primary" x-text="'Total: ' + result?.total_hours + ' hours'"></span>
                </div>
                <div class="card-body">
                    <template x-if="result">
                        <div>
                            <div class="alert alert-info mb-4">
                                <i class="bi bi-info-circle me-2"></i>
                                <span x-text="result.overview"></span>
                            </div>

                            <div class="mb-4">
                                <h6><i class="bi bi-flag me-2"></i>Milestones</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <template x-for="(milestone, index) in result.milestones || []">
                                        <span class="badge bg-secondary" x-text="'Week ' + (index + 1) + ': ' + milestone"></span>
                                    </template>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6><i class="bi bi-lightbulb me-2"></i>Study Tips</h6>
                                <ul class="list-unstyled">
                                    <template x-for="tip in result.tips || []">
                                        <li class="mb-1"><i class="bi bi-check-circle text-success me-2"></i><span x-text="tip"></span></li>
                                    </template>
                                </ul>
                            </div>

                            <h6><i class="bi bi-calendar-week me-2"></i>Daily Schedule</h6>
                            <div class="accordion" id="scheduleAccordion">
                                <template x-for="(day, index) in (result.daily_schedule || []).slice(0, 7)">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" :data-bs-target="'#day' + index" data-bs-toggle="collapse">
                                                <span x-text="'Day ' + day.day + ' (' + day.total_study_hours + ' hours)'"></span>
                                            </button>
                                        </h2>
                                        <div :id="'day' + index" class="accordion-collapse collapse" data-bs-parent="#scheduleAccordion">
                                            <div class="accordion-body">
                                                <template x-for="subject in day.subjects || []">
                                                    <div class="mb-3 p-2 bg-light rounded">
                                                        <div class="d-flex justify-content-between">
                                                            <strong x-text="subject.subject"></strong>
                                                            <span class="badge bg-primary" x-text="subject.duration_minutes + ' min'"></span>
                                                        </div>
                                                        <small class="text-muted" x-text="'Topic: ' + subject.topic"></small>
                                                        <div class="mt-1">
                                                            <template x-for="activity in subject.activities || []">
                                                                <span class="badge bg-secondary me-1" x-text="activity"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="card border-0 shadow-sm" x-show="!result && !isLoading">
                <div class="card-body text-center py-5">
                    <i class="bi bi-calendar-check text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Generate Personalized Study Plan</h5>
                    <p class="text-muted">Configure your study preferences and click "Generate Study Plan" to create a customized day-by-day schedule.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function studyPlanGenerator() {
    return {
        targetExam: '',
        weakSubjects: [],
        availableHours: 4,
        durationDays: 30,
        currentLevel: 'intermediate',
        isLoading: false,
        result: null,

        isValid() {
            return this.targetExam && this.weakSubjects.length > 0 && this.availableHours;
        },

        async generate() {
            if (!this.isValid()) return;
            
            this.isLoading = true;
            this.result = null;

            try {
                const response = await fetch('/api/v1/ai/study-plan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        target_exam: this.targetExam,
                        weak_subjects: Array.from(this.weakSubjects),
                        available_hours: parseFloat(this.availableHours),
                        duration_days: parseInt(this.durationDays),
                        current_level: this.currentLevel
                    })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    this.result = data.data;
                } else {
                    alert('Error: ' + (data.message || 'Failed to generate study plan'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while generating study plan');
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endpush
