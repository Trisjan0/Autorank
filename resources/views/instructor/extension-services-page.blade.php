@extends('layouts.view-all-layout')

@section('title', 'Extension Services | Autorank')

@section('content')
<div class="header">
    <h1>KRA III Extension Services</h1>
</div>
<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Service Type</th>
                <th>Date</th>
                <th>Link</th>
                <th>
                    {{-- The Search Bar Form --}}
                    <div class="search-bar-container">
                        <form action="{{ route('extension-services-page') }}" method="GET">
                            <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></button>
                        </form>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($extension_services as $service)
            <tr>
                <td>{{ $service->id }}</td>
                <td>{{ $service->title }}</td>
                <td>{{ $service->service_type }}</td>
                <td>{{ $service->date }}</td>
                <td>
                    @if($service->link)
                    <a href="{{ $service->link }}" target="_blank"><button>View</button></a>
                    @else
                    <span>No Link</span>
                    @endif
                </td>
                <td>
                    <div>
                        <button>Edit</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">No extension services found.</td>
            </tr>
            @endforelse
            {{-- Static row for uploading a new document --}}
            <tr>
                <td>&nbsp;</td>
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