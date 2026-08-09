@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@section('content_header')
    <h1>Admin Dashboard</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <p>Logged in as: <strong>{{ auth()->user()->name }}</strong></p>
            <p>Role: <strong>Admin</strong></p>
        </div>
    </div>
@endsection