<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function signin()
    {
        return view('signin-page');
    }

    public function showDashboard()
    {
        return view('dashboard');
    }

    public function showApplicationsPage()
    {
        return view('application-page');
    }

    public function showProfilePage()
    {
        $user = Auth::user();

        if ($user) {
            /** @var \App\Models\User $user */
            $user->load('credentials');
        } else {
            return redirect('/signin')->with('error', 'You must be logged in to view your profile.');
        }

        return view('profile-page', compact('user'));
    }

    public function showResearchDocumentsPage()
    {
        return view('research-documents-page');
    }

    public function showEvaluationsPage()
    {
        return view('evaluations-page');
    }

    public function showEventParticipationsPage()
    {
        return view('event-participations-page');
    }

    public function redirectGoogleAuth()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->user();

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
                'password' => Str::password(12),
                'avatar' => $avatarUrl
            ]
        );

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/signin');
    }
}
