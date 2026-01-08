{{-- Backup Settings View --}}
{{-- Prompt 280: Backup creation, restore, auto-backup configuration, download/delete --}}

@extends('layouts.app')

@section('title', 'Backup Settings')

@section('content')
<div x-data="backupSettings()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Backup Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active">Backups</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('settings.general') ?? '#' }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Settings
            </a>
            <button type="button" class="btn btn-primary" @click="createBackup()" :disabled="creating">
                <span x-show="!creating"><i class="bi bi-plus-lg me-1"></i> Create Backup</span>
                <span x-show="creating"><span class="spinner-border spinner-border-sm me-1"></span> Creating...</span>
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
        <div class="col-lg-8">
            <!-- Create Backup -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-cloud-upload me-2 text-primary"></i>Create New Backup</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Backup Name</label>
                            <input type="text" class="form-control" x-model="newBackup.name" 
                                   placeholder="e.g., Monthly Backup January 2026">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Backup Type</label>
                            <select class="form-select" x-model="newBackup.type">
                                <option value="full">Full Backup (Database + Files)</option>
                                <option value="database">Database Only</option>
                                <option value="files">Files Only</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Include</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" x-model="newBackup.include_database" 
                                           id="includeDatabase" :disabled="newBackup.type === 'files'">
                                    <label class="form-check-label" for="includeDatabase">Database</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" x-model="newBackup.include_uploads" 
                                           id="includeUploads" :disabled="newBackup.type === 'database'">
                                    <label class="form-check-label" for="includeUploads">Uploads</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" x-model="newBackup.include_documents" 
                                           id="includeDocuments" :disabled="newBackup.type === 'database'">
                                    <label class="form-check-label" for="includeDocuments">Documents</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" x-model="newBackup.include_logs" 
                                           id="includeLogs" :disabled="newBackup.type === 'database'">
                                    <label class="form-check-label" for="includeLogs">Logs</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" x-model="newBackup.notes" rows="2" 
                                      placeholder="Add any notes about this backup..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Backup List -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-archive me-2 text-success"></i>Available Backups</h5>
                    <span class="badge bg-primary" x-text="backups.length + ' Backups'"></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Backup Name</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Created</th>
                                    <th>Status</th>
                                    <th style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="backup in backups" :key="backup.id">
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-zip fs-4 text-warning me-2"></i>
                                                <div>
                                                    <div class="fw-medium" x-text="backup.name"></div>
                                                    <small class="text-muted" x-text="backup.filename"></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge" 
                                                  :class="{
                                                      'bg-primary': backup.type === 'full',
                                                      'bg-info': backup.type === 'database',
                                                      'bg-secondary': backup.type === 'files'
                                                  }"
                                                  x-text="backup.type.charAt(0).toUpperCase() + backup.type.slice(1)"></span>
                                        </td>
                                        <td x-text="backup.size"></td>
                                        <td>
                                            <div x-text="backup.created_at"></div>
                                            <small class="text-muted" x-text="backup.created_by"></small>
                                        </td>
                                        <td>
                                            <span class="badge" 
                                                  :class="{
                                                      'bg-success': backup.status === 'completed',
                                                      'bg-warning': backup.status === 'in_progress',
                                                      'bg-danger': backup.status === 'failed'
                                                  }">
                                                <span x-text="backup.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        @click="downloadBackup(backup)" title="Download"
                                                        :disabled="backup.status !== 'completed'">
                                                    <i class="bi bi-download"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-warning" 
                                                        @click="restoreBackup(backup)" title="Restore"
                                                        :disabled="backup.status !== 'completed'">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-info" 
                                                        @click="viewBackupDetails(backup)" title="Details">
                                                    <i class="bi bi-info-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        @click="deleteBackup(backup)" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="backups.length === 0">
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-archive fs-1 d-block mb-2"></i>
                                            No backups found. Create your first backup.
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
            <!-- Auto Backup Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-info"></i>Auto Backup</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" x-model="autoBackup.enabled" id="autoBackupEnabled">
                        <label class="form-check-label" for="autoBackupEnabled">
                            Enable Auto Backup
                        </label>
                    </div>
                    <div x-show="autoBackup.enabled">
                        <div class="mb-3">
                            <label class="form-label">Frequency</label>
                            <select class="form-select" x-model="autoBackup.frequency">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div class="mb-3" x-show="autoBackup.frequency === 'weekly'">
                            <label class="form-label">Day of Week</label>
                            <select class="form-select" x-model="autoBackup.day_of_week">
                                <option value="0">Sunday</option>
                                <option value="1">Monday</option>
                                <option value="2">Tuesday</option>
                                <option value="3">Wednesday</option>
                                <option value="4">Thursday</option>
                                <option value="5">Friday</option>
                                <option value="6">Saturday</option>
                            </select>
                        </div>
                        <div class="mb-3" x-show="autoBackup.frequency === 'monthly'">
                            <label class="form-label">Day of Month</label>
                            <select class="form-select" x-model="autoBackup.day_of_month">
                                <template x-for="day in 28" :key="day">
                                    <option :value="day" x-text="day"></option>
                                </template>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Time</label>
                            <input type="time" class="form-control" x-model="autoBackup.time">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Backup Type</label>
                            <select class="form-select" x-model="autoBackup.type">
                                <option value="full">Full Backup</option>
                                <option value="database">Database Only</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keep Last</label>
                            <select class="form-select" x-model="autoBackup.retention">
                                <option value="5">5 Backups</option>
                                <option value="10">10 Backups</option>
                                <option value="15">15 Backups</option>
                                <option value="30">30 Backups</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary w-100" @click="saveAutoBackupSettings()">
                            <i class="bi bi-check-lg me-1"></i> Save Auto Backup Settings
                        </button>
                    </div>
                </div>
            </div>

            <!-- Storage Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-hdd me-2 text-warning"></i>Storage</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Used Space</span>
                            <span x-text="storage.used + ' / ' + storage.total"></span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" :class="storageBarClass" role="progressbar" 
                                 :style="'width: ' + storage.percentage + '%'"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Database</span>
                        <strong x-text="storage.database">0 MB</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Uploads</span>
                        <strong x-text="storage.uploads">0 MB</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Backups</span>
                        <strong x-text="storage.backups">0 MB</strong>
                    </div>
                </div>
            </div>

            <!-- Last Backup Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-clock me-2 text-success"></i>Last Backup</h5>
                </div>
                <div class="card-body">
                    <template x-if="lastBackup">
                        <div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Name</span>
                                <strong x-text="lastBackup.name"></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Date</span>
                                <strong x-text="lastBackup.created_at"></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Size</span>
                                <strong x-text="lastBackup.size"></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Status</span>
                                <span class="badge bg-success" x-text="lastBackup.status"></span>
                            </div>
                        </div>
                    </template>
                    <template x-if="!lastBackup">
                        <p class="text-muted text-center mb-0">No backups yet</p>
                    </template>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2 text-primary"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary text-start" @click="createBackup()">
                            <i class="bi bi-database-add me-2"></i> Quick Database Backup
                        </button>
                        <button type="button" class="btn btn-outline-primary text-start" @click="uploadBackup()">
                            <i class="bi bi-upload me-2"></i> Upload Backup File
                        </button>
                        <button type="button" class="btn btn-outline-danger text-start" @click="cleanOldBackups()">
                            <i class="bi bi-trash me-2"></i> Clean Old Backups
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Details Modal -->
    <div class="modal fade" :class="{ 'show d-block': showDetailsModal }" tabindex="-1" 
         x-show="showDetailsModal" @click.self="showDetailsModal = false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Backup Details</h5>
                    <button type="button" class="btn-close" @click="showDetailsModal = false"></button>
                </div>
                <div class="modal-body" x-show="selectedBackup">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Name:</td>
                            <td class="fw-medium" x-text="selectedBackup?.name"></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Filename:</td>
                            <td x-text="selectedBackup?.filename"></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Type:</td>
                            <td x-text="selectedBackup?.type"></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Size:</td>
                            <td x-text="selectedBackup?.size"></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Created:</td>
                            <td x-text="selectedBackup?.created_at"></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Created By:</td>
                            <td x-text="selectedBackup?.created_by"></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status:</td>
                            <td><span class="badge bg-success" x-text="selectedBackup?.status"></span></td>
                        </tr>
                        <tr x-show="selectedBackup?.notes">
                            <td class="text-muted">Notes:</td>
                            <td x-text="selectedBackup?.notes"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showDetailsModal = false">Close</button>
                    <button type="button" class="btn btn-primary" @click="downloadBackup(selectedBackup)">
                        <i class="bi bi-download me-1"></i> Download
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" x-show="showDetailsModal" @click="showDetailsModal = false"></div>
</div>
@endsection

@push('scripts')
<script>
function backupSettings() {
    return {
        creating: false,
        showDetailsModal: false,
        selectedBackup: null,
        newBackup: {
            name: '',
            type: 'full',
            include_database: true,
            include_uploads: true,
            include_documents: true,
            include_logs: false,
            notes: ''
        },
        autoBackup: {
            enabled: true,
            frequency: 'weekly',
            day_of_week: '0',
            day_of_month: 1,
            time: '02:00',
            type: 'full',
            retention: '10'
        },
        storage: {
            used: '2.4 GB',
            total: '10 GB',
            percentage: 24,
            database: '156 MB',
            uploads: '1.8 GB',
            backups: '450 MB'
        },
        backups: [
            { id: 1, name: 'Weekly Backup', filename: 'backup_2026_01_05_020000.zip', type: 'full', size: '125 MB', created_at: 'Jan 05, 2026 02:00 AM', created_by: 'System (Auto)', status: 'completed', notes: 'Automated weekly backup' },
            { id: 2, name: 'Pre-Update Backup', filename: 'backup_2026_01_03_143022.zip', type: 'database', size: '45 MB', created_at: 'Jan 03, 2026 02:30 PM', created_by: 'Admin', status: 'completed', notes: 'Before system update' },
            { id: 3, name: 'Monthly Backup December', filename: 'backup_2025_12_31_235959.zip', type: 'full', size: '118 MB', created_at: 'Dec 31, 2025 11:59 PM', created_by: 'System (Auto)', status: 'completed', notes: '' },
            { id: 4, name: 'Weekly Backup', filename: 'backup_2025_12_29_020000.zip', type: 'full', size: '115 MB', created_at: 'Dec 29, 2025 02:00 AM', created_by: 'System (Auto)', status: 'completed', notes: '' },
            { id: 5, name: 'Emergency Backup', filename: 'backup_2025_12_25_101530.zip', type: 'database', size: '42 MB', created_at: 'Dec 25, 2025 10:15 AM', created_by: 'Admin', status: 'completed', notes: 'Before data migration' }
        ],
        
        get lastBackup() {
            return this.backups.length > 0 ? this.backups[0] : null;
        },
        
        get storageBarClass() {
            if (this.storage.percentage >= 90) return 'bg-danger';
            if (this.storage.percentage >= 70) return 'bg-warning';
            return 'bg-success';
        },
        
        async createBackup() {
            if (!this.newBackup.name) {
                const now = new Date();
                this.newBackup.name = 'Backup ' + now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }
            
            this.creating = true;
            
            try {
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                const newId = this.backups.length + 1;
                const now = new Date();
                const filename = 'backup_' + now.toISOString().replace(/[-:T]/g, '_').split('.')[0] + '.zip';
                
                this.backups.unshift({
                    id: newId,
                    name: this.newBackup.name,
                    filename: filename,
                    type: this.newBackup.type,
                    size: this.newBackup.type === 'full' ? '120 MB' : '45 MB',
                    created_at: now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' }),
                    created_by: 'Admin',
                    status: 'completed',
                    notes: this.newBackup.notes
                });
                
                this.newBackup = {
                    name: '',
                    type: 'full',
                    include_database: true,
                    include_uploads: true,
                    include_documents: true,
                    include_logs: false,
                    notes: ''
                };
                
                Swal.fire({
                    icon: 'success',
                    title: 'Backup Created!',
                    text: 'Your backup has been created successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to create backup. Please try again.'
                });
            } finally {
                this.creating = false;
            }
        },
        
        downloadBackup(backup) {
            Swal.fire({
                icon: 'info',
                title: 'Download Started',
                text: 'Your backup file ' + backup.filename + ' is being downloaded.',
                timer: 2000,
                showConfirmButton: false
            });
        },
        
        async restoreBackup(backup) {
            const result = await Swal.fire({
                icon: 'warning',
                title: 'Restore Backup?',
                html: `<p>Are you sure you want to restore from <strong>${backup.name}</strong>?</p>
                       <p class="text-danger"><small>This will overwrite current data. This action cannot be undone.</small></p>`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Restore',
                confirmButtonColor: '#dc3545',
                input: 'checkbox',
                inputValue: 0,
                inputPlaceholder: 'I understand this will overwrite current data'
            });
            
            if (result.isConfirmed && result.value) {
                Swal.fire({
                    title: 'Restoring...',
                    text: 'Please wait while the backup is being restored.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                await new Promise(resolve => setTimeout(resolve, 3000));
                
                Swal.fire({
                    icon: 'success',
                    title: 'Restore Complete!',
                    text: 'The backup has been restored successfully. Please refresh the page.',
                    confirmButtonText: 'Refresh Page'
                }).then(() => {
                    // window.location.reload();
                });
            } else if (result.isConfirmed && !result.value) {
                Swal.fire({
                    icon: 'info',
                    title: 'Confirmation Required',
                    text: 'Please check the confirmation box to proceed with restore.'
                });
            }
        },
        
        viewBackupDetails(backup) {
            this.selectedBackup = backup;
            this.showDetailsModal = true;
        },
        
        async deleteBackup(backup) {
            const result = await Swal.fire({
                icon: 'warning',
                title: 'Delete Backup?',
                text: `Are you sure you want to delete "${backup.name}"? This cannot be undone.`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                confirmButtonColor: '#dc3545'
            });
            
            if (result.isConfirmed) {
                this.backups = this.backups.filter(b => b.id !== backup.id);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Backup Deleted!',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        },
        
        async saveAutoBackupSettings() {
            Swal.fire({
                icon: 'success',
                title: 'Auto Backup Settings Saved!',
                text: 'Your auto backup configuration has been updated.',
                timer: 2000,
                showConfirmButton: false
            });
        },
        
        uploadBackup() {
            Swal.fire({
                icon: 'info',
                title: 'Upload Backup',
                text: 'Backup upload feature coming soon!'
            });
        },
        
        cleanOldBackups() {
            Swal.fire({
                icon: 'warning',
                title: 'Clean Old Backups?',
                text: 'This will delete backups older than 30 days. Continue?',
                showCancelButton: true,
                confirmButtonText: 'Yes, Clean',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cleanup Complete!',
                        text: '0 old backups were removed.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
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
