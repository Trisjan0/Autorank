@extends('layouts.dashboard-layout')

@section('title', 'Dashboard | Autorank')

@if(session('success'))
<div class="server-alert-success">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="server-alert-danger">
    {{ session('error') }}
</div>
@endif

@section('content')
<div class="main-content-container">
    <div class="available-positions-container">
        <div class="available-positions-header">
            @role('admin')
            <h1>Manage Faculty Ranks</h1>
            <div class="legend">
                <span class="legend-item"><span class="legend-color available"></span> Available</span>
                <span class="legend-item"><span class="legend-color unavailable"></span> Unavailable</span>
            </div>
            @else
        <h1>Open Faculty Ranks</h1>
        @endrole
    </div>
    <div class="available-positions-body-grid">
        @forelse ($positions as $position)
        @role('admin')
        <div class="position-card" id="position-card-{{ $position->id }}">
            <div class="card-header">
                <h3>{{ $position->title }}</h3>
                <span id="badge-{{ $position->id }}" class="status-indicator {{ $position->is_available ? 'available' : 'unavailable' }}"></span>
            </div>
            <div class="card-body">
                <p class="slots">Available Slots: <span id="slots-{{ $position->id }}">{{ $position->available_slots }}</span></p>
            </div>
            <div class="card-footer-admin">
                @if($position->is_available)
                <button
                    id="toggle-button-{{ $position->id }}"
                    class="toggle-button set-unavailable confirm-action-btn"
                    data-url="{{ route('admin.positions.toggle', $position) }}"
                    data-action-url="{{ route('admin.positions.toggle', $position) }}"
                    data-method="PATCH"
                    data-modal-title="Confirm Action"
                    data-modal-text="Are you sure you want to set the &quot;{{ $position->title }}&quot; rank to unavailable?"
                    data-confirm-button-text="Confirm">
                    Set Unavailable
                </button>
                @else
                <button
                    id="toggle-button-{{ $position->id }}"
                    class="toggle-button set-available"
                    data-id="{{ $position->id }}"
                    data-url="{{ route('admin.positions.toggle', $position) }}">
                    Set Available
                </button>
                @endif
            </div>
        </div>
        @else
        {{-- REGULAR USER VIEW --}}
        <div class="position-card">
            <div class="card-header">
                <h3>{{ $position->title }}</h3>
            </div>
            <div class="card-body">
                <p class="slots">Available Slots: {{ $position->available_slots }}</p>
            </div>
            <div class="card-footer-user">
                <div class="dropdown">
                    <button class="details-button">Requirements</button>
                    <div class="dropdown-content">
                        @if(is_array($position->requirements))
                        @foreach($position->requirements as $key => $value)
                        <p><strong>{{ $key }}:</strong> {{ $value }}%</p>
                        @endforeach
                        @else
                        <p>No requirements specified.</p>
                        @endif
                    </div>
                </div>
                @unlessrole('evaluator')
                <button class="apply-button apply-btn" data-position-id="{{ $position->id }}">
                    <span class="button-text">Apply</span>
                    <div class="mini-loader-container button-loader" style="display: none;">
                        <div class="loader3">
                            <div class="circle1"></div>
                            <div class="circle1"></div>
                            <div class="circle1"></div>
                        </div>
                    </div>
                </button>
                @endunlessrole
            </div>
        </div>
        @endrole
        @empty
        <p class="no-positions">No open faculty ranks at this time.</p>
        @endforelse
    </div>
</div>

{{-- SLOTS MODAL --}}
<div class="role-modal-container" id="slots-modal" style="display: none;">
    <div class="role-modal">
        <div class="role-modal-navigation">
            <i class="fa-solid fa-xmark" style="color: #ffffff;" id="slots-modal-close-btn"></i>
        </div>

        {{-- STEP 1: Form Input --}}
        <div id="slots-modal-initial-step">
            <div class="role-modal-content">
                <div class="role-modal-content-header">
                    <h1>Set Available Slots</h1>
                    <p>Please specify the number of available slots for this position.</p>
                </div>
                <div class="role-modal-content-body">
                    <div class="form-group">
                        <label class="form-group-title" for="available-slots-input">Available Slots:</label>
                        <input type="number" id="available-slots-input" style="color-scheme: dark;" min="1" value="1" class="form-control mt-2">
                    </div>
                    <div id="slots-modal-messages" class="mt-2"></div>
                </div>
            </div>
            <div class="role-modal-actions">
                <button type="button" id="slots-proceed-to-confirmation-btn">Proceed</button>
            </div>
        </div>

        {{-- STEP 2: Confirmation --}}
        <div id="slots-modal-confirmation-step" style="display: none;">
            <div class="role-modal-content">
                <div class="role-modal-content-header">
                    <h1>Confirm Slots</h1>
                    <p id="slots-confirmation-message-area"></p>
                </div>
                <div class="role-modal-content-body">
                    <div id="slots-final-status-message-area" class="mt-2"></div>
                </div>
            </div>
            <div class="role-modal-actions">
                <button type="button" class="btn btn-info" id="slots-back-to-selection-btn">Back</button>
                <button type="button" class="btn btn-success" id="slots-confirm-btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

{{-- CONFIRMATION MODAL --}}
<div class="modal-container" id="setUnavailableConfirmationModal" style="display: none;">
    <div class="delete-modal-box">
        <div class="delete-modal-header">
            <i class="fa-solid fa-xmark" id="closeSetUnavailableModalBtn"></i>
        </div>
        <div class="delete-modal-body">
            <h1 class="delete-modal-title">Confirm Action</h1>
            <p id="deleteModalText">Are you sure you want to set this position to unavailable?</p>
            <div id="set-unavailable-final-status-message-area" class="mt-2"></div>
        </div>
        <div class="delete-modal-actions">
            <button type="button" class="btn-cancel" id="cancelSetUnavailableBtn">Cancel</button>
            <button type="button" class="btn-delete" id="confirmSetUnavailableBtn">Confirm</button>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="{{ asset('js/modal-scripts.js') }}"></script>
<script src="{{ asset('js/dashboard-scripts.js') }}"></script>
@endpush