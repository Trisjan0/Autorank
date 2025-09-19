<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AhpController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Evaluator\EvaluatorController;
use App\Http\Controllers\Instructor\InstructorMetricsController;
use App\Http\Controllers\Instructor\EvaluationsController;
use App\Http\Controllers\Instructor\InstructionalMaterialsController;
use App\Http\Controllers\Instructor\ResearchDocumentsController;
use App\Http\Controllers\Instructor\ExtensionServicesController;
use App\Http\Controllers\Instructor\ProfessionalDevelopmentsController;
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
        // Route for KRA I-A: Evaluations Page
        Route::get('/evaluations', [EvaluationsController::class, 'index'])->name('instructor.evaluations-page');
        Route::post('/evaluations', [EvaluationsController::class, 'storeEvaluation'])->name('instructor.evaluations.store');
        Route::get('/evaluations/file/{id}', [EvaluationsController::class, 'viewFile'])->name('instructor.evaluations.view-file');
        Route::get('/evaluations/file-info/{id}', [EvaluationsController::class, 'getFileInfo'])->name('instructor.evaluations.file-info');
        Route::delete('/evaluations/{id}', [EvaluationsController::class, 'destroy'])->name('instructor.evaluations.destroy');

        // Route for KRA I-B: Instructional Materials Page
        Route::get('/instructional-materials', [InstructionalMaterialsController::class, 'index'])->name('instructor.instructional-materials-page');
        Route::post('/instructional-materials', [InstructionalMaterialsController::class, 'store'])->name('instructor.instructional-materials.store');
        Route::get('/instructional-materials/file/{id}', [InstructionalMaterialsController::class, 'viewFile'])->name('instructor.instructional-materials.view-file');
        Route::get('/instructional-materials/file-info/{id}', [InstructionalMaterialsController::class, 'getFileInfo'])->name('instructor.instructional-materials.file-info');
        Route::delete('/instructional-materials/{id}', [InstructionalMaterialsController::class, 'destroy'])->name('instructor.instructional-materials.destroy');

        // Route for KRA II: Research Documents Page
        Route::get('/research-documents', [ResearchDocumentsController::class, 'index'])->name('instructor.research-documents-page');
        Route::post('/research-documents', [ResearchDocumentsController::class, 'store'])->name('instructor.research-documents.store');
        Route::get('/research-documents/file/{id}', [ResearchDocumentsController::class, 'viewFile'])->name('instructor.research-documents.view-file');
        Route::get('/research-documents/file-info/{id}', [ResearchDocumentsController::class, 'getFileInfo'])->name('instructor.research-documents.file-info');
        Route::delete('/research-documents/{id}', [ResearchDocumentsController::class, 'destroy'])->name('instructor.research-documents.destroy');

        // Route for KRA III: Extension Services Page
        Route::get('/extension-services', [ExtensionServicesController::class, 'index'])->name('instructor.extension-services-page');
        Route::post('/extension-services', [ExtensionServicesController::class, 'store'])->name('instructor.extension-services.store');
        Route::get('/extension-services/file/{id}', [ExtensionServicesController::class, 'viewFile'])->name('instructor.extension-services.view-file');
        Route::get('/extension-services/file-info/{id}', [ExtensionServicesController::class, 'getFileInfo'])->name('instructor.extension-services.file-info');
        Route::delete('/extension-services/{id}', [ExtensionServicesController::class, 'destroy'])->name('instructor.extension-services.destroy');

        // Route for KRA IV: Professional Developments Page
        Route::get('/professional-developments', [ProfessionalDevelopmentsController::class, 'index'])->name('instructor.professional-developments-page');
        Route::post('/professional-developments', [ProfessionalDevelopmentsController::class, 'store'])->name('instructor.professional-developments.store');
        Route::get('/professional-developments/file/{id}', [ProfessionalDevelopmentsController::class, 'viewFile'])->name('instructor.professional-developments.view-file');
        Route::get('/professional-developments/file-info/{id}', [ProfessionalDevelopmentsController::class, 'getFileInfo'])->name('instructor.professional-developments.file-info');
        Route::delete('/professional-developments/{id}', [ProfessionalDevelopmentsController::class, 'destroy'])->name('instructor.professional-developments.destroy');
    });
});
