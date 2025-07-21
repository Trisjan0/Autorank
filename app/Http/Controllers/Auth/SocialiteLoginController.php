<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SocialiteLoginController extends Controller
{
    public function redirectGoogleAuth()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('Google authentication failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect('/login')->withErrors('Google authentication failed. Please try again.');
        }

        $originalAvatarUrl = $googleUser->getAvatar();

        if ($originalAvatarUrl) {
            $avatarUrl = preg_replace('/=s\d+(-c)?$/', '=s400-c', $originalAvatarUrl);

            if (strpos($avatarUrl, '=s') === false) {
                $avatarUrl .= '=s400-c'; // Appends if no size parameter found
            }
        } else {
            $avatarUrl = null;
        }

        $user = User::updateOrCreate(
            ['google_id' => $googleUser->id],
            [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => bcrypt(Str::random(40)),
                'avatar' => $avatarUrl
            ]
        );

        // --- ROLE ASSIGNMENT ---
        $user->assignDefaultRoleByEmail();
        // --- END OF ROLE ASSIGNMENT ---

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
