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
            <h1>Manage Job Positions</h1>
            @else
            <h1>Available Positions</h1>
            @endrole
        </div>
        <div class="available-positions-body">
            <ul>
                @forelse ($positions as $position)
                @role('admin')
                {{-- ADMIN VIEW --}}
                <li>
                    <span>{{ $position->title }}</span>
                    <div class="admin-controls">
                        <span id="badge-{{ $position->id }}" class="status-badge {{ $position->is_available ? 'available' : 'unavailable' }}">
                            {{ $position->is_available ? 'Available' : 'Unavailable' }}
                        </span>

                        <button
                            id="toggle-button-{{ $position->id }}"
                            class="toggle-button {{ $position->is_available ? 'set-unavailable' : 'set-available' }}"
                            data-url="{{ route('admin.positions.toggle', $position) }}">
                            {{ $position->is_available ? 'Set Unavailable' : 'Set Available' }}
                        </button>
                    </div>
                </li>
                @else
                {{-- REGULAR USER VIEW --}}
                @if ($position->is_available)
                <li>
                    <span>{{ $position->title }}</span>
                    <div class="actions-container">
                        <div class="dropdown">
                            <button class="details-button">Requirements</button>
                            <div class="dropdown-content">
                                <h4>Requirements</h4>
                                @if(is_array($position->requirements))
                                @foreach($position->requirements as $key => $value)
                                <p><strong>{{ $key }}:</strong> {{ $value }}%</p>
                                @endforeach
                                @else
                                <p>No requirements specified.</p>
                                @endif
                            </div>
                        </div>
                        <button class="apply-button">Apply</button>
                    </div>
                </li>
                @endif
                @endrole
                @empty
                <li>
                    <span>No positions to display at this time.</span>
                </li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="overall-completion-overview-container">
        <div class="research-publication-container">
            <div class="research-publication-top-part">
                <h1>Research Publication</h1><i class="fa-solid fa-ellipsis-vertical"></i>
            </div>
            <div class="research-publication-bottom-part">
                <div class="percentage-container">
                    <h1>90%</h1>
                </div>
                <div class="progress-bar-container">
                    <div class="research-publication-progress"></div>
                    <div class="bar"></div>
                </div>
            </div>
        </div>
        <div class="event-participation-container">
            <div class="event-participation-top-part">
                <h1>Event Participation</h1><i class="fa-solid fa-ellipsis-vertical"></i>
            </div>
            <div class="event-participation-bottom-part">
                <div class="percentage-container">
                    <h1>30%</h1>
                </div>
                <div class="progress-bar-container">
                    <div class="event-participation-progress"></div>
                    <div class="bar"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="online-faculty-evaluation-container">
        <div class="online-faculty-evaluation-top-part">
            <h1>Online Evaluation of Faculty</h1>
            <h1>2nd Semester A.Y. 2023-2024</h1>
        </div>
        <div class="online-faculty-evaluation-bottom-part">
            <button>View</button>
        </div>
    </div>
    <div class="latest-research-title-container">
        <div class="latest-research-title-header">
            <h1>Your Latest Research Title</h1>
        </div>
        <div class="latest-research-title-table-container">
            <table>
                <tbody>
                    <tr>
                        <td><b>Title: </b>Lorem ipsum dolor sit amet, consectetur adipscing elit</td>
                        <td><b>Status: </b>Ongoing</td>
                        <td><b>Date Published: </b>N/A</td>
                        <td><button>Upload</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="{{ asset('js/dashboard-scripts.js') }}"></script>
@endpush