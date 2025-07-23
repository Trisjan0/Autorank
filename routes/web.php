<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\Auth\SocialiteLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public Routes (Accessible without login)
|--------------------------------------------------------------------------
*/

// Route for the initial page load, index page load
Route::get('/', [PageController::class, 'signin'])->name('signin-page');

// Route for Google OAuth Redirect
Route::get('/auth/google/redirect', [SocialiteLoginController::class, 'redirectGoogleAuth'])->name('auth.google.redirect');

// Route for Google OAuth Callback
Route::get('/auth/google/callback', [SocialiteLoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Accessible only to logged-in users)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Route for the Home Page
    Route::get('/dashboard', [PageController::class, 'showDashboard'])->name('dashboard');

    // Route for the Profile Page
    Route::get('/profile', [PageController::class, 'showProfilePage'])->name('profile-page');

    // Route for the System Settings
    Route::get('/settings', [SystemSettingsController::class, 'showSystemSettings'])->name('system-settings');

    //Route for Logging Out
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('signin-page');
    })->name('logout');

    // Route to send OTP (THIS IS FOR DEMO ONLY - WILL CHANGE BEFORE DEPLOYMENT)
    Route::post('/profile/send-otp', [ProfileController::class, 'sendOtpForPhoneNumber'])->name('profile.send_otp'); // Ensure ProfileController here

    // Route to verify OTP and save phone number (THIS IS FOR DEMO ONLY - WILL CHANGE BEFORE DEPLOYMENT)
    Route::post('/profile/verify-phone-otp', [ProfileController::class, 'verifyOtpAndSavePhoneNumber'])->name('profile.verify_otp_save_phone');

    /*
    |--------------------------------------------------------------------------
    | Admin Only Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role_or_permission:admin|access applications page|view research documents|view evaluations|view event participations'])->group(function () {
        // Route for the Review Applications Page
        Route::get('/applications', [PageController::class, 'showApplicationsPage'])->name('application-page');

        // Route for the Research Documents Page
        Route::get('/research-documents', [PageController::class, 'showResearchDocumentsPage'])->name('research-documents-page');

        // Route for the Evaluations Page
        Route::get('/evaluations', [PageController::class, 'showEvaluationsPage'])->name('evaluations-page');

        // Route for the Event Participations Page
        Route::get('/event-participations', [PageController::class, 'showEventParticipationsPage'])->name('event-participations-page');
    });
});
