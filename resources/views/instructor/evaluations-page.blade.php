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
                        <form action="{{ route('instructor.evaluations-page') }}" method="GET" id="kra-search-form">
                            <input type="text" name="search" placeholder="Search evaluations..." value="{{ request('search') }}">
                            <button type="submit">
                                <i class="fa-solid fa-magnifying-glass" id="kra-search-btn-icon"></i>
                            </button>
                        </form>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody id="kra-table-body">
            @forelse($evaluations as $evaluation)
            <tr data-id="{{ $evaluation->id }}">
                @include('partials._evaluations_table_row', ['evaluation' => $evaluation])
            </tr>
            @empty
            <tr id="no-results-row">
                <td colspan="7" style="text-align: center;">No items found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="load-more-container">
    <button onclick="window.history.back()">Back</button>
    <button id="upload-kra-button" class="upload-new-button">Upload New</button>
    <button id="load-more-kra-btn" data-current-offset="{{ $perPage }}"
        @if (!$initialHasMore) style="display: none;" @endif>
        Load More +
    </button>
</div>

{{-- UPLOAD MODAL --}}
<div class="role-modal-container" id="kra-upload-modal" style="display: none;">
    <div class="role-modal">
        <div class="role-modal-navigation">
            <i class="fa-solid fa-xmark" style="color: #ffffff;" id="kra-modal-close-btn"></i>
        </div>

        {{-- STEP 1: Form Input --}}
        <div id="kra-modal-initial-step">
            <form id="kra-upload-form" action="{{ route('instructor.evaluations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="role-modal-content">
                    <div class="role-modal-content-header">
                        <h1>Upload Evaluation</h1>
                        <p>Fill out the details below. You will be asked to confirm before the file is uploaded.</p>
                    </div>
                    <div class="role-modal-content-body">
                        <div class="form-group">
                            <label class="form-group-title">Category:</label>
                            <div class="checkbox-group">
                                <div class="radio-option">
                                    <input type="radio" id="category-student" name="category" value="student" required data-label="Category">
                                    <label for="category-student">Student</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" id="category-supervisor" name="category" value="supervisor" required data-label="Category">
                                    <label for="category-supervisor">Supervisor</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="eval-title">Title:</label>
                            <input type="text" id="eval-title" name="title" required data-label="Title">
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="eval-publish-date">Publish Date:</label>
                            <input type="date" id="eval-publish-date" name="publish_date" style="color-scheme: dark;" required data-label="Publish Date">
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="eval-score">Score:</label>
                            <input type="number" id="eval-score" name="score" style="color-scheme: dark;" step="0.01" required data-label="Score">
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="evaluation_file">Upload File:</label>
                            <input type="file" id="evaluation_file" name="evaluation_file" required data-label="File">
                        </div>
                        <div id="kra-modal-messages" class="mt-2"></div>
                    </div>
                </div>
                <div class="role-modal-actions">
                    <button type="button" id="kra-proceed-to-confirmation-btn">Proceed</button>
                </div>
            </form>
        </div>

        {{-- STEP 2: Confirmation --}}
        <div id="kra-modal-confirmation-step" style="display: none;">
            <div class="role-modal-content">
                <div class="role-modal-content-header">
                    <h1>Confirm Upload</h1>
                    <p id="kra-confirmation-message-area"></p>
                </div>
                <div class="role-modal-content-body">
                    <div id="kra-final-status-message-area" class="mt-2"></div>
                </div>
            </div>
            <div class="role-modal-actions">
                <button type="button" class="btn btn-info" id="kra-back-to-selection-btn">Back</button>
                <button type="button" class="btn btn-success" id="kra-confirm-upload-btn">Confirm & Upload</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="{{ asset('js/kra-scripts.js') }}"></script>
<script src="{{ asset('js/modal-scripts.js') }}"></script>
@endpush