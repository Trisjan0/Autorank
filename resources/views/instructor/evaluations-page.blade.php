@extends('layouts.view-all-layout')

@section('title', 'KRA I-A: Evaluations | Autorank')

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
    <h1>KRA I-A: Evaluations</h1>
</div>

<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Date Uploaded</th>
                <th>Publish Date</th>
                <th>Score</th>
                <th>
                    <div class="search-bar-container">
                        <form action="{{ route('instructor.evaluations-page') }}" method="GET" id="evaluations-search-form">
                            <input type="text" name="search" placeholder="Search evaluations..." value="{{ request('search') }}">
                            <button type="submit">
                                <i class="fa-solid fa-magnifying-glass" id="eval-search-btn-icon"></i>
                            </button>
                        </form>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody id="evaluations-table-body">
            @forelse($evaluations as $evaluation)
            @include('partials._evaluations_table_row', ['evaluation' => $evaluation])
            @empty
            <tr id="no-evaluations-row">
                <td colspan="7" style="text-align: center;">No evaluations found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="load-more-container">
    <button onclick="window.history.back()">Back</button>
    <button id="upload-evaluations-button" class="upload-new-button">Upload New</button>
    <button id="loadMoreEvaluationsBtn" data-current-offset="{{ $perPage }}"
        @if (!$initialHasMore) style="display: none;" @endif>
        Load More +
    </button>
</div>

{{-- UPLOAD EVALUATIONS MODAL --}}
<div class="role-modal-container" id="uploadEvaluationModal" style="display: none;">
    <div class="role-modal">
        <div class="role-modal-navigation">
            <i class="fa-solid fa-xmark" style="color: #ffffff;" id="closeUploadEvalModalBtn"></i>
        </div>

        {{-- STEP 1: Form Input --}}
        <div id="uploadEvaluationInitialStep">
            <form id="upload-evaluations-form" action="{{ route('instructor.evaluations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="role-modal-content">
                    <div class="role-modal-content-header">
                        <h1>Upload Evaluation</h1>
                        <p>Fill out the details below. You will be asked to confirm before the file is uploaded.</p>
                    </div>
                    <div class="role-modal-content-body">
                        {{-- Form fields are wrapped for consistent styling --}}
                        <div class="form-group">
                            <label class="form-group-title">Category:</label>
                            <div class="checkbox-group">
                                <div class="radio-option">
                                    <input type="radio" id="category-student" name="category" value="student" required>
                                    <label for="category-student">Student</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" id="category-supervisor" name="category" value="supervisor" required>
                                    <label for="category-supervisor">Supervisor</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="eval-title">Title:</label>
                            <input type="text" id="eval-title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="eval-publish-date">Publish Date:</label>
                            {{-- Changed the name and id attributes --}}
                            <input type="date" id="eval-publish-date" name="publish_date" style="color-scheme: dark;" required>
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="eval-score">Score:</label>
                            <input type="number" id="eval-score" name="score" style="color-scheme: dark;" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="evaluation_file">Upload File:</label>
                            <input type="file" id="evaluation_file" name="evaluation_file" required>
                        </div>
                        <div id="eval-modal-messages" class="mt-2"></div>
                    </div>
                </div>
                <div class="role-modal-actions">
                    <button type="button" id="proceedToEvalConfirmationBtn">Proceed</button>
                </div>
            </form>
        </div>

        {{-- STEP 2: Confirmation --}}
        <div id="uploadEvaluationConfirmationStep" style="display: none;">
            <div class="role-modal-content">
                <div class="role-modal-content-header">
                    <h1>Confirm Upload</h1>
                    {{-- Confirmation details will be injected here by JavaScript --}}
                    <p id="evalConfirmationMessageArea"></p>
                </div>
                <div class="role-modal-content-body">
                    <div id="evalFinalStatusMessageArea" class="mt-2"></div>
                </div>
            </div>
            <div class="role-modal-actions">
                <button type="button" class="btn btn-info" id="backToEvalSelectionBtn">Back</button>
                {{-- This is the final button that submits the form --}}
                <button type="button" class="btn btn-success" id="confirmUploadEvalBtn">Confirm & Upload</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="{{ asset('js/kra-scripts.js') }}"></script>
@endpush