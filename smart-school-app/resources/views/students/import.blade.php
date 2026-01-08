{{-- Student Bulk Import View --}}
{{-- Prompt 151: Student bulk import view with Excel/CSV upload --}}

@extends('layouts.app')

@section('title', 'Import Students')

@section('content')
<div x-data="studentImport()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Import Students</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">Import</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Students
            </a>
        </div>
    </div>

    <!-- Import Instructions -->
    <x-card title="Import Instructions" icon="bi-info-circle" class="mb-4">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <span class="text-primary fw-bold fs-4">1</span>
                    </div>
                    <h6>Download Template</h6>
                    <p class="text-muted small mb-0">Download the Excel or CSV template with the required format.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <span class="text-primary fw-bold fs-4">2</span>
                    </div>
                    <h6>Fill Template</h6>
                    <p class="text-muted small mb-0">Fill in the student data following the template format.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <span class="text-primary fw-bold fs-4">3</span>
                    </div>
                    <h6>Upload File</h6>
                    <p class="text-muted small mb-0">Upload the filled template file for validation.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <span class="text-primary fw-bold fs-4">4</span>
                    </div>
                    <h6>Review & Import</h6>
                    <p class="text-muted small mb-0">Review the data and confirm the import.</p>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Template Download -->
    <x-card title="Download Template" icon="bi-download" class="mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <p class="mb-2">Download the import template in your preferred format. The template includes all required and optional fields with sample data.</p>
                <p class="text-muted small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Required fields: First Name, Last Name, Date of Birth, Gender, Class, Section, Academic Session, Admission Number, Admission Date
                </p>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2 justify-content-md-end">
                    <a href="{{ route('students.import.template', 'xlsx') }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel (.xlsx)
                    </a>
                    <a href="{{ route('students.import.template', 'csv') }}" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-text me-1"></i> CSV (.csv)
                    </a>
                </div>
            </div>
        </div>

        <!-- Template Format Info -->
        <div class="mt-4 pt-4 border-top">
            <h6 class="mb-3">Template Columns</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Column</th>
                            <th>Required</th>
                            <th>Format</th>
                            <th>Example</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>first_name</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                            <td>Text</td>
                            <td>John</td>
                        </tr>
                        <tr>
                            <td>last_name</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                            <td>Text</td>
                            <td>Doe</td>
                        </tr>
                        <tr>
                            <td>date_of_birth</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                            <td>YYYY-MM-DD</td>
                            <td>2010-05-15</td>
                        </tr>
                        <tr>
                            <td>gender</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                            <td>male/female/other</td>
                            <td>male</td>
                        </tr>
                        <tr>
                            <td>admission_number</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                            <td>Text (Unique)</td>
                            <td>ADM2024001</td>
                        </tr>
                        <tr>
                            <td>class_name</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                            <td>Text (Must exist)</td>
                            <td>Class 5</td>
                        </tr>
                        <tr>
                            <td>section_name</td>
                            <td><span class="badge bg-danger">Yes</span></td>
                            <td>Text (Must exist)</td>
                            <td>A</td>
                        </tr>
                        <tr>
                            <td>father_name</td>
                            <td><span class="badge bg-secondary">No</span></td>
                            <td>Text</td>
                            <td>Robert Doe</td>
                        </tr>
                        <tr>
                            <td>father_phone</td>
                            <td><span class="badge bg-secondary">No</span></td>
                            <td>10 digits</td>
                            <td>9876543210</td>
                        </tr>
                        <tr>
                            <td>email</td>
                            <td><span class="badge bg-secondary">No</span></td>
                            <td>Email</td>
                            <td>john@example.com</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </x-card>

    <!-- File Upload -->
    <x-card title="Upload File" icon="bi-upload" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                <select class="form-select" x-model="importSettings.academic_session_id" required>
                    <option value="">Select Session</option>
                    @foreach($academicSessions ?? [] as $session)
                    <option value="{{ $session->id }}">{{ $session->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Default Class</label>
                <select class="form-select" x-model="importSettings.default_class_id">
                    <option value="">Use from file</option>
                    @foreach($classes ?? [] as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Default Section</label>
                <select class="form-select" x-model="importSettings.default_section_id">
                    <option value="">Use from file</option>
                    <template x-for="section in sections" :key="section.id">
                        <option :value="section.id" x-text="section.name"></option>
                    </template>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <label class="form-label">Select File <span class="text-danger">*</span></label>
            <div 
                class="border-2 border-dashed rounded p-5 text-center"
                :class="{ 'border-primary bg-primary bg-opacity-10': isDragging, 'border-success bg-success bg-opacity-10': uploadedFile }"
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleDrop($event)"
            >
                <input type="file" id="fileInput" class="d-none" @change="handleFileSelect($event)" accept=".xlsx,.xls,.csv">
                
                <div x-show="!uploadedFile">
                    <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                    <p class="mb-1 mt-2">Drag and drop your file here or</p>
                    <label for="fileInput" class="btn btn-primary">
                        <i class="bi bi-folder2-open me-1"></i> Browse Files
                    </label>
                    <p class="small text-muted mt-2 mb-0">
                        Accepted formats: .xlsx, .xls, .csv (Max size: 10MB)
                    </p>
                </div>
                
                <div x-show="uploadedFile">
                    <i class="bi bi-file-earmark-check fs-1 text-success"></i>
                    <p class="mb-1 mt-2 fw-medium" x-text="uploadedFile?.name"></p>
                    <p class="small text-muted mb-2" x-text="formatFileSize(uploadedFile?.size)"></p>
                    <button type="button" class="btn btn-outline-danger btn-sm" @click="removeFile()">
                        <i class="bi bi-x me-1"></i> Remove File
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            <button type="button" class="btn btn-primary" @click="uploadFile()" :disabled="!uploadedFile || !importSettings.academic_session_id || uploading">
                <span x-show="!uploading"><i class="bi bi-upload me-1"></i> Upload & Validate</span>
                <span x-show="uploading"><span class="spinner-border spinner-border-sm me-1"></span> Uploading...</span>
            </button>
        </div>
    </x-card>

    <!-- Preview Section -->
    <div x-show="previewData.length > 0">
        <x-card title="Preview & Validate" icon="bi-table" class="mb-4">
            <x-slot name="actions">
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-success" x-text="validCount + ' Valid'"></span>
                    <span class="badge bg-danger" x-text="errorCount + ' Errors'"></span>
                    <span class="badge bg-warning" x-text="warningCount + ' Warnings'"></span>
                </div>
            </x-slot>

            <!-- Validation Errors Summary -->
            <div class="alert alert-danger mb-3" x-show="errorCount > 0">
                <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Validation Errors</h6>
                <p class="mb-0 small">Please fix the following errors before importing:</p>
                <ul class="mb-0 mt-2 small">
                    <template x-for="error in validationErrors" :key="error">
                        <li x-text="error"></li>
                    </template>
                </ul>
            </div>

            <!-- Preview Table -->
            <div class="table-responsive" style="max-height: 400px;">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Status</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Admission No</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Father Name</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, index) in previewData" :key="index">
                            <tr :class="{ 'table-danger': row.hasError, 'table-warning': row.hasWarning }">
                                <td x-text="index + 1"></td>
                                <td>
                                    <span x-show="row.hasError" class="badge bg-danger" title="Error">
                                        <i class="bi bi-x-circle"></i>
                                    </span>
                                    <span x-show="row.hasWarning && !row.hasError" class="badge bg-warning" title="Warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </span>
                                    <span x-show="!row.hasError && !row.hasWarning" class="badge bg-success" title="Valid">
                                        <i class="bi bi-check-circle"></i>
                                    </span>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" x-model="row.first_name" :class="{ 'is-invalid': !row.first_name }">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" x-model="row.last_name" :class="{ 'is-invalid': !row.last_name }">
                                </td>
                                <td>
                                    <input type="date" class="form-control form-control-sm" x-model="row.date_of_birth" :class="{ 'is-invalid': !row.date_of_birth }">
                                </td>
                                <td>
                                    <select class="form-select form-select-sm" x-model="row.gender" :class="{ 'is-invalid': !row.gender }">
                                        <option value="">-</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" x-model="row.admission_number" :class="{ 'is-invalid': !row.admission_number || row.duplicateAdmission }">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" x-model="row.class_name" :class="{ 'is-invalid': !row.class_name }">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" x-model="row.section_name" :class="{ 'is-invalid': !row.section_name }">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" x-model="row.father_name">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" x-model="row.father_phone">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="removeRow(index)" title="Remove">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Import Options -->
            <div class="border-top pt-3 mt-3">
                <h6 class="mb-3">Import Options</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="skipExisting" x-model="importSettings.skip_existing">
                            <label class="form-check-label" for="skipExisting">
                                Skip existing students (by admission number)
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="createAccounts" x-model="importSettings.create_accounts">
                            <label class="form-check-label" for="createAccounts">
                                Create user accounts for students
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="sendWelcome" x-model="importSettings.send_welcome_email" :disabled="!importSettings.create_accounts">
                            <label class="form-check-label" for="sendWelcome">
                                Send welcome email to students
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between mb-4">
            <button type="button" class="btn btn-outline-secondary" @click="cancelImport()">
                <i class="bi bi-x-circle me-1"></i> Cancel
            </button>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" @click="revalidate()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Re-validate
                </button>
                <button type="button" class="btn btn-success" @click="confirmImport()" :disabled="errorCount > 0 || importing">
                    <span x-show="!importing">
                        <i class="bi bi-check-lg me-1"></i> Import <span x-text="validCount"></span> Students
                    </span>
                    <span x-show="importing">
                        <span class="spinner-border spinner-border-sm me-1"></span> Importing...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Import Results -->
    <div x-show="importResults" class="mb-4">
        <x-card>
            <div class="text-center py-4">
                <i class="bi bi-check-circle-fill text-success fs-1"></i>
                <h4 class="mt-3">Import Completed!</h4>
                <p class="text-muted">
                    <strong x-text="importResults?.imported || 0"></strong> students imported successfully.
                    <span x-show="importResults?.skipped > 0">
                        <strong x-text="importResults?.skipped"></strong> skipped.
                    </span>
                    <span x-show="importResults?.failed > 0">
                        <strong x-text="importResults?.failed"></strong> failed.
                    </span>
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('students.index') }}" class="btn btn-primary">
                        <i class="bi bi-people me-1"></i> View Students
                    </a>
                    <button type="button" class="btn btn-outline-primary" @click="resetImport()">
                        <i class="bi bi-plus-circle me-1"></i> Import More
                    </button>
                </div>
            </div>
        </x-card>
    </div>
</div>

@push('scripts')
<script>
function studentImport() {
    return {
        // File upload
        uploadedFile: null,
        isDragging: false,
        uploading: false,
        
        // Settings
        importSettings: {
            academic_session_id: '',
            default_class_id: '',
            default_section_id: '',
            skip_existing: true,
            create_accounts: false,
            send_welcome_email: false
        },
        
        // Sections for default class
        sections: [],
        
        // Preview data
        previewData: [],
        validationErrors: [],
        
        // Import state
        importing: false,
        importResults: null,
        
        get validCount() {
            return this.previewData.filter(r => !r.hasError).length;
        },
        
        get errorCount() {
            return this.previewData.filter(r => r.hasError).length;
        },
        
        get warningCount() {
            return this.previewData.filter(r => r.hasWarning && !r.hasError).length;
        },
        
        formatFileSize(bytes) {
            if (!bytes) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            this.validateAndSetFile(file);
        },
        
        handleDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            this.validateAndSetFile(file);
        },
        
        validateAndSetFile(file) {
            if (!file) return;
            
            const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'];
            const validExtensions = ['.xlsx', '.xls', '.csv'];
            
            const extension = '.' + file.name.split('.').pop().toLowerCase();
            
            if (!validExtensions.includes(extension)) {
                Swal.fire({ icon: 'error', title: 'Invalid File', text: 'Please upload an Excel (.xlsx, .xls) or CSV file.' });
                return;
            }
            
            if (file.size > 10 * 1024 * 1024) {
                Swal.fire({ icon: 'error', title: 'File Too Large', text: 'Maximum file size is 10MB.' });
                return;
            }
            
            this.uploadedFile = file;
        },
        
        removeFile() {
            this.uploadedFile = null;
            this.previewData = [];
            this.validationErrors = [];
            document.getElementById('fileInput').value = '';
        },
        
        async uploadFile() {
            if (!this.uploadedFile || !this.importSettings.academic_session_id) return;
            
            this.uploading = true;
            
            try {
                const formData = new FormData();
                formData.append('file', this.uploadedFile);
                formData.append('academic_session_id', this.importSettings.academic_session_id);
                if (this.importSettings.default_class_id) {
                    formData.append('default_class_id', this.importSettings.default_class_id);
                }
                if (this.importSettings.default_section_id) {
                    formData.append('default_section_id', this.importSettings.default_section_id);
                }
                
                const response = await fetch('{{ route("students.import.preview") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                if (response.ok) {
                    const result = await response.json();
                    this.previewData = result.data || [];
                    this.validationErrors = result.errors || [];
                    this.validateData();
                } else {
                    const error = await response.json();
                    throw new Error(error.message || 'Failed to process file');
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'Failed to upload file. Please try again.' });
            } finally {
                this.uploading = false;
            }
        },
        
        validateData() {
            const admissionNumbers = new Set();
            this.validationErrors = [];
            
            this.previewData.forEach((row, index) => {
                row.hasError = false;
                row.hasWarning = false;
                row.duplicateAdmission = false;
                
                // Required field validation
                if (!row.first_name) {
                    row.hasError = true;
                    this.validationErrors.push(`Row ${index + 1}: First name is required`);
                }
                if (!row.last_name) {
                    row.hasError = true;
                    this.validationErrors.push(`Row ${index + 1}: Last name is required`);
                }
                if (!row.date_of_birth) {
                    row.hasError = true;
                    this.validationErrors.push(`Row ${index + 1}: Date of birth is required`);
                }
                if (!row.gender) {
                    row.hasError = true;
                    this.validationErrors.push(`Row ${index + 1}: Gender is required`);
                }
                if (!row.admission_number) {
                    row.hasError = true;
                    this.validationErrors.push(`Row ${index + 1}: Admission number is required`);
                }
                if (!row.class_name) {
                    row.hasError = true;
                    this.validationErrors.push(`Row ${index + 1}: Class is required`);
                }
                if (!row.section_name) {
                    row.hasError = true;
                    this.validationErrors.push(`Row ${index + 1}: Section is required`);
                }
                
                // Duplicate admission number check
                if (row.admission_number) {
                    if (admissionNumbers.has(row.admission_number)) {
                        row.hasError = true;
                        row.duplicateAdmission = true;
                        this.validationErrors.push(`Row ${index + 1}: Duplicate admission number "${row.admission_number}"`);
                    }
                    admissionNumbers.add(row.admission_number);
                }
                
                // Warning for missing optional fields
                if (!row.father_name || !row.father_phone) {
                    row.hasWarning = true;
                }
            });
        },
        
        revalidate() {
            this.validateData();
            Swal.fire({
                icon: 'info',
                title: 'Validation Complete',
                text: `${this.validCount} valid, ${this.errorCount} errors, ${this.warningCount} warnings`,
                timer: 2000,
                showConfirmButton: false
            });
        },
        
        removeRow(index) {
            this.previewData.splice(index, 1);
            this.validateData();
        },
        
        cancelImport() {
            this.previewData = [];
            this.validationErrors = [];
            this.removeFile();
        },
        
        async confirmImport() {
            if (this.errorCount > 0) {
                Swal.fire({ icon: 'error', title: 'Validation Errors', text: 'Please fix all errors before importing.' });
                return;
            }
            
            const result = await Swal.fire({
                title: 'Confirm Import',
                html: `
                    <p>You are about to import <strong>${this.validCount}</strong> students.</p>
                    <p class="text-muted small">This action will create new student records in the system.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Yes, Import',
                cancelButtonText: 'Cancel'
            });
            
            if (!result.isConfirmed) return;
            
            this.importing = true;
            
            try {
                const response = await fetch('{{ route("students.import.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        students: this.previewData.filter(r => !r.hasError),
                        settings: this.importSettings
                    })
                });
                
                if (response.ok) {
                    this.importResults = await response.json();
                    this.previewData = [];
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Successful!',
                        text: `${this.importResults.imported} students imported successfully.`,
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    const error = await response.json();
                    throw new Error(error.message || 'Failed to import students');
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Import Failed', text: error.message || 'Failed to import students. Please try again.' });
            } finally {
                this.importing = false;
            }
        },
        
        resetImport() {
            this.uploadedFile = null;
            this.previewData = [];
            this.validationErrors = [];
            this.importResults = null;
            this.importSettings = {
                academic_session_id: '',
                default_class_id: '',
                default_section_id: '',
                skip_existing: true,
                create_accounts: false,
                send_welcome_email: false
            };
        },
        
        init() {
            this.$watch('importSettings.default_class_id', async (classId) => {
                if (!classId) {
                    this.sections = [];
                    return;
                }
                
                try {
                    const response = await fetch(`/api/classes/${classId}/sections`);
                    this.sections = await response.json();
                } catch (error) {
                    this.sections = [];
                }
            });
        }
    };
}
</script>
@endpush
@endsection
