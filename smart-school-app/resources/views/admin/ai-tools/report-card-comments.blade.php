@extends('layouts.app')

@section('title', 'Report Card Comments Generator')

@section('content')
<div class="container-fluid" x-data="reportCardComments()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Report Card Comments Generator</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ai-tools.index') }}">AI Tools</a></li>
                    <li class="breadcrumb-item active">Report Card Comments</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-input-cursor-text me-2"></i>Student Information
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Student Name</label>
                        <input type="text" class="form-control" x-model="studentName" placeholder="Enter student name">
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Grade</label>
                            <select class="form-select" x-model="grade">
                                <option value="">Select...</option>
                                <option value="A+">A+</option>
                                <option value="A">A</option>
                                <option value="B+">B+</option>
                                <option value="B">B</option>
                                <option value="C+">C+</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="F">F</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Attendance %</label>
                            <input type="number" class="form-control" x-model="attendance" min="0" max="100" placeholder="85">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Strengths (comma separated)</label>
                        <input type="text" class="form-control" x-model="strengthsInput" placeholder="e.g., Problem solving, Creativity">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Areas for Improvement (comma separated)</label>
                        <input type="text" class="form-control" x-model="weaknessesInput" placeholder="e.g., Time management, Participation">
                    </div>
                    <button class="btn btn-success w-100" @click="generate()" :disabled="!isValid() || isLoading">
                        <span x-show="!isLoading"><i class="bi bi-magic me-2"></i>Generate Comment</span>
                        <span x-show="isLoading"><i class="bi bi-hourglass-split me-2"></i>Generating...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" x-show="comment">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-chat-quote me-2"></i>Generated Comment</span>
                    <button class="btn btn-sm btn-outline-primary" @click="copyToClipboard()">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
                <div class="card-body">
                    <div class="p-3 bg-light rounded">
                        <p class="mb-0" x-text="comment" style="white-space: pre-wrap;"></p>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm" x-show="!comment && !isLoading">
                <div class="card-body text-center py-5">
                    <i class="bi bi-chat-quote text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Generate Report Card Comments</h5>
                    <p class="text-muted">Fill in the student information and click "Generate Comment" to create a personalized report card comment.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function reportCardComments() {
    return {
        studentName: '',
        subject: '',
        grade: '',
        attendance: 85,
        strengthsInput: '',
        weaknessesInput: '',
        isLoading: false,
        comment: '',

        isValid() {
            return this.studentName && this.subject && this.grade && this.attendance;
        },

        async generate() {
            if (!this.isValid()) return;
            
            this.isLoading = true;
            this.comment = '';

            const strengths = this.strengthsInput.split(',').map(s => s.trim()).filter(s => s);
            const weaknesses = this.weaknessesInput.split(',').map(s => s.trim()).filter(s => s);

            try {
                const response = await fetch('/api/v1/ai/report-card-comment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        student_name: this.studentName,
                        subject: this.subject,
                        grade: this.grade,
                        attendance: parseFloat(this.attendance),
                        strengths: strengths,
                        weaknesses: weaknesses
                    })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    this.comment = data.data.comment;
                } else {
                    alert('Error: ' + (data.message || 'Failed to generate comment'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while generating comment');
            } finally {
                this.isLoading = false;
            }
        },

        copyToClipboard() {
            navigator.clipboard.writeText(this.comment);
            alert('Comment copied to clipboard!');
        }
    };
}
</script>
@endpush
