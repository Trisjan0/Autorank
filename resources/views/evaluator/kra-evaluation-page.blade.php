@extends('layouts.view-all-layout')

@section('title', 'Evaluate KRA Submissions | Autorank')

{{-- Map KRA slugs to human-readable titles --}}
@php
    $kraTitleMap = [
        'instruction' => 'KRA I: Instruction',
        'research' => 'KRA II: Research',
        'extension' => 'KRA III: Extension',
        'professional-development' => 'KRA IV: Professional Development',
    ];
    $kraTitle = $kraTitleMap[$kra_slug] ?? 'Unknown KRA';
@endphp

@section('content')

<div class="header">
    <div class="header-text">
        <h1>{{ $kraTitle }}</h1>
        <p class="text-muted">Evaluating submissions for: <strong style="font-weight: 550">{{ $application->user->name }}</strong></p>
    </div>
</div>

{{-- Main container for the submissions table --}}
<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                {{-- DYNAMIC HEADERS BASED ON KRA --}}
                @if($kra_slug === 'instruction')
                    <th>Criterion</th>
                    <th>Title / Details</th>
                @elseif($kra_slug === 'research')
                    <th>Title</th>
                    <th>Category</th>
                    <th>Type</th>
                @elseif($kra_slug === 'extension')
                    <th>Title</th>
                    <th>Category</th>
                    <th>Role</th>
                @elseif($kra_slug === 'professional-development')
                    <th>Title</th>
                    <th>Type</th>
                @endif
                <th>Date Uploaded</th>
                <th>Score</th>
                <th><i class="fa-solid fa-filter" style="color: #ffffff;"></i></th> {{-- added filter, no logic yet --}}
            </tr>
        </thead>
        <tbody>
            @forelse($submissions as $item) {{-- needs to be limited to 5 rows on initial page load, this need to adapt the exisiting load more feature of kra pages --}}
            <tr data-id="{{ $item->id }}">
                <td>{{ $item->id }}</td>
                
                {{-- for here maybe just the columns id, criterion, title/details, date uploade should work for every single kra --}}
                @if($kra_slug === 'instruction')
                    <td>{{ ucwords(str_replace('-', ' ', $item->criterion)) }}</td>
                    <td>{{ $item->title ?? $item->academic_period ?? $item->service_type }}</td>
                @elseif($kra_slug === 'research')
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->category ?? 'N/A' }}</td>
                    <td>{{ $item->type ?? 'N/A' }}</td>
                @elseif($kra_slug === 'extension')
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->category ?? 'N/A' }}</td>
                    <td>{{ $item->role ?? 'N/A' }}</td>
                @elseif($kra_slug === 'professional-development')
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->membership_type ?? $item->type ?? N/A}}</td>
                @endif

                <td>{{ $item->created_at->format('F d, Y') }}</td>
                
                {{-- DYNAMIC SCORE CELL --}}
                <td class="score-cell" id="score-cell-{{ $item->id }}">
                    @if($item->score === null)
                        <form class="score-form" data-kra-slug="{{ $kra_slug }}" data-submission-id="{{ $item->id }}">
                            @csrf
                            <input type="number" name="score" class="score-input" step="0.01" min="0" placeholder="0.00" required>
                            <button type="submit" class="btn btn-success btn-sm">Save</button> {{-- there should be a confirm modal here before submitting the score, also tells the evaluator that the action cannot be undone --}}
                        </form>
                    @else
                        <div class="score-display">
                            <span class="score-value">{{ number_format($item->score, 2) }}</span>
                            <span class="badge badge-scored">[ <i>Scored</i> ]</span>
                        </div>
                    @endif
                </td>

                <td>
                    <div class="action-buttons">
                        {{-- The file viewer button logic here would be complex, can be added as an enhancement --}}
                        <a href="#" class="btn btn-info btn-sm" onclick="alert('File viewer for evaluation page to be implemented.')">
                            <button>View File(s)</button>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="no-results-row">
                <td colspan="7" style="text-align: center;">No submissions found for this KRA in this application.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="load-more-container">
    <a href="{{ route('evaluator.application.details', ['application' => $application->id]) }}" class="btn btn-secondary">
        <button>Back</button>
    </a>
</div>

@endsection

@push('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('submit', function(event) {
        if (event.target.matches('.score-form')) {
            event.preventDefault();
            const form = event.target;
            const kraSlug = form.dataset.kraSlug;
            const submissionId = form.dataset.submissionId;
            const scoreInput = form.querySelector('.score-input');
            const saveButton = form.querySelector('button[type="submit"]');
            const scoreCell = document.getElementById(`score-cell-${submissionId}`);

            const score = scoreInput.value;
            const csrfToken = form.querySelector('input[name="_token"]').value;

            saveButton.disabled = true;
            saveButton.textContent = '...';

            fetch(`/evaluator/application/score/${kraSlug}/${submissionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ score: score })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const formattedScore = parseFloat(score).toFixed(2);
                    scoreCell.innerHTML = `
                        <div class="score-display">
                            <span class="score-value">${formattedScore}</span>
                            <span class="badge badge-scored">Scored</span>
                        </div>
                    `;
                } else {
                    alert('Error: ' + data.message);
                    saveButton.disabled = false;
                    saveButton.textContent = 'Save';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred. Please check the console.');
                saveButton.disabled = false;
                saveButton.textContent = 'Save';
            });
        }
    });
});
</script>
@endpush
