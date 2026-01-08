{{-- Student Documents View --}}
{{-- Prompt 146: Student documents management view with upload/download --}}

@extends('layouts.app')

@section('title', 'Student Documents - ' . ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))

@section('content')
<div x-data="studentDocuments()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Student Documents</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.show', $student->id ?? 0) }}">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</a></li>
                    <li class="breadcrumb-item active">Documents</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('students.show', $student->id ?? 0) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Profile
            </a>
            <button type="button" class="btn btn-primary" @click="showUploadModal = true">
                <i class="bi bi-upload me-1"></i> Upload Document
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

    <!-- Document Categories -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card shadow-sm border-0 h-100 cursor-pointer" :class="{ 'border-primary border-2': selectedCategory === 'all' }" @click="selectedCategory = 'all'">
                <div class="card-body text-center py-3">
                    <i class="bi bi-folder fs-4 text-primary"></i>
                    <p class="mb-0 small mt-1">All</p>
                    <span class="badge bg-primary" x-text="documents.length"></span>
                </div>
            </div>
        </div>
        <template x-for="category in categories" :key="category.value">
            <div class="col-6 col-md-2">
                <div class="card shadow-sm border-0 h-100 cursor-pointer" :class="{ 'border-primary border-2': selectedCategory === category.value }" @click="selectedCategory = category.value">
                    <div class="card-body text-center py-3">
                        <i class="bi fs-4" :class="category.icon + ' text-' + category.color"></i>
                        <p class="mb-0 small mt-1" x-text="category.label"></p>
                        <span class="badge" :class="'bg-' + category.color" x-text="getDocumentCount(category.value)"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Documents List -->
    <x-card title="Documents" icon="bi-file-earmark">
        <x-slot name="actions">
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="Search documents..." x-model="searchQuery">
            </div>
        </x-slot>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2">Loading documents...</p>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && filteredDocuments.length === 0" class="text-center py-5">
            <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
            <p class="text-muted mt-2">No documents found</p>
            <button type="button" class="btn btn-primary btn-sm" @click="showUploadModal = true">
                <i class="bi bi-upload me-1"></i> Upload First Document
            </button>
        </div>

        <!-- Documents Grid -->
        <div x-show="!loading && filteredDocuments.length > 0" class="row g-3">
            <template x-for="doc in filteredDocuments" :key="doc.id">
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="rounded bg-light p-2 me-3">
                                    <i class="bi fs-4" :class="getFileIcon(doc.file_type)"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" x-text="doc.document_type"></h6>
                                    <p class="text-muted small mb-1" x-text="doc.file_name"></p>
                                    <p class="text-muted small mb-0">
                                        <span x-text="formatFileSize(doc.file_size)"></span>
                                        <span class="mx-1">|</span>
                                        <span x-text="formatDate(doc.created_at)"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="badge" :class="doc.is_verified ? 'bg-success' : 'bg-warning'" x-text="doc.is_verified ? 'Verified' : 'Pending'"></span>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" @click="previewDocument(doc)" title="Preview">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a :href="doc.file_path" download class="btn btn-outline-success" title="Download">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" @click="confirmDelete(doc)" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </x-card>

    <!-- Upload Modal -->
    <div class="modal fade" :class="{ 'show d-block': showUploadModal }" tabindex="-1" x-show="showUploadModal" x-transition>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-upload me-2"></i>Upload Document</h5>
                    <button type="button" class="btn-close" @click="showUploadModal = false"></button>
                </div>
                <form @submit.prevent="uploadDocument">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Document Type <span class="text-danger">*</span></label>
                                <select class="form-select" x-model="uploadForm.document_type" required>
                                    <option value="">Select document type</option>
                                    <option value="birth_certificate">Birth Certificate</option>
                                    <option value="transfer_certificate">Transfer Certificate</option>
                                    <option value="aadhar_card">Aadhar Card</option>
                                    <option value="marksheet">Marksheet</option>
                                    <option value="caste_certificate">Caste Certificate</option>
                                    <option value="income_certificate">Income Certificate</option>
                                    <option value="medical_certificate">Medical Certificate</option>
                                    <option value="photo">Photo</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Document Title</label>
                                <input type="text" class="form-control" x-model="uploadForm.title" placeholder="Enter document title">
                            </div>
                            <div class="col-12">
                                <label class="form-label">File <span class="text-danger">*</span></label>
                                <div 
                                    class="border-2 border-dashed rounded p-4 text-center"
                                    :class="{ 'border-primary bg-primary bg-opacity-10': isDragging }"
                                    @dragover.prevent="isDragging = true"
                                    @dragleave.prevent="isDragging = false"
                                    @drop.prevent="handleDrop($event)"
                                >
                                    <input type="file" id="fileInput" class="d-none" @change="handleFileSelect($event)" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <div x-show="!uploadForm.file">
                                        <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                                        <p class="mb-1">Drag and drop file here or</p>
                                        <label for="fileInput" class="btn btn-outline-primary btn-sm">Browse Files</label>
                                        <p class="small text-muted mt-2 mb-0">Supported formats: PDF, JPG, PNG, DOC, DOCX (Max: 5MB)</p>
                                    </div>
                                    <div x-show="uploadForm.file">
                                        <i class="bi bi-file-earmark-check fs-1 text-success"></i>
                                        <p class="mb-1" x-text="uploadForm.file?.name"></p>
                                        <p class="small text-muted mb-1" x-text="formatFileSize(uploadForm.file?.size)"></p>
                                        <button type="button" class="btn btn-outline-danger btn-sm" @click="uploadForm.file = null">
                                            <i class="bi bi-x me-1"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" rows="2" x-model="uploadForm.notes" placeholder="Add any notes about this document..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showUploadModal = false">Cancel</button>
                        <button type="submit" class="btn btn-primary" :disabled="uploading || !uploadForm.document_type || !uploadForm.file">
                            <span x-show="!uploading"><i class="bi bi-upload me-1"></i> Upload</span>
                            <span x-show="uploading"><span class="spinner-border spinner-border-sm me-1"></span> Uploading...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" x-show="showUploadModal" x-transition></div>

    <!-- Preview Modal -->
    <div class="modal fade" :class="{ 'show d-block': showPreviewModal }" tabindex="-1" x-show="showPreviewModal" x-transition>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Document Preview</h5>
                    <button type="button" class="btn-close" @click="showPreviewModal = false"></button>
                </div>
                <div class="modal-body p-0">
                    <template x-if="previewDoc && previewDoc.file_type === 'pdf'">
                        <iframe :src="previewDoc.file_path" class="w-100" style="height: 70vh;"></iframe>
                    </template>
                    <template x-if="previewDoc && ['jpg', 'jpeg', 'png', 'gif'].includes(previewDoc.file_type)">
                        <div class="text-center p-4">
                            <img :src="previewDoc.file_path" class="img-fluid" style="max-height: 70vh;">
                        </div>
                    </template>
                    <template x-if="previewDoc && !['pdf', 'jpg', 'jpeg', 'png', 'gif'].includes(previewDoc.file_type)">
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Preview not available for this file type</p>
                            <a :href="previewDoc.file_path" download class="btn btn-primary">
                                <i class="bi bi-download me-1"></i> Download to View
                            </a>
                        </div>
                    </template>
                </div>
                <div class="modal-footer">
                    <div class="me-auto">
                        <span class="badge" :class="previewDoc?.is_verified ? 'bg-success' : 'bg-warning'" x-text="previewDoc?.is_verified ? 'Verified' : 'Pending Verification'"></span>
                    </div>
                    <a :href="previewDoc?.file_path" download class="btn btn-success">
                        <i class="bi bi-download me-1"></i> Download
                    </a>
                    <button type="button" class="btn btn-secondary" @click="showPreviewModal = false">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" x-show="showPreviewModal" x-transition></div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" :class="{ 'show d-block': showDeleteModal }" tabindex="-1" x-show="showDeleteModal" x-transition>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" @click="showDeleteModal = false"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this document?</p>
                    <p class="fw-bold" x-text="docToDelete?.document_type + ' - ' + docToDelete?.file_name"></p>
                    <p class="text-danger small mb-0"><i class="bi bi-exclamation-circle me-1"></i>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showDeleteModal = false">Cancel</button>
                    <button type="button" class="btn btn-danger" @click="deleteDocument()" :disabled="deleting">
                        <span x-show="!deleting"><i class="bi bi-trash me-1"></i> Delete</span>
                        <span x-show="deleting"><span class="spinner-border spinner-border-sm me-1"></span> Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" x-show="showDeleteModal" x-transition></div>
</div>

@push('styles')
<style>
    .cursor-pointer { cursor: pointer; }
    .border-dashed { border-style: dashed !important; }
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
function studentDocuments() {
    return {
        documents: @json($documents ?? []),
        categories: [
            { value: 'birth_certificate', label: 'Birth Cert', icon: 'bi-file-earmark-text', color: 'primary' },
            { value: 'transfer_certificate', label: 'Transfer Cert', icon: 'bi-file-earmark-arrow-up', color: 'success' },
            { value: 'aadhar_card', label: 'Aadhar', icon: 'bi-credit-card', color: 'info' },
            { value: 'marksheet', label: 'Marksheet', icon: 'bi-file-earmark-bar-graph', color: 'warning' },
            { value: 'other', label: 'Other', icon: 'bi-file-earmark', color: 'secondary' }
        ],
        
        selectedCategory: 'all',
        searchQuery: '',
        loading: false,
        
        showUploadModal: false,
        showPreviewModal: false,
        showDeleteModal: false,
        
        uploadForm: {
            document_type: '',
            title: '',
            file: null,
            notes: ''
        },
        uploading: false,
        isDragging: false,
        
        previewDoc: null,
        docToDelete: null,
        deleting: false,
        
        get filteredDocuments() {
            let filtered = this.documents;
            
            if (this.selectedCategory !== 'all') {
                filtered = filtered.filter(d => d.document_type === this.selectedCategory);
            }
            
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(d => 
                    (d.document_type || '').toLowerCase().includes(query) ||
                    (d.file_name || '').toLowerCase().includes(query) ||
                    (d.title || '').toLowerCase().includes(query)
                );
            }
            
            return filtered;
        },
        
        getDocumentCount(category) {
            return this.documents.filter(d => d.document_type === category).length;
        },
        
        getFileIcon(fileType) {
            const icons = {
                'pdf': 'bi-file-earmark-pdf text-danger',
                'jpg': 'bi-file-earmark-image text-primary',
                'jpeg': 'bi-file-earmark-image text-primary',
                'png': 'bi-file-earmark-image text-primary',
                'doc': 'bi-file-earmark-word text-primary',
                'docx': 'bi-file-earmark-word text-primary'
            };
            return icons[fileType] || 'bi-file-earmark text-secondary';
        },
        
        formatFileSize(bytes) {
            if (!bytes) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
        },
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({ icon: 'error', title: 'File too large', text: 'Maximum file size is 5MB' });
                    return;
                }
                this.uploadForm.file = file;
            }
        },
        
        handleDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({ icon: 'error', title: 'File too large', text: 'Maximum file size is 5MB' });
                    return;
                }
                this.uploadForm.file = file;
            }
        },
        
        async uploadDocument() {
            if (!this.uploadForm.document_type || !this.uploadForm.file) return;
            
            this.uploading = true;
            
            try {
                const formData = new FormData();
                formData.append('document_type', this.uploadForm.document_type);
                formData.append('title', this.uploadForm.title);
                formData.append('file', this.uploadForm.file);
                formData.append('notes', this.uploadForm.notes);
                
                const response = await fetch('{{ route("students.documents.store", $student->id ?? 0) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                if (response.ok) {
                    const result = await response.json();
                    this.documents.push(result.document);
                    this.showUploadModal = false;
                    this.uploadForm = { document_type: '', title: '', file: null, notes: '' };
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Uploaded!',
                        text: 'Document has been uploaded successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error('Failed to upload document');
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to upload document. Please try again.' });
            } finally {
                this.uploading = false;
            }
        },
        
        previewDocument(doc) {
            this.previewDoc = doc;
            this.showPreviewModal = true;
        },
        
        confirmDelete(doc) {
            this.docToDelete = doc;
            this.showDeleteModal = true;
        },
        
        async deleteDocument() {
            if (!this.docToDelete) return;
            
            this.deleting = true;
            
            try {
                const response = await fetch(`/students/{{ $student->id ?? 0 }}/documents/${this.docToDelete.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    this.documents = this.documents.filter(d => d.id !== this.docToDelete.id);
                    this.showDeleteModal = false;
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Document has been deleted.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error('Failed to delete document');
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete document. Please try again.' });
            } finally {
                this.deleting = false;
                this.docToDelete = null;
            }
        }
    };
}
</script>
@endpush
@endsection
