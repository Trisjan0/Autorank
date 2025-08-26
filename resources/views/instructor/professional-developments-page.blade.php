@extends('layouts.view-all-layout')

@section('title', 'Professional Development | Autorank')

@section('content')

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
                <th>File</th>
                <th>
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
                <td>{{ \Carbon\Carbon::parse($development->date)->format('m/d/Y') }}</td>
                <td>
                    @if($development->file_path)
                    <a href="{{ asset('storage/' . $development->file_path) }}" target="_blank">View File</a>
                    @else
                    N/A
                    @endif
                </td>
                <td>
                    <button>Edit</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">No professional development records found.</td>
            </tr>
            @endforelse
            {{-- Static row for uploading a new document --}}
            <tr>
                <td colspan="5">&nbsp;</td>
                <td>
                    <button id="upload-evidence-button">Upload New</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>


{{-- Floating Window Start --}}

<div id="upload-window-container" class="floating-window-container" style="display: none;">
    <div class="floating-window">
        <span class="close-button">&times;</span>
        <h2>UPLOAD EVIDENCE</h2>

        <form id="upload-evidence-form" action="{{ route('professional-developments.store') }}" method="POST" class="floating-window-form" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Category:</label>
                <div class="checkbox-group" style="flex-wrap: wrap; gap: 10px; align-items: flex-start; flex-direction: column;">
                    <div><input type="checkbox" id="cat-prof-org" name="category" value="Professional Organization Involvement"><label for="cat-prof-org">Professional Organization Involvement</label></div>
                    <div><input type="checkbox" id="cat-cont-dev" name="category" value="Continuing Development"><label for="cat-cont-dev">Continuing Development</label></div>
                    <div><input type="checkbox" id="cat-awards" name="category" value="Awards"><label for="cat-awards">Awards</label></div>
                    <div><input type="checkbox" id="cat-exp" name="category" value="Experience"><label for="cat-exp">Experience</label></div>
                </div>
            </div>

            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
            </div>

            <div class="form-group">
                <label for="evidence_file">File Picker:</label>
                <input type="file" id="evidence_file" name="evidence_file" required>
            </div>

            <div class="form-actions">
                <button type="submit" id="submit-upload-button" disabled>Upload</button>
            </div>
        </form>
    </div>
</div>

{{-- Floating Window end --}}



<div class="load-more-container">
    <button onclick="goBack()">Back</button>
    <button>Load More +</button>
</div>

<script>
    function goBack() {
        window.history.back();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const uploadWindow = document.getElementById('upload-window-container');
        const openButton = document.getElementById('upload-evidence-button');
        const closeButton = uploadWindow.querySelector('.close-button');
        const form = document.getElementById('upload-evidence-form');
        const submitButton = document.getElementById('submit-upload-button');

        const categoryCheckboxes = form.querySelectorAll('input[name="category"]');
        const requiredInputs = form.querySelectorAll('input[required]');

        function validateForm() {
            let isCategorySelected = false;
            categoryCheckboxes.forEach(checkbox => {
                if (checkbox.checked) isCategorySelected = true;
            });

            let areInputsFilled = true;
            requiredInputs.forEach(input => {
                if (input.type === 'file') {
                    if (input.files.length === 0) areInputsFilled = false;
                } else if (!input.value.trim()) {
                    areInputsFilled = false;
                }
            });

            submitButton.disabled = !(isCategorySelected && areInputsFilled);
        }

        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Uncheck all other boxes
                categoryCheckboxes.forEach(otherCheckbox => {
                    if (otherCheckbox !== this) {
                        otherCheckbox.checked = false;
                    }
                });
                validateForm();
            });
        });

        form.addEventListener('input', validateForm);
        openButton.addEventListener('click', (e) => {
            e.preventDefault();
            uploadWindow.style.display = 'flex';
        });
        closeButton.addEventListener('click', () => {
            uploadWindow.style.display = 'none';
        });
        window.addEventListener('click', (e) => {
            if (e.target == uploadWindow) uploadWindow.style.display = 'none';
        });
    });
</script>
@endsection