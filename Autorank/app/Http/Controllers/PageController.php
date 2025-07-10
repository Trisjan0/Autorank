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

    public function home()
    {
        return view('home');
    }

    public function showApplicationsPage()
    {
        return view('application-page');
    }

    public function showProfilePage()
    {
        return view('profile-page');
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

        $user = User::updateOrCreate(
            ['google_id' => $googleUser->id],
            [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Str::password(12)
            ]
        );

        Auth::login($user);

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/signin');
    }
}
