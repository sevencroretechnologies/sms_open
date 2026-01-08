{{-- Student Promotion View --}}
{{-- Prompt 150: Student promotion view for bulk promotion --}}

@extends('layouts.app')

@section('title', 'Student Promotion')

@section('content')
<div x-data="studentPromotion()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Student Promotion</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">Promotion</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Students
            </a>
        </div>
    </div>

    <!-- Promotion Form -->
    <x-card title="Promotion Settings" icon="bi-gear" class="mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <h6 class="text-primary mb-3"><i class="bi bi-arrow-right-circle me-2"></i>From (Current)</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="fromSession" @change="loadStudents()" required>
                            <option value="">Select Session</option>
                            @foreach($academicSessions ?? [] as $session)
                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="fromClass" @change="loadFromSections(); loadStudents()" required>
                            <option value="">Select Class</option>
                            @foreach($classes ?? [] as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Section</label>
                        <select class="form-select" x-model="fromSection" @change="loadStudents()">
                            <option value="">All Sections</option>
                            <template x-for="section in fromSections" :key="section.id">
                                <option :value="section.id" x-text="section.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <h6 class="text-success mb-3"><i class="bi bi-arrow-up-circle me-2"></i>To (Promoted)</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="toSession" required>
                            <option value="">Select Session</option>
                            @foreach($academicSessions ?? [] as $session)
                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="toClass" @change="loadToSections()" required>
                            <option value="">Select Class</option>
                            @foreach($classes ?? [] as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Section <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="toSection" required>
                            <option value="">Select Section</option>
                            <template x-for="section in toSections" :key="section.id">
                                <option :value="section.id" x-text="section.name"></option>
                            </template>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Promotion Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" x-model="promotionDate" required>
                    </div>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Students List -->
    <x-card title="Students" icon="bi-people">
        <x-slot name="actions">
            <div class="d-flex gap-2">
                <div class="input-group" style="width: 250px;">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" placeholder="Search students..." x-model="searchQuery">
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-success btn-sm" @click="setAllResult('promoted')" :disabled="students.length === 0">
                        <i class="bi bi-check-all me-1"></i> Promote All
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm" @click="setAllResult('detained')" :disabled="students.length === 0">
                        <i class="bi bi-x-circle me-1"></i> Detain All
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" @click="setAllResult('left')" :disabled="students.length === 0">
                        <i class="bi bi-box-arrow-right me-1"></i> Left All
                    </button>
                </div>
            </div>
        </x-slot>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2">Loading students...</p>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && students.length === 0" class="text-center py-5">
            <i class="bi bi-people fs-1 text-muted"></i>
            <p class="text-muted mt-2">No students found. Please select a class and session to load students.</p>
        </div>

        <!-- Students Table -->
        <div x-show="!loading && students.length > 0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" @change="toggleSelectAll($event)" :checked="allSelected">
                            </th>
                            <th style="width: 60px;">Photo</th>
                            <th>Admission No</th>
                            <th>Name</th>
                            <th>Current Class</th>
                            <th>Current Section</th>
                            <th style="width: 150px;">Result</th>
                            <th style="width: 200px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="student in filteredStudents" :key="student.id">
                            <tr :class="{ 'table-success': student.result === 'promoted', 'table-warning': student.result === 'detained', 'table-danger': student.result === 'left' }">
                                <td>
                                    <input type="checkbox" class="form-check-input" :value="student.id" x-model="selectedStudents">
                                </td>
                                <td>
                                    <img 
                                        :src="student.photo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(student.first_name + ' ' + student.last_name) + '&background=4f46e5&color=fff&size=40'"
                                        :alt="student.first_name"
                                        class="rounded-circle"
                                        style="width: 40px; height: 40px; object-fit: cover;"
                                    >
                                </td>
                                <td>
                                    <span class="font-monospace" x-text="student.admission_number"></span>
                                </td>
                                <td>
                                    <span class="fw-medium" x-text="student.first_name + ' ' + student.last_name"></span>
                                    <small class="d-block text-muted" x-text="'Roll No: ' + (student.roll_number || '-')"></small>
                                </td>
                                <td x-text="student.class_name"></td>
                                <td x-text="student.section_name"></td>
                                <td>
                                    <select class="form-select form-select-sm" x-model="student.result">
                                        <option value="">Select</option>
                                        <option value="promoted">Promoted</option>
                                        <option value="detained">Detained</option>
                                        <option value="left">Left</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" placeholder="Remarks..." x-model="student.remarks">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="border-top pt-3 mt-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light p-2 me-2">
                                <i class="bi bi-people text-primary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" x-text="students.length"></h5>
                                <small class="text-muted">Total Students</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success bg-opacity-10 p-2 me-2">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" x-text="promotedCount"></h5>
                                <small class="text-muted">To Promote</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-2 me-2">
                                <i class="bi bi-x-circle text-warning"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" x-text="detainedCount"></h5>
                                <small class="text-muted">To Detain</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-2">
                                <i class="bi bi-box-arrow-right text-danger"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" x-text="leftCount"></h5>
                                <small class="text-muted">Left School</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between mt-4" x-show="students.length > 0">
        <div>
            <button type="button" class="btn btn-outline-secondary" @click="resetForm()">
                <i class="bi bi-x-circle me-1"></i> Reset
            </button>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" @click="previewPromotion()" :disabled="!canPromote">
                <i class="bi bi-eye me-1"></i> Preview
            </button>
            <button type="button" class="btn btn-success" @click="confirmPromotion()" :disabled="!canPromote || processing">
                <span x-show="!processing">
                    <i class="bi bi-check-lg me-1"></i> Promote Selected (<span x-text="promotedCount"></span>)
                </span>
                <span x-show="processing">
                    <span class="spinner-border spinner-border-sm me-1"></span> Processing...
                </span>
            </button>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" :class="{ 'show d-block': showPreviewModal }" tabindex="-1" x-show="showPreviewModal" x-transition>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Promotion Preview</h5>
                    <button type="button" class="btn-close" @click="showPreviewModal = false"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Review the promotion details before confirming.
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">From</h6>
                            <p class="mb-0 fw-medium" x-text="getSessionName(fromSession) + ' - ' + getClassName(fromClass) + ' ' + getSectionName(fromSection)"></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">To</h6>
                            <p class="mb-0 fw-medium" x-text="getSessionName(toSession) + ' - ' + getClassName(toClass) + ' ' + getSectionName(toSection)"></p>
                        </div>
                    </div>

                    <h6>Students to be Promoted (<span x-text="promotedCount"></span>)</h6>
                    <div class="table-responsive" style="max-height: 300px;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Admission No</th>
                                    <th>Name</th>
                                    <th>Result</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="student in studentsToPromote" :key="student.id">
                                    <tr>
                                        <td x-text="student.admission_number"></td>
                                        <td x-text="student.first_name + ' ' + student.last_name"></td>
                                        <td>
                                            <span class="badge" :class="{
                                                'bg-success': student.result === 'promoted',
                                                'bg-warning': student.result === 'detained',
                                                'bg-danger': student.result === 'left'
                                            }" x-text="student.result.charAt(0).toUpperCase() + student.result.slice(1)"></span>
                                        </td>
                                        <td x-text="student.remarks || '-'"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showPreviewModal = false">Close</button>
                    <button type="button" class="btn btn-success" @click="showPreviewModal = false; confirmPromotion()">
                        <i class="bi bi-check-lg me-1"></i> Confirm Promotion
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" x-show="showPreviewModal" x-transition></div>
</div>

@push('scripts')
<script>
function studentPromotion() {
    return {
        // Form data
        fromSession: '',
        fromClass: '',
        fromSection: '',
        toSession: '',
        toClass: '',
        toSection: '',
        promotionDate: new Date().toISOString().split('T')[0],
        
        // Sections
        fromSections: [],
        toSections: [],
        
        // Students
        students: [],
        selectedStudents: [],
        searchQuery: '',
        loading: false,
        processing: false,
        
        // Modal
        showPreviewModal: false,
        
        // Reference data
        academicSessions: @json($academicSessions ?? []),
        classes: @json($classes ?? []),
        
        get filteredStudents() {
            if (!this.searchQuery) return this.students;
            const query = this.searchQuery.toLowerCase();
            return this.students.filter(s => 
                (s.first_name || '').toLowerCase().includes(query) ||
                (s.last_name || '').toLowerCase().includes(query) ||
                (s.admission_number || '').toLowerCase().includes(query) ||
                (s.roll_number || '').toString().includes(query)
            );
        },
        
        get allSelected() {
            return this.students.length > 0 && this.selectedStudents.length === this.students.length;
        },
        
        get promotedCount() {
            return this.students.filter(s => s.result === 'promoted').length;
        },
        
        get detainedCount() {
            return this.students.filter(s => s.result === 'detained').length;
        },
        
        get leftCount() {
            return this.students.filter(s => s.result === 'left').length;
        },
        
        get studentsToPromote() {
            return this.students.filter(s => s.result);
        },
        
        get canPromote() {
            return this.fromSession && this.fromClass && this.toSession && this.toClass && this.toSection && this.promotionDate && this.promotedCount > 0;
        },
        
        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedStudents = this.students.map(s => s.id);
            } else {
                this.selectedStudents = [];
            }
        },
        
        setAllResult(result) {
            this.students.forEach(s => {
                s.result = result;
            });
        },
        
        async loadFromSections() {
            if (!this.fromClass) {
                this.fromSections = [];
                return;
            }
            
            try {
                const response = await fetch(`/api/classes/${this.fromClass}/sections`);
                this.fromSections = await response.json();
            } catch (error) {
                this.fromSections = [];
            }
        },
        
        async loadToSections() {
            if (!this.toClass) {
                this.toSections = [];
                return;
            }
            
            try {
                const response = await fetch(`/api/classes/${this.toClass}/sections`);
                this.toSections = await response.json();
            } catch (error) {
                this.toSections = [];
            }
        },
        
        async loadStudents() {
            if (!this.fromSession || !this.fromClass) {
                this.students = [];
                return;
            }
            
            this.loading = true;
            
            try {
                let url = `/api/students?academic_session_id=${this.fromSession}&class_id=${this.fromClass}`;
                if (this.fromSection) {
                    url += `&section_id=${this.fromSection}`;
                }
                
                const response = await fetch(url);
                const data = await response.json();
                
                this.students = (data.data || data).map(s => ({
                    ...s,
                    result: '',
                    remarks: ''
                }));
            } catch (error) {
                this.students = [];
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load students.' });
            } finally {
                this.loading = false;
            }
        },
        
        getSessionName(id) {
            const session = this.academicSessions.find(s => s.id == id);
            return session ? session.name : '-';
        },
        
        getClassName(id) {
            const cls = this.classes.find(c => c.id == id);
            return cls ? cls.name : '-';
        },
        
        getSectionName(id) {
            const section = [...this.fromSections, ...this.toSections].find(s => s.id == id);
            return section ? section.name : '';
        },
        
        previewPromotion() {
            if (!this.canPromote) return;
            this.showPreviewModal = true;
        },
        
        async confirmPromotion() {
            if (!this.canPromote) return;
            
            const result = await Swal.fire({
                title: 'Confirm Promotion',
                html: `
                    <p>You are about to promote <strong>${this.promotedCount}</strong> student(s).</p>
                    <p class="text-muted small">This action will update student records and cannot be easily undone.</p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Yes, Promote',
                cancelButtonText: 'Cancel'
            });
            
            if (!result.isConfirmed) return;
            
            this.processing = true;
            
            try {
                const response = await fetch('{{ route("students.promote.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        from_session: this.fromSession,
                        from_class: this.fromClass,
                        from_section: this.fromSection,
                        to_session: this.toSession,
                        to_class: this.toClass,
                        to_section: this.toSection,
                        promotion_date: this.promotionDate,
                        students: this.studentsToPromote.map(s => ({
                            id: s.id,
                            result: s.result,
                            remarks: s.remarks
                        }))
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Promotion Successful!',
                        html: `
                            <p><strong>${data.promoted || this.promotedCount}</strong> student(s) have been promoted.</p>
                            ${data.detained ? `<p><strong>${data.detained}</strong> student(s) have been detained.</p>` : ''}
                            ${data.left ? `<p><strong>${data.left}</strong> student(s) marked as left.</p>` : ''}
                        `,
                        confirmButtonText: 'View Students'
                    }).then(() => {
                        window.location.href = '{{ route("students.index") }}';
                    });
                } else {
                    throw new Error('Failed to process promotion');
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to process promotion. Please try again.' });
            } finally {
                this.processing = false;
            }
        },
        
        resetForm() {
            this.fromSession = '';
            this.fromClass = '';
            this.fromSection = '';
            this.toSession = '';
            this.toClass = '';
            this.toSection = '';
            this.promotionDate = new Date().toISOString().split('T')[0];
            this.students = [];
            this.selectedStudents = [];
            this.fromSections = [];
            this.toSections = [];
        }
    };
}
</script>
@endpush
@endsection
