<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\PermissionRegistrar;

class SocialiteLoginController extends Controller
{
    /**
     * Redirects the user to the Google authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectGoogleAuth()
    {
        // Use Socialite to redirect to Google's OAuth consent screen.
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handles the callback from Google after the user has authenticated.
     * This method receives the user's data from Google.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Attempt to retrieve the user's information from Google.
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            // If authentication fails (e.g., user denies access, network issue), log the error.
            Log::error('Google authentication failed: ' . $e->getMessage(), ['exception' => $e]);
            // Redirect back to the login page with an error message.
            return redirect('/signin-page')->withErrors('Google authentication failed. Please try again.');
        }

        // Process the user's avatar URL to get a specific size (s400-c for 400px square, cropped)
        $originalAvatarUrl = $googleUser->getAvatar();
        $avatarUrl = null;
        if ($originalAvatarUrl) {
            // Use regex to replace or append size parameter to the Google avatar URL
            $avatarUrl = preg_replace('/=s\d+(-c)?$/', '=s400-c', $originalAvatarUrl);
            if (strpos($avatarUrl, '=s') === false) {
                $avatarUrl .= '=s400-c'; // Appends if no size parameter was found at all
            }
        }

        // Find or create the user in your database based on their Google ID.
        // Manually check for existence to get the $wasCreated flag accurately.
        $user = User::where('email', $googleUser->email)->first(); // 1. Try to find user by email FIRST
        $wasCreated = false;

        if (!$user) {
            // User not found by email, check by google_id
            $user = User::where('google_id', $googleUser->id)->first();

            if (!$user) {
                // If user still not found by either email or google_id, create a new user
                $user = User::create([
                    'google_id' => $googleUser->id,
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    // Assign a random, hashed password as it won't be used for direct login.
                    'password' => bcrypt(Str::random(40)),
                    'avatar' => $avatarUrl
                ]);
                $wasCreated = true; // Set flag to true as the user is new
            }
        }

        // If user was found by email but doesn't have a google_id, link it
        if ($user && empty($user->google_id)) {
            $user->google_id = $googleUser->id;
            // Also update name/avatar in case they changed on Google
            $user->name = $googleUser->name;
            $user->avatar = $avatarUrl;
            $user->save();
        } else if ($user) {
            // If user exists and has google_id, just update name/avatar in case they changed on Google
            $user->update([
                'name' => $googleUser->name,
                'avatar' => $avatarUrl
            ]);
        }

        // --- Role Assignment Logic ---
        // This ensures the default role is assigned ONLY when a user registers for the first time
        // via Google OAuth. If an admin manually changes a user's role, this login process
        // will NOT overwrite that change.
        if ($wasCreated) {
            $user->assignDefaultRoleByEmail();
        }

        // --- Ensure Fresh User Data on Login ---
        // Reload the user model from the database to guarantee that the authenticated user
        // object contains the very latest data, including any role changes made by an admin.
        $freshUser = User::find($user->id);
        $freshUser = User::find($user->id);

        // --- Clear Spatie Permissions Cache ---
        // This is a crucial step to ensure that Spatie's internal cache of permissions
        // and roles is invalidated. Any subsequent check for permissions/roles will
        // then fetch the fresh data from the database. This is a safeguard
        // even if cleared on role update, ensuring the logging-in user gets accurate data.
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        Auth::login($freshUser);

        return redirect()->route('dashboard');
    }
}
