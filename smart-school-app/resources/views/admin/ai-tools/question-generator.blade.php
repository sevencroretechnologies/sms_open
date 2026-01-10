@extends('layouts.app')

@section('title', 'Question Bank Generator')

@section('content')
<div class="container-fluid" x-data="questionGenerator()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Question Bank Generator</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ai-tools.index') }}">AI Tools</a></li>
                    <li class="breadcrumb-item active">Question Generator</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <i class="bi bi-gear me-2"></i>Generation Settings
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
                        <input type="text" class="form-control" x-model="topic" placeholder="e.g., Algebra, Photosynthesis">
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Difficulty</label>
                            <select class="form-select" x-model="difficulty">
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                                <option value="mixed">Mixed</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Count</label>
                            <input type="number" class="form-control" x-model="count" min="1" max="20" placeholder="5">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question Types</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" x-model="questionTypes" value="mcq" id="typeMcq">
                            <label class="form-check-label" for="typeMcq">Multiple Choice (MCQ)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" x-model="questionTypes" value="short" id="typeShort">
                            <label class="form-check-label" for="typeShort">Short Answer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" x-model="questionTypes" value="long" id="typeLong">
                            <label class="form-check-label" for="typeLong">Long Answer</label>
                        </div>
                    </div>
                    <button class="btn btn-secondary w-100" @click="generate()" :disabled="!isValid() || isLoading">
                        <span x-show="!isLoading"><i class="bi bi-magic me-2"></i>Generate Questions</span>
                        <span x-show="isLoading"><i class="bi bi-hourglass-split me-2"></i>Generating...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" x-show="questions.length > 0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-ol me-2"></i>Generated Questions</span>
                    <span class="badge bg-primary" x-text="questions.length + ' questions'"></span>
                </div>
                <div class="card-body">
                    <template x-for="(q, index) in questions" :key="index">
                        <div class="mb-4 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge" :class="getDifficultyClass(q.difficulty)" x-text="q.difficulty"></span>
                                <span class="badge bg-info" x-text="q.type.toUpperCase()"></span>
                            </div>
                            <h6 class="mb-2"><span x-text="'Q' + (index + 1) + '. '"></span><span x-text="q.question"></span></h6>
                            
                            <template x-if="q.type === 'mcq' && q.options">
                                <div class="ms-3 mb-2">
                                    <template x-for="(opt, optIndex) in q.options" :key="optIndex">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" disabled :checked="opt === q.answer">
                                            <label class="form-check-label" :class="{'text-success fw-bold': opt === q.answer}" x-text="String.fromCharCode(65 + optIndex) + ') ' + opt"></label>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <div class="mt-2 p-2 bg-success bg-opacity-10 rounded">
                                <small class="text-success"><strong>Answer:</strong> <span x-text="q.answer"></span></small>
                            </div>
                            <div class="mt-1 p-2 bg-light rounded" x-show="q.marking_scheme">
                                <small class="text-muted"><strong>Marking Scheme:</strong> <span x-text="q.marking_scheme"></span></small>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="card border-0 shadow-sm" x-show="questions.length === 0 && !isLoading">
                <div class="card-body text-center py-5">
                    <i class="bi bi-question-circle text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Generate Question Bank</h5>
                    <p class="text-muted">Configure the settings and click "Generate Questions" to create a custom question bank with answers.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function questionGenerator() {
    return {
        subject: '',
        topic: '',
        classLevel: '',
        difficulty: 'medium',
        count: 5,
        questionTypes: ['mcq'],
        isLoading: false,
        questions: [],

        isValid() {
            return this.subject && this.topic && this.classLevel && this.questionTypes.length > 0;
        },

        getDifficultyClass(difficulty) {
            const classes = {
                'easy': 'bg-success',
                'medium': 'bg-warning',
                'hard': 'bg-danger'
            };
            return classes[difficulty] || 'bg-secondary';
        },

        async generate() {
            if (!this.isValid()) return;
            
            this.isLoading = true;
            this.questions = [];

            try {
                const response = await fetch('/api/v1/ai/generate-questions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        subject: this.subject,
                        topic: this.topic,
                        class: this.classLevel,
                        difficulty: this.difficulty,
                        count: parseInt(this.count),
                        question_types: this.questionTypes
                    })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    this.questions = data.data.questions || [];
                } else {
                    alert('Error: ' + (data.message || 'Failed to generate questions'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while generating questions');
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endpush
