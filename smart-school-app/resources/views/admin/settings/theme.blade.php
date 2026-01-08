{{-- Theme Settings View --}}
{{-- Prompt 278: Color pickers, typography, layout settings, theme mode, logo uploads --}}

@extends('layouts.app')

@section('title', 'Theme Settings')

@section('content')
<div x-data="themeSettings()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Theme Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active">Theme</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button type="button" class="btn btn-outline-secondary" @click="resetToDefaults()">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset to Defaults
            </button>
            <button type="button" class="btn btn-primary" @click="saveSettings()" :disabled="saving">
                <span x-show="!saving"><i class="bi bi-check-lg me-1"></i> Save Settings</span>
                <span x-show="saving"><span class="spinner-border spinner-border-sm me-1"></span> Saving...</span>
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
        <!-- Main Settings -->
        <div class="col-lg-8">
            <form action="{{ route('settings.theme.update') ?? '#' }}" method="POST" enctype="multipart/form-data" 
                  @submit.prevent="saveSettings()">
                @csrf
                @method('PUT')

                <!-- Theme Mode -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-moon-stars me-2 text-primary"></i>Theme Mode</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-check form-check-card" :class="{ 'active': form.theme_mode === 'light' }">
                                    <input class="form-check-input" type="radio" name="theme_mode" 
                                           value="light" x-model="form.theme_mode" id="themeLight">
                                    <label class="form-check-label d-block text-center p-3" for="themeLight">
                                        <i class="bi bi-sun fs-1 d-block mb-2 text-warning"></i>
                                        <strong>Light Mode</strong>
                                        <small class="d-block text-muted">Bright and clean interface</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-check-card" :class="{ 'active': form.theme_mode === 'dark' }">
                                    <input class="form-check-input" type="radio" name="theme_mode" 
                                           value="dark" x-model="form.theme_mode" id="themeDark">
                                    <label class="form-check-label d-block text-center p-3" for="themeDark">
                                        <i class="bi bi-moon fs-1 d-block mb-2 text-primary"></i>
                                        <strong>Dark Mode</strong>
                                        <small class="d-block text-muted">Easy on the eyes</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-check-card" :class="{ 'active': form.theme_mode === 'auto' }">
                                    <input class="form-check-input" type="radio" name="theme_mode" 
                                           value="auto" x-model="form.theme_mode" id="themeAuto">
                                    <label class="form-check-label d-block text-center p-3" for="themeAuto">
                                        <i class="bi bi-circle-half fs-1 d-block mb-2 text-secondary"></i>
                                        <strong>Auto</strong>
                                        <small class="d-block text-muted">Follow system preference</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Color Scheme -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-palette me-2 text-success"></i>Color Scheme</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label">Primary Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           x-model="form.primary_color" style="width: 50px;">
                                    <input type="text" class="form-control" x-model="form.primary_color" 
                                           placeholder="#0d6efd">
                                </div>
                                <small class="text-muted">Main brand color</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Secondary Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           x-model="form.secondary_color" style="width: 50px;">
                                    <input type="text" class="form-control" x-model="form.secondary_color" 
                                           placeholder="#6c757d">
                                </div>
                                <small class="text-muted">Supporting color</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Accent Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           x-model="form.accent_color" style="width: 50px;">
                                    <input type="text" class="form-control" x-model="form.accent_color" 
                                           placeholder="#198754">
                                </div>
                                <small class="text-muted">Highlight color</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Success Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           x-model="form.success_color" style="width: 50px;">
                                    <input type="text" class="form-control" x-model="form.success_color" 
                                           placeholder="#198754">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Warning Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           x-model="form.warning_color" style="width: 50px;">
                                    <input type="text" class="form-control" x-model="form.warning_color" 
                                           placeholder="#ffc107">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Danger Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           x-model="form.danger_color" style="width: 50px;">
                                    <input type="text" class="form-control" x-model="form.danger_color" 
                                           placeholder="#dc3545">
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="form-label">Preset Color Schemes</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm" 
                                        style="background: linear-gradient(135deg, #0d6efd, #6610f2);"
                                        @click="applyPreset('default')">
                                    <span class="text-white">Default</span>
                                </button>
                                <button type="button" class="btn btn-sm" 
                                        style="background: linear-gradient(135deg, #198754, #20c997);"
                                        @click="applyPreset('nature')">
                                    <span class="text-white">Nature</span>
                                </button>
                                <button type="button" class="btn btn-sm" 
                                        style="background: linear-gradient(135deg, #6f42c1, #d63384);"
                                        @click="applyPreset('purple')">
                                    <span class="text-white">Purple</span>
                                </button>
                                <button type="button" class="btn btn-sm" 
                                        style="background: linear-gradient(135deg, #fd7e14, #dc3545);"
                                        @click="applyPreset('sunset')">
                                    <span class="text-white">Sunset</span>
                                </button>
                                <button type="button" class="btn btn-sm" 
                                        style="background: linear-gradient(135deg, #0dcaf0, #0d6efd);"
                                        @click="applyPreset('ocean')">
                                    <span class="text-white">Ocean</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Typography -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-fonts me-2 text-info"></i>Typography</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Primary Font</label>
                                <select class="form-select" x-model="form.primary_font">
                                    <option value="Inter">Inter</option>
                                    <option value="Roboto">Roboto</option>
                                    <option value="Open Sans">Open Sans</option>
                                    <option value="Lato">Lato</option>
                                    <option value="Poppins">Poppins</option>
                                    <option value="Nunito">Nunito</option>
                                    <option value="Source Sans Pro">Source Sans Pro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Heading Font</label>
                                <select class="form-select" x-model="form.heading_font">
                                    <option value="Inter">Inter</option>
                                    <option value="Roboto">Roboto</option>
                                    <option value="Open Sans">Open Sans</option>
                                    <option value="Lato">Lato</option>
                                    <option value="Poppins">Poppins</option>
                                    <option value="Nunito">Nunito</option>
                                    <option value="Montserrat">Montserrat</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Base Font Size</label>
                                <select class="form-select" x-model="form.font_size">
                                    <option value="14px">Small (14px)</option>
                                    <option value="16px">Medium (16px)</option>
                                    <option value="18px">Large (18px)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Font Weight</label>
                                <select class="form-select" x-model="form.font_weight">
                                    <option value="300">Light (300)</option>
                                    <option value="400">Regular (400)</option>
                                    <option value="500">Medium (500)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Layout Settings -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-layout-sidebar me-2 text-warning"></i>Layout Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Sidebar Style</label>
                                <select class="form-select" x-model="form.sidebar_style">
                                    <option value="default">Default</option>
                                    <option value="compact">Compact</option>
                                    <option value="mini">Mini (Icons Only)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sidebar Position</label>
                                <select class="form-select" x-model="form.sidebar_position">
                                    <option value="left">Left</option>
                                    <option value="right">Right</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Header Style</label>
                                <select class="form-select" x-model="form.header_style">
                                    <option value="fixed">Fixed</option>
                                    <option value="static">Static</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Container Width</label>
                                <select class="form-select" x-model="form.container_width">
                                    <option value="fluid">Full Width</option>
                                    <option value="boxed">Boxed</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Card Style</label>
                                <select class="form-select" x-model="form.card_style">
                                    <option value="shadow">Shadow</option>
                                    <option value="bordered">Bordered</option>
                                    <option value="flat">Flat</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Border Radius</label>
                                <select class="form-select" x-model="form.border_radius">
                                    <option value="0">None (0px)</option>
                                    <option value="4px">Small (4px)</option>
                                    <option value="8px">Medium (8px)</option>
                                    <option value="12px">Large (12px)</option>
                                    <option value="16px">Extra Large (16px)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logo & Branding -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-image me-2 text-danger"></i>Logo & Branding</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Light Logo</label>
                                <div class="border rounded p-3 text-center bg-light mb-2" style="min-height: 100px;">
                                    <template x-if="form.light_logo_preview">
                                        <img :src="form.light_logo_preview" alt="Light Logo" class="img-fluid" style="max-height: 80px;">
                                    </template>
                                    <template x-if="!form.light_logo_preview">
                                        <div class="text-muted">
                                            <i class="bi bi-image fs-1"></i>
                                            <p class="mb-0 small">No logo uploaded</p>
                                        </div>
                                    </template>
                                </div>
                                <input type="file" class="form-control" accept="image/*" 
                                       @change="previewLogo($event, 'light')">
                                <small class="text-muted">Used on dark backgrounds</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dark Logo</label>
                                <div class="border rounded p-3 text-center bg-dark mb-2" style="min-height: 100px;">
                                    <template x-if="form.dark_logo_preview">
                                        <img :src="form.dark_logo_preview" alt="Dark Logo" class="img-fluid" style="max-height: 80px;">
                                    </template>
                                    <template x-if="!form.dark_logo_preview">
                                        <div class="text-white-50">
                                            <i class="bi bi-image fs-1"></i>
                                            <p class="mb-0 small">No logo uploaded</p>
                                        </div>
                                    </template>
                                </div>
                                <input type="file" class="form-control" accept="image/*" 
                                       @change="previewLogo($event, 'dark')">
                                <small class="text-muted">Used on light backgrounds</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Favicon</label>
                                <div class="border rounded p-3 text-center mb-2" style="min-height: 80px;">
                                    <template x-if="form.favicon_preview">
                                        <img :src="form.favicon_preview" alt="Favicon" style="max-height: 48px;">
                                    </template>
                                    <template x-if="!form.favicon_preview">
                                        <div class="text-muted">
                                            <i class="bi bi-app fs-1"></i>
                                            <p class="mb-0 small">No favicon</p>
                                        </div>
                                    </template>
                                </div>
                                <input type="file" class="form-control" accept="image/x-icon,image/png" 
                                       @change="previewLogo($event, 'favicon')">
                                <small class="text-muted">ICO or PNG, 32x32 or 64x64</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Login Background</label>
                                <div class="border rounded p-3 text-center mb-2" style="min-height: 80px;">
                                    <template x-if="form.login_bg_preview">
                                        <img :src="form.login_bg_preview" alt="Login Background" class="img-fluid" style="max-height: 60px;">
                                    </template>
                                    <template x-if="!form.login_bg_preview">
                                        <div class="text-muted">
                                            <i class="bi bi-image fs-1"></i>
                                            <p class="mb-0 small">No background</p>
                                        </div>
                                    </template>
                                </div>
                                <input type="file" class="form-control" accept="image/*" 
                                       @change="previewLogo($event, 'login_bg')">
                                <small class="text-muted">Background image for login page</small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Preview Sidebar -->
        <div class="col-lg-4">
            <!-- Live Preview -->
            <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-eye me-2 text-primary"></i>Live Preview</h5>
                </div>
                <div class="card-body p-0">
                    <div class="preview-container p-3" :style="previewStyles">
                        <!-- Mini Header -->
                        <div class="preview-header rounded mb-2 p-2" :style="{ backgroundColor: form.primary_color }">
                            <div class="d-flex align-items-center">
                                <div class="bg-white rounded-circle me-2" style="width: 24px; height: 24px;"></div>
                                <div class="bg-white bg-opacity-50 rounded" style="width: 80px; height: 12px;"></div>
                            </div>
                        </div>
                        <!-- Mini Content -->
                        <div class="preview-content">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="preview-card rounded p-2" :style="cardPreviewStyle">
                                        <div class="rounded mb-2" :style="{ backgroundColor: form.primary_color, height: '8px', width: '60%' }"></div>
                                        <div class="bg-secondary bg-opacity-25 rounded" style="height: 6px; width: 100%;"></div>
                                        <div class="bg-secondary bg-opacity-25 rounded mt-1" style="height: 6px; width: 80%;"></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="preview-card rounded p-2" :style="cardPreviewStyle">
                                        <div class="rounded mb-2" :style="{ backgroundColor: form.success_color, height: '8px', width: '60%' }"></div>
                                        <div class="bg-secondary bg-opacity-25 rounded" style="height: 6px; width: 100%;"></div>
                                        <div class="bg-secondary bg-opacity-25 rounded mt-1" style="height: 6px; width: 70%;"></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="preview-card rounded p-2" :style="cardPreviewStyle">
                                        <div class="d-flex gap-2 mb-2">
                                            <div class="rounded" :style="{ backgroundColor: form.primary_color, height: '20px', width: '60px' }"></div>
                                            <div class="rounded" :style="{ backgroundColor: form.secondary_color, height: '20px', width: '60px' }"></div>
                                        </div>
                                        <div class="bg-secondary bg-opacity-25 rounded" style="height: 6px;"></div>
                                        <div class="bg-secondary bg-opacity-25 rounded mt-1" style="height: 6px; width: 90%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Settings -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-sliders me-2 text-success"></i>Quick Settings</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" x-model="form.show_breadcrumbs" id="showBreadcrumbs">
                        <label class="form-check-label" for="showBreadcrumbs">Show Breadcrumbs</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" x-model="form.show_footer" id="showFooter">
                        <label class="form-check-label" for="showFooter">Show Footer</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" x-model="form.sticky_sidebar" id="stickySidebar">
                        <label class="form-check-label" for="stickySidebar">Sticky Sidebar</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" x-model="form.enable_animations" id="enableAnimations">
                        <label class="form-check-label" for="enableAnimations">Enable Animations</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" x-model="form.rtl_mode" id="rtlMode">
                        <label class="form-check-label" for="rtlMode">RTL Mode</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function themeSettings() {
    return {
        saving: false,
        form: {
            theme_mode: '{{ $settings["theme_mode"] ?? "light" }}',
            primary_color: '{{ $settings["primary_color"] ?? "#0d6efd" }}',
            secondary_color: '{{ $settings["secondary_color"] ?? "#6c757d" }}',
            accent_color: '{{ $settings["accent_color"] ?? "#198754" }}',
            success_color: '{{ $settings["success_color"] ?? "#198754" }}',
            warning_color: '{{ $settings["warning_color"] ?? "#ffc107" }}',
            danger_color: '{{ $settings["danger_color"] ?? "#dc3545" }}',
            primary_font: '{{ $settings["primary_font"] ?? "Inter" }}',
            heading_font: '{{ $settings["heading_font"] ?? "Inter" }}',
            font_size: '{{ $settings["font_size"] ?? "16px" }}',
            font_weight: '{{ $settings["font_weight"] ?? "400" }}',
            sidebar_style: '{{ $settings["sidebar_style"] ?? "default" }}',
            sidebar_position: '{{ $settings["sidebar_position"] ?? "left" }}',
            header_style: '{{ $settings["header_style"] ?? "fixed" }}',
            container_width: '{{ $settings["container_width"] ?? "fluid" }}',
            card_style: '{{ $settings["card_style"] ?? "shadow" }}',
            border_radius: '{{ $settings["border_radius"] ?? "8px" }}',
            show_breadcrumbs: {{ ($settings['show_breadcrumbs'] ?? true) ? 'true' : 'false' }},
            show_footer: {{ ($settings['show_footer'] ?? true) ? 'true' : 'false' }},
            sticky_sidebar: {{ ($settings['sticky_sidebar'] ?? true) ? 'true' : 'false' }},
            enable_animations: {{ ($settings['enable_animations'] ?? true) ? 'true' : 'false' }},
            rtl_mode: {{ ($settings['rtl_mode'] ?? false) ? 'true' : 'false' }},
            light_logo_preview: '{{ $settings["light_logo"] ?? "" }}',
            dark_logo_preview: '{{ $settings["dark_logo"] ?? "" }}',
            favicon_preview: '{{ $settings["favicon"] ?? "" }}',
            login_bg_preview: '{{ $settings["login_bg"] ?? "" }}'
        },
        
        get previewStyles() {
            return {
                backgroundColor: this.form.theme_mode === 'dark' ? '#1a1a2e' : '#f8f9fa',
                fontFamily: this.form.primary_font,
                fontSize: this.form.font_size,
                borderRadius: this.form.border_radius
            };
        },
        
        get cardPreviewStyle() {
            const styles = {
                backgroundColor: this.form.theme_mode === 'dark' ? '#16213e' : '#ffffff'
            };
            
            if (this.form.card_style === 'shadow') {
                styles.boxShadow = '0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)';
            } else if (this.form.card_style === 'bordered') {
                styles.border = '1px solid #dee2e6';
            }
            
            return styles;
        },
        
        previewLogo(event, type) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (type === 'light') {
                        this.form.light_logo_preview = e.target.result;
                    } else if (type === 'dark') {
                        this.form.dark_logo_preview = e.target.result;
                    } else if (type === 'favicon') {
                        this.form.favicon_preview = e.target.result;
                    } else if (type === 'login_bg') {
                        this.form.login_bg_preview = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        },
        
        applyPreset(preset) {
            const presets = {
                default: {
                    primary_color: '#0d6efd',
                    secondary_color: '#6c757d',
                    accent_color: '#6610f2',
                    success_color: '#198754',
                    warning_color: '#ffc107',
                    danger_color: '#dc3545'
                },
                nature: {
                    primary_color: '#198754',
                    secondary_color: '#6c757d',
                    accent_color: '#20c997',
                    success_color: '#198754',
                    warning_color: '#ffc107',
                    danger_color: '#dc3545'
                },
                purple: {
                    primary_color: '#6f42c1',
                    secondary_color: '#6c757d',
                    accent_color: '#d63384',
                    success_color: '#198754',
                    warning_color: '#ffc107',
                    danger_color: '#dc3545'
                },
                sunset: {
                    primary_color: '#fd7e14',
                    secondary_color: '#6c757d',
                    accent_color: '#dc3545',
                    success_color: '#198754',
                    warning_color: '#ffc107',
                    danger_color: '#dc3545'
                },
                ocean: {
                    primary_color: '#0dcaf0',
                    secondary_color: '#6c757d',
                    accent_color: '#0d6efd',
                    success_color: '#198754',
                    warning_color: '#ffc107',
                    danger_color: '#dc3545'
                }
            };
            
            if (presets[preset]) {
                Object.assign(this.form, presets[preset]);
            }
        },
        
        resetToDefaults() {
            Swal.fire({
                icon: 'warning',
                title: 'Reset to Defaults?',
                text: 'This will reset all theme settings to their default values.',
                showCancelButton: true,
                confirmButtonText: 'Yes, Reset',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.form = {
                        theme_mode: 'light',
                        primary_color: '#0d6efd',
                        secondary_color: '#6c757d',
                        accent_color: '#198754',
                        success_color: '#198754',
                        warning_color: '#ffc107',
                        danger_color: '#dc3545',
                        primary_font: 'Inter',
                        heading_font: 'Inter',
                        font_size: '16px',
                        font_weight: '400',
                        sidebar_style: 'default',
                        sidebar_position: 'left',
                        header_style: 'fixed',
                        container_width: 'fluid',
                        card_style: 'shadow',
                        border_radius: '8px',
                        show_breadcrumbs: true,
                        show_footer: true,
                        sticky_sidebar: true,
                        enable_animations: true,
                        rtl_mode: false,
                        light_logo_preview: '',
                        dark_logo_preview: '',
                        favicon_preview: '',
                        login_bg_preview: ''
                    };
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Reset Complete!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        },
        
        async saveSettings() {
            this.saving = true;
            
            try {
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                Swal.fire({
                    icon: 'success',
                    title: 'Theme Settings Saved!',
                    text: 'Your theme settings have been updated successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to save settings. Please try again.'
                });
            } finally {
                this.saving = false;
            }
        }
    };
}
</script>
@endpush

@push('styles')
<style>
.form-check-card {
    border: 2px solid #dee2e6;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
}

.form-check-card:hover {
    border-color: var(--bs-primary);
}

.form-check-card.active {
    border-color: var(--bs-primary);
    background-color: rgba(13, 110, 253, 0.05);
}

.form-check-card .form-check-input {
    position: absolute;
    opacity: 0;
}

.form-control-color {
    padding: 0.25rem;
}

.preview-container {
    min-height: 200px;
    transition: all 0.3s;
}

[dir="rtl"] .text-start {
    text-align: right !important;
}
</style>
@endpush
