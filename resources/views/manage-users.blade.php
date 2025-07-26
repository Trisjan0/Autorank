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

<div class="role-modal-container" id="updateRoleModal">
    <div class="role-modal">
        <div class="role-modal-navigation">
            <i class="fa-solid fa-xmark" style="color: #ffffff;" id="closeUpdateRoleModalBtn"></i>
        </div>

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
                        {{-- Roles will be dynamically loaded here by JavaScript --}}
                        @forelse($allRoles as $role)
                        <div class="role-modal-form-check">
                            <input class="role-modal-form-check-input" type="radio" name="role_id" value="{{ $role->id }}" id="modal_role_{{ $role->id }}">
                            <label class="role-modal-form-check-label" for="modal_role_{{ $role->id }}">
                                {{ Str::title(str_replace('_', ' ', $role->name)) }}
                            </label>
                        </div>
                        @empty
                        <p>No roles available.</p>
                        @endforelse
                    </div>
                    <div id="modal-messages" class="mt-2"></div>
                </div>
            </div>
            <div class="role-modal-confirmation">
                <button type="button" id="cancelUpdateRoleBtn">Close</button>
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection