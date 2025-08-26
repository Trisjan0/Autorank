@extends('layouts.view-all-layout')

@section('title', 'Research Documents | Autorank')

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
    <h1>KRA II Research Output</h1>
</div>
<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Type</th>
                <th>Category</th>
                <th>File</th>
                <th>
                    <div class="search-bar-container">
                        <form action="{{ route('research-documents-page') }}" method="GET">
                            <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></button>
                        </form>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($research_documents as $document)
            <tr>
                <td>{{ $document->id }}</td>
                <td>{{ $document->title }}</td>
                <td>{{ $document->type }}</td>
                <td>{{ $document->category }}</td>
                <td>
                    @if($document->file_path)
                    <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">View File</a>
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
                <td colspan="6" style="text-align: center;">No research documents found.</td>
            </tr>
            @endforelse

            <tr>
                <td colspan="5">&nbsp;</td>
                <td>
                    <button id="upload-document-button">Upload New</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Floating window Start --}}
<div id="upload-window-container" class="floating-window-container" style="display: none;">
    <div class="floating-window">
        <span class="close-button">&times;</span>
        <h2>UPLOAD DOCUMENT</h2>

        <form id="upload-document-form" action="{{ route('research-documents.store') }}" method="POST" class="floating-window-form" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Type:</label>
                <div class="checkbox-group" style="flex-wrap: wrap; gap: 10px;">
                    <input type="checkbox" id="type-book" name="type" value="Book"><label for="type-book">Book</label>
                    <input type="checkbox" id="type-monograph" name="type" value="Monograph"><label for="type-monograph">Monograph</label>
                    <input type="checkbox" id="type-journal" name="type" value="Journal"><label for="type-journal">Journal</label>
                    <input type="checkbox" id="type-chapter" name="type" value="Chapter"><label for="type-chapter">Chapter</label>
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
                <label for="category">Category:</label>
                <input type="text" id="category" name="category" required>
            </div>

            <div class="form-group">
                <label for="document_file">Input File:</label>
                <input type="file" id="document_file" name="document_file" required>
            </div>

            <div class="form-actions">
                <button type="submit" id="submit-upload-button" disabled>Upload</button>
            </div>
        </form>
    </div>
</div>
{{-- Floating Window End --}}


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
        const openButton = document.getElementById('upload-document-button');
        const closeButton = uploadWindow.querySelector('.close-button');
        const form = document.getElementById('upload-document-form');
        const submitButton = document.getElementById('submit-upload-button');

        const typeCheckboxes = form.querySelectorAll('input[name="type"]');
        const requiredInputs = form.querySelectorAll('input[required]');

        function validateForm() {
            let isTypeSelected = false;
            typeCheckboxes.forEach(checkbox => {
                if (checkbox.checked) isTypeSelected = true;
            });

            let areInputsFilled = true;
            requiredInputs.forEach(input => {
                if (input.type === 'file') {
                    if (input.files.length === 0) areInputsFilled = false;
                } else if (!input.value.trim()) {
                    areInputsFilled = false;
                }
            });

            submitButton.disabled = !(isTypeSelected && areInputsFilled);
        }

        typeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Uncheck all other boxes
                typeCheckboxes.forEach(otherCheckbox => {
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