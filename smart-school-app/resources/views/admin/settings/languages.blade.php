{{-- Language Settings View --}}
{{-- Prompt 276: Language management, RTL support, default language selection --}}

@extends('layouts.app')

@section('title', 'Language Settings')

@section('content')
<div x-data="languageSettings()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Language Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active">Languages</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('settings.general') ?? '#' }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Settings
            </a>
            <button type="button" class="btn btn-primary" @click="showAddModal = true">
                <i class="bi bi-plus-lg me-1"></i> Add Language
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Languages List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-translate me-2 text-primary"></i>Available Languages</h5>
                    <span class="badge bg-primary" x-text="languages.length + ' Languages'"></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Language</th>
                                    <th>Code</th>
                                    <th>Native Name</th>
                                    <th>Direction</th>
                                    <th>Default</th>
                                    <th>Status</th>
                                    <th style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="language in languages" :key="language.id">
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="flag-icon me-2" x-text="language.flag"></span>
                                                <span class="fw-medium" x-text="language.name"></span>
                                            </div>
                                        </td>
                                        <td>
                                            <code x-text="language.code"></code>
                                        </td>
                                        <td x-text="language.native_name"></td>
                                        <td>
                                            <span class="badge" 
                                                  :class="language.direction === 'rtl' ? 'bg-warning' : 'bg-secondary'"
                                                  x-text="language.direction.toUpperCase()"></span>
                                        </td>
                                        <td>
                                            <template x-if="language.is_default">
                                                <span class="badge bg-success">Default</span>
                                            </template>
                                            <template x-if="!language.is_default">
                                                <button class="btn btn-sm btn-outline-secondary" 
                                                        @click="setDefault(language)" title="Set as Default">
                                                    <i class="bi bi-star"></i>
                                                </button>
                                            </template>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       :checked="language.is_active"
                                                       @change="toggleStatus(language)"
                                                       :disabled="language.is_default">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a :href="'/admin/settings/translations/' + language.code" 
                                                   class="btn btn-outline-primary" title="Manage Translations">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-secondary" 
                                                        @click="editLanguage(language)" title="Edit">
                                                    <i class="bi bi-gear"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        @click="deleteLanguage(language)" 
                                                        :disabled="language.is_default" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Default Language -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-star me-2 text-warning"></i>Default Language</h5>
                </div>
                <div class="card-body text-center">
                    <div class="display-4 mb-2" x-text="defaultLanguage.flag">🌐</div>
                    <h5 x-text="defaultLanguage.name">English</h5>
                    <p class="text-muted mb-0" x-text="'Code: ' + defaultLanguage.code">Code: en</p>
                </div>
            </div>

            <!-- RTL Languages -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-arrow-left-right me-2 text-info"></i>RTL Support</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Languages with Right-to-Left text direction are automatically detected and the UI adjusts accordingly.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <template x-for="lang in rtlLanguages" :key="lang.code">
                            <span class="badge bg-warning text-dark" x-text="lang.name"></span>
                        </template>
                        <template x-if="rtlLanguages.length === 0">
                            <span class="text-muted">No RTL languages configured</span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2 text-success"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary text-start" @click="importLanguage()">
                            <i class="bi bi-upload me-2"></i> Import Language Pack
                        </button>
                        <button type="button" class="btn btn-outline-primary text-start" @click="exportLanguages()">
                            <i class="bi bi-download me-2"></i> Export All Languages
                        </button>
                        <button type="button" class="btn btn-outline-primary text-start" @click="syncTranslations()">
                            <i class="bi bi-arrow-repeat me-2"></i> Sync Translations
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Languages</span>
                        <strong x-text="languages.length">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Active Languages</span>
                        <strong class="text-success" x-text="activeLanguages.length">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">RTL Languages</span>
                        <strong x-text="rtlLanguages.length">0</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Translation Keys</span>
                        <strong x-text="totalTranslationKeys">0</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Language Modal -->
    <div class="modal fade" :class="{ 'show d-block': showAddModal || showEditModal }" tabindex="-1" 
         x-show="showAddModal || showEditModal" @click.self="closeModal()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" x-text="showEditModal ? 'Edit Language' : 'Add Language'"></h5>
                    <button type="button" class="btn-close" @click="closeModal()"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveLanguage()">
                        <div class="mb-3">
                            <label class="form-label">Language Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" x-model="modalForm.name" required 
                                   placeholder="e.g., English">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Language Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" x-model="modalForm.code" required 
                                   placeholder="e.g., en" maxlength="5" :disabled="showEditModal">
                            <small class="text-muted">ISO 639-1 code (e.g., en, fr, es, ar)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Native Name</label>
                            <input type="text" class="form-control" x-model="modalForm.native_name" 
                                   placeholder="e.g., English">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Flag Emoji</label>
                            <input type="text" class="form-control" x-model="modalForm.flag" 
                                   placeholder="e.g., 🇺🇸" maxlength="4">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Text Direction <span class="text-danger">*</span></label>
                            <select class="form-select" x-model="modalForm.direction" required>
                                <option value="ltr">Left to Right (LTR)</option>
                                <option value="rtl">Right to Left (RTL)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" x-model="modalForm.is_default" 
                                       id="isDefault">
                                <label class="form-check-label" for="isDefault">
                                    Set as Default Language
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" x-model="modalForm.is_active" 
                                       id="isActive">
                                <label class="form-check-label" for="isActive">
                                    Active
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="saveLanguage()" :disabled="savingLanguage">
                        <span x-show="!savingLanguage" x-text="showEditModal ? 'Update' : 'Add Language'"></span>
                        <span x-show="savingLanguage"><span class="spinner-border spinner-border-sm me-1"></span> Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" x-show="showAddModal || showEditModal" @click="closeModal()"></div>
</div>
@endsection

@push('scripts')
<script>
function languageSettings() {
    return {
        showAddModal: false,
        showEditModal: false,
        savingLanguage: false,
        totalTranslationKeys: 1250,
        languages: [
            { id: 1, name: 'English', code: 'en', native_name: 'English', flag: '🇺🇸', direction: 'ltr', is_default: true, is_active: true },
            { id: 2, name: 'Hindi', code: 'hi', native_name: 'हिन्दी', flag: '🇮🇳', direction: 'ltr', is_default: false, is_active: true },
            { id: 3, name: 'Arabic', code: 'ar', native_name: 'العربية', flag: '🇸🇦', direction: 'rtl', is_default: false, is_active: true },
            { id: 4, name: 'Spanish', code: 'es', native_name: 'Español', flag: '🇪🇸', direction: 'ltr', is_default: false, is_active: true },
            { id: 5, name: 'French', code: 'fr', native_name: 'Français', flag: '🇫🇷', direction: 'ltr', is_default: false, is_active: false },
            { id: 6, name: 'Urdu', code: 'ur', native_name: 'اردو', flag: '🇵🇰', direction: 'rtl', is_default: false, is_active: true }
        ],
        modalForm: {
            id: null,
            name: '',
            code: '',
            native_name: '',
            flag: '',
            direction: 'ltr',
            is_default: false,
            is_active: true
        },
        
        get defaultLanguage() {
            return this.languages.find(l => l.is_default) || { name: 'English', code: 'en', flag: '🌐' };
        },
        
        get activeLanguages() {
            return this.languages.filter(l => l.is_active);
        },
        
        get rtlLanguages() {
            return this.languages.filter(l => l.direction === 'rtl' && l.is_active);
        },
        
        closeModal() {
            this.showAddModal = false;
            this.showEditModal = false;
            this.resetForm();
        },
        
        resetForm() {
            this.modalForm = {
                id: null,
                name: '',
                code: '',
                native_name: '',
                flag: '',
                direction: 'ltr',
                is_default: false,
                is_active: true
            };
        },
        
        editLanguage(language) {
            this.modalForm = { ...language };
            this.showEditModal = true;
        },
        
        async saveLanguage() {
            if (!this.modalForm.name || !this.modalForm.code) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Required Fields',
                    text: 'Please fill in all required fields.'
                });
                return;
            }
            
            this.savingLanguage = true;
            
            try {
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                if (this.showEditModal) {
                    const index = this.languages.findIndex(l => l.id === this.modalForm.id);
                    if (index !== -1) {
                        this.languages[index] = { ...this.modalForm };
                    }
                } else {
                    this.modalForm.id = this.languages.length + 1;
                    this.languages.push({ ...this.modalForm });
                }
                
                if (this.modalForm.is_default) {
                    this.languages.forEach(l => {
                        if (l.id !== this.modalForm.id) l.is_default = false;
                    });
                }
                
                Swal.fire({
                    icon: 'success',
                    title: this.showEditModal ? 'Language Updated!' : 'Language Added!',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                this.closeModal();
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to save language. Please try again.'
                });
            } finally {
                this.savingLanguage = false;
            }
        },
        
        async setDefault(language) {
            const result = await Swal.fire({
                icon: 'question',
                title: 'Set as Default?',
                text: `Are you sure you want to set ${language.name} as the default language?`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Set Default'
            });
            
            if (result.isConfirmed) {
                this.languages.forEach(l => l.is_default = false);
                language.is_default = true;
                language.is_active = true;
                
                Swal.fire({
                    icon: 'success',
                    title: 'Default Language Updated!',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        },
        
        toggleStatus(language) {
            language.is_active = !language.is_active;
        },
        
        async deleteLanguage(language) {
            if (language.is_default) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Delete',
                    text: 'You cannot delete the default language.'
                });
                return;
            }
            
            const result = await Swal.fire({
                icon: 'warning',
                title: 'Delete Language?',
                text: `Are you sure you want to delete ${language.name}? This will also delete all translations.`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                confirmButtonColor: '#dc3545'
            });
            
            if (result.isConfirmed) {
                this.languages = this.languages.filter(l => l.id !== language.id);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Language Deleted!',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        },
        
        importLanguage() {
            Swal.fire({
                icon: 'info',
                title: 'Import Language Pack',
                text: 'Language pack import feature coming soon!'
            });
        },
        
        exportLanguages() {
            Swal.fire({
                icon: 'info',
                title: 'Export Languages',
                text: 'Language export feature coming soon!'
            });
        },
        
        syncTranslations() {
            Swal.fire({
                icon: 'info',
                title: 'Sync Translations',
                text: 'Translation sync feature coming soon!'
            });
        }
    };
}
</script>
@endpush

@push('styles')
<style>
.modal.show {
    background-color: rgba(0, 0, 0, 0.5);
}

.flag-icon {
    font-size: 1.25rem;
}

[dir="rtl"] .text-start {
    text-align: right !important;
}
</style>
@endpush
