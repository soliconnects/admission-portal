@extends('adminlte::page')

@section('title', 'Student Dashboard')

@section('content_header')
    <h1>Student Dashboard</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <p>Logged in as: <strong>{{ auth()->user()->name }}</strong></p>
            <p>Role: <strong>Student</strong></p>
        </div>
    </div>
@endsection