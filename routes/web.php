<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AhpController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Evaluator\EvaluatorController;
use App\Http\Controllers\Instructor\InstructorMetricsController;
use App\Http\Controllers\Instructor\EvaluationsController;
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
Route::get('/', [PageController::class, 'signin'])->name('login');

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

    // Routes for Instructor Metrics
    Route::get('/my-metrics', [InstructorMetricsController::class, 'showForm'])->name('instructor.metrics.form');
    Route::post('/my-metrics', [InstructorMetricsController::class, 'store'])->name('instructor.metrics.store');

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
    Route::middleware(['role_or_permission:admin|access applications page'])->group(function () {
        // Route for the Review Applications Page
        Route::get('/applications', [PageController::class, 'showApplicationsPage'])->name('application-page'); // this can be deleted

        Route::get('/applications', [ApplicationController::class, 'index'])->name('application-page');

        // Route for the Review Documents Page
        Route::get('/review-documents', [PageController::class, 'showReviewDocumentsPage'])->name('review-documents-page');

        // Route for Managing Users
        Route::get('/manage-users', [UserController::class, 'index'])->name('manage-users');

        // Route for AJAX update of user roles
        Route::put('/manage-users/{user}/update-roles', [UserController::class, 'updateRoles'])->name('manage-users.updateRoles');

        // Route for viewing a specific user's profile
        Route::get('/users/{user}', [UserController::class, 'show'])->name('user.profile');

        // Routes for AHP Weights
        Route::get('/ahp/weights', [AhpController::class, 'showWeightsForm'])->name('ahp.weights.form');
        Route::post('/ahp/weights', [AhpController::class, 'updateWeights'])->name('ahp.weights.update');

        // Route to store created material
        Route::post('/materials', [InstructorMetricsController::class, 'storeMaterial'])->name('materials.store');

        // Route to store created research document
        Route::post('/research-documents', [InstructorMetricsController::class, 'storeResearchDocument'])->name('research-documents.store');

        // Route to store created research document
        Route::post('/extension-services', [InstructorMetricsController::class, 'storeExtensionService'])->name('extension-services.store');

        //route to store created professional development
        Route::post('/professional-developments', [InstructorMetricsController::class, 'storeProfessionalDevelopment'])->name('professional-developments.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Evaluator Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'permission:access evaluate applications page'])->group(function () {
        Route::get('/evaluator/evaluate-applications', [EvaluatorController::class, 'showEvaluateApplications'])->name('evaluator.evaluate-applications');
    });

    /*
    |--------------------------------------------------------------------------
    | Instructor Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:super_admin|user'])->group(function () {
        // Route for the KRA I-A: Evaluations Page
        Route::get('/evaluations', [EvaluationsController::class, 'index'])->name('instructor.evaluations-page');
        // Route to store created evaluation
        Route::post('/evaluations', [EvaluationsController::class, 'storeEvaluation'])->name('instructor.evaluations.store');

        // Route for the KRA I-B: Instructional Materials Page
        Route::get('/instructional-materials', [PageController::class, 'showInstructionalMaterialsPage'])->name('instructor.instructional-materials-page');

        // Route for the KRA II: Research Documents Page
        Route::get('/research-outputs', [PageController::class, 'showResearchDocumentsPage'])->name('instructor.research-documents-page');

        // Route for the KRA III: Extension Services Page
        Route::get('/extension-services', [PageController::class, 'showExtensionServicesPage'])->name('instructor.extension-services-page');

        // Route for the KRA IV: Professional Developments Page
        Route::get('/professional-developments', [PageController::class, 'showProfessionalDevelopmentsPage'])->name('instructor.professional-developments-page');
    });
});
