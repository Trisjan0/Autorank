@extends('layouts.view-all-layout')

@section('title', 'Manage Users | Autorank')

@section('content')
<div class="header">
    <h1>Manage Users</h1>
</div>

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

<div class="performance-metric-container">
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>
                    <div class="search-bar-container">
                        <form action="{{ route('manage-users') }}" method="GET" id="search-form">
                            <input type="text" name="search" placeholder="Search.." value="{{ request('search') }}">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass" style="color: #ffffff;"></i></button>
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
                <td colspan="5" style="text-align: center;">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="load-more-container">
    <button onclick="window.history.back()">Back</button>
    {{-- Conditionally display the load more button and add data attributes --}}
    <button id="loadMoreUsersBtn" data-current-offset="{{ $perPage }}"
        @if (!$initialHasMore || $users->isEmpty()) style="display: none;" @endif> {{-- Hide if no more to load initially or no users at all --}}
        Load More +
    </button>
</div>

<div class="custom-modal-overlay" id="updateRoleModal" style="display: none;">
    <div class="custom-modal-content">
        <form id="updateRoleForm" method="POST">
            @csrf
            @method('PUT')
            <div class="custom-modal-header">
                <h5 class="custom-modal-title" id="updateRoleModalLabel">Update Roles for [<span id="modal-user-name"></span>]</h5>
                <button type="button" class="custom-close-button" id="closeUpdateRoleModalBtn" aria-label="Close">&times;</button>
            </div>
            <div class="custom-modal-body">
                <input type="hidden" name="user_id" id="modal-user-id">
                <div id="modal-roles-radio-buttons">
                    {{-- Roles will be dynamically loaded here by JavaScript --}}
                    @forelse($allRoles as $role)
                    <div class="custom-form-check">
                        <input class="custom-form-check-input" type="radio" name="role_id" value="{{ $role->id }}" id="modal_role_{{ $role->id }}">
                        <label class="custom-form-check-label" for="modal_role_{{ $role->id }}">
                            {{ Str::title($role->name) }}
                        </label>
                    </div>
                    @empty
                    <p>No roles available.</p>
                    @endforelse
                </div>
                <div id="modal-messages" class="mt-2"></div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="custom-button custom-button-secondary" id="cancelUpdateRoleBtn">Close</button>
                <button type="submit" class="custom-button custom-button-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection