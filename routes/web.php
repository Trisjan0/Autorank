<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AhpController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Evaluator\EvaluationController;
use App\Http\Controllers\Instructor\CredentialController;
use App\Http\Controllers\Instructor\InstructionController;
use App\Http\Controllers\Instructor\ResearchController;
use App\Http\Controllers\Instructor\ExtensionController;
use App\Http\Controllers\Instructor\ProfessionalDevelopmentController;
use App\Http\Controllers\Instructor\ApplyController;
use App\Http\Controllers\PageController;
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

        // Route for Managing User Roles
        Route::get('/manage-users', [UserController::class, 'index'])->name('manage-users');
        Route::put('/manage-users/{user}/update-roles', [UserController::class, 'updateRoles'])->name('manage-users.updateRoles');

        // Route for Managing User Roles
        Route::get('/manage-faculty-ranks', [UserController::class, 'manageFacultyRanks'])->name('manage-faculty-ranks');
        Route::put('/users/{user}/update-faculty-rank', [UserController::class, 'updateFacultyRank'])->name('users.update-faculty-rank');

        // Route for AJAX update of user roles

        // Route for viewing a specific user's profile
        Route::get('/users/{user}', [UserController::class, 'show'])->name('user.profile');

        // Routes for AHP Weights
        Route::get('/ahp/weights', [AhpController::class, 'showWeightsForm'])->name('ahp.weights.form');
        Route::post('/ahp/weights', [AhpController::class, 'updateWeights'])->name('ahp.weights.update');

        // Routes for toggling position availability
        Route::patch('/positions/{position}/toggle', [PositionController::class, 'toggle'])->name('admin.positions.toggle');
    });

    /*
    |--------------------------------------------------------------------------
    | Evaluator Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'permission:access evaluate applications page'])->group(function () {
        Route::prefix('evaluator')->name('evaluator.')->group(function () {
            // Main dashboard for viewing all pending applications
            Route::get('/applications', [EvaluationController::class, 'index'])->name('applications.dashboard');

            // View the summary of a single application
            Route::get('/application/{application}', [EvaluationController::class, 'showApplication'])->name('application.details');

            // View the specific submissions for one KRA within an application
            Route::get('/application/{application}/kra/{kra_slug}', [EvaluationController::class, 'showApplicationKra'])->name('application.kra');

            // Endpoint for saving a score for a submission
            Route::post('/application/score/{kra_slug}/{submission_id}', [EvaluationController::class, 'scoreSubmission'])->name('submission.score');

            // Endpoint to trigger the final score calculation
            Route::post('/application/{application}/calculate-score', [EvaluationController::class, 'calculateFinalScore'])->name('application.calculate-score');
        });
    });


    /*
    |--------------------------------------------------------------------------
    | Instructor Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:super_admin|user'])->group(function () {
        // Route for Checking Eligibility Before Application
        Route::get('/application/check-completeness', [ApplyController::class, 'checkCompleteness'])->name('application.check-completeness');
        Route::post('/application/submit/{position}', [ApplyController::class, 'submit'])->name('application.submit');

        Route::prefix('instructor')->name('instructor.')->group(function () {
            // KRA I: Instruction Routes
            Route::get('/instruction', [InstructionController::class, 'index'])->name('instructional-page');
            Route::post('/instruction', [InstructionController::class, 'store'])->name('instruction.store');
            Route::delete('/instruction/{instruction}', [InstructionController::class, 'destroy'])->name('instruction.destroy');
            Route::get('/instruction/{id}/file-info', [InstructionController::class, 'getFileInfo'])->name('instruction.file-info');
            Route::get('/instruction/{id}/view-file', [InstructionController::class, 'viewFile'])->name('instruction.view-file');

            // KRA II: Research Routes
            Route::get('/research', [ResearchController::class, 'index'])->name('research-page');
            Route::post('/research', [ResearchController::class, 'store'])->name('research.store');
            Route::delete('/research/{research}', [ResearchController::class, 'destroy'])->name('research.destroy');
            Route::get('/research/{id}/file-info', [ResearchController::class, 'getFileInfo'])->name('research.file-info');
            Route::get('/research/{id}/view-file', [ResearchController::class, 'viewFile'])->name('research.view-file');

            // KRA III: Extension & Community Involvement Routes
            Route::get('/extension', [ExtensionController::class, 'index'])->name('extension-page');
            Route::post('/extension', [ExtensionController::class, 'store'])->name('extension.store');
            Route::delete('/extension/{extension}', [ExtensionController::class, 'destroy'])->name('extension.destroy');
            Route::get('/extension/{id}/file-info', [ExtensionController::class, 'getFileInfo'])->name('extension.file-info');
            Route::get('/extension/{id}/view-file', [ExtensionController::class, 'viewFile'])->name('extension.view-file');

            // KRA IV: Professional Development Routes
            Route::get('/professional-development', [ProfessionalDevelopmentController::class, 'index'])->name('professional-development-page');
            Route::post('/professional-development', [ProfessionalDevelopmentController::class, 'store'])->name('professional-development.store');
            Route::delete('/professional-development/{professionalDevelopment}', [ProfessionalDevelopmentController::class, 'destroy'])->name('professional-development.destroy');
            Route::get('/professional-development/{id}/file-info', [ProfessionalDevelopmentController::class, 'getFileInfo'])->name('professional-development.file-info');
            Route::get('/professional-development/{id}/view-file', [ProfessionalDevelopmentController::class, 'viewFile'])->name('professional-development.view-file');
        });
    });
});
