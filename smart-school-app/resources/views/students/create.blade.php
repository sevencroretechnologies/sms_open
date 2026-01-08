{{-- Student Create View --}}
{{-- Prompt 143: Student admission form with multi-step wizard --}}

@extends('layouts.app')

@section('title', 'Add New Student')

@section('content')
<div x-data="studentCreateWizard()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Add New Student</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between position-relative">
                <!-- Progress Line -->
                <div class="position-absolute" style="top: 20px; left: 5%; right: 5%; height: 2px; background: #e5e7eb; z-index: 0;">
                    <div 
                        class="h-100 bg-primary transition-all" 
                        :style="{ width: ((currentStep - 1) / 7 * 100) + '%' }"
                    ></div>
                </div>
                
                <!-- Steps -->
                <template x-for="(step, index) in steps" :key="index">
                    <div class="text-center position-relative" style="z-index: 1;">
                        <div 
                            class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2"
                            :class="{
                                'bg-primary text-white': currentStep > index + 1 || currentStep === index + 1,
                                'bg-light text-muted border': currentStep < index + 1
                            }"
                            style="width: 40px; height: 40px;"
                        >
                            <i class="bi" :class="currentStep > index + 1 ? 'bi-check' : step.icon"></i>
                        </div>
                        <small 
                            class="d-none d-md-block"
                            :class="{ 'text-primary fw-medium': currentStep === index + 1, 'text-muted': currentStep !== index + 1 }"
                            x-text="step.title"
                        ></small>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" @submit.prevent="submitForm">
        @csrf
        
        <!-- Step 1: Personal Information -->
        <div x-show="currentStep === 1" x-transition>
            <x-card title="Personal Information" icon="bi-person">
                <div class="row g-3">
                    <div class="col-md-4">
                        <x-form-input 
                            name="first_name" 
                            label="First Name" 
                            placeholder="Enter first name"
                            required 
                            x-model="formData.first_name"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="middle_name" 
                            label="Middle Name" 
                            placeholder="Enter middle name"
                            x-model="formData.middle_name"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="last_name" 
                            label="Last Name" 
                            placeholder="Enter last name"
                            required 
                            x-model="formData.last_name"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-datepicker 
                            name="date_of_birth" 
                            label="Date of Birth" 
                            required 
                            x-model="formData.date_of_birth"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-select 
                            name="gender" 
                            label="Gender" 
                            :options="['male' => 'Male', 'female' => 'Female', 'other' => 'Other']"
                            required 
                            x-model="formData.gender"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-select 
                            name="blood_group" 
                            label="Blood Group" 
                            :options="['A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+', 'O-' => 'O-']"
                            placeholder="Select blood group"
                            x-model="formData.blood_group"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="religion" 
                            label="Religion" 
                            placeholder="Enter religion"
                            x-model="formData.religion"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="caste" 
                            label="Caste" 
                            placeholder="Enter caste"
                            x-model="formData.caste"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="nationality" 
                            label="Nationality" 
                            placeholder="Enter nationality"
                            value="Indian"
                            x-model="formData.nationality"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="mother_tongue" 
                            label="Mother Tongue" 
                            placeholder="Enter mother tongue"
                            x-model="formData.mother_tongue"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="mobile" 
                            label="Mobile Number" 
                            type="tel"
                            placeholder="Enter mobile number"
                            x-model="formData.mobile"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="email" 
                            label="Email Address" 
                            type="email"
                            placeholder="Enter email address"
                            x-model="formData.email"
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Photo</label>
                        <div class="d-flex align-items-center gap-3">
                            <div 
                                class="rounded-circle bg-light d-flex align-items-center justify-content-center overflow-hidden"
                                style="width: 80px; height: 80px;"
                            >
                                <img 
                                    x-show="photoPreview" 
                                    :src="photoPreview" 
                                    class="w-100 h-100 object-fit-cover"
                                >
                                <i x-show="!photoPreview" class="bi bi-person fs-1 text-muted"></i>
                            </div>
                            <div>
                                <input 
                                    type="file" 
                                    name="photo" 
                                    id="photo" 
                                    class="form-control"
                                    accept="image/*"
                                    @change="previewPhoto($event)"
                                >
                                <small class="text-muted">Max size: 2MB. Formats: JPG, PNG</small>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Step 2: Academic Information -->
        <div x-show="currentStep === 2" x-transition>
            <x-card title="Academic Information" icon="bi-mortarboard">
                <div class="row g-3">
                    <div class="col-md-4">
                        <x-form-select 
                            name="academic_session_id" 
                            label="Academic Session" 
                            :options="$academicSessions ?? []"
                            optionValue="id"
                            optionLabel="name"
                            required 
                            x-model="formData.academic_session_id"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="admission_number" 
                            label="Admission Number" 
                            placeholder="Auto-generated"
                            :value="$nextAdmissionNumber ?? ''"
                            helpText="Leave blank for auto-generation"
                            x-model="formData.admission_number"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="roll_number" 
                            label="Roll Number" 
                            placeholder="Enter roll number"
                            x-model="formData.roll_number"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-select 
                            name="class_id" 
                            label="Class" 
                            :options="$classes ?? []"
                            optionValue="id"
                            optionLabel="name"
                            required 
                            x-model="formData.class_id"
                            @change="loadSections()"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Section <span class="text-danger">*</span></label>
                        <select 
                            name="section_id" 
                            class="form-select" 
                            required
                            x-model="formData.section_id"
                        >
                            <option value="">Select Section</option>
                            <template x-for="section in sections" :key="section.id">
                                <option :value="section.id" x-text="section.name"></option>
                            </template>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <x-form-select 
                            name="category_id" 
                            label="Category" 
                            :options="$categories ?? []"
                            optionValue="id"
                            optionLabel="name"
                            placeholder="Select category"
                            x-model="formData.category_id"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-datepicker 
                            name="admission_date" 
                            label="Date of Admission" 
                            required 
                            :value="date('Y-m-d')"
                            x-model="formData.admission_date"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-select 
                            name="admission_type" 
                            label="Admission Type" 
                            :options="['new' => 'New Admission', 'transfer' => 'Transfer', 'rte' => 'RTE']"
                            x-model="formData.admission_type"
                        />
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mt-4 pt-2">
                            <input 
                                type="checkbox" 
                                class="form-check-input" 
                                id="is_rte" 
                                name="is_rte"
                                x-model="formData.is_rte"
                            >
                            <label class="form-check-label" for="is_rte">RTE Student</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <hr class="my-3">
                        <h6 class="text-muted mb-3">Previous School Information</h6>
                    </div>
                    <div class="col-md-6">
                        <x-form-input 
                            name="previous_school" 
                            label="Previous School Name" 
                            placeholder="Enter previous school name"
                            x-model="formData.previous_school"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form-input 
                            name="previous_class" 
                            label="Previous Class" 
                            placeholder="Enter previous class"
                            x-model="formData.previous_class"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form-input 
                            name="tc_number" 
                            label="Transfer Certificate Number" 
                            placeholder="Enter TC number"
                            x-model="formData.tc_number"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form-datepicker 
                            name="tc_date" 
                            label="TC Date" 
                            x-model="formData.tc_date"
                        />
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Step 3: Family Information -->
        <div x-show="currentStep === 3" x-transition>
            <x-card title="Family Information" icon="bi-people">
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Father's Information</h6>
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="father_name" 
                            label="Father's Name" 
                            placeholder="Enter father's name"
                            required 
                            x-model="formData.father_name"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="father_phone" 
                            label="Father's Phone" 
                            type="tel"
                            placeholder="Enter father's phone"
                            required 
                            x-model="formData.father_phone"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="father_occupation" 
                            label="Father's Occupation" 
                            placeholder="Enter father's occupation"
                            x-model="formData.father_occupation"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="father_email" 
                            label="Father's Email" 
                            type="email"
                            placeholder="Enter father's email"
                            x-model="formData.father_email"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="father_qualification" 
                            label="Father's Qualification" 
                            placeholder="Enter qualification"
                            x-model="formData.father_qualification"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="father_annual_income" 
                            label="Father's Annual Income" 
                            type="number"
                            placeholder="Enter annual income"
                            x-model="formData.father_annual_income"
                        />
                    </div>

                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Mother's Information</h6>
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="mother_name" 
                            label="Mother's Name" 
                            placeholder="Enter mother's name"
                            required 
                            x-model="formData.mother_name"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="mother_phone" 
                            label="Mother's Phone" 
                            type="tel"
                            placeholder="Enter mother's phone"
                            required 
                            x-model="formData.mother_phone"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="mother_occupation" 
                            label="Mother's Occupation" 
                            placeholder="Enter mother's occupation"
                            x-model="formData.mother_occupation"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="mother_email" 
                            label="Mother's Email" 
                            type="email"
                            placeholder="Enter mother's email"
                            x-model="formData.mother_email"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="mother_qualification" 
                            label="Mother's Qualification" 
                            placeholder="Enter qualification"
                            x-model="formData.mother_qualification"
                        />
                    </div>

                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Guardian Information (if different from parents)</h6>
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="guardian_name" 
                            label="Guardian's Name" 
                            placeholder="Enter guardian's name"
                            x-model="formData.guardian_name"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="guardian_phone" 
                            label="Guardian's Phone" 
                            type="tel"
                            placeholder="Enter guardian's phone"
                            x-model="formData.guardian_phone"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-select 
                            name="guardian_relation" 
                            label="Relation with Guardian" 
                            :options="['uncle' => 'Uncle', 'aunt' => 'Aunt', 'grandparent' => 'Grandparent', 'sibling' => 'Sibling', 'other' => 'Other']"
                            placeholder="Select relation"
                            x-model="formData.guardian_relation"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="guardian_email" 
                            label="Guardian's Email" 
                            type="email"
                            placeholder="Enter guardian's email"
                            x-model="formData.guardian_email"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="guardian_occupation" 
                            label="Guardian's Occupation" 
                            placeholder="Enter guardian's occupation"
                            x-model="formData.guardian_occupation"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="guardian_address" 
                            label="Guardian's Address" 
                            placeholder="Enter guardian's address"
                            x-model="formData.guardian_address"
                        />
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Step 4: Address Information -->
        <div x-show="currentStep === 4" x-transition>
            <x-card title="Address Information" icon="bi-geo-alt">
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="text-primary mb-3"><i class="bi bi-house me-2"></i>Current Address</h6>
                    </div>
                    <div class="col-md-12">
                        <x-form-input 
                            name="address" 
                            label="Address" 
                            placeholder="Enter full address"
                            required 
                            x-model="formData.address"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="city" 
                            label="City" 
                            placeholder="Enter city"
                            required 
                            x-model="formData.city"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="state" 
                            label="State" 
                            placeholder="Enter state"
                            required 
                            x-model="formData.state"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="country" 
                            label="Country" 
                            placeholder="Enter country"
                            value="India"
                            x-model="formData.country"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="postal_code" 
                            label="Postal Code" 
                            placeholder="Enter postal code"
                            required 
                            x-model="formData.postal_code"
                        />
                    </div>

                    <div class="col-12 mt-4">
                        <div class="form-check mb-3">
                            <input 
                                type="checkbox" 
                                class="form-check-input" 
                                id="same_as_current"
                                x-model="sameAsCurrentAddress"
                                @change="copyCurrentAddress()"
                            >
                            <label class="form-check-label" for="same_as_current">Permanent address same as current address</label>
                        </div>
                        <h6 class="text-primary mb-3"><i class="bi bi-house-door me-2"></i>Permanent Address</h6>
                    </div>
                    <div class="col-md-12">
                        <x-form-input 
                            name="permanent_address" 
                            label="Address" 
                            placeholder="Enter permanent address"
                            x-model="formData.permanent_address"
                            :disabled="sameAsCurrentAddress"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="permanent_city" 
                            label="City" 
                            placeholder="Enter city"
                            x-model="formData.permanent_city"
                            :disabled="sameAsCurrentAddress"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="permanent_state" 
                            label="State" 
                            placeholder="Enter state"
                            x-model="formData.permanent_state"
                            :disabled="sameAsCurrentAddress"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="permanent_postal_code" 
                            label="Postal Code" 
                            placeholder="Enter postal code"
                            x-model="formData.permanent_postal_code"
                            :disabled="sameAsCurrentAddress"
                        />
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Step 5: Emergency Contact -->
        <div x-show="currentStep === 5" x-transition>
            <x-card title="Emergency Contact" icon="bi-telephone">
                <div class="row g-3">
                    <div class="col-md-4">
                        <x-form-input 
                            name="emergency_contact_name" 
                            label="Contact Person Name" 
                            placeholder="Enter contact person name"
                            required 
                            x-model="formData.emergency_contact_name"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="emergency_contact_phone" 
                            label="Contact Phone" 
                            type="tel"
                            placeholder="Enter contact phone"
                            required 
                            x-model="formData.emergency_contact_phone"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-select 
                            name="emergency_contact_relation" 
                            label="Relation" 
                            :options="['father' => 'Father', 'mother' => 'Mother', 'uncle' => 'Uncle', 'aunt' => 'Aunt', 'grandparent' => 'Grandparent', 'sibling' => 'Sibling', 'neighbor' => 'Neighbor', 'other' => 'Other']"
                            required 
                            x-model="formData.emergency_contact_relation"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form-input 
                            name="emergency_contact_address" 
                            label="Contact Address" 
                            placeholder="Enter contact address"
                            x-model="formData.emergency_contact_address"
                        />
                    </div>

                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-3"><i class="bi bi-heart-pulse me-2"></i>Medical Information</h6>
                    </div>
                    <div class="col-md-6">
                        <x-form-input 
                            name="medical_conditions" 
                            label="Medical Conditions (if any)" 
                            placeholder="Enter any medical conditions"
                            x-model="formData.medical_conditions"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form-input 
                            name="allergies" 
                            label="Allergies (if any)" 
                            placeholder="Enter any allergies"
                            x-model="formData.allergies"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form-input 
                            name="medications" 
                            label="Current Medications (if any)" 
                            placeholder="Enter current medications"
                            x-model="formData.medications"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form-input 
                            name="doctor_name" 
                            label="Family Doctor Name" 
                            placeholder="Enter doctor name"
                            x-model="formData.doctor_name"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form-input 
                            name="doctor_phone" 
                            label="Doctor's Phone" 
                            type="tel"
                            placeholder="Enter doctor's phone"
                            x-model="formData.doctor_phone"
                        />
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Step 6: Documents -->
        <div x-show="currentStep === 6" x-transition>
            <x-card title="Documents" icon="bi-file-earmark">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Birth Certificate</label>
                        <input 
                            type="file" 
                            name="birth_certificate" 
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >
                        <small class="text-muted">Formats: PDF, JPG, PNG. Max: 5MB</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Transfer Certificate</label>
                        <input 
                            type="file" 
                            name="transfer_certificate" 
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >
                        <small class="text-muted">Formats: PDF, JPG, PNG. Max: 5MB</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Aadhar Card</label>
                        <input 
                            type="file" 
                            name="aadhar_card" 
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >
                        <small class="text-muted">Formats: PDF, JPG, PNG. Max: 5MB</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Previous Marksheet</label>
                        <input 
                            type="file" 
                            name="previous_marksheet" 
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >
                        <small class="text-muted">Formats: PDF, JPG, PNG. Max: 5MB</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Caste Certificate</label>
                        <input 
                            type="file" 
                            name="caste_certificate" 
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >
                        <small class="text-muted">Formats: PDF, JPG, PNG. Max: 5MB</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Income Certificate</label>
                        <input 
                            type="file" 
                            name="income_certificate" 
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >
                        <small class="text-muted">Formats: PDF, JPG, PNG. Max: 5MB</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Other Documents</label>
                        <input 
                            type="file" 
                            name="other_documents[]" 
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png"
                            multiple
                        >
                        <small class="text-muted">You can select multiple files. Formats: PDF, JPG, PNG. Max: 5MB each</small>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Step 7: Transport & Hostel -->
        <div x-show="currentStep === 7" x-transition>
            <x-card title="Transport & Hostel" icon="bi-bus-front">
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="text-primary mb-3"><i class="bi bi-bus-front me-2"></i>Transport Information</h6>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input 
                                type="checkbox" 
                                class="form-check-input" 
                                id="transport_required"
                                x-model="formData.transport_required"
                            >
                            <label class="form-check-label" for="transport_required">Transport Required</label>
                        </div>
                    </div>
                    <template x-if="formData.transport_required">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <x-form-select 
                                        name="transport_route_id" 
                                        label="Route" 
                                        :options="$routes ?? []"
                                        optionValue="id"
                                        optionLabel="name"
                                        placeholder="Select route"
                                        x-model="formData.transport_route_id"
                                        @change="loadStops()"
                                    />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Stop</label>
                                    <select 
                                        name="transport_stop_id" 
                                        class="form-select"
                                        x-model="formData.transport_stop_id"
                                    >
                                        <option value="">Select Stop</option>
                                        <template x-for="stop in stops" :key="stop.id">
                                            <option :value="stop.id" x-text="stop.name + ' - Rs. ' + stop.fare"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <x-form-select 
                                        name="transport_vehicle_id" 
                                        label="Vehicle" 
                                        :options="$vehicles ?? []"
                                        optionValue="id"
                                        optionLabel="vehicle_number"
                                        placeholder="Select vehicle"
                                        x-model="formData.transport_vehicle_id"
                                    />
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-3"><i class="bi bi-building me-2"></i>Hostel Information</h6>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input 
                                type="checkbox" 
                                class="form-check-input" 
                                id="hostel_required"
                                x-model="formData.hostel_required"
                            >
                            <label class="form-check-label" for="hostel_required">Hostel Required</label>
                        </div>
                    </div>
                    <template x-if="formData.hostel_required">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <x-form-select 
                                        name="hostel_id" 
                                        label="Hostel" 
                                        :options="$hostels ?? []"
                                        optionValue="id"
                                        optionLabel="name"
                                        placeholder="Select hostel"
                                        x-model="formData.hostel_id"
                                        @change="loadRoomTypes()"
                                    />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Room Type</label>
                                    <select 
                                        name="hostel_room_type_id" 
                                        class="form-select"
                                        x-model="formData.hostel_room_type_id"
                                        @change="loadRooms()"
                                    >
                                        <option value="">Select Room Type</option>
                                        <template x-for="type in roomTypes" :key="type.id">
                                            <option :value="type.id" x-text="type.name + ' - Rs. ' + type.fee"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Room</label>
                                    <select 
                                        name="hostel_room_id" 
                                        class="form-select"
                                        x-model="formData.hostel_room_id"
                                    >
                                        <option value="">Select Room</option>
                                        <template x-for="room in rooms" :key="room.id">
                                            <option :value="room.id" x-text="room.room_number + ' (Available: ' + room.available_beds + ')'"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </x-card>
        </div>

        <!-- Step 8: Review & Submit -->
        <div x-show="currentStep === 8" x-transition>
            <x-card title="Review & Submit" icon="bi-check-circle">
                <div class="row g-4">
                    <!-- Personal Info Summary -->
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-primary mb-3"><i class="bi bi-person me-2"></i>Personal Information</h6>
                            <div class="row g-2">
                                <div class="col-6"><small class="text-muted">Name:</small></div>
                                <div class="col-6"><small x-text="formData.first_name + ' ' + (formData.middle_name || '') + ' ' + formData.last_name"></small></div>
                                <div class="col-6"><small class="text-muted">Date of Birth:</small></div>
                                <div class="col-6"><small x-text="formData.date_of_birth || '-'"></small></div>
                                <div class="col-6"><small class="text-muted">Gender:</small></div>
                                <div class="col-6"><small x-text="formData.gender || '-'"></small></div>
                                <div class="col-6"><small class="text-muted">Mobile:</small></div>
                                <div class="col-6"><small x-text="formData.mobile || '-'"></small></div>
                                <div class="col-6"><small class="text-muted">Email:</small></div>
                                <div class="col-6"><small x-text="formData.email || '-'"></small></div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Info Summary -->
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-primary mb-3"><i class="bi bi-mortarboard me-2"></i>Academic Information</h6>
                            <div class="row g-2">
                                <div class="col-6"><small class="text-muted">Admission No:</small></div>
                                <div class="col-6"><small x-text="formData.admission_number || 'Auto-generated'"></small></div>
                                <div class="col-6"><small class="text-muted">Roll No:</small></div>
                                <div class="col-6"><small x-text="formData.roll_number || '-'"></small></div>
                                <div class="col-6"><small class="text-muted">Class:</small></div>
                                <div class="col-6"><small x-text="getClassName() || '-'"></small></div>
                                <div class="col-6"><small class="text-muted">Section:</small></div>
                                <div class="col-6"><small x-text="getSectionName() || '-'"></small></div>
                                <div class="col-6"><small class="text-muted">Admission Date:</small></div>
                                <div class="col-6"><small x-text="formData.admission_date || '-'"></small></div>
                            </div>
                        </div>
                    </div>

                    <!-- Family Info Summary -->
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-primary mb-3"><i class="bi bi-people me-2"></i>Family Information</h6>
                            <div class="row g-2">
                                <div class="col-6"><small class="text-muted">Father's Name:</small></div>
                                <div class="col-6"><small x-text="formData.father_name || '-'"></small></div>
                                <div class="col-6"><small class="text-muted">Father's Phone:</small></div>
                                <div class="col-6"><small x-text="formData.father_phone || '-'"></small></div>
                                <div class="col-6"><small class="text-muted">Mother's Name:</small></div>
                                <div class="col-6"><small x-text="formData.mother_name || '-'"></small></div>
                                <div class="col-6"><small class="text-muted">Mother's Phone:</small></div>
                                <div class="col-6"><small x-text="formData.mother_phone || '-'"></small></div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Summary -->
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-primary mb-3"><i class="bi bi-geo-alt me-2"></i>Address</h6>
                            <div class="row g-2">
                                <div class="col-12"><small x-text="formData.address || '-'"></small></div>
                                <div class="col-12"><small x-text="(formData.city || '') + ', ' + (formData.state || '') + ' - ' + (formData.postal_code || '')"></small></div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact Summary -->
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-primary mb-3"><i class="bi bi-telephone me-2"></i>Emergency Contact</h6>
                            <div class="row g-2">
                                <div class="col-6"><small class="text-muted">Name:</small></div>
                                <div class="col-6"><small x-text="formData.emergency_contact_name || '-'"></small></div>
                                <div class="col-6"><small class="text-muted">Phone:</small></div>
                                <div class="col-6"><small x-text="formData.emergency_contact_phone || '-'"></small></div>
                                <div class="col-6"><small class="text-muted">Relation:</small></div>
                                <div class="col-6"><small x-text="formData.emergency_contact_relation || '-'"></small></div>
                            </div>
                        </div>
                    </div>

                    <!-- Transport & Hostel Summary -->
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-primary mb-3"><i class="bi bi-bus-front me-2"></i>Transport & Hostel</h6>
                            <div class="row g-2">
                                <div class="col-6"><small class="text-muted">Transport:</small></div>
                                <div class="col-6"><small x-text="formData.transport_required ? 'Yes' : 'No'"></small></div>
                                <div class="col-6"><small class="text-muted">Hostel:</small></div>
                                <div class="col-6"><small x-text="formData.hostel_required ? 'Yes' : 'No'"></small></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <i class="bi bi-info-circle me-2"></i>
                    Please review all the information above before submitting. You can go back to any step to make changes.
                </div>
            </x-card>
        </div>

        <!-- Navigation Buttons -->
        <div class="d-flex justify-content-between mt-4">
            <div>
                <button 
                    type="button" 
                    class="btn btn-outline-secondary"
                    x-show="currentStep > 1"
                    @click="previousStep()"
                >
                    <i class="bi bi-arrow-left me-1"></i> Previous
                </button>
            </div>
            <div class="d-flex gap-2">
                <button 
                    type="button" 
                    class="btn btn-outline-primary"
                    @click="saveDraft()"
                    :disabled="saving"
                >
                    <span x-show="!saving">
                        <i class="bi bi-save me-1"></i> Save Draft
                    </span>
                    <span x-show="saving">
                        <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                    </span>
                </button>
                <button 
                    type="button" 
                    class="btn btn-primary"
                    x-show="currentStep < 8"
                    @click="nextStep()"
                >
                    Next <i class="bi bi-arrow-right ms-1"></i>
                </button>
                <button 
                    type="submit" 
                    class="btn btn-success"
                    x-show="currentStep === 8"
                    :disabled="submitting"
                >
                    <span x-show="!submitting">
                        <i class="bi bi-check-lg me-1"></i> Submit Admission
                    </span>
                    <span x-show="submitting">
                        <span class="spinner-border spinner-border-sm me-1"></span> Submitting...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    .transition-all {
        transition: all 0.3s ease;
    }
    
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
function studentCreateWizard() {
    return {
        currentStep: 1,
        steps: [
            { title: 'Personal', icon: 'bi-person' },
            { title: 'Academic', icon: 'bi-mortarboard' },
            { title: 'Family', icon: 'bi-people' },
            { title: 'Address', icon: 'bi-geo-alt' },
            { title: 'Emergency', icon: 'bi-telephone' },
            { title: 'Documents', icon: 'bi-file-earmark' },
            { title: 'Transport', icon: 'bi-bus-front' },
            { title: 'Review', icon: 'bi-check-circle' }
        ],
        
        formData: {
            // Personal
            first_name: '',
            middle_name: '',
            last_name: '',
            date_of_birth: '',
            gender: '',
            blood_group: '',
            religion: '',
            caste: '',
            nationality: 'Indian',
            mother_tongue: '',
            mobile: '',
            email: '',
            
            // Academic
            academic_session_id: '',
            admission_number: '',
            roll_number: '',
            class_id: '',
            section_id: '',
            category_id: '',
            admission_date: '{{ date("Y-m-d") }}',
            admission_type: 'new',
            is_rte: false,
            previous_school: '',
            previous_class: '',
            tc_number: '',
            tc_date: '',
            
            // Family
            father_name: '',
            father_phone: '',
            father_occupation: '',
            father_email: '',
            father_qualification: '',
            father_annual_income: '',
            mother_name: '',
            mother_phone: '',
            mother_occupation: '',
            mother_email: '',
            mother_qualification: '',
            guardian_name: '',
            guardian_phone: '',
            guardian_relation: '',
            guardian_email: '',
            guardian_occupation: '',
            guardian_address: '',
            
            // Address
            address: '',
            city: '',
            state: '',
            country: 'India',
            postal_code: '',
            permanent_address: '',
            permanent_city: '',
            permanent_state: '',
            permanent_postal_code: '',
            
            // Emergency
            emergency_contact_name: '',
            emergency_contact_phone: '',
            emergency_contact_relation: '',
            emergency_contact_address: '',
            medical_conditions: '',
            allergies: '',
            medications: '',
            doctor_name: '',
            doctor_phone: '',
            
            // Transport & Hostel
            transport_required: false,
            transport_route_id: '',
            transport_stop_id: '',
            transport_vehicle_id: '',
            hostel_required: false,
            hostel_id: '',
            hostel_room_type_id: '',
            hostel_room_id: ''
        },
        
        // Dynamic data
        sections: [],
        stops: [],
        roomTypes: [],
        rooms: [],
        classes: @json($classes ?? []),
        
        // UI state
        photoPreview: null,
        sameAsCurrentAddress: false,
        saving: false,
        submitting: false,
        
        // Methods
        nextStep() {
            if (this.validateCurrentStep()) {
                this.currentStep++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        
        previousStep() {
            this.currentStep--;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        
        validateCurrentStep() {
            // Basic validation for required fields per step
            const validations = {
                1: ['first_name', 'last_name', 'date_of_birth', 'gender'],
                2: ['academic_session_id', 'class_id', 'section_id', 'admission_date'],
                3: ['father_name', 'father_phone', 'mother_name', 'mother_phone'],
                4: ['address', 'city', 'state', 'postal_code'],
                5: ['emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation'],
                6: [],
                7: [],
                8: []
            };
            
            const requiredFields = validations[this.currentStep] || [];
            let isValid = true;
            
            for (const field of requiredFields) {
                if (!this.formData[field]) {
                    isValid = false;
                    break;
                }
            }
            
            if (!isValid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Required Fields',
                    text: 'Please fill in all required fields before proceeding.'
                });
            }
            
            return isValid;
        },
        
        previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.photoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        copyCurrentAddress() {
            if (this.sameAsCurrentAddress) {
                this.formData.permanent_address = this.formData.address;
                this.formData.permanent_city = this.formData.city;
                this.formData.permanent_state = this.formData.state;
                this.formData.permanent_postal_code = this.formData.postal_code;
            }
        },
        
        loadSections() {
            if (!this.formData.class_id) {
                this.sections = [];
                this.formData.section_id = '';
                return;
            }
            
            fetch(`/api/classes/${this.formData.class_id}/sections`)
                .then(res => res.json())
                .then(data => {
                    this.sections = data;
                })
                .catch(() => {
                    this.sections = [];
                });
        },
        
        loadStops() {
            if (!this.formData.transport_route_id) {
                this.stops = [];
                this.formData.transport_stop_id = '';
                return;
            }
            
            fetch(`/api/routes/${this.formData.transport_route_id}/stops`)
                .then(res => res.json())
                .then(data => {
                    this.stops = data;
                })
                .catch(() => {
                    this.stops = [];
                });
        },
        
        loadRoomTypes() {
            if (!this.formData.hostel_id) {
                this.roomTypes = [];
                this.formData.hostel_room_type_id = '';
                return;
            }
            
            fetch(`/api/hostels/${this.formData.hostel_id}/room-types`)
                .then(res => res.json())
                .then(data => {
                    this.roomTypes = data;
                })
                .catch(() => {
                    this.roomTypes = [];
                });
        },
        
        loadRooms() {
            if (!this.formData.hostel_room_type_id) {
                this.rooms = [];
                this.formData.hostel_room_id = '';
                return;
            }
            
            fetch(`/api/hostels/${this.formData.hostel_id}/rooms?type=${this.formData.hostel_room_type_id}`)
                .then(res => res.json())
                .then(data => {
                    this.rooms = data;
                })
                .catch(() => {
                    this.rooms = [];
                });
        },
        
        getClassName() {
            const cls = this.classes.find(c => c.id == this.formData.class_id);
            return cls ? cls.name : '';
        },
        
        getSectionName() {
            const section = this.sections.find(s => s.id == this.formData.section_id);
            return section ? section.name : '';
        },
        
        async saveDraft() {
            this.saving = true;
            
            try {
                // Save to localStorage for now
                localStorage.setItem('student_draft', JSON.stringify(this.formData));
                
                Swal.fire({
                    icon: 'success',
                    title: 'Draft Saved',
                    text: 'Your progress has been saved.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save draft. Please try again.'
                });
            } finally {
                this.saving = false;
            }
        },
        
        async submitForm() {
            this.submitting = true;
            
            try {
                const form = document.querySelector('form');
                const formData = new FormData(form);
                
                // Add all formData fields
                Object.keys(this.formData).forEach(key => {
                    if (this.formData[key] !== '' && this.formData[key] !== null) {
                        formData.set(key, this.formData[key]);
                    }
                });
                
                const response = await fetch('{{ route("students.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    // Clear draft
                    localStorage.removeItem('student_draft');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Student has been admitted successfully.',
                        confirmButtonText: 'View Student'
                    }).then(() => {
                        window.location.href = result.redirect || '{{ route("students.index") }}';
                    });
                } else {
                    throw new Error(result.message || 'Failed to submit admission');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to submit admission. Please try again.'
                });
            } finally {
                this.submitting = false;
            }
        },
        
        init() {
            // Load draft if exists
            const draft = localStorage.getItem('student_draft');
            if (draft) {
                const savedData = JSON.parse(draft);
                this.formData = { ...this.formData, ...savedData };
                
                // Load dependent data
                if (this.formData.class_id) {
                    this.loadSections();
                }
            }
        }
    };
}
</script>
@endpush
@endsection
