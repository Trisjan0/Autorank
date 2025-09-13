@extends('layouts.view-all-layout')

@section('title', 'KRA I-B: Instructional Materials | Autorank')

@section('content')

{{-- Success and Error Messages --}}
@if(session('success'))
<div class="alert alert-success" style="padding: 10px; margin-bottom: 20px; border: 1px solid green; color: green; background-color: #e6ffed;">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="padding: 10px; margin-bottom: 20px; border: 1px solid red; color: red; background-color: #ffeeee;">
    {{ session('error') }}
</div>
@endif

{{-- ======================= INSTRUCTIONAL MATERIALS SECTION ======================= --}}
<div class="header">
    <h1>KRA I-B: Instructional Materials</h1>
</div>
<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Date</th>
                <th>Type</th>
                <th>File</th>
                <th>
                    <div class="search-bar-container">
                        <form action="{{ route('instructor.instructional-materials-page') }}" method="GET">
                            <input type="text" name="search_materials" placeholder="Search materials..." value="{{ request('search_materials') }}">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></button>
                        </form>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($materials as $material)
            <tr>
                <td>{{ $material->id }}</td>
                <td>{{ $material->title }}</td>
                <td>{{ Str::title(str_replace('_', ' ', $material->category)) }}</td>
                <td>{{ \Carbon\Carbon::parse($material->date)->format('m/d/Y') }}</td>
                <td>{{ $material->type }}</td>
                <td>
                    @if($material->file_path)
                    <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank">View File</a>
                    @else
                    N/A
                    @endif
                </td>
                <td><button>Edit</button></td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">No materials found.</td>
            </tr>
            @endforelse
            {{-- CORRECTED: Static row for uploading a new material --}}
            <tr>
                <td colspan="7"><button id="upload-material-button" class="upload-new-button">Upload New</button></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ====================================================== --}}
{{-- START: FLOATING WINDOWS --}}
{{-- ====================================================== --}}

<div id="upload-material-window-container" class="floating-window-container" style="display: none;">
    <div class="floating-window">
        <span class="close-button">&times;</span>
        <h2>UPLOAD INSTRUCTIONAL MATERIAL</h2>
        <form id="upload-material-form" action="{{ route('materials.store') }}" method="POST" class="floating-window-form" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Category:</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="category-sole-author" name="category" value="sole_author">
                    <label for="category-sole-author">Sole Author</label>
                    <input type="checkbox" id="category-co-author" name="category" value="co_author">
                    <label for="category-co-author">Co-Author</label>
                </div>
            </div>
            <div class="form-group"><label for="material-title">Title:</label><input type="text" id="material-title" name="title" required></div>
            <div class="form-group"><label for="material-date">Date:</label><input type="date" id="material-date" name="date" required></div>
            <div class="form-group"><label for="material-type">Type:</label><input type="text" id="material-type" name="type" required></div>
            <div class="form-group"><label for="material_file">Upload File:</label><input type="file" id="material_file" name="material_file" required></div>
            <div class="form-actions"><button type="submit" id="submit-material-button" disabled>Upload</button></div>
        </form>
    </div>
</div>

{{-- ====================================================== --}}
{{-- END: FLOATING WINDOWS --}}
{{-- ====================================================== --}}

<div class="load-more-container">
    <button onclick="goBack()">Back</button>
    <button>Load More +</button>
</div>
@endsection