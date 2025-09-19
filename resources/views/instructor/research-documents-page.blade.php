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
                <th>Type</th>
                <th>Category</th>
                <th>Date Uploaded</th>
                <th>Publish Date</th>
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
            @include('partials._research_documents_table_row', ['document' => $document])
            @empty
            <tr id="no-results-row">
                <td colspan="7" style="text-align: center;">No research documents found.</td>
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
                            <label class="form-group-title">Type:</label>
                            <div class="checkbox-group" style="flex-wrap: wrap; gap: 10px;">
                                <div class="radio-option"><input type="radio" id="type-book" name="type" value="Book" required data-label="Type"><label for="type-book">Book</label></div>
                                <div class="radio-option"><input type="radio" id="type-monograph" name="type" value="Monograph" required data-label="Type"><label for="type-monograph">Monograph</label></div>
                                <div class="radio-option"><input type="radio" id="type-journal" name="type" value="Journal" required data-label="Type"><label for="type-journal">Journal</label></div>
                                <div class="radio-option"><input type="radio" id="type-chapter" name="type" value="Chapter" required data-label="Type"><label for="type-chapter">Chapter</label></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="title">Title:</label>
                            <input type="text" id="title" name="title" required data-label="Title">
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="publish_date">Publish Date:</label>
                            <input type="date" id="publish_date" name="publish_date" style="color-scheme: dark;" data-label="Publish Date">
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" for="category">Category:</label>
                            <input type="text" id="category" name="category" required data-label="Category">
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

{{-- VIEW FILE MODAL --}}
<div class="modal-container modal-container--hidden" id="fileViewerModal">
    <div class="modal-content modal-fullscreen">
        <div class="modal-header">
            <h5 class="modal-title" id="fileViewerModalLabel">Viewing File</h5>
            <button class="close-modal-btn" id="closeModalBtn">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="loader-container">
                <div class="loader">
                    <div class="bar1"></div>
                    <div class="bar2"></div>
                    <div class="bar3"></div>
                    <div class="bar4"></div>
                    <div class="bar5"></div>
                    <div class="bar6"></div>
                    <div class="bar7"></div>
                    <div class="bar8"></div>
                    <div class="bar9"></div>
                    <div class="bar10"></div>
                    <div class="bar11"></div>
                    <div class="bar12"></div>
                </div>
            </div>
            <div class="file-feedback-container" id="fileViewerFeedback" style="display: none;">
                <p>This file type cannot be previewed directly on the website.</p>
                <a href="#" id="fileViewerDownloadBtn" class="btn btn-primary" download>Download File</a>
            </div>
            <iframe id="fileViewerIframe" src="" frameborder="0"></iframe>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="{{ asset('js/kra-scripts.js') }}"></script>
@endpush