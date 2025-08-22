<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display user table with search bar. Limits initial rows to 5.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = 5; // Number of users to load initially and per "load more"

        $query = User::with('roles.permissions')->orderBy('created_at', 'desc'); // Always order by latest

        // Apply search filter if present
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%'); // Added ID search
            });
        }

        if ($request->ajax()) {
            // This part handles AJAX requests for "Load More"
            $offset = $request->input('offset', 0); // Get the current offset from the request

            $users = $query->skip($offset)->take($perPage)->get();

            // Render new rows using a partial Blade view
            $html = '';
            foreach ($users as $user) {
                $html .= view('partials._user_table_row', ['user' => $user])->render();
            }

            // Determine if there are more users to load (for the "Load More" button)
            $totalUsersMatchingSearch = $query->count(); // Count only matching users if searching

            $hasMore = ($offset + $perPage) < $totalUsersMatchingSearch;

            return response()->json([
                'html' => $html,
                'hasMore' => $hasMore,
                'nextOffset' => $offset + $perPage
            ]);
        } else {
            // This part handles the initial page load
            $users = $query->take($perPage)->get(); // Get only the first 'perPage' users

            // Fetch all roles by rank to pass to the modal for selection
            $allRoles = Role::orderBy('rank', 'asc')->get();

            // Determine initial hasMore for the "Load More" button
            $totalUsersMatchingSearch = $query->count(); // Count total matching users for initial check
            $initialHasMore = ($perPage < $totalUsersMatchingSearch);

            return view('admin.manage-users', compact('users', 'allRoles', 'initialHasMore', 'perPage'));
        }
    }

    /**
     * Update the specified user's roles in storage via AJAX.
     * This method will be called by the modal form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRoles(Request $request, User $user)
    {
        // 1. Validation: Ensure at least one role is selected and it exists.
        $request->validate([
            'roles' => 'required|array|min:1', // A user must have at least one role
            'roles.*' => [
                'integer',
                Rule::exists('roles', 'id'), // Ensure the role ID exists in the 'roles' table
            ],
        ], [
            'roles.required' => 'At least one role must be selected.',
            'roles.min' => 'At least one role must be selected.',
            'roles.*.exists' => 'The selected role is invalid.',
        ]);

        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();
        $assignedRoleIds = $request->input('roles', []);

        // Get the ID of the specific role being assigned (assuming single role assignment based on radio buttons)
        $targetRoleId = $assignedRoleIds[0] ?? null;

        // Retrieve the Role model for the target role
        $targetRole = null;
        if ($targetRoleId) {
            $targetRole = Role::findById($targetRoleId);
        }

        // Defensive check
        if (!$targetRole) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid role selected for assignment.',
            ], 400); // Bad Request
        }

        // Retrieve the specific role objects for comparison
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        // --- START: HIERARCHY-BASED AUTHORIZATION ---
        // A. Super Admin's role cannot be changed by anyone.
        if ($user->hasRole('super_admin')) {
            return response()->json([
                'message' => 'Cannot change the role of a Super Admin.',
            ], 403); // Forbidden
        }

        // B. General Authorization: Only 'admin' or 'super_admin' can update roles via this endpoint.
        // This acts as a first line of defense if non-authorized users somehow hit this endpoint.
        if (!$loggedInUser->hasAnyRole(['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update roles.',
            ], 403); // Forbidden
        }

        // C. Specific Hierarchy Restrictions
        // If the logged-in user is an 'admin' (but NOT a 'super_admin'):
        // They cannot assign the 'super_admin' role.
        if ($loggedInUser->hasRole('admin') && !$loggedInUser->hasRole('super_admin')) {
            if ($superAdminRole && $targetRole->id === $superAdminRole->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to assign the Super Admin role.',
                ], 403); // Forbidden
            }
            // Furthermore, an 'admin' should only be able to assign 'admin' or 'user' roles.
            // If the target role is neither 'admin' nor 'user', prevent it.
            if ($targetRole->id !== $adminRole->id && $targetRole->id !== $userRole->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'As an Admin, you can only assign "Admin" or "User" roles.',
                ], 403); // Forbidden
            }
            // An Admin cannot change another existing Admin's role
            if ($loggedInUser->id !== $user->id && $user->hasRole('admin')) {
                return response()->json([
                    'message' => 'You cannot change the role of another Admin.',
                ], 403); // Forbidden
            }
            // An Admin cannot demote themselves
            if ($loggedInUser->id === $user->id && $targetRole->id !== $adminRole->id) {
                return response()->json([
                    'message' => 'You are an Admin. You cannot demote yourself. A Super Admin must change your role.',
                ], 403); // Forbidden
            }
        }

        // C. Super Admin Authorization: If we reach here and the user is 'super_admin',
        //     they are implicitly allowed to assign any valid role. No further checks needed.

        // --- END: HIERARCHY-BASED AUTHORIZATION ---

        // Sync (update) the user's roles
        $user->syncRoles($assignedRoleIds);

        // Update role assignment timestamp and assignee
        $user->role_assigned_at = Carbon::now();
        $user->role_assigned_by = $loggedInUser->email;
        $user->save(); // Save the user model to persist these changes

        // Clear Spatie's permission cache to ensure new roles are recognized immediately
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // If the user whose role was updated is currently logged in, log them out.
        // This is crucial for their session to reflect the new roles immediately.
        if (Auth::check() && Auth::id() === $user->id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => 'Your role has been updated. Please sign in again.',
                'redirect_url' => route('signin-page'), // This URL will be used by your JavaScript
            ], 200);
        }

        // If another user's role was updated (not the logged-in user)
        // This is the common success path for managing other users
        return response()->json([
            'success' => true,
            'message' => 'User\'s role updated successfully!',
            // Pass new HTML for the roles badge to dynamically update the table on the frontend
            'newRolesHtml' => view('partials._roles_badge', ['user' => $user->fresh()])->render(), // Use fresh() to ensure reloaded roles
            // Pass new formatted values for the 'role_assigned_at' and 'role_assigned_by' columns
            'newRoleAssignedAt' => $user->role_assigned_at->timezone('Asia/Manila')->format('F d, Y | h:iA'),
            'newRoleAssignedBy' => $user->role_assigned_by,
        ]);
    }

    /**
     * Display the specified user's profile.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // Load roles and permissions for the profile user
        $user->load('roles.permissions');

        // Determine if the currently authenticated user is viewing their own profile
        // This is crucial for differentiating between "My Profile" and "Other User's Profile" views
        $isOwnProfile = (Auth::check() && Auth::id() === $user->id);

        // Return the existing 'profile-page' view, passing both the target user
        // and the flag indicating if it's the authenticated user's own profile.
        return view('instructor.profile-page', [
            'user' => $user, // The user object for the profile being displayed
            'isOwnProfile' => $isOwnProfile, // True if the logged-in user is viewing their own profile
        ]);
    }
}
