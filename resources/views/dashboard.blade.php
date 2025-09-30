@extends('layouts.dashboard-layout')

@section('title', 'Dashboard | Autorank')

@if(session('success'))
<div class="server-alert-success">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="server-alert-danger">
    {{ session('error') }}
</div>
@endif

@section('content')
<div class="main-content-container">
    {{-- to do --}}
    Nothing here yet..
</div>
@endsection