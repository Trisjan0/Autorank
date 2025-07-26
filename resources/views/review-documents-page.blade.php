@extends('layouts.view-all-layout')

@section('title', content: 'Review Documents | Autorank')

@section('content')
<div class="header">
    <h1>Review Documents</h1>
</div>
<div class="performance-metric-container">
    <table>
        <tbody>
            <tr>
                <th>Document ID</th>
                <th>Name</th>
                <th>Publisher</th>
                <th>Publishing Date</th>
                <th>Document Score</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <tr>
                <td>29001-1</td>
                <td>Heroes Never fade</td>
                <td>Consuelo Reyes</td>
                <td>August 9, 2022</td>
                <td>92.5</td>
                <td>Valid</td>
                <td>
                    <button>Summary</button>
                    <br>
                    <button>Source</button>
                </td>
            </tr>
            <tr>
                <td>29001-2</td>
                <td>Break The Limits!</td>
                <td>Roberto Reyes</td>
                <td>September 15, 2022</td>
                <td>88.0</td>
                <td>Valid</td>
                <td>
                    <button>Summary</button>
                    <br>
                    <button>Source</button>
                </td>

            </tr>
            <tr>
                <td>29001-2</td>
                <td>Let Him Cook!</td>
                <td>Severino Reyes</td>
                <td>November 5, 2021</td>
                <td>89.0</td>
                <td>Valid</td>
                <td>
                    <button>Summary</button>
                    <br>
                    <button>Source</button>
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