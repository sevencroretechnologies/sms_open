@extends('layouts.app')

@section('title', 'Curriculum Alignment Checker')

@section('content')
<div class="container-fluid" x-data="curriculumChecker()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Curriculum Alignment Checker</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ai-tools.index') }}">AI Tools</a></li>
                    <li class="breadcrumb-item active">Curriculum Checker</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-file-earmark-check me-2"></i>Lesson Plan Details
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Education Board</label>
                        <select class="form-select" x-model="board">
                            <option value="CBSE">CBSE</option>
                            <option value="ICSE">ICSE</option>
                            <option value="State Board">State Board</option>
                            <option value="IB">International Baccalaureate (IB)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class</label>
                        <select class="form-select" x-model="classLevel">
                            <option value="">Select class...</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->name }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <select class="form-select" x-model="subject">
                            <option value="">Select subject...</option>
                            @foreach($subjects as $subj)
                                <option value="{{ $subj->name }}">{{ $subj->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lesson Plan</label>
                        <textarea class="form-control" x-model="lessonPlan" rows="10" placeholder="Enter your lesson plan content here...&#10;&#10;Include:&#10;- Learning objectives&#10;- Topics covered&#10;- Activities planned&#10;- Assessment methods"></textarea>
                    </div>
                    <button class="btn btn-warning w-100" @click="check()" :disabled="!isValid() || isLoading">
                        <span x-show="!isLoading"><i class="bi bi-check2-square me-2"></i>Check Alignment</span>
                        <span x-show="isLoading"><i class="bi bi-hourglass-split me-2"></i>Analyzing...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" x-show="result">
                <div class="card-header">
                    <i class="bi bi-clipboard-check me-2"></i>Alignment Report
                </div>
                <div class="card-body">
                    <template x-if="result">
                        <div>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="text-center p-3 rounded" :class="getScoreClass(result.alignment_score)">
                                        <h6 class="text-uppercase small mb-1">Alignment Score</h6>
                                        <h2 class="mb-0" x-text="result.alignment_score + '%'"></h2>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h6 class="text-uppercase small mb-1">Topics Covered</h6>
                                        <h2 class="mb-0 text-success" x-text="result.topics_covered?.length || 0"></h2>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h6 class="text-uppercase small mb-1">Missing Topics</h6>
                                        <h2 class="mb-0 text-danger" x-text="result.missing_topics?.length || 0"></h2>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6><i class="bi bi-info-circle me-2"></i>Overall Assessment</h6>
                                <p class="text-muted" x-text="result.overall_assessment"></p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6><i class="bi bi-check-circle text-success me-2"></i>Topics Covered</h6>
                                    <ul class="list-unstyled">
                                        <template x-for="topic in result.topics_covered || []">
                                            <li class="mb-1"><i class="bi bi-check text-success"></i> <span x-text="topic"></span></li>
                                        </template>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="bi bi-x-circle text-danger me-2"></i>Missing Topics</h6>
                                    <ul class="list-unstyled">
                                        <template x-for="topic in result.missing_topics || []">
                                            <li class="mb-1"><i class="bi bi-x text-danger"></i> <span x-text="topic"></span></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>

                            <div class="mb-4" x-show="result.learning_outcomes && result.learning_outcomes.length > 0">
                                <h6><i class="bi bi-bullseye me-2"></i>Learning Outcomes Alignment</h6>
                                <ul class="list-group list-group-flush">
                                    <template x-for="outcome in result.learning_outcomes || []">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span x-text="outcome.outcome"></span>
                                            <span class="badge" :class="outcome.aligned ? 'bg-success' : 'bg-warning'" x-text="outcome.aligned ? 'Aligned' : 'Partial'"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            <div class="mb-3">
                                <h6><i class="bi bi-lightbulb me-2"></i>Suggestions for Improvement</h6>
                                <ul class="list-group list-group-flush">
                                    <template x-for="suggestion in result.suggestions || []">
                                        <li class="list-group-item"><i class="bi bi-arrow-right text-primary me-2"></i><span x-text="suggestion"></span></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="card border-0 shadow-sm" x-show="!result && !isLoading">
                <div class="card-body text-center py-5">
                    <i class="bi bi-check2-square text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Check Curriculum Alignment</h5>
                    <p class="text-muted">Enter your lesson plan details to verify alignment with CBSE/ICSE curriculum standards.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function curriculumChecker() {
    return {
        board: 'CBSE',
        classLevel: '',
        subject: '',
        lessonPlan: '',
        isLoading: false,
        result: null,

        isValid() {
            return this.board && this.classLevel && this.subject && this.lessonPlan;
        },

        getScoreClass(score) {
            if (score >= 80) return 'bg-success bg-opacity-10 text-success';
            if (score >= 60) return 'bg-warning bg-opacity-10 text-warning';
            return 'bg-danger bg-opacity-10 text-danger';
        },

        async check() {
            if (!this.isValid()) return;
            
            this.isLoading = true;
            this.result = null;

            try {
                const response = await fetch('/api/v1/ai/check-curriculum', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        lesson_plan: this.lessonPlan,
                        board: this.board,
                        class: this.classLevel,
                        subject: this.subject
                    })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    this.result = data.data;
                } else {
                    alert('Error: ' + (data.message || 'Failed to check curriculum alignment'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while checking curriculum alignment');
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endpush
