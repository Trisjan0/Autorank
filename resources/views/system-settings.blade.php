@extends('layouts.system-settings-layout')

@section('title', 'Settings | Autorank')

@section('content')
<div class="settings-content-container">
    <div class="settings-content">
        <!-- LEFT SIDE (Navigation) -->
        <div class="settings-content-left-side">
            <div class="settings-menu-header">
                <h1>System Settings</h1>
                <hr>
            </div>
            <div class="system-menu-list">
                <a href="#revoke-google-drive-token-section">Privacy</a>
                <ul>
                    <li>Google Drive Access</li>
                </ul>
                @auth
                @can('manage users')
                <a href="#functionality-section-bookmark">Functionality</a>
                <ul>
                    <li>Rank Weights</li>
                </ul>
                @endcan
                @endauth
                <a href="#display-settings-section-bookmark">Display</a>
                <ul>
                    @auth
                    @can('manage users')
                    <li>Website Logo</li>
                    @endcan
                    @endauth
                    <li>Darkmode</li>
                    @auth
                    @can('manage users')
                    <li>Color Scheme</li>
                    @endcan
                    @endauth
                </ul>
            </div>
        </div>
        
        <div class="settings-separator"></div>

        <!-- RIGHT SIDE (Content) -->
        <div class="settings-content-right-side">
            <section>
                <div class="settings-section-child" id="revoke-google-drive-token-section">
                    <h1>Google Drive Access Control</h1>
                    <hr>
                    <p class="settings-section-description">
                        This application uses Google Drive access only to allow users to upload, view, and manage files within the system. At any time, users may disconnect their Google account and remove all Drive permissions by visiting their <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener noreferrer">Google Account Permissions <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: .8rem;"></i></a> page. Once removed, the application will immediately lose access and will no longer be able to view, store, or manage any files in Google Drive.
                    </p>
                    <div class="settings-section-child-div" id="functionality-section-bookmark">
                        <button>Disable Google Drive access</button>
                    </div>
                    <hr>
                </div>
            </section>

            <section>
                @auth
                @can('manage users')
                <div class="settings-section-child">
                    <h1>Set Faculty Rank Weights</h1>
                    <hr>
                    <p class="settings-section-description">Adjust or reset evaluation weights for each faculty rank to maintain consistency with institutional standards.</p>
                    <div class="settings-section-child-div">
                    </div>
                    <hr>
                </div>
                @endcan
                @endauth
            </section>

            <section id="display-settings-section-bookmark">
                @auth
                @can('manage users')
                <div class="settings-section-child">
                <h1>Website Logo</h1>
                <hr>
                <p class="settings-section-description">Update the official logo displayed across the website to reflect institutional branding</p>
                <div class="settings-section-child-div">
                    @if($logo = \App\Models\Setting::where('key', 'site_logo')->value('value'))
                    <div class="settings-logo-preview">
                        <img src="{{ asset($logo) }}" alt="Website Logo">
                        <h6>Current Logo</h6>
                    </div>
                    @endif

                    <div class="role-modal-container" id="change-website-logo-modal" style="display: none;">
                        <div class="role-modal">
                            <div class="role-modal-navigation">
                                <i class="fa-solid fa-xmark close-modal-btn" style="color: #ffffff;"></i>
                            </div>
                            <div class="initial-step">
                                <form class="kra-upload-form" action="{{ route('settings.logo.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="criterion" value="instructional-materials">
                                    <div class="role-modal-content">
                                        <div class="role-modal-content-header">
                                            <h1>Upload an Image</h1>
                                            <p>Maximum of 2MB.</p>
                                        </div>
                                        <div class="role-modal-content-body">
                                            <div class="form-group"><label class="form-group-title" data-label="File Uploaded">File *</label><input type="file" name="logo" required></div>
                                            <div class="modal-messages mt-2"></div>
                                        </div>
                                    </div>
                                    <div class="role-modal-actions"><button type="button" class="proceed-btn">Proceed</button></div>
                                </form>
                            </div>
                            <div class="confirmation-step" style="display: none;">
                                <div class="role-modal-content">
                                    <div class="role-modal-content-header">
                                        <h1>Confirm Upload</h1>
                                        <p class="confirmation-message-area"></p>
                                    </div>
                                    <div class="role-modal-content-body">
                                        <div id="logoConfirmPreviewContainer">
                                            <img id="logoConfirmPreview" src="" alt="Preview">
                                        </div>
                                        <div class="final-status-message-area mt-2"></div>
                                    </div>
                                </div>
                                <div class="role-modal-actions"><button type="button" class="back-btn">Back</button><button type="button" class="confirm-btn">Confirm & Upload</button></div>
                            </div>
                        </div>
                    </div>
                    <button id="upload-logo-button">Upload Logo</button>
                </div>
                <hr>
                </div>
                @endcan
                @endauth

                <div class="settings-section-child">
                    <hr style="margin-top: -1.5rem">
                    <div class="settings-section-child-div" id="darkmode-toggle-section">
                        <h1>Darkmode</h1>
                        <input type="checkbox" id="darkModeToggle" {{ Auth::user()->theme === 'dark' ? 'checked' : '' }}>
                        <label for="darkModeToggle" class="toggleSwitch"></label>
                    </div>
                    <hr style="margin-bottom: -1.5rem">
                </div>

                 @auth
                @can('manage users')
                <div class="settings-section-child">
                    <h1>Color Scheme</h1>
                    <hr>
                    <p class="settings-section-description">Customize the website's primary colors to match institutional branding or preferred design standards.</p>
                    <div class="settings-section-child-div">
                        <div id="change-color-scheme-container">
                            <div class="color-grid">
                                <div class="color-control">
                                    <label for="primaryColor">Primary Color</label>
                                    <input type="color" id="primaryColor" value="{{ $primaryColor }}">
                                </div>
                            </div>
                            <div class="reset-button-wrapper">
                                <button id="resetColorsBtn" class="reset-button">
                                    Reset to Default Colors
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
                @endcan
                @endauth
            </section>
        </div>
    </div>
</div>
@endsection