@extends('layouts.app')

@section('title', 'Library Members')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('librarian.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Members</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Library Members</h1>
        <a href="{{ route('librarian.members.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Member
        </a>
    </div>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="danger" :message="session('error')" />
    @endif

    <x-card title="Filter Members">
        <form action="{{ route('librarian.members.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="student" {{ request('type') == 'student' ? 'selected' : '' }}>Students</option>
                    <option value="teacher" {{ request('type') == 'teacher' ? 'selected' : '' }}>Teachers</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Member name" value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </x-card>

    <x-card title="Members List" class="mt-4">
        @if($members->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                            <tr>
                                <td>
                                    <a href="{{ route('librarian.members.show', $member->id) }}" class="text-decoration-none fw-semibold">
                                        {{ $member->user->name ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $member->member_type == 'student' ? 'info' : 'secondary' }}">
                                        {{ ucfirst($member->member_type) }}
                                    </span>
                                </td>
                                <td>{{ $member->user->email ?? 'N/A' }}</td>
                                <td>{{ $member->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($member->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('librarian.members.show', $member->id) }}" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('librarian.members.toggle-status', $member->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-{{ $member->is_active ? 'warning' : 'success' }}" title="{{ $member->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="bi bi-{{ $member->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('librarian.members.destroy', $member->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this member?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $members->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state 
                icon="bi-people"
                title="No Members Found"
                description="No library members match your search criteria."
            />
        @endif
    </x-card>
</div>
@endsection
