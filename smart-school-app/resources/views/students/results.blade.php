{{-- Student Results View --}}
{{-- Prompt 149: Student exam results view with charts and report card --}}

@extends('layouts.app')

@section('title', 'Student Results - ' . ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))

@section('content')
<div x-data="studentResults()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Exam Results</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.show', $student->id ?? 0) }}">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</a></li>
                    <li class="breadcrumb-item active">Results</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('students.show', $student->id ?? 0) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Profile
            </a>
            <button type="button" class="btn btn-outline-primary" @click="exportResults()">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button type="button" class="btn btn-primary" @click="printReportCard()">
                <i class="bi bi-printer me-1"></i> Print Report Card
            </button>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <img 
                    src="{{ $student->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) . '&background=4f46e5&color=fff&size=60' }}"
                    alt="{{ $student->first_name ?? '' }}"
                    class="rounded-circle me-3"
                    style="width: 60px; height: 60px; object-fit: cover;"
                >
                <div>
                    <h5 class="mb-1">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</h5>
                    <p class="text-muted mb-0">
                        <span class="badge bg-light text-dark me-2">{{ $student->admission_number ?? 'N/A' }}</span>
                        <span class="badge bg-primary">{{ $student->class->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-percent text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $overallPercentage ?? 0 }}%</h3>
                            <small class="text-muted">Overall Percentage</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="bi bi-award text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $overallGrade ?? 'N/A' }}</h3>
                            <small class="text-muted">Overall Grade</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="bi bi-journal-check text-info fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $totalExams ?? 0 }}</h3>
                            <small class="text-muted">Total Exams</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="bi bi-graph-up text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $averageMarks ?? 0 }}</h3>
                            <small class="text-muted">Average Marks</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <x-card class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Academic Session</label>
                <select class="form-select" x-model="filterSession" @change="loadResults()">
                    <option value="">All Sessions</option>
                    @foreach($academicSessions ?? [] as $session)
                    <option value="{{ $session->id }}">{{ $session->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Exam Type</label>
                <select class="form-select" x-model="filterExamType">
                    <option value="">All Types</option>
                    @foreach($examTypes ?? [] as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Exam</label>
                <select class="form-select" x-model="filterExam">
                    <option value="">All Exams</option>
                    <template x-for="exam in filteredExams" :key="exam.id">
                        <option :value="exam.id" x-text="exam.name"></option>
                    </template>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-secondary w-100" @click="resetFilters()">
                    <i class="bi bi-x-circle me-1"></i> Reset Filters
                </button>
            </div>
        </div>
    </x-card>

    <div class="row g-4">
        <!-- Results Table -->
        <div class="col-lg-8">
            <x-card title="Exam Results" icon="bi-table">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Exam</th>
                                <th>Subject</th>
                                <th class="text-center">Full Marks</th>
                                <th class="text-center">Obtained</th>
                                <th class="text-center">Percentage</th>
                                <th class="text-center">Grade</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="result in filteredResults" :key="result.id">
                                <tr>
                                    <td>
                                        <span class="fw-medium" x-text="result.exam_name"></span>
                                        <small class="d-block text-muted" x-text="result.exam_type"></small>
                                    </td>
                                    <td x-text="result.subject_name"></td>
                                    <td class="text-center" x-text="result.full_marks"></td>
                                    <td class="text-center">
                                        <span class="fw-bold" :class="{
                                            'text-success': result.obtained_marks >= result.passing_marks,
                                            'text-danger': result.obtained_marks < result.passing_marks
                                        }" x-text="result.obtained_marks"></span>
                                    </td>
                                    <td class="text-center" x-text="result.percentage + '%'"></td>
                                    <td class="text-center">
                                        <span class="badge" :class="{
                                            'bg-success': ['A+', 'A'].includes(result.grade),
                                            'bg-primary': ['B+', 'B'].includes(result.grade),
                                            'bg-info': ['C+', 'C'].includes(result.grade),
                                            'bg-warning': ['D+', 'D'].includes(result.grade),
                                            'bg-danger': result.grade === 'F'
                                        }" x-text="result.grade"></span>
                                    </td>
                                    <td>
                                        <small x-text="result.remarks || '-'"></small>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredResults.length === 0">
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                                    No exam results found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Exam Summary -->
                <template x-if="selectedExamSummary">
                    <div class="border-top pt-3 mt-3">
                        <div class="row g-3">
                            <div class="col-md-3 text-center">
                                <h5 class="mb-0" x-text="selectedExamSummary.total_marks + '/' + selectedExamSummary.max_marks"></h5>
                                <small class="text-muted">Total Marks</small>
                            </div>
                            <div class="col-md-3 text-center">
                                <h5 class="mb-0" x-text="selectedExamSummary.percentage + '%'"></h5>
                                <small class="text-muted">Percentage</small>
                            </div>
                            <div class="col-md-3 text-center">
                                <h5 class="mb-0">
                                    <span class="badge bg-primary" x-text="selectedExamSummary.grade"></span>
                                </h5>
                                <small class="text-muted">Grade</small>
                            </div>
                            <div class="col-md-3 text-center">
                                <h5 class="mb-0" x-text="selectedExamSummary.rank || '-'"></h5>
                                <small class="text-muted">Class Rank</small>
                            </div>
                        </div>
                    </div>
                </template>
            </x-card>
        </div>

        <!-- Subject Performance Chart -->
        <div class="col-lg-4">
            <x-card title="Subject Performance" icon="bi-bar-chart">
                <canvas id="subjectChart" height="300"></canvas>
            </x-card>
        </div>

        <!-- Performance Trend Chart -->
        <div class="col-12">
            <x-card title="Performance Trend" icon="bi-graph-up">
                <canvas id="trendChart" height="100"></canvas>
            </x-card>
        </div>

        <!-- Grade Distribution -->
        <div class="col-md-6">
            <x-card title="Grade Distribution" icon="bi-pie-chart">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <canvas id="gradeChart" height="200"></canvas>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex flex-column gap-2">
                            <template x-for="(count, grade) in gradeDistribution" :key="grade">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <span class="badge me-2" :class="{
                                            'bg-success': ['A+', 'A'].includes(grade),
                                            'bg-primary': ['B+', 'B'].includes(grade),
                                            'bg-info': ['C+', 'C'].includes(grade),
                                            'bg-warning': ['D+', 'D'].includes(grade),
                                            'bg-danger': grade === 'F'
                                        }" x-text="grade"></span>
                                        <span x-text="getGradeLabel(grade)"></span>
                                    </span>
                                    <span class="fw-bold" x-text="count"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Exam History -->
        <div class="col-md-6">
            <x-card title="Exam History" icon="bi-clock-history">
                <div class="list-group list-group-flush">
                    <template x-for="exam in examHistory" :key="exam.id">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0" x-text="exam.name"></h6>
                                <small class="text-muted" x-text="exam.date"></small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary" x-text="exam.percentage + '%'"></span>
                                <span class="badge" :class="{
                                    'bg-success': ['A+', 'A'].includes(exam.grade),
                                    'bg-primary': ['B+', 'B'].includes(exam.grade),
                                    'bg-info': ['C+', 'C'].includes(exam.grade),
                                    'bg-warning': ['D+', 'D'].includes(exam.grade),
                                    'bg-danger': exam.grade === 'F'
                                }" x-text="exam.grade"></span>
                            </div>
                        </div>
                    </template>
                    <div x-show="examHistory.length === 0" class="text-center py-4 text-muted">
                        No exam history available
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function studentResults() {
    return {
        results: @json($results ?? []),
        exams: @json($exams ?? []),
        
        filterSession: '',
        filterExamType: '',
        filterExam: '',
        
        subjectChart: null,
        trendChart: null,
        gradeChart: null,
        
        get filteredExams() {
            let exams = this.exams;
            if (this.filterExamType) {
                exams = exams.filter(e => e.exam_type_id == this.filterExamType);
            }
            return exams;
        },
        
        get filteredResults() {
            let results = this.results;
            
            if (this.filterSession) {
                results = results.filter(r => r.academic_session_id == this.filterSession);
            }
            if (this.filterExamType) {
                results = results.filter(r => r.exam_type_id == this.filterExamType);
            }
            if (this.filterExam) {
                results = results.filter(r => r.exam_id == this.filterExam);
            }
            
            return results;
        },
        
        get selectedExamSummary() {
            if (!this.filterExam || this.filteredResults.length === 0) return null;
            
            const total = this.filteredResults.reduce((sum, r) => sum + (r.obtained_marks || 0), 0);
            const max = this.filteredResults.reduce((sum, r) => sum + (r.full_marks || 0), 0);
            const percentage = max > 0 ? Math.round((total / max) * 100) : 0;
            
            return {
                total_marks: total,
                max_marks: max,
                percentage: percentage,
                grade: this.getGradeFromPercentage(percentage),
                rank: this.filteredResults[0]?.class_rank || null
            };
        },
        
        get gradeDistribution() {
            const distribution = {};
            this.filteredResults.forEach(r => {
                if (r.grade) {
                    distribution[r.grade] = (distribution[r.grade] || 0) + 1;
                }
            });
            return distribution;
        },
        
        get examHistory() {
            const examMap = new Map();
            this.results.forEach(r => {
                if (!examMap.has(r.exam_id)) {
                    examMap.set(r.exam_id, {
                        id: r.exam_id,
                        name: r.exam_name,
                        date: r.exam_date,
                        results: []
                    });
                }
                examMap.get(r.exam_id).results.push(r);
            });
            
            return Array.from(examMap.values()).map(exam => {
                const total = exam.results.reduce((sum, r) => sum + (r.obtained_marks || 0), 0);
                const max = exam.results.reduce((sum, r) => sum + (r.full_marks || 0), 0);
                const percentage = max > 0 ? Math.round((total / max) * 100) : 0;
                
                return {
                    ...exam,
                    percentage: percentage,
                    grade: this.getGradeFromPercentage(percentage)
                };
            }).slice(0, 5);
        },
        
        getGradeFromPercentage(percentage) {
            if (percentage >= 90) return 'A+';
            if (percentage >= 80) return 'A';
            if (percentage >= 70) return 'B+';
            if (percentage >= 60) return 'B';
            if (percentage >= 50) return 'C+';
            if (percentage >= 40) return 'C';
            if (percentage >= 33) return 'D';
            return 'F';
        },
        
        getGradeLabel(grade) {
            const labels = {
                'A+': 'Outstanding',
                'A': 'Excellent',
                'B+': 'Very Good',
                'B': 'Good',
                'C+': 'Above Average',
                'C': 'Average',
                'D+': 'Below Average',
                'D': 'Pass',
                'F': 'Fail'
            };
            return labels[grade] || grade;
        },
        
        resetFilters() {
            this.filterSession = '';
            this.filterExamType = '';
            this.filterExam = '';
        },
        
        loadResults() {
            // Results are already loaded, filtering is done client-side
            this.updateCharts();
        },
        
        exportResults() {
            let url = '{{ route("students.results.export", $student->id ?? 0) }}?';
            if (this.filterSession) url += 'session=' + this.filterSession + '&';
            if (this.filterExamType) url += 'exam_type=' + this.filterExamType + '&';
            if (this.filterExam) url += 'exam=' + this.filterExam;
            window.location.href = url;
        },
        
        printReportCard() {
            let url = '{{ route("students.results.report-card", $student->id ?? 0) }}?';
            if (this.filterExam) url += 'exam=' + this.filterExam;
            window.open(url, '_blank');
        },
        
        initCharts() {
            // Subject Performance Chart
            const subjectCtx = document.getElementById('subjectChart');
            if (subjectCtx) {
                const subjectData = this.getSubjectData();
                this.subjectChart = new Chart(subjectCtx, {
                    type: 'bar',
                    data: {
                        labels: subjectData.labels,
                        datasets: [{
                            label: 'Marks Obtained',
                            data: subjectData.obtained,
                            backgroundColor: 'rgba(79, 70, 229, 0.8)',
                            borderRadius: 4
                        }, {
                            label: 'Full Marks',
                            data: subjectData.full,
                            backgroundColor: 'rgba(229, 231, 235, 0.8)',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        indexAxis: 'y',
                        plugins: {
                            legend: { position: 'bottom' }
                        },
                        scales: {
                            x: { beginAtZero: true }
                        }
                    }
                });
            }
            
            // Performance Trend Chart
            const trendCtx = document.getElementById('trendChart');
            if (trendCtx) {
                const trendData = this.getTrendData();
                this.trendChart = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: trendData.labels,
                        datasets: [{
                            label: 'Percentage',
                            data: trendData.percentages,
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                min: 0,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Grade Distribution Chart
            const gradeCtx = document.getElementById('gradeChart');
            if (gradeCtx) {
                const gradeData = this.getGradeData();
                this.gradeChart = new Chart(gradeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: gradeData.labels,
                        datasets: [{
                            data: gradeData.counts,
                            backgroundColor: ['#10b981', '#22c55e', '#3b82f6', '#6366f1', '#06b6d4', '#14b8a6', '#f59e0b', '#ef4444'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '60%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        },
        
        getSubjectData() {
            const subjectMap = new Map();
            this.filteredResults.forEach(r => {
                if (!subjectMap.has(r.subject_name)) {
                    subjectMap.set(r.subject_name, { obtained: 0, full: 0, count: 0 });
                }
                const data = subjectMap.get(r.subject_name);
                data.obtained += r.obtained_marks || 0;
                data.full += r.full_marks || 0;
                data.count++;
            });
            
            const labels = [];
            const obtained = [];
            const full = [];
            
            subjectMap.forEach((data, subject) => {
                labels.push(subject);
                obtained.push(Math.round(data.obtained / data.count));
                full.push(Math.round(data.full / data.count));
            });
            
            return { labels, obtained, full };
        },
        
        getTrendData() {
            const examMap = new Map();
            this.results.forEach(r => {
                if (!examMap.has(r.exam_id)) {
                    examMap.set(r.exam_id, {
                        name: r.exam_name,
                        date: r.exam_date,
                        total: 0,
                        max: 0
                    });
                }
                const data = examMap.get(r.exam_id);
                data.total += r.obtained_marks || 0;
                data.max += r.full_marks || 0;
            });
            
            const sorted = Array.from(examMap.values()).sort((a, b) => new Date(a.date) - new Date(b.date));
            
            return {
                labels: sorted.map(e => e.name),
                percentages: sorted.map(e => e.max > 0 ? Math.round((e.total / e.max) * 100) : 0)
            };
        },
        
        getGradeData() {
            const labels = Object.keys(this.gradeDistribution);
            const counts = Object.values(this.gradeDistribution);
            return { labels, counts };
        },
        
        updateCharts() {
            if (this.subjectChart) {
                const subjectData = this.getSubjectData();
                this.subjectChart.data.labels = subjectData.labels;
                this.subjectChart.data.datasets[0].data = subjectData.obtained;
                this.subjectChart.data.datasets[1].data = subjectData.full;
                this.subjectChart.update();
            }
            
            if (this.gradeChart) {
                const gradeData = this.getGradeData();
                this.gradeChart.data.labels = gradeData.labels;
                this.gradeChart.data.datasets[0].data = gradeData.counts;
                this.gradeChart.update();
            }
        },
        
        init() {
            this.$nextTick(() => {
                this.initCharts();
            });
            
            this.$watch('filteredResults', () => {
                this.updateCharts();
            });
        }
    };
}
</script>
@endpush
@endsection
