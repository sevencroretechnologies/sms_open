@extends('layouts.app')

@section('title', 'Add Member')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.members.index') }}">Members</a></li>
            <li class="breadcrumb-item active">Add Member</li>
        </ol>
    </nav>

    <h1 class="h3 mb-4">Add Library Member</h1>

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <x-card title="Member Details">
        <form action="{{ route('librarian.members.store') }}" method="POST" x-data="memberForm()">
            @csrf

            <div class="mb-3">
                <label class="form-label">Member Type <span class="text-danger">*</span></label>
                <select name="member_type" class="form-select @error('member_type') is-invalid @enderror" x-model="memberType" required>
                    <option value="">Select Type</option>
                    <option value="student" {{ old('member_type') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="teacher" {{ old('member_type') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                </select>
                @error('member_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3" x-show="memberType === 'student'">
                <label class="form-label">Student <span class="text-danger">*</span></label>
                <select name="member_id" class="form-select @error('member_id') is-invalid @enderror" x-bind:required="memberType === 'student'" x-bind:disabled="memberType !== 'student'">
                    <option value="">Select Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('member_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->user->name ?? 'N/A' }} ({{ $student->admission_number ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                @error('member_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if($students->isEmpty())
                    <small class="text-muted">All students are already library members.</small>
                @endif
            </div>

            <div class="mb-3" x-show="memberType === 'teacher'">
                <label class="form-label">Teacher <span class="text-danger">*</span></label>
                <select name="member_id" class="form-select @error('member_id') is-invalid @enderror" x-bind:required="memberType === 'teacher'" x-bind:disabled="memberType !== 'teacher'">
                    <option value="">Select Teacher</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('member_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->user->name ?? 'N/A' }} ({{ $teacher->employee_id ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                @error('member_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if($teachers->isEmpty())
                    <small class="text-muted">All teachers are already library members.</small>
                @endif
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Add Member
                </button>
                <a href="{{ route('librarian.members.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </x-card>
</div>

@push('scripts')
<script>
function memberForm() {
    return {
        memberType: '{{ old('member_type', '') }}'
    }
}
</script>
@endpush
@endsection
