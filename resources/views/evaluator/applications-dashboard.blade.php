@extends('layouts.view-all-layout')

@section('title', 'Evaluate Applications | Autorank')

@section('content')

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

<div class="header">
    <h1>Applications for Evaluation</h1>
    {{-- No criterion selector needed for this page --}}
</div>

{{-- Main container for the applications table --}}
<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Applicant Name</th>
                <th>Position Applied For</th>
                <th>Date Submitted</th>
                <th>
                    <div class="search-bar-container">
                        <form action="{{ route('instructor.extension-page') }}" method="GET"> {{-- still needs to be configured for applications dashboard --}}
                            <input type="hidden" name="criterion" value="applications">
                            <input type="text" name="search" placeholder="Search...">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                            <button><i class="fa-solid fa-filter" style="color: #ffffff;"></i></button> {{-- added filtering function no logic yet --}}
                        </form>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($applications as $application)
            <tr data-id="{{ $application->id }}">
                <td>{{ $application->id }}</td>
                <td>{{ $application->user->name }}</td>
                <td>{{ $application->position->title }}</td>
                <td>{{ $application->created_at->format('F d, Y, g:i a') }}</td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('evaluator.application.details', ['application' => $application->id]) }}" class="btn btn-primary">
                            <button>Evaluate</button>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="no-results-row">
                <td colspan="5" style="text-align: center;">No applications are currently pending evaluation.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- The confirmation modal can be included for future actions if needed --}}
@include('partials._action_modals')

@endsection

@push('page-scripts')
{{-- No complex KRA scripts are needed for this page --}}
@endpush
