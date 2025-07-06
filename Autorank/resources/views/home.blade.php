@extends('layouts.home-layout')

@section('title', 'Home')

@section('content')
<nav>
    <div class="navbar-container">
        <div class="navbar-left-side">
            <i onclick="toggleMenu()" class="fa-solid fa-bars" style="color: #ffffff;"></i>
            <div class="navbar-logo-container">
                <img src="https://cdn.prod.website-files.com/63285a261aa73dfdbba80503/63285a261aa73d4edba80542_Placeholder-Image-1x1.svg" alt="website logo">
                <h1>AutoRank</h1>
            </div>
        </div>
        <div class="navbar-right-side">
            <a href="profile-page.html"><i class="fa-solid fa-user" style="color: #ffffff;"></i></a>
        </div>
    </div>
    <div id="hidden-menu">
        <a href="index.html">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-house" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Home</p>
            </div>
        </a>
        <a href="application-page.html">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-file-circle-check" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Review Applications</p>
            </div>
        </a>
        <a href="profile-page.html">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-address-book" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Profile</p>
            </div>
        </a>
        <a href="research-documents-page.html">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-file-lines" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Research Documents</p>
            </div>
        </a>
        <a href="evaluations-page.html">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-users" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Evaluations</p>
            </div>
        </a>
        <a href="event-participation-page.html">
            <div class="hidden-menu-icon">
                <i class="fa-solid fa-calendar-day" style="color: #ffffff;"></i>
            </div>
            <div class="hidden-menu-title">
                <p>Event Participations</p>
            </div>
        </a>
    </div>
</nav>
<div class="main-content-container">
    <div class="overall-completion-overview-container">
        <div class="research-publication-container">
            <div class="research-publication-top-part">
                <h1>Research Publication</h1><i class="fa-solid fa-ellipsis-vertical" style="color: #00156a;"></i>
            </div>
            <div class="research-publication-bottom-part">
                <div class="percentage-container">
                    <h1>90%</h1>
                </div>
                <div class="progress-bar-container">
                    <div class="research-publication-progress"></div>
                    <div class="bar"></div>
                </div>
            </div>
        </div>
        <div class="event-participation-container">
            <div class="event-participation-top-part">
                <h1>Event Participation</h1><i class="fa-solid fa-ellipsis-vertical" style="color: #00156a;"></i>
            </div>
            <div class="event-participation-bottom-part">
                <div class="percentage-container">
                    <h1>30%</h1>
                </div>
                <div class="progress-bar-container">
                    <div class="event-participation-progress"></div>
                    <div class="bar"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="online-faculty-evaluation-container">
        <div class="online-faculty-evaluation-top-part">
            <h1>Online Evaluation of Faculty</h1>
            <h1>2nd Semester A.Y. 2023-2024</h1>
        </div>
        <div class="online-faculty-evaluation-bottom-part">
            <button>View</button>
        </div>
    </div>
    <div class="latest-research-title-container">
        <div class="latest-research-title-header">
            <h1>Latest Research Title</h1>
        </div>
        <div class="latest-research-title-table-container">
            <table>
                <tbody>
                    <tr>
                        <td><b>Title: </b>Lorem ipsum dolor sit amet, consectetur adipscing elit</td>
                        <td><b>Status: </b>Ongoing</td>
                        <td><b>Date Published: </b>N/A</td>
                        <td><button>Upload</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection