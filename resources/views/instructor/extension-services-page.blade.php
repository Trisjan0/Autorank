@extends('layouts.view-all-layout')

@section('title', 'KRA III: Extension Services | Autorank')

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
    <h1>KRA III: Extension Services</h1>
</div>

<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Service Type</th>
                <th>Date Uploaded</th>
                <th>
                    <div class="search-bar-container">
                        <form action="{{ route('instructor.extension-services-page') }}" method="GET" id="kra-search-form">
                            <input type="text" name="search" placeholder="Search services..." value="{{ request('search') }}">
                            <button type="submit">
                                <i class="fa-solid fa-magnifying-glass" id="kra-search-btn-icon"></i>
                            </button>
                        </form>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody id="kra-table-body">
            @forelse ($extension_services as $service)
            @include('partials._extension_services_table_row', ['service' => $service])
            @empty
            <tr id="no-results-row">
                <td colspan="6" style="text-align: center;">No extension services found.</td>
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
            <form id="kra-upload-form" action="{{ route('instructor.extension-services.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="role-modal-content">
                    <div class="role-modal-content-header">
                        <h1>Upload Extension Service</h1>
                        <p>Fill out the details below. You will be asked to confirm before the file is uploaded.</p>
                    </div>
                    <div class="role-modal-content-body">
                        <div class="form-group">
                            <label class="form-group-title">Service Type:</label>
                            <div class="checkbox-group">
                                <div class="radio-option"><input type="radio" id="type-institution" name="service_type" value="Institution" required data-label="Service Type"><label for="type-institution">Institution</label></div>
                                <div class="radio-option"><input type="radio" id="type-community" name="service_type" value="Community" required data-label="Service Type"><label for="type-community">Community</label></div>
                                <div class="radio-option"><input type="radio" id="type-involvement" name="service_type" value="Extension Involvement" required data-label="Service Type"><label for="type-involvement">Extension Involvement</label></div>
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

{{-- VIEW FILE MODAL (NEWLY ADDED) --}}
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