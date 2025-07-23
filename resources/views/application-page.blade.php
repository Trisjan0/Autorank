@extends('layouts.view-all-layout')

@section('title', 'Applications - Autorank')

@section('content')
<div class="header">
    <h1>Applications</h1>
</div>
<div class="performance-metric-container">
    <table>
        <tbody>
            <tr>
                <th>ID Number</th>
                <th>Name</th>
                <th>Ranking</th>
                <th>Requirements</th>
                <th>Evaluation</th>
                <th>Date of Application</th>
                <th>Action</th>
            </tr>
            <tr>
                <td>12345</td>
                <td>Consuelo Reyes</td>
                <td>Instructor I</td>
                <td style="color: rgb(31, 212, 31)">Complete</td>
                <td style="color: rgb(31, 212, 31)">Passed</td>
                <td>December 15, 2024</td>
                <td>
                    <a href="{{ route('review-documents-page') }}">
                        <button>Revise</button>
                    </a>
                </td>
            </tr>
            <tr>
                <td>67890</td>
                <td>Roberto Reyes</td>
                <td>Instructor III</td>
                <td style="color: rgb(31, 212, 31)">Complete</td>
                <td style="color: rgb(31, 212, 31)">Passed</td>
                <td>January 15, 2025</td>
                <td>
                    <a href="{{ route('review-documents-page') }}">
                        <button>Revise</button>
                    </a>
                </td>
            </tr>
            <tr>
                <td>24681</td>
                <td>Josefina Santos</td>
                <td>Instructor I</td>
                <td style="color: rgb(31, 212, 31)">Complete</td>
                <td style="color: rgb(31, 212, 31)">Passed</td>
                <td>February 9, 2025</td>
                <td>
                    <a href="{{ route('review-documents-page') }}">
                        <button>Revise</button>
                    </a>
                </td>
            </tr>
            <tr>
                <td>36912</td>
                <td>Salvador Santos</td>
                <td>Instructor I</td>
                <td style="color: rgb(31, 212, 31)">Complete</td>
                <td style="color: rgb(31, 212, 31)">Passed</td>
                <td>February 16, 2025</td>
                <td>
                    <a href="{{ route('review-documents-page') }}">
                        <button>Revise</button>
                    </a>
                </td>
            </tr>
            <tr>
                <td>48121</td>
                <td>Teresita Garcia</td>
                <td>Instructor I</td>
                <td style="color: rgb(237, 150, 69)">90%</td>
                <td style="color: rgb(31, 212, 31)">Passed</td>
                <td>March 17, 2025</td>
                <td>
                    <a href="{{ route('review-documents-page') }}">
                        <button>Revise</button>
                    </a>
                </td>
            </tr>
            <tr>
                <td>51015</td>
                <td>Eduardo Garcia</td>
                <td>Instructor II</td>
                <td style="color: rgb(31, 212, 31)">Complete</td>
                <td style="color: rgb(31, 212, 31)">Passed</td>
                <td>April 2, 2025</td>
                <td>
                    <a href="{{ route('review-documents-page') }}">
                        <button>Revise</button>
                    </a>
                </td>
            </tr>
            <tr>
                <td>61261</td>
                <td>Magdalena Cruz</td>
                <td>Instructor I</td>
                <td style="color: rgb(237, 150, 69)">90%</td>
                <td style="color: rgb(31, 212, 31)">Passed</td>
                <td>April 15, 2025</td>
                <td>
                    <a href="{{ route('review-documents-page') }}">
                        <button>Revise</button>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="load-more-container">
    <button onclick="goBack()">Back</button>
    <button>Load More +</button>
</div>
@endsection