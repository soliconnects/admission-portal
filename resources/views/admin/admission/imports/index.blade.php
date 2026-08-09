@extends('adminlte::page')

@section('title', 'Admission Imports')

@section('content_header')
    <h1>Admission Imports</h1>
@endsection

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.admission.imports.create') }}" class="btn btn-primary">Upload New Sheet</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Uploaded By</th>
                        <th>Total Rows</th>
                        <th>New Leads</th>
                        <th>Duplicates</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($imports as $import)
                        <tr>
                            <td>{{ $import->filename }}</td>
                            <td>{{ $import->uploadedBy->name ?? '-' }}</td>
                            <td>{{ $import->total_rows }}</td>
                            <td>{{ $import->new_leads_count }}</td>
                            <td>{{ $import->duplicate_count }}</td>
                            <td><span class="badge badge-{{ $import->status === 'completed' ? 'success' : ($import->status === 'failed' ? 'danger' : 'warning') }}">{{ $import->status }}</span></td>
                            <td>{{ $import->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No imports yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $imports->links() }}
        </div>
    </div>
@endsection