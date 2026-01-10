@extends('layouts.app')

@section('title', 'Issue Book')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('librarian.issues.index') }}">Book Issues</a></li>
            <li class="breadcrumb-item active">Issue Book</li>
        </ol>
    </nav>

    <h1 class="h3 mb-4">Issue Book</h1>

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <x-card title="Issue Details">
        <form action="{{ route('librarian.issues.store') }}" method="POST" x-data="issueForm()">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Book <span class="text-danger">*</span></label>
                        <select name="book_id" class="form-select @error('book_id') is-invalid @enderror" required>
                            <option value="">Select Book</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}" {{ old('book_id', request('book_id')) == $book->id ? 'selected' : '' }}>
                                    {{ $book->title }} ({{ $book->available_quantity }} available)
                                </option>
                            @endforeach
                        </select>
                        @error('book_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
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
                </div>
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
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Issue Date <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date" class="form-control @error('issue_date') is-invalid @enderror" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                        @error('issue_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', date('Y-m-d', strtotime('+14 days'))) }}" required>
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="2">{{ old('remarks') }}</textarea>
                @error('remarks')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-box-arrow-right me-1"></i> Issue Book
                </button>
                <a href="{{ route('librarian.issues.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </x-card>
</div>

@push('scripts')
<script>
function issueForm() {
    return {
        memberType: '{{ old('member_type', '') }}'
    }
}
</script>
@endpush
@endsection
