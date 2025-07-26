{{-- This partial is used to render roles for a user. It expects a $user variable. --}}
@forelse ($user->roles as $role)
<span class="badge bg-primary">
     {{ Str::title(str_replace('_', ' ', $role->name)) }}
</span>
@empty
<span class="badge bg-secondary">No Role</span>
@endforelse