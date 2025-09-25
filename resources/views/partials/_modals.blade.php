@php
$academicPeriods = $academicPeriods ?? [];
@endphp

{{-- Criterion A: Teaching Effectiveness Upload Modal --}}
<div class="role-modal-container" id="teaching-effectiveness-modal" style="display: none;">
    <div class="role-modal">
        <div class="role-modal-navigation">
            <i class="fa-solid fa-xmark close-modal-btn" style="color: #ffffff;"></i>
        </div>
        {{-- STEP 1: Form Input --}}
        <div class="initial-step">
            <form class="kra-upload-form" action="{{ route('instructor.instruction.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="criterion" value="teaching-effectiveness">
                <div class="role-modal-content">
                    <div class="role-modal-content-header">
                        <h1>Upload Teaching Effectiveness</h1>
                        <p>Fill out the details below. You will be asked to confirm before the file is uploaded.</p>
                    </div>
                    <div class="role-modal-content-body">
                        <div class="form-group">
                            <label class="form-group-title" data-label="Academic Year/Semester">Academic Year / Semester:</label>
                            <select class="select-input" name="academic_period" required>
                                <option value="" disabled selected>Click here to select</option>
                                @isset($academicPeriods)
                                @foreach ($academicPeriods as $period)
                                <option value="{{ $period }}">{{ $period }}</option>
                                @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" data-label="Student Evaluation Score">Student Evaluation Score:</label>
                            <input type="number" name="student_score" style="color-scheme: dark;" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" data-label="Supervisor Evaluation Score">Supervisor Evaluation Score:</label>
                            <input type="number" name="supervisor_score" style="color-scheme: dark;" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" data-label="Student Evaluation Proof">Student Evaluation Proof:</label>
                            <input type="file" name="student_proof" required>
                        </div>
                        <div class="form-group">
                            <label class="form-group-title" data-label="Supervisor Evaluation Proof">Supervisor Evaluation Proof:</label>
                            <input type="file" name="supervisor_proof">
                        </div>
                        <div class="modal-messages mt-2"></div>
                    </div>
                </div>
                <div class="role-modal-actions">
                    <button type="button" class="proceed-btn">Proceed</button>
                </div>
            </form>
        </div>

        {{-- STEP 2: Confirmation --}}
        <div class="confirmation-step" style="display: none;">
            <div class="role-modal-content">
                <div class="role-modal-content-header">
                    <h1>Confirm Upload</h1>
                    <p class="confirmation-message-area"></p>
                </div>
                <div class="role-modal-content-body">
                    <div class="final-status-message-area mt-2"></div>
                </div>
            </div>
            <div class="role-modal-actions">
                <button type="button" class="back-btn">Back</button>
                <button type="button" class="confirm-btn">Confirm & Upload</button>
            </div>
        </div>
    </div>
</div>

{{-- Criterion B: Instructional Materials Upload Modal --}}
<div class="role-modal-container" id="instructional-materials-modal" style="display: none;">
    <div class="role-modal">
        <div class="role-modal-navigation">
            <i class="fa-solid fa-xmark close-modal-btn" style="color: #ffffff;"></i>
        </div>
        {{-- STEP 1: Form Input --}}
        <div class="initial-step">
            <form class="kra-upload-form" action="{{ route('instructor.instruction.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="criterion" value="instructional-materials">
                <div class="role-modal-content">
                    <div class="role-modal-content-header">
                        <h1>Upload Instructional Material</h1>
                        <p>Fill out the details below.</p>
                    </div>
                    <div class="role-modal-content-body">
                        <div class="form-group"><label class="form-group-title" data-label="Title">Title:</label><input type="text" name="title" required></div>
                        <div class="form-group"><label class="form-group-title" data-label="Category">Category:</label><select class="select-input" name="category" required>
                                <option value="" disabled selected>Click here to select</option>
                                <option value="Instructional Material">Instructional Material</option>
                                <option value="Curriculum Development/Revision">Curriculum Development / Revision</option>
                            </select></div>
                        <div class="form-group"><label class="form-group-title" data-label="Type of Material">Type of Material:</label><select class="select-input" name="type">
                                <option value="" disabled selected>Click here to select</option>
                                <option value="Textbook">Textbook</option>
                                <option value="Module">Module</option>
                            </select></div>
                        <div class="form-group"><label class="form-group-title" data-label="Role">Role:</label><select class="select-input" name="role" required>
                                <option value="" disabled selected>Click here to select</option>
                                <option value="Sole Author/Developer">Sole Author / Developer</option>
                                <option value="Co-author/Contributor">Co-author / Contributor</option>
                            </select></div>
                        <div class="form-group"><label class="form-group-title" data-label="Publication/Approval Date">Publication/Approval Date:</label><input type="date" name="publication_date" style="color-scheme: dark;" required></div>
                        <div class="form-group"><label class="form-group-title" data-label="Proof of Output">Proof of Output:</label><input type="file" name="proof_file" required></div>
                        <div class="modal-messages mt-2"></div>
                    </div>
                </div>
                <div class="role-modal-actions"><button type="button" class="proceed-btn">Proceed</button></div>
            </form>
        </div>
        {{-- STEP 2: Confirmation --}}
        <div class="confirmation-step" style="display: none;">
            <div class="role-modal-content">
                <div class="role-modal-content-header">
                    <h1>Confirm Upload</h1>
                    <p class="confirmation-message-area"></p>
                </div>
                <div class="role-modal-content-body">
                    <div class="final-status-message-area mt-2"></div>
                </div>
            </div>
            <div class="role-modal-actions"><button type="button" class="back-btn">Back</button><button type="button" class="confirm-btn">Confirm & Upload</button></div>
        </div>
    </div>
</div>

{{-- Criterion C: Mentorship Services Upload Modal --}}
<div class="role-modal-container" id="mentorship-services-modal" style="display: none;">
    <div class="role-modal">
        <div class="role-modal-navigation"><i class="fa-solid fa-xmark close-modal-btn" style="color: #ffffff;"></i></div>
        {{-- STEP 1: Form Input --}}
        <div class="initial-step">
            <form class="kra-upload-form" action="{{ route('instructor.instruction.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="criterion" value="mentorship-services">
                <div class="role-modal-content">
                    <div class="role-modal-content-header">
                        <h1>Upload Mentorship Service</h1>
                        <p>Fill out the details below.</p>
                    </div>
                    <div class="role-modal-content-body">
                        <div class="form-group"><label class="form-group-title" data-label="Service Type">Service Type:</label><select class="select-input" name="service_type" required>
                                <option value="" disabled selected>Click here to select</option>
                                <option value="Thesis/Dissertation Service">Thesis / Dissertation Service</option>
                                <option value="Competition Mentorship">Competition Mentorship</option>
                            </select></div>
                        <div class="form-group"><label class="form-group-title" data-label="Role">Role:</label><select class="select-input" name="role" required>
                                <option value="" disabled selected>Click here to select</option>
                                <option value="Adviser">Adviser</option>
                                <option value="Panel Member">Panel Member</option>
                                <option value="Mentor">Mentor</option>
                            </select></div>
                        <div class="form-group"><label class="form-group-title" data-label="Student Name(s) or Competition Title">Student Name(s) or Competition Title:</label><input type="text" name="student_or_competition" required></div>
                        <div class="form-group"><label class="form-group-title" data-label="Completion or Award Date">Completion or Award Date:</label><input type="date" name="completion_date" style="color-scheme: dark;" required></div>
                        <div class="form-group"><label class="form-group-title" data-label="Level">Level:</label><select class="select-input" name="level">
                                <option value="" disabled selected>Click here to select</option>
                                <option value="Institutional">Institutional</option>
                                <option value="Regional">Regional</option>
                                <option value="National">National</option>
                                <option value="International">International</option>
                            </select></div>
                        <div class="form-group"><label class="form-group-title" data-label="Proof of Service">Proof of Service:</label><input type="file" name="proof_file" required></div>
                        <div class="modal-messages mt-2"></div>
                    </div>
                </div>
                <div class="role-modal-actions"><button type="button" class="proceed-btn">Proceed</button></div>
            </form>
        </div>
        {{-- STEP 2: Confirmation --}}
        <div class="confirmation-step" style="display: none;">
            <div class="role-modal-content">
                <div class="role-modal-content-header">
                    <h1>Confirm Upload</h1>
                    <p class="confirmation-message-area"></p>
                </div>
                <div class="role-modal-content-body">
                    <div class="final-status-message-area mt-2"></div>
                </div>
            </div>
            <div class="role-modal-actions"><button type="button" class="back-btn">Back</button><button type="button" class="confirm-btn">Confirm & Upload</button></div>
        </div>
    </div>
</div>