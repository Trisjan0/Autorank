@extends('layouts.view-all-layout')

@section('title', 'Professional Development | Autorank')

@section('content')
<div class="header">
    <h1>KRA IV Professional Development</h1>
</div>
<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Date</th>
                <th>
                    {{-- The Search Bar Form --}}
                    <div class="search-bar-container">
                        <form action="{{ route('professional-developments-page') }}" method="GET">
                            <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></button>
                        </form>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($professional_developments as $development)
            <tr>
                <td>{{ $development->id }}</td>
                <td>{{ $development->title }}</td>
                <td>{{ $development->category }}</td>
                <td>{{ $development->date }}</td>
                <td>
                    <div>
                        <button>Edit</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No professional development records found.</td>
            </tr>
            @endforelse
            {{-- Static row for uploading a new document --}}
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>
                    <button>Upload New</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="load-more-container">
    <button onclick="goBack()">Back</button>
    <button>Load More +</button>
</div>

<script>
    function goBack() {
        window.history.back();
    }
</script>
@endsection