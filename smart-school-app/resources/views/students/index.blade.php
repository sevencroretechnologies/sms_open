{{-- Student List View --}}
{{-- Prompt 142: Student listing page with search, filter, and pagination --}}

@extends('layouts.app')

@section('title', 'Students')

@section('content')
<div x-data="studentListManager()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Students</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Students</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('students.import') }}" class="btn btn-outline-primary">
                <i class="bi bi-upload me-1"></i> Import
            </a>
            <a href="{{ route('students.export') ?? '#' }}" class="btn btn-outline-success">
                <i class="bi bi-download me-1"></i> Export
            </a>
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Student
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <x-card class="mb-4">
        <div class="row g-3">
            <!-- Search -->
            <div class="col-md-4">
                <label class="form-label small text-muted">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input 
                        type="text" 
                        class="form-control border-start-0" 
                        placeholder="Name, Admission No, Roll No, Father's Name..."
                        x-model="filters.search"
                        @input.debounce.300ms="applyFilters()"
                    >
                </div>
            </div>

            <!-- Class Filter -->
            <div class="col-md-2">
                <label class="form-label small text-muted">Class</label>
                <select class="form-select" x-model="filters.class_id" @change="loadSections(); applyFilters()">
                    <option value="">All Classes</option>
                    @foreach($classes ?? [] as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Section Filter -->
            <div class="col-md-2">
                <label class="form-label small text-muted">Section</label>
                <select class="form-select" x-model="filters.section_id" @change="applyFilters()">
                    <option value="">All Sections</option>
                    <template x-for="section in sections" :key="section.id">
                        <option :value="section.id" x-text="section.name"></option>
                    </template>
                </select>
            </div>

            <!-- Academic Session Filter -->
            <div class="col-md-2">
                <label class="form-label small text-muted">Session</label>
                <select class="form-select" x-model="filters.academic_session_id" @change="applyFilters()">
                    <option value="">All Sessions</option>
                    @foreach($academicSessions ?? [] as $session)
                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Category Filter -->
            <div class="col-md-2">
                <label class="form-label small text-muted">Category</label>
                <select class="form-select" x-model="filters.category_id" @change="applyFilters()">
                    <option value="">All Categories</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Gender Filter -->
            <div class="col-md-2">
                <label class="form-label small text-muted">Gender</label>
                <select class="form-select" x-model="filters.gender" @change="applyFilters()">
                    <option value="">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select class="form-select" x-model="filters.status" @change="applyFilters()">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="left">Left</option>
                    <option value="passed_out">Passed Out</option>
                </select>
            </div>

            <!-- Clear Filters -->
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-secondary w-100" @click="clearFilters()">
                    <i class="bi bi-x-lg me-1"></i> Clear
                </button>
            </div>
        </div>
    </x-card>

    <!-- Bulk Actions -->
    <div x-show="selectedStudents.length > 0" x-cloak class="mb-3">
        <div class="alert alert-info d-flex align-items-center justify-content-between py-2">
            <span>
                <strong x-text="selectedStudents.length"></strong> student(s) selected
            </span>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-primary" @click="bulkPromote()">
                    <i class="bi bi-arrow-up-circle me-1"></i> Promote
                </button>
                <button type="button" class="btn btn-outline-success" @click="bulkExport()">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <button type="button" class="btn btn-outline-danger" @click="bulkDelete()">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
                <button type="button" class="btn btn-outline-secondary" @click="selectedStudents = []; selectAll = false">
                    Clear Selection
                </button>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <x-card :noPadding="true">
        <x-slot name="header">
            <div class="d-flex align-items-center justify-content-between w-100">
                <span>
                    <i class="bi bi-people me-2"></i>
                    Student List
                    <span class="badge bg-primary ms-2" x-text="totalStudents"></span>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">Show</label>
                    <select class="form-select form-select-sm" style="width: auto;" x-model="perPage" @change="applyFilters()">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </x-slot>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input 
                                type="checkbox" 
                                class="form-check-input"
                                x-model="selectAll"
                                @change="toggleSelectAll()"
                            >
                        </th>
                        <th class="sortable" @click="sortBy('admission_number')">
                            <div class="d-flex align-items-center gap-1">
                                Adm. No
                                <i class="bi" :class="getSortIcon('admission_number')"></i>
                            </div>
                        </th>
                        <th>Photo</th>
                        <th class="sortable" @click="sortBy('name')">
                            <div class="d-flex align-items-center gap-1">
                                Name
                                <i class="bi" :class="getSortIcon('name')"></i>
                            </div>
                        </th>
                        <th class="sortable" @click="sortBy('roll_number')">
                            <div class="d-flex align-items-center gap-1">
                                Roll No
                                <i class="bi" :class="getSortIcon('roll_number')"></i>
                            </div>
                        </th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Father's Name</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loading State -->
                    <template x-if="loading">
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-2 mb-0">Loading students...</p>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!loading && students.length === 0">
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    <p class="mb-2">No students found</p>
                                    <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Add First Student
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Student Rows -->
                    <template x-for="student in students" :key="student.id">
                        <tr>
                            <td>
                                <input 
                                    type="checkbox" 
                                    class="form-check-input"
                                    :value="student.id"
                                    x-model="selectedStudents"
                                >
                            </td>
                            <td>
                                <span class="badge bg-light text-dark" x-text="student.admission_number"></span>
                            </td>
                            <td>
                                <img 
                                    :src="student.photo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(student.first_name + ' ' + student.last_name) + '&background=4f46e5&color=fff'"
                                    :alt="student.first_name + ' ' + student.last_name"
                                    class="rounded-circle"
                                    style="width: 40px; height: 40px; object-fit: cover;"
                                >
                            </td>
                            <td>
                                <a :href="'/students/' + student.id" class="text-decoration-none fw-medium">
                                    <span x-text="student.first_name + ' ' + student.last_name"></span>
                                </a>
                                <br>
                                <small class="text-muted" x-text="student.email || '-'"></small>
                            </td>
                            <td x-text="student.roll_number || '-'"></td>
                            <td x-text="student.class_name || '-'"></td>
                            <td x-text="student.section_name || '-'"></td>
                            <td x-text="student.father_name || '-'"></td>
                            <td x-text="student.father_phone || student.mobile || '-'"></td>
                            <td>
                                <span 
                                    class="badge"
                                    :class="{
                                        'bg-success': student.status === 'active',
                                        'bg-danger': student.status === 'inactive',
                                        'bg-warning': student.status === 'left',
                                        'bg-secondary': student.status === 'passed_out'
                                    }"
                                    x-text="student.status ? student.status.charAt(0).toUpperCase() + student.status.slice(1).replace('_', ' ') : 'Active'"
                                ></span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-info" 
                                        title="Quick View"
                                        @click="showQuickView(student)"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a 
                                        :href="'/students/' + student.id" 
                                        class="btn btn-outline-primary" 
                                        title="View Profile"
                                    >
                                        <i class="bi bi-person"></i>
                                    </a>
                                    <a 
                                        :href="'/students/' + student.id + '/edit'" 
                                        class="btn btn-outline-warning" 
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-danger" 
                                        title="Delete"
                                        @click="confirmDelete(student)"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <x-slot name="footer">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="text-muted small">
                    Showing <span x-text="showingFrom"></span> to <span x-text="showingTo"></span> 
                    of <span x-text="totalStudents"></span> entries
                </div>
                
                <nav aria-label="Student pagination">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                            <button class="page-link" @click="goToPage(1)" :disabled="currentPage === 1">
                                <i class="bi bi-chevron-double-left"></i>
                            </button>
                        </li>
                        <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                            <button class="page-link" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                        </li>
                        
                        <template x-for="page in visiblePages" :key="page">
                            <li class="page-item" :class="{ 'active': currentPage === page }">
                                <button class="page-link" @click="goToPage(page)" x-text="page"></button>
                            </li>
                        </template>
                        
                        <li class="page-item" :class="{ 'disabled': currentPage === totalPages || totalPages === 0 }">
                            <button class="page-link" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages || totalPages === 0">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </li>
                        <li class="page-item" :class="{ 'disabled': currentPage === totalPages || totalPages === 0 }">
                            <button class="page-link" @click="goToPage(totalPages)" :disabled="currentPage === totalPages || totalPages === 0">
                                <i class="bi bi-chevron-double-right"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </x-slot>
    </x-card>

    <!-- Quick View Modal -->
    <div 
        class="modal fade" 
        id="quickViewModal" 
        tabindex="-1" 
        x-ref="quickViewModal"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-badge me-2"></i>
                        Student Quick View
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" x-show="selectedStudent">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img 
                                :src="selectedStudent?.photo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent((selectedStudent?.first_name || '') + ' ' + (selectedStudent?.last_name || '')) + '&background=4f46e5&color=fff&size=150'"
                                class="rounded-circle mb-3"
                                style="width: 150px; height: 150px; object-fit: cover;"
                            >
                            <h5 x-text="(selectedStudent?.first_name || '') + ' ' + (selectedStudent?.last_name || '')"></h5>
                            <p class="text-muted mb-0" x-text="'Adm. No: ' + (selectedStudent?.admission_number || '-')"></p>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-0">Roll Number</label>
                                    <p class="mb-2 fw-medium" x-text="selectedStudent?.roll_number || '-'"></p>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-0">Class & Section</label>
                                    <p class="mb-2 fw-medium" x-text="(selectedStudent?.class_name || '-') + ' - ' + (selectedStudent?.section_name || '-')"></p>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-0">Gender</label>
                                    <p class="mb-2 fw-medium" x-text="selectedStudent?.gender ? selectedStudent.gender.charAt(0).toUpperCase() + selectedStudent.gender.slice(1) : '-'"></p>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-0">Date of Birth</label>
                                    <p class="mb-2 fw-medium" x-text="selectedStudent?.date_of_birth || '-'"></p>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-0">Father's Name</label>
                                    <p class="mb-2 fw-medium" x-text="selectedStudent?.father_name || '-'"></p>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-0">Father's Phone</label>
                                    <p class="mb-2 fw-medium" x-text="selectedStudent?.father_phone || '-'"></p>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-0">Mother's Name</label>
                                    <p class="mb-2 fw-medium" x-text="selectedStudent?.mother_name || '-'"></p>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-0">Email</label>
                                    <p class="mb-2 fw-medium" x-text="selectedStudent?.email || '-'"></p>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small text-muted mb-0">Address</label>
                                    <p class="mb-2 fw-medium" x-text="selectedStudent?.address || '-'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a :href="'/students/' + selectedStudent?.id" class="btn btn-primary">
                        <i class="bi bi-person me-1"></i> View Full Profile
                    </a>
                    <a :href="'/students/' + selectedStudent?.id + '/edit'" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" x-ref="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the student:</p>
                    <p class="fw-bold" x-text="(studentToDelete?.first_name || '') + ' ' + (studentToDelete?.last_name || '') + ' (' + (studentToDelete?.admission_number || '') + ')'"></p>
                    <p class="text-danger small mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone. All related data will be permanently deleted.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" @click="deleteStudent()" :disabled="deleting">
                        <span x-show="!deleting">
                            <i class="bi bi-trash me-1"></i> Delete
                        </span>
                        <span x-show="deleting">
                            <span class="spinner-border spinner-border-sm me-1"></span> Deleting...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .sortable {
        cursor: pointer;
        user-select: none;
    }
    
    .sortable:hover {
        background-color: #f8f9fa;
    }
    
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
function studentListManager() {
    return {
        // Data
        students: @json($students ?? []),
        allStudents: @json($students ?? []),
        sections: [],
        
        // Pagination
        currentPage: 1,
        perPage: 10,
        totalStudents: {{ $totalStudents ?? 0 }},
        totalPages: {{ $totalPages ?? 1 }},
        
        // Sorting
        sortColumn: 'admission_number',
        sortDirection: 'asc',
        
        // Filters
        filters: {
            search: '',
            class_id: '',
            section_id: '',
            academic_session_id: '',
            category_id: '',
            gender: '',
            status: ''
        },
        
        // Selection
        selectedStudents: [],
        selectAll: false,
        
        // Modal data
        selectedStudent: null,
        studentToDelete: null,
        
        // Loading states
        loading: false,
        deleting: false,
        
        // Computed
        get showingFrom() {
            return this.totalStudents === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
        },
        
        get showingTo() {
            return Math.min(this.currentPage * this.perPage, this.totalStudents);
        },
        
        get visiblePages() {
            const pages = [];
            const start = Math.max(1, this.currentPage - 2);
            const end = Math.min(this.totalPages, this.currentPage + 2);
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },
        
        // Methods
        init() {
            // Initialize with server-side data if available
            this.applyClientFilters();
        },
        
        applyFilters() {
            this.currentPage = 1;
            this.applyClientFilters();
        },
        
        applyClientFilters() {
            let filtered = [...this.allStudents];
            
            // Search filter
            if (this.filters.search) {
                const query = this.filters.search.toLowerCase();
                filtered = filtered.filter(s => 
                    (s.first_name + ' ' + s.last_name).toLowerCase().includes(query) ||
                    (s.admission_number || '').toLowerCase().includes(query) ||
                    (s.roll_number || '').toLowerCase().includes(query) ||
                    (s.father_name || '').toLowerCase().includes(query)
                );
            }
            
            // Class filter
            if (this.filters.class_id) {
                filtered = filtered.filter(s => s.class_id == this.filters.class_id);
            }
            
            // Section filter
            if (this.filters.section_id) {
                filtered = filtered.filter(s => s.section_id == this.filters.section_id);
            }
            
            // Gender filter
            if (this.filters.gender) {
                filtered = filtered.filter(s => s.gender === this.filters.gender);
            }
            
            // Status filter
            if (this.filters.status) {
                filtered = filtered.filter(s => s.status === this.filters.status);
            }
            
            // Sort
            filtered.sort((a, b) => {
                let aVal = a[this.sortColumn] || '';
                let bVal = b[this.sortColumn] || '';
                
                if (this.sortColumn === 'name') {
                    aVal = (a.first_name + ' ' + a.last_name).toLowerCase();
                    bVal = (b.first_name + ' ' + b.last_name).toLowerCase();
                }
                
                if (typeof aVal === 'string') aVal = aVal.toLowerCase();
                if (typeof bVal === 'string') bVal = bVal.toLowerCase();
                
                if (aVal < bVal) return this.sortDirection === 'asc' ? -1 : 1;
                if (aVal > bVal) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });
            
            this.totalStudents = filtered.length;
            this.totalPages = Math.ceil(filtered.length / this.perPage);
            
            // Paginate
            const start = (this.currentPage - 1) * this.perPage;
            this.students = filtered.slice(start, start + this.perPage);
        },
        
        clearFilters() {
            this.filters = {
                search: '',
                class_id: '',
                section_id: '',
                academic_session_id: '',
                category_id: '',
                gender: '',
                status: ''
            };
            this.sections = [];
            this.applyFilters();
        },
        
        loadSections() {
            if (!this.filters.class_id) {
                this.sections = [];
                this.filters.section_id = '';
                return;
            }
            
            // Load sections for selected class via API
            fetch(`/api/classes/${this.filters.class_id}/sections`)
                .then(res => res.json())
                .then(data => {
                    this.sections = data;
                })
                .catch(() => {
                    this.sections = [];
                });
        },
        
        sortBy(column) {
            if (this.sortColumn === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = column;
                this.sortDirection = 'asc';
            }
            this.applyClientFilters();
        },
        
        getSortIcon(column) {
            if (this.sortColumn !== column) return 'bi-chevron-expand';
            return this.sortDirection === 'asc' ? 'bi-chevron-up' : 'bi-chevron-down';
        },
        
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedStudents = this.students.map(s => s.id);
            } else {
                this.selectedStudents = [];
            }
        },
        
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
                this.applyClientFilters();
            }
        },
        
        showQuickView(student) {
            this.selectedStudent = student;
            const modal = new bootstrap.Modal(this.$refs.quickViewModal);
            modal.show();
        },
        
        confirmDelete(student) {
            this.studentToDelete = student;
            const modal = new bootstrap.Modal(this.$refs.deleteModal);
            modal.show();
        },
        
        async deleteStudent() {
            if (!this.studentToDelete) return;
            
            this.deleting = true;
            
            try {
                const response = await fetch(`/students/${this.studentToDelete.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    // Remove from list
                    this.allStudents = this.allStudents.filter(s => s.id !== this.studentToDelete.id);
                    this.applyClientFilters();
                    
                    // Close modal
                    bootstrap.Modal.getInstance(this.$refs.deleteModal).hide();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Student has been deleted successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error('Failed to delete student');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to delete student. Please try again.'
                });
            } finally {
                this.deleting = false;
                this.studentToDelete = null;
            }
        },
        
        bulkPromote() {
            if (this.selectedStudents.length === 0) return;
            window.location.href = `/students/promote?ids=${this.selectedStudents.join(',')}`;
        },
        
        bulkExport() {
            if (this.selectedStudents.length === 0) return;
            window.location.href = `/students/export?ids=${this.selectedStudents.join(',')}`;
        },
        
        async bulkDelete() {
            if (this.selectedStudents.length === 0) return;
            
            const result = await Swal.fire({
                icon: 'warning',
                title: 'Delete Selected Students?',
                text: `Are you sure you want to delete ${this.selectedStudents.length} student(s)? This action cannot be undone.`,
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete them!'
            });
            
            if (result.isConfirmed) {
                try {
                    const response = await fetch('/students/bulk-delete', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ ids: this.selectedStudents })
                    });
                    
                    if (response.ok) {
                        this.allStudents = this.allStudents.filter(s => !this.selectedStudents.includes(s.id));
                        this.selectedStudents = [];
                        this.selectAll = false;
                        this.applyClientFilters();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Selected students have been deleted.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error('Failed to delete students');
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to delete students. Please try again.'
                    });
                }
            }
        }
    };
}
</script>
@endpush
@endsection
