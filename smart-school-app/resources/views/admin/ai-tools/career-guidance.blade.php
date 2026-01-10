@extends('layouts.app')

@section('title', 'Career Guidance Assistant')

@section('content')
<div class="container-fluid" x-data="careerGuidance()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Career Guidance Assistant</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ai-tools.index') }}">AI Tools</a></li>
                    <li class="breadcrumb-item active">Career Guidance</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-person-badge me-2"></i>Student Profile
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Interests (comma separated)</label>
                        <input type="text" class="form-control" x-model="interestsInput" placeholder="e.g., Technology, Art, Science">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Strong Subjects (comma separated)</label>
                        <input type="text" class="form-control" x-model="strongSubjectsInput" placeholder="e.g., Mathematics, Physics">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Career Aspirations</label>
                        <textarea class="form-control" x-model="aspirations" rows="3" placeholder="What career goals does the student have?"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Grade Level</label>
                        <select class="form-select" x-model="gradeLevel">
                            <option value="8">Grade 8</option>
                            <option value="9">Grade 9</option>
                            <option value="10">Grade 10</option>
                            <option value="11">Grade 11</option>
                            <option value="12">Grade 12</option>
                        </select>
                    </div>
                    <button class="btn btn-success w-100" @click="getGuidance()" :disabled="!isValid() || isLoading">
                        <span x-show="!isLoading"><i class="bi bi-compass me-2"></i>Get Career Guidance</span>
                        <span x-show="isLoading"><i class="bi bi-hourglass-split me-2"></i>Analyzing...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" x-show="result">
                <div class="card-header">
                    <i class="bi bi-briefcase me-2"></i>Career Guidance Report
                </div>
                <div class="card-body">
                    <template x-if="result">
                        <div>
                            <div class="alert alert-info mb-4">
                                <i class="bi bi-info-circle me-2"></i>
                                <span x-text="result.summary"></span>
                            </div>

                            <h6><i class="bi bi-signpost-split me-2"></i>Recommended Career Paths</h6>
                            <div class="row mb-4">
                                <template x-for="(career, index) in result.career_paths || []" :key="index">
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <h6 class="card-title text-primary" x-text="career.title"></h6>
                                                <p class="card-text small text-muted" x-text="career.description"></p>
                                                <div class="mb-2">
                                                    <small class="text-success"><strong>Match Score:</strong> <span x-text="career.match_score + '%'"></span></small>
                                                </div>
                                                <div>
                                                    <small class="text-muted"><strong>Salary Range:</strong> <span x-text="career.salary_range"></span></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <h6><i class="bi bi-tools me-2"></i>Required Skills</h6>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <template x-for="skill in result.required_skills || []">
                                    <span class="badge bg-primary" x-text="skill"></span>
                                </template>
                            </div>

                            <h6><i class="bi bi-book me-2"></i>Recommended Courses</h6>
                            <ul class="list-group list-group-flush mb-4">
                                <template x-for="course in result.recommended_courses || []">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span x-text="course.name"></span>
                                        <span class="badge bg-secondary" x-text="course.type"></span>
                                    </li>
                                </template>
                            </ul>

                            <h6><i class="bi bi-list-check me-2"></i>Next Steps</h6>
                            <ol class="list-group list-group-numbered">
                                <template x-for="step in result.next_steps || []">
                                    <li class="list-group-item" x-text="step"></li>
                                </template>
                            </ol>
                        </div>
                    </template>
                </div>
            </div>

            <div class="card border-0 shadow-sm" x-show="!result && !isLoading">
                <div class="card-body text-center py-5">
                    <i class="bi bi-briefcase text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Get Personalized Career Guidance</h5>
                    <p class="text-muted">Enter the student's interests, strengths, and aspirations to receive AI-powered career recommendations.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function careerGuidance() {
    return {
        interestsInput: '',
        strongSubjectsInput: '',
        aspirations: '',
        gradeLevel: '10',
        isLoading: false,
        result: null,

        isValid() {
            return this.interestsInput && this.strongSubjectsInput;
        },

        async getGuidance() {
            if (!this.isValid()) return;
            
            this.isLoading = true;
            this.result = null;

            const interests = this.interestsInput.split(',').map(s => s.trim()).filter(s => s);
            const strongSubjects = this.strongSubjectsInput.split(',').map(s => s.trim()).filter(s => s);

            try {
                const response = await fetch('/api/v1/ai/career-guidance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        interests: interests,
                        strong_subjects: strongSubjects,
                        aspirations: this.aspirations,
                        grade_level: this.gradeLevel
                    })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    this.result = data.data;
                } else {
                    alert('Error: ' + (data.message || 'Failed to get career guidance'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while getting career guidance');
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endpush
