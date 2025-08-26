@extends('layouts.view-all-layout')

@section('title', 'Evaluations & Materials | Autorank')

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

{{-- ======================= EVALUATIONS SECTION ======================= --}}
<div class="header">
    <h1>KRA I Evaluations</h1>
</div>
<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th> {{-- RESTORED --}}
                <th>Date</th>
                <th>Score</th>
                <th>File</th>
                <th>
                    <div class="search-bar-container">
                        <form action="{{ route('evaluations-page') }}" method="GET">
                            <input type="text" name="search_evaluations" placeholder="Search evaluations..." value="{{ request('search_evaluations') }}">
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
                <td>{{ Str::title($evaluation->category) }}</td> {{-- RESTORED --}}
                <td>{{ \Carbon\Carbon::parse($evaluation->date)->format('m/d/Y') }}</td>
                <td>{{ $evaluation->score }}</td>
                <td>
                    @if($evaluation->file_path)
                    <a href="{{ asset('storage/' . $evaluation->file_path) }}" target="_blank">View File</a>
                    @else
                    N/A
                    @endif
                </td>
                <td><button>Edit</button></td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">No evaluations found.</td>
            </tr>
            @endforelse
            {{-- Static row for uploading a new evaluation --}}
            <tr>
                <td colspan="6">&nbsp;</td>
                <td><button id="upload-evaluation-button">Upload New</button></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ======================= INSTRUCTIONAL MATERIALS SECTION ======================= --}}
<div class="header" style="margin-top: 40px;">
    <h1>Instructional Materials</h1>
</div>
<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>File</th>
                <th>
                    <div class="search-bar-container">
                        <form action="{{ route('evaluations-page') }}" method="GET">
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
                <td>{{ $material->description }}</td>
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
                <td colspan="5" style="text-align: center;">No materials found.</td>
            </tr>
            @endforelse
            <tr>
                <td colspan="4">&nbsp;</td>
                <td><button id="upload-material-button">Upload New</button></td>
            </tr>
        </tbody>
    </table>
</div>


{{-- ====================================================== --}}
{{-- START: FLOATING WINDOWS --}}
{{-- ====================================================== --}}

<div id="upload-evaluation-window-container" class="floating-window-container" style="display: none;">
    <div class="floating-window">
        <span class="close-button">&times;</span>
        <h2>UPLOAD EVALUATION</h2>
        <form id="upload-evaluation-form" action="{{ route('evaluations.store') }}" method="POST" class="floating-window-form" enctype="multipart/form-data">
            @csrf
            {{-- CATEGORY CHECKBOXES RESTORED --}}
            <div class="form-group">
                <label>Category:</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="category-student" name="category" value="student">
                    <label for="category-student">Student</label>
                    <input type="checkbox" id="category-supervisor" name="category" value="supervisor">
                    <label for="category-supervisor">Supervisor</label>
                </div>
            </div>
            <div class="form-group"><label for="eval-title">Title:</label><input type="text" id="eval-title" name="title" required></div>
            <div class="form-group"><label for="eval-date">Date:</label><input type="date" id="eval-date" name="date" required></div>
            <div class="form-group"><label for="eval-score">Score:</label><input type="number" id="eval-score" name="score" step="0.01" required></div>
            <div class="form-group"><label for="evaluation_file">Upload File:</label><input type="file" id="evaluation_file" name="evaluation_file" required></div>
            <div class="form-actions"><button type="submit" id="submit-evaluation-button" disabled>Upload</button></div>
        </form>
    </div>
</div>

<div id="upload-material-window-container" class="floating-window-container" style="display: none;">
    <div class="floating-window">
        <span class="close-button">&times;</span>
        <h2>UPLOAD INSTRUCTIONAL MATERIAL</h2>
        <form id="upload-material-form" action="{{ route('materials.store') }}" method="POST" class="floating-window-form" enctype="multipart/form-data">
            @csrf
            <div class="form-group"><label for="material-title">Title:</label><input type="text" id="material-title" name="title" required></div>
            <div class="form-group"><label for="material-description">Description:</label><input type="text" id="material-description" name="description"></div>
            <div class="form-group"><label for="material_file">Upload File:</label><input type="file" id="material_file" name="material_file" required></div>
            <div class="form-actions"><button type="submit" id="submit-material-button" disabled>Upload</button></div>
        </form>
    </div>
</div>

{{-- ====================================================== --}}
{{-- FLOATING WINDOWS --}}
{{-- ====================================================== --}}

<div class="load-more-container">
    <button onclick="goBack()">Back</button>
    <button>Load More +</button>
</div>

<script>
    function goBack() {
        window.history.back();
    }

    document.addEventListener('DOMContentLoaded', function() {
        function setupModal(openBtnId, windowContainerId, formId, submitBtnId) {
            const openButton = document.getElementById(openBtnId);
            const windowContainer = document.getElementById(windowContainerId);
            if (!openButton || !windowContainer) return;

            const closeButton = windowContainer.querySelector('.close-button');
            const form = document.getElementById(formId);
            const submitButton = document.getElementById(submitBtnId);

            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            const categoryCheckboxes = form.querySelectorAll('input[name="category"]');

            function validateForm() {
                let isCategoryValid = true;
                if (formId === 'upload-evaluation-form') {
                    isCategoryValid = form.querySelector('input[name="category"]:checked');
                }

                let areOtherFieldsValid = true;
                inputs.forEach(input => {
                    if (input.type === 'file') {
                        if (input.files.length === 0) areOtherFieldsValid = false;
                    } else {
                        if (!input.value.trim()) areOtherFieldsValid = false;
                    }
                });

                submitButton.disabled = !(isCategoryValid && areOtherFieldsValid);
            }

            //handling for evaluation category checkboxes
            if (categoryCheckboxes.length > 0) {
                const studentCheck = form.querySelector('#category-student');
                const supervisorCheck = form.querySelector('#category-supervisor');
                studentCheck.addEventListener('change', () => {
                    if (studentCheck.checked) supervisorCheck.checked = false;
                    validateForm();
                });
                supervisorCheck.addEventListener('change', () => {
                    if (supervisorCheck.checked) studentCheck.checked = false;
                    validateForm();
                });
            }

            form.addEventListener('input', validateForm);
            openButton.addEventListener('click', (e) => {
                e.preventDefault();
                windowContainer.style.display = 'flex';
            });
            closeButton.addEventListener('click', () => {
                windowContainer.style.display = 'none';
            });
            window.addEventListener('click', (e) => {
                if (e.target == windowContainer) windowContainer.style.display = 'none';
            });

            validateForm();
        }

        // --- SETUP ALL MODALS ---
        setupModal('upload-evaluation-button', 'upload-evaluation-window-container', 'upload-evaluation-form', 'submit-evaluation-button');
        setupModal('upload-material-button', 'upload-material-window-container', 'upload-material-form', 'submit-material-button');
    });
</script>
@endsection