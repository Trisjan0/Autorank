@extends('layouts.view-all-layout')

@section('title', 'Research Documents | Autorank')

@section('content')
<div class="header">
    <h1>KRA II Research Output</h1>
</div>
<div class="performance-metric-container">
    <table>
        <tbody>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Type</th>
                <th>Category</th>
                <th>
                    {{-- The Search Bar Form --}}
                    <div class="search-bar-container">
                        <form action="{{ route('research-documents-page') }}" method="GET">
                            <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></button>
                        </form>
                    </div>
                </th>
                </thead>
        <tbody>
            @forelse($research_documents as $document)
            <tr>
                <td>{{ $document->id }}</td>
                <td>{{ $document->title }}</td>
                <td>{{ $document->type }}</td>
                <td>{{ $document->category }}</td>
                <td>
                    <div>
                        @if($document->link)
                        <a href="{{ $document->link }}" target="_blank"><button>View</button></a>
                        <button>Edit</button>
                        @else
                        <button>Upload</button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No research documents found.</td>
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
@endsection