@extends('adminlte::page')

@section('title', 'Assess Lead')

@section('content_header')
    <h1>Assess: {{ $lead->student_name }}</h1>
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
                <tr><th>Previous School</th><td>{{ $lead->previous_school }}</td></tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Submit Assessment</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('teacher.admission.leads.assess', $lead) }}">
                @csrf
                <div class="form-group">
                    <label>Recommended Class</label>
                    <select name="recommended_class_id" class="form-control" required>
                        <option value="">-- Choose Class --</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }} {{ $class->section }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Assessment Remarks</label>
                    <textarea name="teacher_remarks" class="form-control" rows="4" required placeholder="Describe your assessment of the student..."></textarea>
                </div>
                <button type="submit" class="btn btn-success" onclick="return confirm('Submit this assessment? It will be sent to admin for final approval.')">Submit Assessment</button>
            </form>
        </div>
    </div>

    <a href="{{ route('teacher.admission.leads.index') }}" class="btn btn-secondary mt-3">Back to My Leads</a>
@endsection