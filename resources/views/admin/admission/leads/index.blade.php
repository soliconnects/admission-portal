@extends('adminlte::page')

@section('title', 'Admission Leads')

@section('content_header')
    <h1>Admission Leads</h1>
@endsection

@section('content')
    <div class="mb-3 d-flex justify-content-between">
        <a href="{{ route('admin.admission.imports.create') }}" class="btn btn-primary">Upload New Sheet</a>
        <a href="{{ route('admin.admission.imports.index') }}" class="btn btn-secondary">View Import History</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <form method="GET" class="form-inline">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control mr-2" placeholder="Search name, parent, phone...">
                <select name="status" class="form-control mr-2">
                    <option value="">All Statuses</option>
                    @foreach (['new','duplicate','forwarded','teacher_reviewed','pending_approval','admitted','rejected'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_',' ', $status)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline-primary">Filter</button>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Applied Class</th>
                        <th>Parent</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Duplicate?</th>
                        <th>Assigned Teacher</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leads as $lead)
                        <tr>
                            <td>{{ $lead->student_name }}</td>
                            <td>{{ $lead->applied_class_text }}</td>
                            <td>{{ $lead->parent_name }}</td>
                            <td>{{ $lead->parent_phone }}</td>
                            <td>
                                <span class="badge badge-{{ match($lead->status) {
                                    'new' => 'secondary',
                                    'duplicate' => 'danger',
                                    'forwarded' => 'info',
                                    'teacher_reviewed', 'pending_approval' => 'warning',
                                    'admitted' => 'success',
                                    'rejected' => 'dark',
                                    default => 'secondary',
                                } }}">
                                    {{ ucfirst(str_replace('_',' ', $lead->status)) }}
                                </span>
                            </td>
                            <td>
                                @if ($lead->is_duplicate)
                                    <span class="text-danger">Yes</span>
                                @else
                                    No
                                @endif
                            </td>
                            <td>{{ $lead->assignedTeacher->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.admission.leads.show', $lead) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">No leads found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $leads->links() }}
        </div>
    </div>
@endsection