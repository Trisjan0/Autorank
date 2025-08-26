@extends('layouts.view-all-layout')

@section('title', 'Evaluations | Autorank')

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

<div class="header">
    <h1>KRA I Evaluations</h1>
</div>
<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Date</th>
                <th>Score</th>
                <th>File</th>
                <th>
                    <div class="search-bar-container">
                        <form action="{{ route('evaluations-page') }}" method="GET">
                            <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></button>
                        </form>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($evaluations as $evaluation)
            <tr>
                <td>{{ $evaluation->id }}</td>
                <td>{{ Str::title($evaluation->title) }}</td>
                <td>{{ Str::title($evaluation->category) }}</td>
                <td>{{ \Carbon\Carbon::parse($evaluation->date)->format('m/d/Y') }}</td>
                <td>{{ $evaluation->score }}</td>
                <td>
                    @if($evaluation->file_path)
                    <a href="{{ asset('storage/' . $evaluation->file_path) }}" target="_blank">View File</a>
                    @else
                    N/A
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
                <td colspan="7" style="text-align: center;">No evaluations found.</td>
            </tr>
            @endforelse
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>
                    <button id="upload-evaluation-button">Upload New</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- START: FLOATING WINDOW --}}
<div id="upload-window-container" class="floating-window-container" style="display: none;">
    <div class="floating-window">
        <span class="close-button">&times;</span>
        <h2>UPLOAD EVALUATION</h2>

        <form id="upload-evaluation-form" action="{{ route('evaluations.store') }}" method="POST" class="floating-window-form" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Category:</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="category-student" name="category" value="student">
                    <label for="category-student">Student</label>
                    <input type="checkbox" id="category-supervisor" name="category" value="supervisor">
                    <label for="category-supervisor">Supervisor</label>
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
                <label for="score">Score:</label>
                <input type="number" id="score" name="score" step="0.01" required>
            </div>

            {{-- NEW FILE INPUT --}}
            <div class="form-group">
                <label for="evaluation_file">Upload File:</label>
                <input type="file" id="evaluation_file" name="evaluation_file" required>
            </div>

            <div class="form-actions">
                <button type="submit" id="submit-upload-button" disabled>Upload</button>
            </div>
        </form>
    </div>
</div>
{{-- END: FLOATING WINDOW --}}

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
        const openButton = document.getElementById('upload-evaluation-button');
        const closeButton = uploadWindow.querySelector('.close-button');
        const form = document.getElementById('upload-evaluation-form');
        const submitButton = document.getElementById('submit-upload-button');

        const studentCheckbox = document.getElementById('category-student');
        const supervisorCheckbox = document.getElementById('category-supervisor');
        const titleInput = document.getElementById('title');
        const dateInput = document.getElementById('date');
        const scoreInput = document.getElementById('score');
        const fileInput = document.getElementById('evaluation_file'); // New file input
        const inputs = [titleInput, dateInput, scoreInput, fileInput]; // Add file input to array

        function validateForm() {
            const isCategorySelected = studentCheckbox.checked || supervisorCheckbox.checked;
            const areOtherFieldsFilled = inputs.every(input => {
                if (input.type === 'file') {
                    return input.files.length > 0; // Check if a file is selected
                }
                return input.value.trim() !== '';
            });

            submitButton.disabled = !(isCategorySelected && areOtherFieldsFilled);
        }

        studentCheckbox.addEventListener('change', function() {
            if (this.checked) supervisorCheckbox.checked = false;
            validateForm();
        });
        supervisorCheckbox.addEventListener('change', function() {
            if (this.checked) studentCheckbox.checked = false;
            validateForm();
        });

        inputs.forEach(input => input.addEventListener('input', validateForm));

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