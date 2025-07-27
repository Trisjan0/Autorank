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
                        <i class="fa-regular fa-copy" style="color: #000000;"></i>
                    </div>
                </div>
                <div class="basic-info-fields">
                    <div class="basic-info">
                        <h3>Instructor Number</h3>
                        <h5 id="instructorsNumber">{{ $user->instructor_number ?? 'TBC' }}</h5>
                    </div>
                    <div class="basic-info-action" title="Copy Instructor Number" onclick="copyInstructorNumber();">
                        <i class="fa-regular fa-copy" style="color: #000000;"></i>
                    </div>
                </div>
                <div class="basic-info-fields">
                    <div class="basic-info">
                        <h3>Rank</h3>
                        <h5>{{ $user->rank ?? 'TBC' }}</h5>
                    </div>
                    <div class="basic-info-action"></div>
                </div>
                <div class="basic-info-fields">
                    <div class="basic-info">
                        <h3>Credentials</h3>
                        @if ($user->credentials->isNotEmpty())
                        @foreach ($user->credentials as $credential)
                        <h5>{{ $credential->name }}</h5>
                        @endforeach
                        @else
                        <h5>TBC</h5>
                        @endif
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
                            <i class="fa-regular fa-envelope" style="color: #000000;"></i>
                        </a>
                    </div>
                    @endif
                </div>
                <div class="basic-info-fields">
                    <div class="basic-info">
                        <h3>Phone Number</h3>
                        <h5>{{ $user->phone_number ?? 'TBC' }}</h5>
                    </div>
                    @if ($isOwnProfile)
                    <div class="basic-info-action">
                        <a id="openPhoneNumberModalBtn" href="#" title="Edit">
                            <i class="fa-regular fa-pen-to-square" style="color: #000000;"></i>
                        </a>
                    </div>
                    @else
                    <div class="basic-info-action">
                        <a href="tel:{{ $user->phone_number }}" title="Call Instructor">
                            <i class="fa-solid fa-phone-flip" style="color: #000000;"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="content-right-side">
            <div class="reclassification-progress-container">
                <div class="title">
                    <h1>Reclassification Progress</h1>
                </div>
                <div class="progress-bars-container">
                    <div class="current-tenure-container">
                        <div class="subtitle">
                            <h4>Current Tenure</h4>
                            <h5>50% Before Eligibility</h5>
                        </div>
                        <div class="current-tenure-progress-bar-container">
                            <div class="current-tenure-progress"></div>
                            <div class="bar"></div>
                        </div>
                        <div class="bottom-note">
                            <h5>Requirement: 4 years</h5>
                        </div>
                    </div>
                    <div class="research-output-container">
                        <div class="subtitle">
                            <h4>Research Output</h4>
                            <h5>30% Before Eligibility</h5>
                        </div>
                        <div class="research-output-progress-bar-container">
                            <div class="research-output-progress"></div>
                            <div class="bar"></div>
                        </div>
                        <div class="bottom-note">
                            <h5>Requirement: 10</h5>
                        </div>
                    </div>
                    <div class="evaluation-container">
                        <div class="subtitle">
                            <h4>Evaluation</h4>
                            <h5>80% Passed&nbsp;<i class="fa-solid fa-check" style="color: #16f34e;"></i></h5>
                        </div>
                        <div class="evaluation-progress-bar-container">
                            <div class="evaluation-progress"></div>
                            <div class="bar"></div>
                        </div>
                    </div>
                </div>
                @if ($isOwnProfile)
                <div class="apply-for-reranking-container">
                    <button>Apply for Reranking</button>
                </div>
                @endif
            </div>
            <div class="performance-metrics-container">
                <div class="title">
                    <h1>Performance Metrics</h1>
                </div>
                <div class="performance-metrics">
                    <div class="subtitle">
                        <h2>Research Documents</h2>
                    </div>
                    <div class="research-documents-container">
                        <table>
                            <tbody>
                                <tr>
                                    <th>Title</th>
                                    <th>Publication Date</th>
                                    <th>Status</th>
                                    @if ($isOwnProfile)
                                    <th>Action</th>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit</td>
                                    <td>N/A</td>
                                    <td>Ongoing</td>
                                    @if ($isOwnProfile)
                                    <td><button>View</button></td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Sed do eiusmod tempor incididunt ut labore</td>
                                    <td>July 12, 2024</td>
                                    <td>Done</td>
                                    @if ($isOwnProfile)
                                    <td><button>Upload</button></td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Eiusmod do tempor incididunt ut labore</td>
                                    <td>June 2, 2024</td>
                                    <td>Done</td>
                                    @if ($isOwnProfile)
                                    <td><button>Upload</button></td>
                                    @endif
                                </tr>
                                <tr>
                                    @if ($isOwnProfile)
                                    <th class="table-navigation" colspan="4"><a href='{{ route("research-documents-page") }}'>View All&nbsp;&nbsp;<i class="fa-solid fa-chevron-right" style="color: #ffffff;"></i></a></th>
                                    @else
                                    <th class="table-navigation" colspan="3">Latest Research Documents</th>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="subtitle">
                        <h2>Evaluations</h2>
                    </div>
                    <div class="evaluations-container">
                        <table>
                            <tbody>
                                <tr>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    @if ($isOwnProfile)
                                    <th>Action</th>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Student Evaluation</td>
                                    <td>May 7, 2024</td>
                                    <td>Uploaded</td>
                                    @if ($isOwnProfile)
                                    <td>
                                        <div><button>View</button><button>Edit</button></div>
                                    </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Faculty/Peer Evaluation</td>
                                    <td>December 2, 2023</td>
                                    <td>Ongoing</td>
                                    @if ($isOwnProfile)
                                    <td>
                                        <div><button>View</button><button>Edit</button></div>
                                    </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Student Evaluation</td>
                                    <td>September 12, 2023</td>
                                    <td>Uploaded</td>
                                    @if ($isOwnProfile)
                                    <td>
                                        <div><button>View</button><button>Edit</button></div>
                                    </td>
                                    @endif
                                </tr>
                                <tr>
                                    @if ($isOwnProfile)
                                    <th class="table-navigation" colspan="4"><a href='{{ route("research-documents-page") }}'>View All&nbsp;&nbsp;<i class="fa-solid fa-chevron-right" style="color: #ffffff;"></i></a></th>
                                    @else
                                    <th class="table-navigation" colspan="3">Latest Public Evaluations</th>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="subtitle">
                        <h2>Event Participation</h2>
                    </div>
                    <div class="event-participation-container">
                        <table>
                            <tbody>
                                <tr>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    @if ($isOwnProfile)
                                    <th>Action</th>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Seminar</td>
                                    <td>October 14, 2023</td>
                                    <td>Uploaded</td>
                                    @if ($isOwnProfile)
                                    <td>
                                        <div><button>View</button><button>Edit</button></div>
                                    </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Seminar</td>
                                    <td>July 30, 2023</td>
                                    <td>Uploaded</td>
                                    @if ($isOwnProfile)
                                    <td>
                                        <div><button>View</button><button>Edit</button></div>
                                    </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Seminar</td>
                                    <td>June 3, 2023</td>
                                    <td>Ongoing</td>
                                    @if ($isOwnProfile)
                                    <td>
                                        <div><button>View</button><button>Edit</button></div>
                                    </td>
                                    @endif
                                </tr>
                                <tr>
                                    @if ($isOwnProfile)
                                    <th class="table-navigation" colspan="4"><a href='{{ route("research-documents-page") }}'>View All&nbsp;&nbsp;<i class="fa-solid fa-chevron-right" style="color: #ffffff;"></i></a></th>
                                    @else
                                    <th class="table-navigation" colspan="3">Latest Public Events</th>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Editing Phone Number -->
@if ($isOwnProfile)
<div class="phone-num-modal-container" id="phoneNumberModal">
    <div class="phone-num-modal">
        <div class="phone-num-modal-navigation">
            <i class="fa-solid fa-xmark" style="color: #ffffff;" id="closePhoneNumberModalBtn"></i>
        </div>

        <form id="phoneNumberUpdateForm" action="/profile/update-phone" method="POST">
            @csrf
            <div id="phoneInputStep">
                <div class="phone-num-modal-content">
                    <div class="phone-num-modal-content-header">
                        <h1>Add a mobile number</h1>
                        <p>This number enables us to provide an additional and efficient means of contact regarding your reranking process. <a href="#" style="color: #48a2f2;"><b>Learn More</b></a></p>
                    </div>
                    <div class="phone-num-modal-content-body">
                        <div class="phone-num-modal-content-body-tip">
                            <h5>Philippines (+63)</h5>
                        </div>
                        <input type="tel" name="phone_number" id="phoneInput" placeholder="Enter your number" pattern="09[0-9]{9}" maxlength="11" value="{{ $user->phone_number ?? '' }}">
                    </div>
                </div>
                <div class="phone-num-modal-confirmation">
                    <button type="button" id="sendOtpBtn">Send OTP</button>
                </div>
            </div>

            <div id="otpInputStep" style="display: none;">
                <div class="phone-num-modal-content">
                    <div class="phone-num-modal-content-header">
                        <h1>Verify Phone Number</h1>
                        <p>An OTP has been sent to your mobile number. Please enter it below.</p>
                    </div>
                    <div class="phone-num-modal-content-body">
                        <input type="text" name="otp_code" id="otpInput" placeholder="Enter OTP" pattern="[0-9]{6}" maxlength="6">
                    </div>
                </div>
                <div class="phone-num-modal-confirmation">
                    <button type="submit" id="verifyOtpBtn">Verify & Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

@endsection