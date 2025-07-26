<tr id="user-row-{{ $user->id }}">
    <td>{{ $user->id }}</td>
    <td>{{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td id="roles-{{ $user->id }}">
        @include('partials._roles_badge', ['user' => $user])
    </td>
    <td>
        <div class="action-container">
            <button class="update-role-btn"
                data-user-id="{{ $user->id }}"
                data-user-name="{{ $user->name }}"
                data-user-roles="{{ json_encode($user->roles->pluck('id')->toArray()) }}">
                Update Role
            </button>
            <a href="{{ route('user.profile', $user->id) }}">
                <button>View Profile</button>
            </a>
        </div>
    </td>
</tr>