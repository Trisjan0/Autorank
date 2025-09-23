@extends('layouts.view-all-layout')

@section('title', 'KRA II: Research Outputs | Autorank')

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
    <h1>KRA II: Research Outputs</h1>
</div>

<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Type</th>
                <th>Date Uploaded</th>
                <th>Publish Date</th>
                <th>Score</th>
                <th>
                    <div class="search-bar-container">
                        <form action="{{ route('instructor.research-documents-page') }}" method="GET" id="kra-search-form">
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
            @forelse($research_documents as $document)
            <tr data-id="{{ $document->id }}">
                @include('partials._research_documents_table_row', ['document' => $document])
            </tr>
            @empty
            <tr id="no-results-row">
                <td colspan="8" style="text-align: center;">No items found.</td>
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
            <form id="kra-upload-form" action="{{ route('instructor.research-documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="role-modal-content">
                    <div class="role-modal-content-header">
                        <h1>Upload Research Document</h1>
                        <p>Fill out the details below. You will be asked to confirm before the file is uploaded.</p>
                    </div>
                    <div class="role-modal-content-body">
                        <div class="form-group">
                            <label class="form-group-title" for="research-category">Category:</label>
                            <select class="eval-category" name="category" required data-label="Category">
                                <option value="" disabled selected> Select a category</option>
                                <option value="Research Outputs">Research Outputs</option>
                                <option value="Inventions">Inventions</option>
                                <option value="Creative Works">Creative Works</option>
                                <option value="Papers Presented in Conferences">Papers Presented in Conferences</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="title">Title:</label>
                            <input type="text" id="title" name="title" required data-label="Title">
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="type">Type:</label>
                            <input type="text" id="type" name="type" required data-label="Type">
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="publish_date">Publish Date:</label>
                            <input type="date" id="publish_date" name="publish_date" style="color-scheme: dark;" required data-label="Publish Date">
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="document_file">Upload File:</label>
                            <input type="file" id="document_file" name="document_file" required data-label="File">
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