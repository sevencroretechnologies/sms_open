{{-- Student Edit View --}}
{{-- Prompt 144: Student edit form with pre-filled data and validation --}}

@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div x-data="studentEditWizard()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Student</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('students.show', $student->id ?? 0) }}">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('students.show', $student->id ?? 0) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Profile
            </a>
            <button type="button" class="btn btn-outline-danger" @click="confirmDelete()">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </div>
    </div>

    <!-- Student Profile Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <img 
                    src="{{ $student->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) . '&background=4f46e5&color=fff&size=80' }}"
                    alt="{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}"
                    class="rounded-circle me-3"
                    style="width: 80px; height: 80px; object-fit: cover;"
                >
                <div>
                    <h4 class="mb-1">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</h4>
                    <p class="text-muted mb-0">
                        <span class="badge bg-light text-dark me-2">{{ $student->admission_number ?? 'N/A' }}</span>
                        <span class="badge bg-primary">{{ $student->class->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between position-relative">
                <div class="position-absolute" style="top: 20px; left: 5%; right: 5%; height: 2px; background: #e5e7eb; z-index: 0;">
                    <div class="h-100 bg-primary transition-all" :style="{ width: ((currentStep - 1) / 7 * 100) + '%' }"></div>
                </div>
                
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
    <form action="{{ route('students.update', $student->id ?? 0) }}" method="POST" enctype="multipart/form-data" @submit.prevent="submitForm">
        @csrf
        @method('PUT')
        
        <!-- Step 1: Personal Information -->
        <div x-show="currentStep === 1" x-transition>
            <x-card title="Personal Information" icon="bi-person">
                <div class="row g-3">
                    <div class="col-md-4">
                        <x-form-input 
                            name="first_name" 
                            label="First Name" 
                            placeholder="Enter first name"
                            :value="$student->first_name ?? ''"
                            required 
                            x-model="formData.first_name"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="middle_name" 
                            label="Middle Name" 
                            placeholder="Enter middle name"
                            :value="$student->middle_name ?? ''"
                            x-model="formData.middle_name"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="last_name" 
                            label="Last Name" 
                            placeholder="Enter last name"
                            :value="$student->last_name ?? ''"
                            required 
                            x-model="formData.last_name"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-datepicker 
                            name="date_of_birth" 
                            label="Date of Birth" 
                            :value="$student->date_of_birth ?? ''"
                            required 
                            x-model="formData.date_of_birth"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-select 
                            name="gender" 
                            label="Gender" 
                            :options="['male' => 'Male', 'female' => 'Female', 'other' => 'Other']"
                            :selected="$student->gender ?? ''"
                            required 
                            x-model="formData.gender"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-select 
                            name="blood_group" 
                            label="Blood Group" 
                            :options="['A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+', 'O-' => 'O-']"
                            :selected="$student->blood_group ?? ''"
                            placeholder="Select blood group"
                            x-model="formData.blood_group"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="religion" 
                            label="Religion" 
                            placeholder="Enter religion"
                            :value="$student->religion ?? ''"
                            x-model="formData.religion"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="caste" 
                            label="Caste" 
                            placeholder="Enter caste"
                            :value="$student->caste ?? ''"
                            x-model="formData.caste"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="nationality" 
                            label="Nationality" 
                            placeholder="Enter nationality"
                            :value="$student->nationality ?? 'Indian'"
                            x-model="formData.nationality"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="mother_tongue" 
                            label="Mother Tongue" 
                            placeholder="Enter mother tongue"
                            :value="$student->mother_tongue ?? ''"
                            x-model="formData.mother_tongue"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="mobile" 
                            label="Mobile Number" 
                            type="tel"
                            placeholder="Enter mobile number"
                            :value="$student->mobile ?? ''"
                            x-model="formData.mobile"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="email" 
                            label="Email Address" 
                            type="email"
                            placeholder="Enter email address"
                            :value="$student->email ?? ''"
                            x-model="formData.email"
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Photo</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 80px; height: 80px;">
                                <img 
                                    x-show="photoPreview" 
                                    :src="photoPreview" 
                                    class="w-100 h-100 object-fit-cover"
                                >
                                <img 
                                    x-show="!photoPreview && currentPhoto" 
                                    :src="currentPhoto" 
                                    class="w-100 h-100 object-fit-cover"
                                >
                                <i x-show="!photoPreview && !currentPhoto" class="bi bi-person fs-1 text-muted"></i>
                            </div>
                            <div>
                                <input type="file" name="photo" id="photo" class="form-control" accept="image/*" @change="previewPhoto($event)">
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
                            :selected="$student->academic_session_id ?? ''"
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
                            :value="$student->admission_number ?? ''"
                            readonly
                            x-model="formData.admission_number"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-input 
                            name="roll_number" 
                            label="Roll Number" 
                            placeholder="Enter roll number"
                            :value="$student->roll_number ?? ''"
                            x-model="formData.roll_number"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-select 
                            name="class_id" 
                            label="Class" 
                            :options="$classes ?? []"
                            :selected="$student->class_id ?? ''"
                            optionValue="id"
                            optionLabel="name"
                            required 
                            x-model="formData.class_id"
                            @change="loadSections()"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Section <span class="text-danger">*</span></label>
                        <select name="section_id" class="form-select" required x-model="formData.section_id">
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
                            :selected="$student->category_id ?? ''"
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
                            :value="$student->admission_date ?? ''"
                            required 
                            x-model="formData.admission_date"
                        />
                    </div>
                    <div class="col-md-4">
                        <x-form-select 
                            name="status" 
                            label="Status" 
                            :options="['active' => 'Active', 'inactive' => 'Inactive', 'left' => 'Left', 'passed_out' => 'Passed Out']"
                            :selected="$student->status ?? 'active'"
                            x-model="formData.status"
                        />
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mt-4 pt-2">
                            <input type="checkbox" class="form-check-input" id="is_rte" name="is_rte" x-model="formData.is_rte">
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
                            :value="$student->previous_school ?? ''"
                            x-model="formData.previous_school"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form-input 
                            name="tc_number" 
                            label="Transfer Certificate Number" 
                            placeholder="Enter TC number"
                            :value="$student->tc_number ?? ''"
                            x-model="formData.tc_number"
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
                        <x-form-input name="father_name" label="Father's Name" :value="$student->father_name ?? ''" required x-model="formData.father_name" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="father_phone" label="Father's Phone" type="tel" :value="$student->father_phone ?? ''" required x-model="formData.father_phone" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="father_occupation" label="Father's Occupation" :value="$student->father_occupation ?? ''" x-model="formData.father_occupation" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="father_email" label="Father's Email" type="email" :value="$student->father_email ?? ''" x-model="formData.father_email" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="father_qualification" label="Father's Qualification" :value="$student->father_qualification ?? ''" x-model="formData.father_qualification" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="father_annual_income" label="Father's Annual Income" type="number" :value="$student->father_annual_income ?? ''" x-model="formData.father_annual_income" />
                    </div>

                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Mother's Information</h6>
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="mother_name" label="Mother's Name" :value="$student->mother_name ?? ''" required x-model="formData.mother_name" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="mother_phone" label="Mother's Phone" type="tel" :value="$student->mother_phone ?? ''" required x-model="formData.mother_phone" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="mother_occupation" label="Mother's Occupation" :value="$student->mother_occupation ?? ''" x-model="formData.mother_occupation" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="mother_email" label="Mother's Email" type="email" :value="$student->mother_email ?? ''" x-model="formData.mother_email" />
                    </div>

                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Guardian Information</h6>
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="guardian_name" label="Guardian's Name" :value="$student->guardian_name ?? ''" x-model="formData.guardian_name" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="guardian_phone" label="Guardian's Phone" type="tel" :value="$student->guardian_phone ?? ''" x-model="formData.guardian_phone" />
                    </div>
                    <div class="col-md-4">
                        <x-form-select 
                            name="guardian_relation" 
                            label="Relation with Guardian" 
                            :options="['uncle' => 'Uncle', 'aunt' => 'Aunt', 'grandparent' => 'Grandparent', 'sibling' => 'Sibling', 'other' => 'Other']"
                            :selected="$student->guardian_relation ?? ''"
                            placeholder="Select relation"
                            x-model="formData.guardian_relation"
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
                        <x-form-input name="address" label="Address" :value="$student->address ?? ''" required x-model="formData.address" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="city" label="City" :value="$student->city ?? ''" required x-model="formData.city" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="state" label="State" :value="$student->state ?? ''" required x-model="formData.state" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="country" label="Country" :value="$student->country ?? 'India'" x-model="formData.country" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="postal_code" label="Postal Code" :value="$student->postal_code ?? ''" required x-model="formData.postal_code" />
                    </div>

                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-3"><i class="bi bi-house-door me-2"></i>Permanent Address</h6>
                    </div>
                    <div class="col-md-12">
                        <x-form-input name="permanent_address" label="Address" :value="$student->permanent_address ?? ''" x-model="formData.permanent_address" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="permanent_city" label="City" :value="$student->permanent_city ?? ''" x-model="formData.permanent_city" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="permanent_state" label="State" :value="$student->permanent_state ?? ''" x-model="formData.permanent_state" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="permanent_postal_code" label="Postal Code" :value="$student->permanent_postal_code ?? ''" x-model="formData.permanent_postal_code" />
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Step 5: Emergency Contact -->
        <div x-show="currentStep === 5" x-transition>
            <x-card title="Emergency Contact" icon="bi-telephone">
                <div class="row g-3">
                    <div class="col-md-4">
                        <x-form-input name="emergency_contact_name" label="Contact Person Name" :value="$student->emergency_contact_name ?? ''" required x-model="formData.emergency_contact_name" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input name="emergency_contact_phone" label="Contact Phone" type="tel" :value="$student->emergency_contact_phone ?? ''" required x-model="formData.emergency_contact_phone" />
                    </div>
                    <div class="col-md-4">
                        <x-form-select 
                            name="emergency_contact_relation" 
                            label="Relation" 
                            :options="['father' => 'Father', 'mother' => 'Mother', 'uncle' => 'Uncle', 'aunt' => 'Aunt', 'grandparent' => 'Grandparent', 'sibling' => 'Sibling', 'neighbor' => 'Neighbor', 'other' => 'Other']"
                            :selected="$student->emergency_contact_relation ?? ''"
                            required 
                            x-model="formData.emergency_contact_relation"
                        />
                    </div>

                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-3"><i class="bi bi-heart-pulse me-2"></i>Medical Information</h6>
                    </div>
                    <div class="col-md-6">
                        <x-form-input name="medical_conditions" label="Medical Conditions" :value="$student->medical_conditions ?? ''" x-model="formData.medical_conditions" />
                    </div>
                    <div class="col-md-6">
                        <x-form-input name="allergies" label="Allergies" :value="$student->allergies ?? ''" x-model="formData.allergies" />
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Step 6: Documents -->
        <div x-show="currentStep === 6" x-transition>
            <x-card title="Documents" icon="bi-file-earmark">
                <div class="row g-3">
                    @if(isset($student->documents) && count($student->documents) > 0)
                    <div class="col-12 mb-3">
                        <h6 class="text-muted">Existing Documents</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>File Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->documents as $doc)
                                    <tr>
                                        <td>{{ $doc->document_type ?? 'Unknown' }}</td>
                                        <td>{{ $doc->file_name ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ $doc->file_path ?? '#' }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-12">
                        <h6 class="text-muted">Upload New Documents</h6>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Birth Certificate</label>
                        <input type="file" name="birth_certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Transfer Certificate</label>
                        <input type="file" name="transfer_certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Aadhar Card</label>
                        <input type="file" name="aadhar_card" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Other Documents</label>
                        <input type="file" name="other_documents[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" multiple>
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
                            <input type="checkbox" class="form-check-input" id="transport_required" x-model="formData.transport_required">
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
                                        :selected="$student->transportStudent->route_id ?? ''"
                                        optionValue="id"
                                        optionLabel="name"
                                        placeholder="Select route"
                                        x-model="formData.transport_route_id"
                                    />
                                </div>
                                <div class="col-md-4">
                                    <x-form-select 
                                        name="transport_vehicle_id" 
                                        label="Vehicle" 
                                        :options="$vehicles ?? []"
                                        :selected="$student->transportStudent->vehicle_id ?? ''"
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
                            <input type="checkbox" class="form-check-input" id="hostel_required" x-model="formData.hostel_required">
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
                                        :selected="$student->hostelAssignment->hostel_id ?? ''"
                                        optionValue="id"
                                        optionLabel="name"
                                        placeholder="Select hostel"
                                        x-model="formData.hostel_id"
                                    />
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
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-primary mb-3"><i class="bi bi-person me-2"></i>Personal Information</h6>
                            <div class="row g-2">
                                <div class="col-6"><small class="text-muted">Name:</small></div>
                                <div class="col-6"><small x-text="formData.first_name + ' ' + formData.last_name"></small></div>
                                <div class="col-6"><small class="text-muted">Date of Birth:</small></div>
                                <div class="col-6"><small x-text="formData.date_of_birth || '-'"></small></div>
                                <div class="col-6"><small class="text-muted">Gender:</small></div>
                                <div class="col-6"><small x-text="formData.gender || '-'"></small></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6 class="text-primary mb-3"><i class="bi bi-mortarboard me-2"></i>Academic Information</h6>
                            <div class="row g-2">
                                <div class="col-6"><small class="text-muted">Admission No:</small></div>
                                <div class="col-6"><small x-text="formData.admission_number || '-'"></small></div>
                                <div class="col-6"><small class="text-muted">Status:</small></div>
                                <div class="col-6"><small x-text="formData.status || 'Active'"></small></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning mt-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Please review all changes before updating. Changes will be saved immediately.
                </div>
            </x-card>
        </div>

        <!-- Navigation Buttons -->
        <div class="d-flex justify-content-between mt-4">
            <div>
                <button type="button" class="btn btn-outline-secondary" x-show="currentStep > 1" @click="previousStep()">
                    <i class="bi bi-arrow-left me-1"></i> Previous
                </button>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('students.show', $student->id ?? 0) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="button" class="btn btn-primary" x-show="currentStep < 8" @click="nextStep()">
                    Next <i class="bi bi-arrow-right ms-1"></i>
                </button>
                <button type="submit" class="btn btn-success" x-show="currentStep === 8" :disabled="submitting">
                    <span x-show="!submitting"><i class="bi bi-check-lg me-1"></i> Update Student</span>
                    <span x-show="submitting"><span class="spinner-border spinner-border-sm me-1"></span> Updating...</span>
                </button>
            </div>
        </div>
    </form>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" x-ref="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this student?</p>
                    <p class="fw-bold">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }} ({{ $student->admission_number ?? '' }})</p>
                    <p class="text-danger small mb-0"><i class="bi bi-exclamation-circle me-1"></i>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('students.destroy', $student->id ?? 0) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function studentEditWizard() {
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
        
        formData: @json($student ?? new stdClass()),
        sections: @json($sections ?? []),
        classes: @json($classes ?? []),
        
        photoPreview: null,
        currentPhoto: '{{ $student->photo ?? "" }}',
        submitting: false,
        
        nextStep() {
            if (this.currentStep < 8) {
                this.currentStep++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        
        previousStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        
        previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => { this.photoPreview = e.target.result; };
                reader.readAsDataURL(file);
            }
        },
        
        loadSections() {
            if (!this.formData.class_id) {
                this.sections = [];
                return;
            }
            fetch(`/api/classes/${this.formData.class_id}/sections`)
                .then(res => res.json())
                .then(data => { this.sections = data; })
                .catch(() => { this.sections = []; });
        },
        
        confirmDelete() {
            const modal = new bootstrap.Modal(this.$refs.deleteModal);
            modal.show();
        },
        
        async submitForm() {
            this.submitting = true;
            try {
                const form = document.querySelector('form');
                const formData = new FormData(form);
                
                Object.keys(this.formData).forEach(key => {
                    if (this.formData[key] !== '' && this.formData[key] !== null) {
                        formData.set(key, this.formData[key]);
                    }
                });
                
                const response = await fetch('{{ route("students.update", $student->id ?? 0) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Student has been updated successfully.',
                        confirmButtonText: 'View Profile'
                    }).then(() => {
                        window.location.href = '{{ route("students.show", $student->id ?? 0) }}';
                    });
                } else {
                    throw new Error('Failed to update student');
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update student. Please try again.' });
            } finally {
                this.submitting = false;
            }
        },
        
        init() {
            if (this.formData.class_id) {
                this.loadSections();
            }
        }
    };
}
</script>
@endpush
@endsection
