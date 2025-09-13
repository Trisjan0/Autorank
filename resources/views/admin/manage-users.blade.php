@extends('layouts.view-all-layout')

@section('title', 'Manage Users | Autorank')

@section('content')
@if (session('success'))
<div class="server-alert-success">
    {{ session('success') }}
</div>
@endif
@if (session('error'))
<div class="server-alert-danger">
    {{ session('error') }}
</div>
@endif

<div class="header">
    <h1>Manage Users</h1>
</div>

<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Role Assigned At</th>
                <th>Role Assigned By</th>
                <th>
                    <div class="search-bar-container">
                        <form action="{{ route('manage-users') }}" method="GET" id="search-form">
                            <input type="text" name="search" placeholder="Search users..." value="{{ request('search') }}">
                            <button type="submit">
                                <i class="fa-solid fa-magnifying-glass" id="search-btn-icon" style="color: #ffffff;"></i>
                            </button>
                        </form>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody id="user-table-body">
            @forelse($users as $user)
            @include('partials._user_table_row', ['user' => $user])
            @empty
            <tr id="no-users-row">
                <td colspan="7" style="text-align: center;">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="load-more-container">
    <button onclick="window.history.back()">Back</button>
    <button id="loadMoreUsersBtn" data-current-offset="{{ $perPage }}"
        @if (!$initialHasMore || $users->isEmpty()) style="display: none;" @endif>
        Load More +
    </button>
</div>

{{-- UPDATE ROLE MODAL --}}
<div class="role-modal-container" id="updateRoleModal">
    <div class="role-modal">
        <div class="role-modal-navigation">
            <i class="fa-solid fa-xmark" style="color: #ffffff;" id="closeUpdateRoleModalBtn"></i>
        </div>

        {{-- STEP 1: Initial Role Selection --}}
        <div id="updateRoleInitialStep">
            <form id="updateRoleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="role-modal-content">
                    <div class="role-modal-content-header">
                        <h1>Update Roles for [ <span id="modal-user-name"></span> ]</h1>
                        <p>Select a new role for this user. Assigning a new role will update their permissions accordingly.</p>
                    </div>
                    <div class="role-modal-content-body">
                        <input type="hidden" name="user_id" id="modal-user-id">
                        <div id="modal-roles-radio-buttons">
                            @forelse($allRoles as $role)
                            <div class="role-modal-form-check">
                                <input class="role-modal-form-check-input" type="radio" name="role_id" value="{{ $role->id }}" id="modal_role_{{ $role->id }}" data-role-name="{{ Str::title(str_replace('_', ' ', $role->name)) }}">
                                <label class="role-modal-form-check-label" for="modal_role_{{ $role->id }}">
                                    {{ Str::title(str_replace('_', ' ', $role->name)) }}
                                </label>
                            </div>
                            @empty
                            <p>No roles available.</p>
                            @endforelse
                        </div>
                        <div id="role-modal-message" class="mt-2"></div>
                    </div>
                </div>
                <div class="role-modal-actions">
                    <button type="submit" id="proceedToConfirmationBtn">Proceed</button>
                </div>
            </form>
        </div>

        {{-- STEP 2: Confirmation --}}
        <div id="updateRoleConfirmationStep" style="display: none;">
            <div class="role-modal-content">
                <div class="role-modal-content-header">
                    <h1>Confirm Role Update</h1>
                    <p id="confirmationMessageArea"></p>
                </div>
                <div class="role-modal-content-body">
                    <div id="finalStatusMessageArea" class="mt-2"></div>
                </div>
            </div>
            <div class="role-modal-actions">
                <button type="button" class="btn btn-info" id="backToSelectionBtn">Back</button>
                <button type="button" class="btn btn-success" id="confirmUpdateRoleBtn">Update</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="{{ asset('js/manage-users-scripts.js') }}"></script>
@endpush