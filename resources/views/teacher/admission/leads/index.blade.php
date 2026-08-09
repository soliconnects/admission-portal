@extends('adminlte::page')

@section('title', 'My Assigned Leads')

@section('content_header')
    <h1>Leads Assigned to Me</h1>
@endsection

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Applied Class</th>
                        <th>Parent</th>
                        <th>Forwarded On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leads as $lead)
                        <tr>
                            <td>{{ $lead->student_name }}</td>
                            <td>{{ $lead->applied_class_text }}</td>
                            <td>{{ $lead->parent_name }}</td>
                            <td>{{ $lead->forwarded_at?->format('d M Y, h:i A') }}</td>
                            <td>
                                <a href="{{ route('teacher.admission.leads.show', $lead) }}" class="btn btn-sm btn-primary">Assess</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No leads pending your assessment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $leads->links() }}
        </div>
    </div>
@endsection