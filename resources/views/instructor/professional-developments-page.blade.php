@extends('layouts.view-all-layout')

@section('title', 'KRA IV: Professional Development | Autorank')

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
    <h1>KRA IV: Professional Development</h1>
</div>

<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Date</th>
                <th>
                    <div class="search-bar-container">
                        <form action="{{ route('instructor.professional-developments-page') }}" method="GET" id="kra-search-form">
                            <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
                            <button type="submit">
                                <i class="fa-solid fa-magnifying-glass" id="kra-search-btn-icon"></i>
                            </button>
                        </form>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody id="kra-table-body">
            @forelse($professional_developments as $development)
            @include('partials._professional_developments_table_row', ['development' => $development])
            @empty
            <tr id="no-results-row">
                <td colspan="6" style="text-align: center;">No professional development records found.</td>
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
            <form id="kra-upload-form" action="{{ route('instructor.professional-developments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="role-modal-content">
                    <div class="role-modal-content-header">
                        <h1>Upload Professional Development</h1>
                        <p>Fill out the details below. You will be asked to confirm before the file is uploaded.</p>
                    </div>
                    <div class="role-modal-content-body">
                        <div class="form-group">
                            <label class="form-group-title">Category:</label>
                            <div class="checkbox-group" style="flex-wrap: wrap; gap: 10px; align-items: flex-start; flex-direction: column;">
                                <div class="radio-option"><input type="radio" id="cat-prof-org" name="category" value="Professional Organization Involvement" required data-label="Category"><label for="cat-prof-org">Professional Organization Involvement</label></div>
                                <div class="radio-option"><input type="radio" id="cat-cont-dev" name="category" value="Continuing Development" required data-label="Category"><label for="cat-cont-dev">Continuing Development</label></div>
                                <div class="radio-option"><input type="radio" id="cat-awards" name="category" value="Awards" required data-label="Category"><label for="cat-awards">Awards</label></div>
                                <div class="radio-option"><input type="radio" id="cat-exp" name="category" value="Experience" required data-label="Category"><label for="cat-exp">Experience</label></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="title">Title:</label>
                            <input type="text" id="title" name="title" required data-label="Title">
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="date">Date:</label>
                            <input type="date" id="date" name="date" style="color-scheme: dark;" required data-label="Date">
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="evidence_file">Upload File:</label>
                            <input type="file" id="evidence_file" name="evidence_file" required data-label="File">
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
@endpush