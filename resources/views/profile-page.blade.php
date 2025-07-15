@extends('layouts.profile-page-layout')

@section('title', 'Profile')

@section('content')
<div class="content-container">
    <div class="content">
        <div class="content-left-side">
            <div class="profile-img-container">
                <img src="{{ Auth::user()->avatar }}" alt="{{ auth()->user()->name }}'s profile picture">
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
                        <h5 id="username">{{ auth()->user()->name }}</h5>
                    </div>
                    <div class="basic-info-action" title="Copy Instructor Name" onclick="copyInstructorsName();">
                        <i class="fa-regular fa-copy" style="color: #000000;"></i>
                    </div>
                </div>
                <div class="basic-info-fields">
                    <div class="basic-info">
                        <h3>Instructor Number</h3>
                        <h5 id="instructorsNumber">{{ auth()->user()->instructor_number ?? 'TBC' }}</h5>
                    </div>
                    <div class="basic-info-action" title="Copy Instructor Number" onclick="copyInstructorNumber();">
                        <i class="fa-regular fa-copy" style="color: #000000;"></i>
                    </div>
                </div>
                <div class="basic-info-fields">
                    <div class="basic-info">
                        <h3>Rank</h3>
                        <h5>{{ auth()->user()->rank ?? 'TBC' }}</h5>
                    </div>
                    <div class="basic-info-action"></div>
                </div>
                <div class="basic-info-fields">
                    <div class="basic-info">
                        <h3>Credentials</h3>
                        @if (auth()->user()->credentials->isNotEmpty())
                        @foreach (auth()->user()->credentials as $credential)
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
                        <h5>{{ auth()->user()->email }}</h5>
                    </div>
                    <div class="basic-info-action">
                        <a href="mailto:{{ auth()->user()->email }}" title="Email Instructor">
                            <i class="fa-regular fa-envelope" style="color: #000000;"></i>
                        </a>
                    </div>
                </div>
                <div class="basic-info-fields">
                    <div class="basic-info">
                        <h3>Phone Number</h3>
                        <h5>{{ auth()->user()->phone_number ?? 'TBC' }}</h5>
                    </div>
                    <div class="basic-info-action">
                        <!-- add if else statement here when admins and users are separated -->
                        <a href="tel:{{ auth()->user()->phone_number }}" title="Edit">
                            <i class="fa-regular fa-pen-to-square" style="color: #000000;"></i>
                        </a>
                    </div>
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
                <div class="apply-for-reranking-container">
                    <button>Apply for Reranking</button>
                </div>
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
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit</td>
                                    <td>N/A</td>
                                    <td>Ongoing</td>
                                    <td><button>View</button></td>
                                </tr>
                                <tr>
                                    <td>Sed do eiusmod tempor incididunt ut labore</td>
                                    <td>July 12, 2024</td>
                                    <td>Done</td>
                                    <td><button>Upload</button></td>
                                </tr>
                                <tr>
                                    <td>Eiusmod do tempor incididunt ut labore</td>
                                    <td>June 2, 2024</td>
                                    <td>Done</td>
                                    <td><button>Upload</button></td>
                                </tr>
                                <tr>
                                    <th class="table-navigation" colspan="4"><a href='{{ route("research-documents-page") }}'>View All&nbsp;&nbsp;<i class="fa-solid fa-chevron-right" style="color: #ffffff;"></i></a></th>
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
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <td>Student Evaluation</td>
                                    <td>May 7, 2024</td>
                                    <td>Uploaded</td>
                                    <td>
                                        <div><button>View</button><button>Edit</button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Faculty/Peer Evaluation</td>
                                    <td>December 2, 2023</td>
                                    <td>Ongoing</td>
                                    <td>
                                        <div><button>View</button><button>Edit</button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Student Evaluation</td>
                                    <td>September 12, 2023</td>
                                    <td>Uploaded</td>
                                    <td>
                                        <div><button>View</button><button>Edit</button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-navigation" colspan="4"><button onclick="window.location.href='evaluations-page.html'">View All&nbsp;&nbsp;<i class="fa-solid fa-chevron-right" style="color: #ffffff;"></i></button></th>
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
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <td>Seminar</td>
                                    <td>October 14, 2023</td>
                                    <td>Uploaded</td>
                                    <td>
                                        <div><button>View</button><button>Edit</button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Seminar</td>
                                    <td>July 30, 2023</td>
                                    <td>Uploaded</td>
                                    <td>
                                        <div><button>View</button><button>Edit</button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Seminar</td>
                                    <td>June 3, 2023</td>
                                    <td>Ongoing</td>
                                    <td>
                                        <div><button>View</button><button>Edit</button></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-navigation" colspan="4"><button onclick="window.location.href='event-participation-page.html'">View All&nbsp;&nbsp;<i class="fa-solid fa-chevron-right" style="color: #ffffff;"></i></button></th>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection