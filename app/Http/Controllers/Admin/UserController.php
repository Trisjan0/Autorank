<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

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

            // Also fetch all roles to pass to the modal for selection
            $allRoles = Role::all();

            // Determine initial hasMore for the "Load More" button
            $totalUsersMatchingSearch = $query->count(); // Count total matching users for initial check
            $initialHasMore = ($perPage < $totalUsersMatchingSearch);

            return view('manage-users', compact('users', 'allRoles', 'initialHasMore', 'perPage')); // Pass perPage as initialOffset
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
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => [
                'integer',
                Rule::exists('roles', 'id'),
            ],
        ]);

        $user->syncRoles($request->input('roles', []));

        // Clear Spatie's permission cache
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // If the user whose role was updated is currently logged in, log them out
        // This ensures they get a fresh session and re-evaluate their roles on next login.
        if (Auth::check() && Auth::id() === $user->id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                // If AJAX request, return a JSON response with the redirect URL
                return response()->json([
                    'success' => true,
                    'message' => 'Your role has been updated. Please sign in again.',
                    'redirect_url' => route('signin-page'), // This URL will be used by your JavaScript
                ], 200);
            } else {
                // If it's a regular (non-AJAX) request, perform a server-side redirect
                return redirect()->route('signin-page')->with('message', 'Your role has been updated. Please sign in again.');
            }
        }

        if ($request->expectsJson()) {
            $user->load('roles'); // Reload user with updated roles for the response
            return response()->json([
                'success' => true,
                'message' => 'User roles updated successfully!',
                'newRolesHtml' => view('partials._roles_badge', ['user' => $user])->render(),
            ]);
        }

        // Fallback for non-AJAX requests
        return redirect()->route('manage-users')->with('success', 'User roles updated successfully!');
    }


    /**
     * Display the specified user's profile.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user->load('roles.permissions');
        return view('user.profile', compact('user'));
    }
}
