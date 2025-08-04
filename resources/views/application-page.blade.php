@extends('layouts.view-all-layout')

@section('title', 'Applications | Autorank')

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
                <th>Current Rank</th>
                <th>Aimed Rank</th>
                <th>Estimated Score</th>
                <th>Evaluation</th>
                <th>Date of Application</th>
                <th>Action</th>
            </tr>
            @foreach($applications as $application)
            <tr>
                <td>{{ $application->id }}</td>
                <td>{{ $application->instructor_name }}</td>
                <td>{{ $application->current_rank }}</td>
                <td>{{ $application->aimed_rank }}</td>
                <td>{{ $application->estimated_score }}</td>
                <td>{{ $application->evaluation }}</td>
                <td>{{ $application->created_at }}</td>
                <td>
                    <a href="{{ route('review-documents-page') }}">
                        <button>Review</button>
                    </a>
                </td>
            </tr>
            @endforeach

            <!-- <tr>
                <td>51015</td>
                <td>Eduardo Garcia</td>
                <td>Instructor II</td>
                <td>Instructor II</td>
                <td style="color: rgb(31, 212, 31)">Complete</td>
                <td style="color: rgb(31, 212, 31)">87.6</td>
                <td>April 2, 2025</td>
                <td>
                    <a href="{{ route('review-documents-page') }}">
                        <button>Review</button>
                    </a>
                </td>
            </tr> -->
        </tbody>
    </table>
</div>
<div class="load-more-container">
    <button onclick="goBack()">Back</button>
    <button>Load More +</button>
</div>
@endsection