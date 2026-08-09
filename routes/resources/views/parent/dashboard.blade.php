@extends('adminlte::page')

@section('title', 'Parent Dashboard')

@section('content_header')
    <h1>Parent Dashboard</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <p>Logged in as: <strong>{{ auth()->user()->name }}</strong></p>
            <p>Role: <strong>Parent</strong></p>
        </div>
    </div>
@endsection