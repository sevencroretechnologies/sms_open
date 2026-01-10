@extends('layouts.app')

@section('title', 'Timetable Optimizer')

@section('content')
<div class="container-fluid" x-data="timetableOptimizer()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Smart Timetable Optimizer</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ai-tools.index') }}">AI Tools</a></li>
                    <li class="breadcrumb-item active">Timetable Optimizer</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-sliders me-2"></i>Optimization Settings
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Select Teachers</label>
                        <select class="form-select" x-model="selectedTeachers" multiple size="4">
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->user->name ?? 'Teacher ' . $teacher->id }}">{{ $teacher->user->name ?? 'Teacher ' . $teacher->id }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Subjects</label>
                        <select class="form-select" x-model="selectedSubjects" multiple size="4">
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Available Rooms (comma separated)</label>
                        <input type="text" class="form-control" x-model="roomsInput" placeholder="e.g., Room 101, Room 102, Lab 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Periods Per Day</label>
                        <input type="number" class="form-control" x-model="periodsPerDay" min="4" max="10" placeholder="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Days Per Week</label>
                        <select class="form-select" x-model="daysPerWeek">
                            <option value="5">5 Days (Mon-Fri)</option>
                            <option value="6">6 Days (Mon-Sat)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Constraints (Optional)</label>
                        <textarea class="form-control" x-model="constraints" rows="3" placeholder="e.g., No Math after lunch, PE only in morning..."></textarea>
                    </div>
                    <button class="btn btn-primary w-100" @click="optimize()" :disabled="!isValid() || isLoading">
                        <span x-show="!isLoading"><i class="bi bi-magic me-2"></i>Optimize Timetable</span>
                        <span x-show="isLoading"><i class="bi bi-hourglass-split me-2"></i>Optimizing...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" x-show="result">
                <div class="card-header">
                    <i class="bi bi-table me-2"></i>Optimized Timetable
                </div>
                <div class="card-body">
                    <template x-if="result">
                        <div>
                            <div class="alert alert-success mb-4">
                                <i class="bi bi-check-circle me-2"></i>
                                <span x-text="result.optimization_notes"></span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Period</th>
                                            <template x-for="day in getDays()">
                                                <th class="text-center" x-text="day"></th>
                                            </template>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="period in parseInt(periodsPerDay)">
                                            <tr>
                                                <td class="fw-bold" x-text="'Period ' + period"></td>
                                                <template x-for="day in getDays()">
                                                    <td class="text-center small">
                                                        <template x-if="getSlot(day, period)">
                                                            <div>
                                                                <div class="fw-bold text-primary" x-text="getSlot(day, period).subject"></div>
                                                                <div class="text-muted" x-text="getSlot(day, period).teacher"></div>
                                                                <div class="badge bg-light text-dark" x-text="getSlot(day, period).room"></div>
                                                            </div>
                                                        </template>
                                                        <template x-if="!getSlot(day, period)">
                                                            <span class="text-muted">-</span>
                                                        </template>
                                                    </td>
                                                </template>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3" x-show="result.conflicts && result.conflicts.length > 0">
                                <h6 class="text-warning"><i class="bi bi-exclamation-triangle me-2"></i>Potential Conflicts</h6>
                                <ul class="list-unstyled">
                                    <template x-for="conflict in result.conflicts || []">
                                        <li class="text-warning small"><i class="bi bi-dot"></i><span x-text="conflict"></span></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="card border-0 shadow-sm" x-show="!result && !isLoading">
                <div class="card-body text-center py-5">
                    <i class="bi bi-table text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Optimize Your Timetable</h5>
                    <p class="text-muted">Configure the settings and click "Optimize Timetable" to generate an AI-optimized schedule avoiding conflicts.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function timetableOptimizer() {
    return {
        selectedTeachers: [],
        selectedSubjects: [],
        roomsInput: '',
        periodsPerDay: 8,
        daysPerWeek: 5,
        constraints: '',
        isLoading: false,
        result: null,

        isValid() {
            return this.selectedTeachers.length > 0 && this.selectedSubjects.length > 0 && this.roomsInput;
        },

        getDays() {
            const allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            return allDays.slice(0, parseInt(this.daysPerWeek));
        },

        getSlot(day, period) {
            if (!this.result || !this.result.schedule) return null;
            const daySchedule = this.result.schedule.find(s => s.day === day);
            if (!daySchedule || !daySchedule.periods) return null;
            return daySchedule.periods.find(p => p.period === period);
        },

        async optimize() {
            if (!this.isValid()) return;
            
            this.isLoading = true;
            this.result = null;

            const rooms = this.roomsInput.split(',').map(r => r.trim()).filter(r => r);

            try {
                const response = await fetch('/api/v1/ai/optimize-timetable', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        teachers: Array.from(this.selectedTeachers),
                        subjects: Array.from(this.selectedSubjects),
                        rooms: rooms,
                        periods_per_day: parseInt(this.periodsPerDay),
                        days_per_week: parseInt(this.daysPerWeek),
                        constraints: this.constraints
                    })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    this.result = data.data;
                } else {
                    alert('Error: ' + (data.message || 'Failed to optimize timetable'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while optimizing timetable');
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endpush
