@extends('layouts.view-all-layout')

@section('title', 'Event Participations')

@section('content')
<div class="header">
    <h1>Event Participations</h1>
</div>
<div class="performance-metric-container">
    <table>
        <tbody>
            <tr>
                <th>ID Number</th>
                <th>Type</th>
                <th>Date</th>
                <th>Status</th>
                <th>
                    <div class="search-bar-container">
                        <form action="">
                            <input type="text" placeholder="Search..">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></button>
                        </form>
                    </div>
                </th>
            </tr>
            <tr>
                <td>107</td>
                <td>Seminar</td>
                <td>N/A</td>
                <td>Ongoing</td>
                <td>
                    <div><button>View</button><button>Edit</button></div>
                </td>
            </tr>
            <tr>
                <td>106</td>
                <td>Seminar</td>
                <td>July 12, 2024</td>
                <td>Uploaded</td>
                <td>
                    <div><button>View</button><button>Edit</button></div>
                </td>
            </tr>
            <tr>
                <td>105</td>
                <td>Seminar</td>
                <td>June 2, 2024</td>
                <td>Ongoing</td>
                <td>
                    <div><button>View</button><button>Edit</button></div>
                </td>
            </tr>
            <tr>
                <td>104</td>
                <td>Seminar</td>
                <td>February 16, 2024</td>
                <td>Uploaded</td>
                <td>
                    <div><button>View</button><button>Edit</button></div>
                </td>
            </tr>
            <tr>
                <td>103</td>
                <td>Seminar</td>
                <td>March 17, 2024</td>
                <td>Uploaded</td>
                <td>
                    <div><button>View</button><button>Edit</button></div>
                </td>
            </tr>
            <tr>
                <td>102</td>
                <td>Seminar</td>
                <td>April 2, 2024</td>
                <td>Uploaded</td>
                <td>
                    <div><button>View</button><button>Edit</button></div>
                </td>
            </tr>
            <tr>
                <td>101</td>
                <td>Seminar</td>
                <td>April 15, 2024</td>
                <td>Uploaded</td>
                <td>
                    <div><button>View</button><button>Edit</button></div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="load-more-container">
    <button onclick="window.location.href='profile-page.html'">Back</button>
    <button>Load More +</button>
</div>
@endsection