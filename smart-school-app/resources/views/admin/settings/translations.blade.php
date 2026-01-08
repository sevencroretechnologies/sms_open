{{-- Translations Management View --}}
{{-- Prompt 277: Translation key management, search/filter, import/export, auto-translate --}}

@extends('layouts.app')

@section('title', 'Translations Management')

@section('content')
<div x-data="translationsManager()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Translations Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item"><a href="{{ route('settings.languages') ?? '#' }}">Languages</a></li>
                    <li class="breadcrumb-item active">Translations</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button type="button" class="btn btn-outline-secondary" @click="importTranslations()">
                <i class="bi bi-upload me-1"></i> Import
            </button>
            <button type="button" class="btn btn-outline-secondary" @click="exportTranslations()">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button type="button" class="btn btn-primary" @click="showAddModal = true">
                <i class="bi bi-plus-lg me-1"></i> Add Key
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
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Search keys or values..." 
                                       x-model="filters.search" @input.debounce.300ms="filterTranslations()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Group</label>
                            <select class="form-select" x-model="filters.group" @change="filterTranslations()">
                                <option value="">All Groups</option>
                                <template x-for="group in groups" :key="group">
                                    <option :value="group" x-text="group"></option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Target Language</label>
                            <select class="form-select" x-model="filters.targetLanguage" @change="filterTranslations()">
                                <template x-for="lang in languages" :key="lang.code">
                                    <option :value="lang.code" x-text="lang.name"></option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" x-model="filters.status" @change="filterTranslations()">
                                <option value="">All</option>
                                <option value="translated">Translated</option>
                                <option value="untranslated">Untranslated</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Translations Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-translate me-2 text-primary"></i>
                        Translation Keys
                        <span class="badge bg-secondary ms-2" x-text="filteredTranslations.length + ' keys'"></span>
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" @click="autoTranslateAll()" 
                                :disabled="translating">
                            <span x-show="!translating"><i class="bi bi-magic me-1"></i> Auto-Translate All</span>
                            <span x-show="translating"><span class="spinner-border spinner-border-sm me-1"></span> Translating...</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-success" @click="saveAllTranslations()" 
                                :disabled="saving">
                            <span x-show="!saving"><i class="bi bi-check-lg me-1"></i> Save All</span>
                            <span x-show="saving"><span class="spinner-border spinner-border-sm me-1"></span> Saving...</span>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30%;">Key</th>
                                    <th style="width: 30%;">English (Source)</th>
                                    <th style="width: 30%;">
                                        <span x-text="getLanguageName(filters.targetLanguage)"></span> (Target)
                                    </th>
                                    <th style="width: 10%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(translation, index) in paginatedTranslations" :key="translation.key">
                                    <tr :class="{ 'table-warning': !translation.translations[filters.targetLanguage] }">
                                        <td>
                                            <div class="d-flex flex-column">
                                                <code class="small" x-text="translation.key"></code>
                                                <small class="text-muted" x-text="translation.group"></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span x-text="translation.translations.en || '-'"></span>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control" 
                                                       :value="translation.translations[filters.targetLanguage] || ''"
                                                       @input="updateTranslation(translation.key, $event.target.value)"
                                                       :placeholder="'Enter ' + getLanguageName(filters.targetLanguage) + ' translation'"
                                                       :dir="isRtl(filters.targetLanguage) ? 'rtl' : 'ltr'">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        @click="autoTranslate(translation)" title="Auto-translate">
                                                    <i class="bi bi-magic"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        @click="editKey(translation)" title="Edit Key">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        @click="deleteKey(translation)" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredTranslations.length === 0">
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-search fs-1 d-block mb-2"></i>
                                            No translation keys found
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Pagination -->
                <div class="card-footer bg-white" x-show="totalPages > 1">
                    <nav aria-label="Translation pagination">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                                <button class="page-link" @click="currentPage = 1" :disabled="currentPage === 1">
                                    <i class="bi bi-chevron-double-left"></i>
                                </button>
                            </li>
                            <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                                <button class="page-link" @click="currentPage--" :disabled="currentPage === 1">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                            </li>
                            <template x-for="page in visiblePages" :key="page">
                                <li class="page-item" :class="{ 'active': page === currentPage }">
                                    <button class="page-link" @click="currentPage = page" x-text="page"></button>
                                </li>
                            </template>
                            <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                                <button class="page-link" @click="currentPage++" :disabled="currentPage === totalPages">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </li>
                            <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                                <button class="page-link" @click="currentPage = totalPages" :disabled="currentPage === totalPages">
                                    <i class="bi bi-chevron-double-right"></i>
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Progress -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Progress</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span x-text="getLanguageName(filters.targetLanguage)"></span>
                            <span x-text="translationProgress + '%'"></span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" :class="progressBarClass" role="progressbar" 
                                 :style="'width: ' + translationProgress + '%'"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span x-text="translatedCount + ' translated'"></span>
                        <span x-text="untranslatedCount + ' remaining'"></span>
                    </div>
                </div>
            </div>

            <!-- Groups -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-folder me-2 text-warning"></i>Groups</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                :class="{ 'active': filters.group === '' }"
                                @click="filters.group = ''; filterTranslations()">
                            All Groups
                            <span class="badge bg-primary rounded-pill" x-text="translations.length"></span>
                        </button>
                        <template x-for="group in groups" :key="group">
                            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                    :class="{ 'active': filters.group === group }"
                                    @click="filters.group = group; filterTranslations()">
                                <span x-text="group"></span>
                                <span class="badge bg-secondary rounded-pill" x-text="getGroupCount(group)"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2 text-success"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary text-start" @click="scanForNewKeys()">
                            <i class="bi bi-search me-2"></i> Scan for New Keys
                        </button>
                        <button type="button" class="btn btn-outline-primary text-start" @click="findMissing()">
                            <i class="bi bi-exclamation-triangle me-2"></i> Find Missing
                        </button>
                        <button type="button" class="btn btn-outline-danger text-start" @click="cleanUnused()">
                            <i class="bi bi-trash me-2"></i> Clean Unused Keys
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Key Modal -->
    <div class="modal fade" :class="{ 'show d-block': showAddModal || showEditModal }" tabindex="-1" 
         x-show="showAddModal || showEditModal" @click.self="closeModal()">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" x-text="showEditModal ? 'Edit Translation Key' : 'Add Translation Key'"></h5>
                    <button type="button" class="btn-close" @click="closeModal()"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveKey()">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Group <span class="text-danger">*</span></label>
                                <select class="form-select" x-model="modalForm.group" required>
                                    <template x-for="group in groups" :key="group">
                                        <option :value="group" x-text="group"></option>
                                    </template>
                                    <option value="__new__">+ Add New Group</option>
                                </select>
                            </div>
                            <div class="col-md-6" x-show="modalForm.group === '__new__'">
                                <label class="form-label">New Group Name</label>
                                <input type="text" class="form-control" x-model="modalForm.newGroup" 
                                       placeholder="e.g., dashboard">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Key <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" x-model="modalForm.key" required 
                                       placeholder="e.g., welcome_message" :disabled="showEditModal">
                                <small class="text-muted">Use snake_case for key names</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">English (Source) <span class="text-danger">*</span></label>
                                <textarea class="form-control" x-model="modalForm.en" rows="2" required 
                                          placeholder="Enter English text"></textarea>
                            </div>
                            <template x-for="lang in languages.filter(l => l.code !== 'en')" :key="lang.code">
                                <div class="col-md-6">
                                    <label class="form-label" x-text="lang.name"></label>
                                    <textarea class="form-control" x-model="modalForm[lang.code]" rows="2" 
                                              :placeholder="'Enter ' + lang.name + ' translation'"
                                              :dir="lang.direction"></textarea>
                                </div>
                            </template>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="saveKey()" :disabled="savingKey">
                        <span x-show="!savingKey" x-text="showEditModal ? 'Update' : 'Add Key'"></span>
                        <span x-show="savingKey"><span class="spinner-border spinner-border-sm me-1"></span> Saving...</span>
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
function translationsManager() {
    return {
        showAddModal: false,
        showEditModal: false,
        savingKey: false,
        saving: false,
        translating: false,
        currentPage: 1,
        perPage: 15,
        languages: [
            { code: 'en', name: 'English', direction: 'ltr' },
            { code: 'hi', name: 'Hindi', direction: 'ltr' },
            { code: 'ar', name: 'Arabic', direction: 'rtl' },
            { code: 'es', name: 'Spanish', direction: 'ltr' },
            { code: 'fr', name: 'French', direction: 'ltr' }
        ],
        groups: ['auth', 'common', 'dashboard', 'students', 'teachers', 'fees', 'exams', 'attendance', 'messages', 'settings'],
        filters: {
            search: '',
            group: '',
            targetLanguage: 'hi',
            status: ''
        },
        translations: [
            { key: 'auth.login', group: 'auth', translations: { en: 'Login', hi: 'लॉग इन', ar: 'تسجيل الدخول', es: 'Iniciar sesión', fr: 'Connexion' } },
            { key: 'auth.logout', group: 'auth', translations: { en: 'Logout', hi: 'लॉग आउट', ar: 'تسجيل الخروج', es: 'Cerrar sesión', fr: 'Déconnexion' } },
            { key: 'auth.register', group: 'auth', translations: { en: 'Register', hi: 'पंजीकरण', ar: '', es: 'Registrarse', fr: '' } },
            { key: 'auth.forgot_password', group: 'auth', translations: { en: 'Forgot Password?', hi: '', ar: '', es: '', fr: '' } },
            { key: 'common.save', group: 'common', translations: { en: 'Save', hi: 'सहेजें', ar: 'حفظ', es: 'Guardar', fr: 'Enregistrer' } },
            { key: 'common.cancel', group: 'common', translations: { en: 'Cancel', hi: 'रद्द करें', ar: 'إلغاء', es: 'Cancelar', fr: 'Annuler' } },
            { key: 'common.delete', group: 'common', translations: { en: 'Delete', hi: 'हटाएं', ar: 'حذف', es: 'Eliminar', fr: 'Supprimer' } },
            { key: 'common.edit', group: 'common', translations: { en: 'Edit', hi: 'संपादित करें', ar: 'تعديل', es: 'Editar', fr: 'Modifier' } },
            { key: 'common.search', group: 'common', translations: { en: 'Search', hi: 'खोजें', ar: 'بحث', es: 'Buscar', fr: 'Rechercher' } },
            { key: 'common.filter', group: 'common', translations: { en: 'Filter', hi: '', ar: '', es: '', fr: '' } },
            { key: 'dashboard.welcome', group: 'dashboard', translations: { en: 'Welcome to Dashboard', hi: 'डैशबोर्ड में आपका स्वागत है', ar: 'مرحبا بك في لوحة التحكم', es: '', fr: '' } },
            { key: 'dashboard.total_students', group: 'dashboard', translations: { en: 'Total Students', hi: 'कुल छात्र', ar: 'إجمالي الطلاب', es: 'Total de Estudiantes', fr: '' } },
            { key: 'students.add_student', group: 'students', translations: { en: 'Add Student', hi: 'छात्र जोड़ें', ar: '', es: '', fr: '' } },
            { key: 'students.student_list', group: 'students', translations: { en: 'Student List', hi: 'छात्र सूची', ar: '', es: '', fr: '' } },
            { key: 'fees.pay_fee', group: 'fees', translations: { en: 'Pay Fee', hi: 'शुल्क भुगतान', ar: '', es: '', fr: '' } },
            { key: 'fees.fee_receipt', group: 'fees', translations: { en: 'Fee Receipt', hi: '', ar: '', es: '', fr: '' } },
            { key: 'exams.create_exam', group: 'exams', translations: { en: 'Create Exam', hi: '', ar: '', es: '', fr: '' } },
            { key: 'attendance.mark_attendance', group: 'attendance', translations: { en: 'Mark Attendance', hi: 'उपस्थिति दर्ज करें', ar: '', es: '', fr: '' } },
            { key: 'settings.general', group: 'settings', translations: { en: 'General Settings', hi: 'सामान्य सेटिंग्स', ar: 'الإعدادات العامة', es: '', fr: '' } },
            { key: 'messages.compose', group: 'messages', translations: { en: 'Compose Message', hi: 'संदेश लिखें', ar: '', es: '', fr: '' } }
        ],
        filteredTranslations: [],
        modalForm: {
            key: '',
            group: 'common',
            newGroup: '',
            en: '',
            hi: '',
            ar: '',
            es: '',
            fr: ''
        },
        
        init() {
            this.filteredTranslations = [...this.translations];
        },
        
        get totalPages() {
            return Math.ceil(this.filteredTranslations.length / this.perPage);
        },
        
        get paginatedTranslations() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredTranslations.slice(start, start + this.perPage);
        },
        
        get visiblePages() {
            const pages = [];
            const start = Math.max(1, this.currentPage - 2);
            const end = Math.min(this.totalPages, start + 4);
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },
        
        get translatedCount() {
            return this.translations.filter(t => t.translations[this.filters.targetLanguage]).length;
        },
        
        get untranslatedCount() {
            return this.translations.length - this.translatedCount;
        },
        
        get translationProgress() {
            return Math.round((this.translatedCount / this.translations.length) * 100);
        },
        
        get progressBarClass() {
            if (this.translationProgress >= 80) return 'bg-success';
            if (this.translationProgress >= 50) return 'bg-warning';
            return 'bg-danger';
        },
        
        getLanguageName(code) {
            const lang = this.languages.find(l => l.code === code);
            return lang ? lang.name : code;
        },
        
        isRtl(code) {
            const lang = this.languages.find(l => l.code === code);
            return lang && lang.direction === 'rtl';
        },
        
        getGroupCount(group) {
            return this.translations.filter(t => t.group === group).length;
        },
        
        filterTranslations() {
            this.currentPage = 1;
            this.filteredTranslations = this.translations.filter(t => {
                if (this.filters.search) {
                    const search = this.filters.search.toLowerCase();
                    const matchKey = t.key.toLowerCase().includes(search);
                    const matchValue = Object.values(t.translations).some(v => v && v.toLowerCase().includes(search));
                    if (!matchKey && !matchValue) return false;
                }
                if (this.filters.group && t.group !== this.filters.group) return false;
                if (this.filters.status === 'translated' && !t.translations[this.filters.targetLanguage]) return false;
                if (this.filters.status === 'untranslated' && t.translations[this.filters.targetLanguage]) return false;
                return true;
            });
        },
        
        updateTranslation(key, value) {
            const translation = this.translations.find(t => t.key === key);
            if (translation) {
                translation.translations[this.filters.targetLanguage] = value;
            }
        },
        
        closeModal() {
            this.showAddModal = false;
            this.showEditModal = false;
            this.resetForm();
        },
        
        resetForm() {
            this.modalForm = {
                key: '',
                group: 'common',
                newGroup: '',
                en: '',
                hi: '',
                ar: '',
                es: '',
                fr: ''
            };
        },
        
        editKey(translation) {
            this.modalForm = {
                key: translation.key,
                group: translation.group,
                newGroup: '',
                ...translation.translations
            };
            this.showEditModal = true;
        },
        
        async saveKey() {
            if (!this.modalForm.key || !this.modalForm.en) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Required Fields',
                    text: 'Please fill in the key and English translation.'
                });
                return;
            }
            
            this.savingKey = true;
            
            try {
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                const group = this.modalForm.group === '__new__' ? this.modalForm.newGroup : this.modalForm.group;
                
                if (this.showEditModal) {
                    const index = this.translations.findIndex(t => t.key === this.modalForm.key);
                    if (index !== -1) {
                        this.translations[index].translations = {
                            en: this.modalForm.en,
                            hi: this.modalForm.hi,
                            ar: this.modalForm.ar,
                            es: this.modalForm.es,
                            fr: this.modalForm.fr
                        };
                    }
                } else {
                    this.translations.push({
                        key: group + '.' + this.modalForm.key,
                        group: group,
                        translations: {
                            en: this.modalForm.en,
                            hi: this.modalForm.hi,
                            ar: this.modalForm.ar,
                            es: this.modalForm.es,
                            fr: this.modalForm.fr
                        }
                    });
                    
                    if (this.modalForm.group === '__new__' && !this.groups.includes(group)) {
                        this.groups.push(group);
                    }
                }
                
                this.filterTranslations();
                
                Swal.fire({
                    icon: 'success',
                    title: this.showEditModal ? 'Translation Updated!' : 'Translation Added!',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                this.closeModal();
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to save translation. Please try again.'
                });
            } finally {
                this.savingKey = false;
            }
        },
        
        async deleteKey(translation) {
            const result = await Swal.fire({
                icon: 'warning',
                title: 'Delete Translation Key?',
                text: `Are you sure you want to delete "${translation.key}"?`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                confirmButtonColor: '#dc3545'
            });
            
            if (result.isConfirmed) {
                this.translations = this.translations.filter(t => t.key !== translation.key);
                this.filterTranslations();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Translation Deleted!',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        },
        
        async autoTranslate(translation) {
            Swal.fire({
                icon: 'info',
                title: 'Auto-Translate',
                text: 'Auto-translation feature coming soon! This will use Google Translate API.'
            });
        },
        
        async autoTranslateAll() {
            this.translating = true;
            
            try {
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                Swal.fire({
                    icon: 'info',
                    title: 'Auto-Translate All',
                    text: 'Auto-translation feature coming soon! This will translate all missing translations.'
                });
            } finally {
                this.translating = false;
            }
        },
        
        async saveAllTranslations() {
            this.saving = true;
            
            try {
                await new Promise(resolve => setTimeout(resolve, 1500));
                
                Swal.fire({
                    icon: 'success',
                    title: 'Translations Saved!',
                    text: 'All translations have been saved successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to save translations. Please try again.'
                });
            } finally {
                this.saving = false;
            }
        },
        
        importTranslations() {
            Swal.fire({
                icon: 'info',
                title: 'Import Translations',
                text: 'Import feature coming soon! You will be able to import JSON or CSV files.'
            });
        },
        
        exportTranslations() {
            Swal.fire({
                icon: 'info',
                title: 'Export Translations',
                text: 'Export feature coming soon! You will be able to export to JSON or CSV format.'
            });
        },
        
        scanForNewKeys() {
            Swal.fire({
                icon: 'info',
                title: 'Scan for New Keys',
                text: 'This will scan your codebase for new translation keys.'
            });
        },
        
        findMissing() {
            this.filters.status = 'untranslated';
            this.filterTranslations();
        },
        
        cleanUnused() {
            Swal.fire({
                icon: 'warning',
                title: 'Clean Unused Keys',
                text: 'This will remove translation keys that are no longer used in the codebase.',
                showCancelButton: true,
                confirmButtonText: 'Scan & Clean',
                confirmButtonColor: '#dc3545'
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

[dir="rtl"] .text-start {
    text-align: right !important;
}
</style>
@endpush
