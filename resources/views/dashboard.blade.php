@extends('layouts.dashboard-layout')

@section('title', 'Dashboard | Autorank')

@section('content')
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