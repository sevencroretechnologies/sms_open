@extends('layouts.app')

@section('title', 'Assignment Grader')

@section('content')
<div class="container-fluid" x-data="assignmentGrader()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">AI Assignment Grader</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ai-tools.index') }}">AI Tools</a></li>
                    <li class="breadcrumb-item active">Assignment Grader</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-pencil-square me-2"></i>Assignment Details
                </div>
                <div class="card-body">
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
                        <label class="form-label">Topic</label>
                        <input type="text" class="form-control" x-model="topic" placeholder="e.g., Photosynthesis, World War II">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rubric (Optional)</label>
                        <textarea class="form-control" x-model="rubric" rows="3" placeholder="Describe the grading criteria..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Student Submission</label>
                        <textarea class="form-control" x-model="submission" rows="8" placeholder="Paste the student's assignment here..."></textarea>
                    </div>
                    <button class="btn btn-warning w-100" @click="grade()" :disabled="!isValid() || isLoading">
                        <span x-show="!isLoading"><i class="bi bi-check2-circle me-2"></i>Grade Assignment</span>
                        <span x-show="isLoading"><i class="bi bi-hourglass-split me-2"></i>Grading...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" x-show="result">
                <div class="card-header">
                    <i class="bi bi-clipboard-check me-2"></i>Grading Results
                </div>
                <div class="card-body">
                    <template x-if="result">
                        <div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                        <h6 class="text-uppercase small mb-1">Score</h6>
                                        <h2 class="mb-0 text-primary" x-text="result.score + '/100'"></h2>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                        <h6 class="text-uppercase small mb-1">Grade</h6>
                                        <h2 class="mb-0 text-success" x-text="result.grade"></h2>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6>Rubric Scores</h6>
                                <div class="row g-2">
                                    <div class="col-6 col-md-3">
                                        <div class="p-2 bg-light rounded text-center">
                                            <small class="text-muted d-block">Content</small>
                                            <strong x-text="result.rubric_scores?.content + '/25'"></strong>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-2 bg-light rounded text-center">
                                            <small class="text-muted d-block">Organization</small>
                                            <strong x-text="result.rubric_scores?.organization + '/25'"></strong>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-2 bg-light rounded text-center">
                                            <small class="text-muted d-block">Language</small>
                                            <strong x-text="result.rubric_scores?.language + '/25'"></strong>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-2 bg-light rounded text-center">
                                            <small class="text-muted d-block">Creativity</small>
                                            <strong x-text="result.rubric_scores?.creativity + '/25'"></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6><i class="bi bi-star text-success me-2"></i>Strengths</h6>
                                    <ul class="list-unstyled">
                                        <template x-for="item in result.feedback?.strengths || []">
                                            <li class="mb-1"><i class="bi bi-check text-success"></i> <span x-text="item"></span></li>
                                        </template>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="bi bi-arrow-up-circle text-warning me-2"></i>Areas to Improve</h6>
                                    <ul class="list-unstyled">
                                        <template x-for="item in result.feedback?.improvements || []">
                                            <li class="mb-1"><i class="bi bi-arrow-right text-warning"></i> <span x-text="item"></span></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6><i class="bi bi-chat-left-text me-2"></i>Detailed Feedback</h6>
                                <div class="p-3 bg-light rounded">
                                    <p class="mb-0" x-text="result.feedback?.detailed_comments" style="white-space: pre-wrap;"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="card border-0 shadow-sm" x-show="!result && !isLoading">
                <div class="card-body text-center py-5">
                    <i class="bi bi-pencil-square text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Grade Student Assignments</h5>
                    <p class="text-muted">Enter the assignment details and student submission to receive AI-powered grading with detailed feedback.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function assignmentGrader() {
    return {
        subject: '',
        topic: '',
        rubric: '',
        submission: '',
        isLoading: false,
        result: null,

        isValid() {
            return this.subject && this.topic && this.submission;
        },

        async grade() {
            if (!this.isValid()) return;
            
            this.isLoading = true;
            this.result = null;

            try {
                const response = await fetch('/api/v1/ai/grade-assignment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        subject: this.subject,
                        topic: this.topic,
                        rubric: this.rubric || 'Standard academic rubric',
                        student_submission: this.submission
                    })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    this.result = data.data;
                } else {
                    alert('Error: ' + (data.message || 'Failed to grade assignment'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while grading assignment');
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endpush
