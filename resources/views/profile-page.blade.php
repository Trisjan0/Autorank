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
                            <i class="fa-regular fa-envelope"></i>
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
                            <i class="fa-regular fa-pen-to-square"></i>
                        </a>
                    </div>
                    @else
                    <div class="basic-info-action">
                        <a href="tel:{{ $user->phone_number }}" title="Call Instructor">
                            <i class="fa-solid fa-phone-flip"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="content-right-side">
            <div class="credentials-and-experience-container">
                <div class="title">
                    <h1>Credentials & Experience</h1>
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
                    <button>Apply for Reranking</button>
                </div>
                @endif
            </div>
            <div class="performance-metrics-container">
                <div class="title">
                    <h1>Key Result Areas</h1>
                </div>
                <div class="performance-metrics">
                    <!-- KRA I-A: EVALUATIONS -->
                    <div class="subtitle">
                        <h2>KRA I-A: Evalutions</h2>
                    </div>
                    <div class="metric-table-container">
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
                                    @if ($isOwnProfile)
                                    <th class="table-navigation" colspan="4"><a href='{{ route("instructor.evaluations-page") }}'>View All&nbsp;&nbsp;<i class="fa-solid fa-chevron-right" style="color: #ffffff;"></i></a></th>
                                    @else
                                    <th class="table-navigation" colspan="3">Latest Evaluations</th>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- KRA I-B: Instructional Materials -->
                    <div class="subtitle">
                        <h2>KRA I-A: Instructional Materials</h2>
                    </div>
                    <div class="metric-table-container">
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
                                    @if ($isOwnProfile)
                                    <th class="table-navigation" colspan="4"><a href='{{ route("instructor.instructional-materials-page") }}'>View All&nbsp;&nbsp;<i class="fa-solid fa-chevron-right" style="color: #ffffff;"></i></a></th>
                                    @else
                                    <th class="table-navigation" colspan="3">Latest Instructional Materials</th>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- KRA II: Research Outputs -->
                    <div class="subtitle">
                        <h2>KRA II: Research Outputs</h2>
                    </div>
                    <div class="metric-table-container">
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
                                    @if ($isOwnProfile)
                                    <th class="table-navigation" colspan="4"><a href='{{ route("instructor.research-documents-page") }}'>View All&nbsp;&nbsp;<i class="fa-solid fa-chevron-right" style="color: #ffffff;"></i></a></th>
                                    @else
                                    <th class="table-navigation" colspan="3">Latest Research Outputs</th>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- KRA III: Extension Services -->
                    <div class="subtitle">
                        <h2>KRA III: Extension Services</h2>
                    </div>
                    <div class="metric-table-container">
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
                                    @if ($isOwnProfile)
                                    <th class="table-navigation" colspan="4"><a href='{{ route("instructor.extension-services-page") }}'>View All&nbsp;&nbsp;<i class="fa-solid fa-chevron-right" style="color: #ffffff;"></i></a></th>
                                    @else
                                    <th class="table-navigation" colspan="3">Latest Extension Services</th>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- KRA IV: Professional Developments -->
                    <div class="subtitle">
                        <h2>KRA IV: Professional Developments</h2>
                    </div>
                    <div class="metric-table-container">
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
                                    @if ($isOwnProfile)
                                    <th class="table-navigation" colspan="4"><a href='{{ route("instructor.professional-developments-page") }}'>View All&nbsp;&nbsp;<i class="fa-solid fa-chevron-right" style="color: #ffffff;"></i></a></th>
                                    @else
                                    <th class="table-navigation" colspan="3">Latest Professional Developments</th>
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

            <div id="otpInputStep">
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