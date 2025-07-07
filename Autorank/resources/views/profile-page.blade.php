@extends('layouts.profile-page-layout')

@section('title', 'Profile')

@section('content')
<div class="content-container">
    <div class="content">
        <div class="content-left-side">
            <div class="profile-img-container">
                <img src="https://www.svgrepo.com/show/508699/landscape-placeholder.svg" alt="Profile Image">
            </div>
            <div class="separator-container">
                <h6>Basic Info</h6>
                <hr>
            </div>
            <div class="basic-info-container">
                <div class="full-name-container">
                    <h3>Full Name</h3>
                    <h5>Juan Dela Cruz</h5>
                </div>
                <div class="instructor-number-container">
                    <h3>Instructor Number</h3>
                    <h5>12345</h5>
                </div>
                <div class="rank-container">
                    <h3>Rank</h3>
                    <h5>Instructor I</h5>
                </div>
                <div class="credentials-container">
                    <h3>Credentials</h3>
                    <h5>Master's Degree</h5>
                    <h5>Bachelor's Degree</h5>
                    <h5>Licensed Professional Teacher</h5>
                </div>
                <div class="email-container">
                    <h3>Email</h3>
                    <h5>juandelacruz@univ.edu.ph</h5>
                </div>
                <div class="email-container">
                    <h3>Phone Number</h3>
                    <h5>+6391 2345 6789</h5>
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
                                    <th class="table-navigation" colspan="4"><button onclick="window.location.href='research-documents-page.html'">View All&nbsp;&nbsp;<i class="fa-solid fa-chevron-right" style="color: #ffffff;"></i></button></th>
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