@extends('adminlte::page')

@section('title', 'Teacher Dashboard')

@section('content_header')
    <h1>Teacher Dashboard</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <p>Logged in as: <strong>{{ auth()->user()->name }}</strong></p>
            <p>Role: <strong>Teacher</strong></p>
        </div>
    </div>
@endsection 