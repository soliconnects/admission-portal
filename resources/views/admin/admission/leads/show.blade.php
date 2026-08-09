@extends('adminlte::page')

@section('title', 'Lead Detail')

@section('content_header')
    <h1>Lead: {{ $lead->student_name }}</h1>
@endsection

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-borderless">
                <tr><th>Student Name</th><td>{{ $lead->student_name }}</td></tr>
                <tr><th>Date of Birth</th><td>{{ $lead->dob?->format('d M Y') }}</td></tr>
                <tr><th>Gender</th><td>{{ $lead->gender }}</td></tr>
                <tr><th>Applied Class</th><td>{{ $lead->applied_class_text }}</td></tr>
                <tr><th>Parent Name</th><td>{{ $lead->parent_name }}</td></tr>
                <tr><th>Parent Phone</th><td>{{ $lead->parent_phone }}</td></tr>
                <tr><th>Parent Email</th><td>{{ $lead->parent_email }}</td></tr>
                <tr><th>Address</th><td>{{ $lead->address }}</td></tr>
                <tr><th>Previous School</th><td>{{ $lead->previous_school }}</td></tr>
                <tr><th>Lead Source</th><td>{{ $lead->lead_source }}</td></tr>
                <tr><th>Status</th><td>{{ ucfirst(str_replace('_',' ', $lead->status)) }}</td></tr>
                <tr><th>Duplicate?</th><td>{{ $lead->is_duplicate ? 'Yes (possible match: ' . ($lead->duplicateOfStudent->name ?? '-') . ')' : 'No' }}</td></tr>
            </table>
        </div>
    </div>

    @if (! in_array($lead->status, ['admitted', 'rejected', 'forwarded']))
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Direct Admit</strong></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.admission.leads.direct-admit', $lead) }}">
                            @csrf
                            <div class="form-group">
                                <label>Select Class</label>
                                <select name="class_id" class="form-control" required>
                                    <option value="">-- Choose Class --</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }} {{ $class->section }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success" onclick="return confirm('Admit this student directly?')">Admit Student</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Forward to Teacher</strong></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.admission.leads.forward', $lead) }}">
                            @csrf
                            <div class="form-group">
                                <label>Select Teacher</label>
                                <select name="teacher_id" class="form-control" required>
                                    <option value="">-- Choose Teacher --</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info" onclick="return confirm('Forward this lead to the selected teacher?')">Forward for Assessment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">This lead's status is <strong>{{ $lead->status }}</strong> — no further admin action available here.</div>
    @endif

    <a href="{{ route('admin.admission.leads.index') }}" class="btn btn-secondary mt-3">Back to Leads</a>
@endsection