@extends('layouts.profile-page-layout')

@section('title')
@if ($isOwnProfile)
Profile | Autorank
@else
{{ $user->name }}'s Profile | Autorank
@endif
@endsection

@section('content')
<div class="content-container">
    <div class="content">
        <div class="content-left-side">
            <div class="profile-img-container">
                <img src="{{ $user->avatar }}" alt="{{ $user->name }}'s profile picture">
            </div>
            <div class="separator-container">
                <h6>Basic Info</h6>
                <hr>
            </div>
            <div class="basic-info-container">
                <div id="copyToast" class="toast-container">
                    <p>Copied to clipboard!</p>
                </div>
                <div class="basic-info-fields">
                    <div class="basic-info">
                        <h3>Full Name</h3>
                        <h5 id="username">{{ $user->name }}</h5>
                    </div>
                    <div class="basic-info-action" title="Copy Instructor Name" onclick="copyInstructorsName();">
                        <i class="fa-regular fa-copy"></i>
                    </div>
                </div>
                <div class="basic-info-fields">
                    <div class="basic-info">
                        <h3>Instructor Number</h3>
                        <h5 id="instructorsNumber">{{ $user->instructor_number ?? 'TBC' }}</h5>
                    </div>
                    <div class="basic-info-action" title="Copy Instructor Number" onclick="copyInstructorNumber();">
                        <i class="fa-regular fa-copy"></i>
                    </div>
                </div>
                <div class="basic-info-fields">
                    <div class="basic-info">
                        <h3>Faculty Rank</h3>
                        <h5>{{ $user->rank ?? 'TBC' }}</h5>
                    </div>
                    <div class="basic-info-action"></div>
                </div>
                <div class="basic-info-fields">
                    <div class="basic-info">
                        <h3>Email</h3>
                        <h5>{{ $user->email }}</h5>
                    </div>
                    @if (!$isOwnProfile)
                    <div class="basic-info-action">
                        <a href="mailto:{{ $user->email }}" title="Email Instructor">
                            <i class="fa-regular fa-envelope"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="content-right-side">
            <div class="credentials-and-experience-container">
                <div class="title">
                    <h1>Profile</h1>
                </div>
                <div class="progress-bars-container">
                    <div class="years-of-teaching-container">
                        <div class="subtitle">
                            <h4>Years of Teaching</h4>
                            <h5></h5>
                        </div>
                        <div class="years-of-teaching-progress-bar-container">
                            <div class="years-of-teaching-progress"></div>
                            <div class="bar"></div>
                        </div>
                        <div class="years-of-teaching-bottom-note">
                            <h5>5 years</h5>
                        </div>
                    </div>
                    <div class="degree-container">
                        <div class="subtitle">
                            <h4>Degrees Achieved</h4>
                            <h5></h5>
                        </div>
                        <div class="degree-progress-bar-container">
                            <div class="degree-progress"></div>
                            <div class="bar"></div>
                        </div>
                        <div class="degree-bottom-note">
                            <h5>Bachelor's</h5>
                            <h5>Master's</h5>
                            <h5>Doctorate</h5>
                        </div>
                    </div>
                </div>
                @if ($isOwnProfile)
                <div class="apply-for-reranking-container">
                    <button>Apply for Merit Promotion</button>
                </div>
                @endif
            </div>
            @if ($isOwnProfile)
            <div class="title">
                <h1>Credentials & Experience</h1>
            </div>
            {{-- Credentials/Documents Section --}}
            <div class="credentials-table-container">
                <table class="credentials-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>File Name</th>
                            <th>Date Uploaded</th>
                            <th>
                                <div class="search-bar-container">
                                    <form action="{{ route('profile-page') }}" method="GET" id="kra-search-form">
                                        <input type="text" name="search" placeholder="Search uploads..." value="{{ request('search') }}">
                                        <button type="submit">
                                            <i class="fa-solid fa-magnifying-glass" id="kra-search-btn-icon"></i>
                                        </button>
                                    </form>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="credentials-table-body">
                        @forelse(Auth::user()->credentials()->latest()->get() as $credential)
                        @include('partials._credential_table_row', ['credential' => $credential])
                        @empty
                        <tr id="no-credentials-row">
                            <td colspan="4" style="text-align: center;">No items found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mini-load-more-container">
                <button id="upload-credential-button" class="upload-new-button">Upload New Document</button>
            </div>

            {{-- CREDENTIAL UPLOAD MODAL --}}
            <div class="role-modal-container" id="credential-upload-modal" style="display: none;">
                <div class="role-modal">
                    <div class="role-modal-navigation">
                        <i class="fa-solid fa-xmark" style="color: #ffffff;" id="credential-modal-close-btn"></i>
                    </div>

                    {{-- STEP 1: Form Input --}}
                    <div id="credential-modal-initial-step">
                        <form id="credential-upload-form" action="{{ route('credentials.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="role-modal-content">
                                <div class="role-modal-content-header">
                                    <h1>Upload Document</h1>
                                    <p>Fill out the details below. You will be asked to confirm before the file is uploaded.</p>
                                </div>
                                <div class="role-modal-content-body">
                                    <div class="form-group">
                                        <label class="form-group-title" for="cred-title">Title:</label>
                                        <input type="text" id="cred-title" name="title" required data-label="Title">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-group-title" for="credential_file">Upload File:</label>
                                        <input type="file" id="credential_file" name="credential_file" required data-label="File">
                                    </div>
                                    <div id="credential-modal-messages" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="role-modal-actions">
                                <button type="button" id="credential-proceed-btn">Proceed</button>
                            </div>
                        </form>
                    </div>

                    {{-- STEP 2: Confirmation --}}
                    <div id="credential-modal-confirmation-step" style="display: none;">
                        <div class="role-modal-content">
                            <div class="role-modal-content-header">
                                <h1>Confirm Upload</h1>
                                <p id="credential-confirmation-area"></p>
                            </div>
                            <div class="role-modal-content-body">
                                <div id="credential-final-status-area" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="role-modal-actions">
                            <button type="button" class="btn btn-info" id="credential-back-btn">Back</button>
                            <button type="button" class="btn btn-success" id="credential-confirm-btn">Confirm & Upload</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection

@push('page-scripts')
<script src="{{ asset('js/kra-scripts.js') }}"></script>
<script src="{{ asset('js/profile-page-scripts.js') }}"></script>
@endpush