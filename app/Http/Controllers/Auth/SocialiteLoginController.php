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
        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/drive.file', // Asks for permission to manage files
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/userinfo.email'
            ])
            ->with(["access_type" => "offline", "prompt" => "consent select_account"]) // Asks for a refresh token
            ->redirect();
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
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('Google authentication failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect('/signin-page')->withErrors('Google authentication failed. Please try again.');
        }

        $originalAvatarUrl = $googleUser->getAvatar();
        $avatarUrl = null;
        if ($originalAvatarUrl) {
            $avatarUrl = preg_replace('/=s\d+(-c)?$/', '=s400-c', $originalAvatarUrl);
            if (strpos($avatarUrl, '=s') === false) {
                $avatarUrl .= '=s400-c';
            }
        }

        $user = User::where('email', $googleUser->email)->first();
        $wasCreated = false;

        if (!$user) {
            $user = User::where('google_id', $googleUser->id)->first();
            if (!$user) {
                $user = User::create([
                    'google_id' => $googleUser->id,
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => bcrypt(Str::random(40)),
                    'avatar' => $avatarUrl,
                    // NEW: Save tokens for new users
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                ]);
                $wasCreated = true;
            }
        }

        if ($user) {
            $user->update([
                'name' => $googleUser->name,
                'google_id' => $googleUser->id, // Ensure google_id is linked if they first signed up with email
                'avatar' => $avatarUrl,
                'google_token' => $googleUser->token, // Always update the short-lived token
                'google_refresh_token' => $googleUser->refreshToken ?? $user->google_refresh_token, // Only update the refresh token if a new one is provided
            ]);
        }

        if ($wasCreated) {
            $user->assignDefaultRoleByEmail();
        }

        $freshUser = User::find($user->id);

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        Auth::login($freshUser);

        return redirect()->route('dashboard');
    }
}
