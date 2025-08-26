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
                <th>Category</th>
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
                <td><button>Edit</button></td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">No evaluations found.</td>
            </tr>
            @endforelse
            {{-- CORRECTED: Static row for uploading a new evaluation --}}
            <tr>
                <td colspan="6">&nbsp;</td>
                <td><button id="upload-evaluation-button" class="upload-new-button">Upload New</button></td>
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
                <th>Category</th>
                <th>Date</th>
                <th>Type</th>
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
                <td colspan="6">&nbsp;</td>
                <td><button id="upload-material-button" class="upload-new-button">Upload New</button></td>
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

<script>
    function goBack() {
        window.history.back();
    }

    document.addEventListener('DOMContentLoaded', function() {

        // --- Setup for Evaluation Modal ---
        const evalModal = {
            openBtn: document.getElementById('upload-evaluation-button'),
            window: document.getElementById('upload-evaluation-window-container'),
            form: document.getElementById('upload-evaluation-form'),
            submitBtn: document.getElementById('submit-evaluation-button'),
            studentCheck: document.getElementById('category-student'),
            supervisorCheck: document.getElementById('category-supervisor'),
            inputs: document.querySelectorAll('#eval-title, #eval-date, #eval-score, #evaluation_file')
        };

        function validateEvalForm() {
            const isCategorySelected = evalModal.studentCheck.checked || evalModal.supervisorCheck.checked;
            let areInputsFilled = true;
            evalModal.inputs.forEach(input => {
                if (input.type === 'file') {
                    if (input.files.length === 0) areInputsFilled = false;
                } else if (!input.value.trim()) {
                    areInputsFilled = false;
                }
            });
            evalModal.submitBtn.disabled = !(isCategorySelected && areInputsFilled);
        }

        if (evalModal.openBtn) {
            evalModal.openBtn.addEventListener('click', (e) => {
                e.preventDefault();
                evalModal.window.style.display = 'flex';
            });
            evalModal.window.querySelector('.close-button').addEventListener('click', () => {
                evalModal.window.style.display = 'none';
            });
            evalModal.form.addEventListener('input', validateEvalForm);
            evalModal.studentCheck.addEventListener('change', () => {
                if (evalModal.studentCheck.checked) evalModal.supervisorCheck.checked = false;
                validateEvalForm();
            });
            evalModal.supervisorCheck.addEventListener('change', () => {
                if (evalModal.supervisorCheck.checked) evalModal.studentCheck.checked = false;
                validateEvalForm();
            });
        }

        // --- Setup for Material Modal ---
        const materialModal = {
            openBtn: document.getElementById('upload-material-button'),
            window: document.getElementById('upload-material-window-container'),
            form: document.getElementById('upload-material-form'),
            submitBtn: document.getElementById('submit-material-button'),
            soleAuthorCheck: document.getElementById('category-sole-author'),
            coAuthorCheck: document.getElementById('category-co-author'),
            inputs: document.querySelectorAll('#material-title, #material-date, #material-type, #material_file')
        };

        function validateMaterialForm() {
            const isCategorySelected = materialModal.soleAuthorCheck.checked || materialModal.coAuthorCheck.checked;
            let areInputsFilled = true;
            materialModal.inputs.forEach(input => {
                if (input.type === 'file') {
                    if (input.files.length === 0) areInputsFilled = false;
                } else if (!input.value.trim()) {
                    areInputsFilled = false;
                }
            });
            materialModal.submitBtn.disabled = !(isCategorySelected && areInputsFilled);
        }

        if (materialModal.openBtn) {
            materialModal.openBtn.addEventListener('click', (e) => {
                e.preventDefault();
                materialModal.window.style.display = 'flex';
            });
            materialModal.window.querySelector('.close-button').addEventListener('click', () => {
                materialModal.window.style.display = 'none';
            });
            materialModal.form.addEventListener('input', validateMaterialForm);
            materialModal.soleAuthorCheck.addEventListener('change', () => {
                if (materialModal.soleAuthorCheck.checked) materialModal.coAuthorCheck.checked = false;
                validateMaterialForm();
            });
            materialModal.coAuthorCheck.addEventListener('change', () => {
                if (materialModal.coAuthorCheck.checked) materialModal.soleAuthorCheck.checked = false;
                validateMaterialForm();
            });
        }

        // --- General click outside to close ---
        window.addEventListener('click', (e) => {
            if (evalModal.window && e.target == evalModal.window) evalModal.window.style.display = 'none';
            if (materialModal.window && e.target == materialModal.window) materialModal.window.style.display = 'none';
        });
    });
</script>
@endsection