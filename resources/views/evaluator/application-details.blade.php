@extends('layouts.view-all-layout')

@section('title', 'Application Details | Autorank')

@section('content')

{{-- Map KRA names to their URL slugs for the route helper --}}
@php
    $kraSlugMap = [
        'KRA I: Instruction' => 'instruction',
        'KRA II: Research' => 'research',
        'KRA III: Extension' => 'extension',
        'KRA IV: Professional Development' => 'professional-development',
    ];
@endphp

<div class="header">
    <div class="header-text" >
        <h1>Applicant: {{ $application->user->name }}</h1>
        <p class="text-muted">Applying for Position: <strong style="font-weight: 550">{{ $application->position->title }}</strong> | Submitted: <strong style="font-weight: 550">{{ $application->created_at->format('F d, Y') }}</strong></p>
    </div>
</div>

{{-- Main container for the KRA summary --}}
<div class="performance-metric-container">
    {{-- We use a table for consistent styling, but it functions as a list --}}
    <table>
        <thead>
            <tr>
                <th>Key Result Area</th>
                <th>Total Submissions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kraCounts as $kraName => $count)
            <tr>
                <td>{{ $kraName }}</td>
                <td>( <strong style="font-size: 1.2rem">{{ $count }}</strong> ) Submissions</td>
                <td>
                    @if($count > 0)
                        <a href="{{ route('evaluator.application.kra', ['application' => $application->id, 'kra_slug' => $kraSlugMap[$kraName]]) }}" class="btn btn-primary">
                            <button>Score Submissions</button>
                        </a>
                    @else
                        <button class="btn btn-secondary" disabled>No Submissions</button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center;">This application has no submissions linked to it.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>



<div class="load-more-container" style="margin-top: 20px;">
    <a href="{{ route('evaluator.applications.dashboard') }}" class="btn btn-secondary"><button>Back</button></a>
    {{-- Final Score Display & Calculation Button --}}
    <div class="final-score-container">
        @if($application->status == 'Evaluated')
            <div class="score-display">
                <h2>Final Score: <span>{{ number_format($application->final_score, 2) }} / 100.00</span></h2>
                <p>This application has been scored and is awaiting review from the administrator.</p>
            </div>
        @else
            {{-- This form submits the request to calculate the final score --}}
            <form method="POST" action="{{ route('evaluator.application.calculate-score', $application->id) }}" onsubmit="return confirm('Are you sure you want to finalize and calculate the score? This action cannot be undone.');">
                @csrf
                <button type="submit" class="upload-new-button">
                    Calculate General Score {{-- there should be a confirmation modal here that prompts the evaluator if they are sure to get the final score for the user, modal also serves to tell them that this action is not revertable --}}
                </button>
            </form>
        @endif
    </div>
</div>

@include('partials._action_modals')

@endsection