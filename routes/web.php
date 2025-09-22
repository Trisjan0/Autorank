<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AhpController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Evaluator\EvaluatorController;
use App\Http\Controllers\Instructor\CredentialController;
use App\Http\Controllers\Instructor\EvaluationsController;
use App\Http\Controllers\Instructor\InstructionalMaterialsController;
use App\Http\Controllers\Instructor\ResearchDocumentsController;
use App\Http\Controllers\Instructor\ExtensionServicesController;
use App\Http\Controllers\Instructor\ProfessionalDevelopmentsController;
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
    Route::get('/profile', [CredentialController::class, 'index'])->name('profile-page');

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

        // Route for Managing Users
        Route::get('/manage-users', [UserController::class, 'index'])->name('manage-users');

        // Route for AJAX update of user roles
        Route::put('/manage-users/{user}/update-roles', [UserController::class, 'updateRoles'])->name('manage-users.updateRoles');

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
        Route::get('/evaluator/evaluate-applications', [EvaluatorController::class, 'showEvaluateApplications'])->name('evaluator.evaluate-applications');
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

        // Routes for Credentials
        Route::post('/credentials', [CredentialController::class, 'store'])->name('credentials.store');
        Route::delete('/credentials/{credential}', [CredentialController::class, 'destroy'])->name('credentials.destroy');
        Route::get('/credentials/file-info/{credential}', [CredentialController::class, 'getFileInfoForCredential'])->name('credentials.file-info');
        Route::get('/credentials/file/{credential}', [CredentialController::class, 'viewFileForCredential'])->name('credentials.view-file');

        // Route for KRA I-A: Evaluations Page
        Route::get('/evaluations', [EvaluationsController::class, 'index'])->name('instructor.evaluations-page');
        Route::post('/evaluations', [EvaluationsController::class, 'storeEvaluation'])->name('instructor.evaluations.store');
        Route::get('/evaluations/file-info/{id}', [EvaluationsController::class, 'getFileInfoForEvaluation'])->name('instructor.evaluations.file-info');
        Route::get('/evaluations/file/{id}', [EvaluationsController::class, 'viewFileForEvaluation'])->name('instructor.evaluations.view-file');
        Route::delete('/evaluations/{evaluation}', [EvaluationsController::class, 'destroy'])->name('instructor.evaluations.destroy');

        // Route for KRA I-B: Instructional Materials Page
        Route::get('/instructional-materials', [InstructionalMaterialsController::class, 'index'])->name('instructor.instructional-materials-page');
        Route::post('/instructional-materials', [InstructionalMaterialsController::class, 'store'])->name('instructor.instructional-materials.store');
        Route::get('/instructional-materials/file-info/{id}', [InstructionalMaterialsController::class, 'getFileInfoForMaterial'])->name('instructor.instructional-materials.file-info');
        Route::get('/instructional-materials/file/{id}', [InstructionalMaterialsController::class, 'viewFileForMaterial'])->name('instructor.instructional-materials.view-file');
        Route::delete('/instructional-materials/{material}', [InstructionalMaterialsController::class, 'destroy'])->name('instructor.instructional-materials.destroy');

        // Route for KRA II: Research Documents Page
        Route::get('/research-documents', [ResearchDocumentsController::class, 'index'])->name('instructor.research-documents-page');
        Route::post('/research-documents', [ResearchDocumentsController::class, 'store'])->name('instructor.research-documents.store');
        Route::get('/research-documents/file-info/{id}', [ResearchDocumentsController::class, 'getFileInfoForDocument'])->name('instructor.research-documents.file-info');
        Route::get('/research-documents/file/{id}', [ResearchDocumentsController::class, 'viewFileForDocument'])->name('instructor.research-documents.view-file');
        Route::delete('/research-documents/{document}', [ResearchDocumentsController::class, 'destroy'])->name('instructor.research-documents.destroy');

        // Route for KRA III: Extension Services Page
        Route::get('/extension-services', [ExtensionServicesController::class, 'index'])->name('instructor.extension-services-page');
        Route::post('/extension-services', [ExtensionServicesController::class, 'store'])->name('instructor.extension-services.store');
        Route::get('/extension-services/file-info/{id}', [ExtensionServicesController::class, 'getFileInfoForService'])->name('instructor.extension-services.file-info');
        Route::get('/extension-services/file/{id}', [ExtensionServicesController::class, 'viewFileForService'])->name('instructor.extension-services.view-file');
        Route::delete('/extension-services/{service}', [ExtensionServicesController::class, 'destroy'])->name('instructor.extension-services.destroy');

        // Route for KRA IV: Professional Developments Page
        Route::get('/professional-developments', [ProfessionalDevelopmentsController::class, 'index'])->name('instructor.professional-developments-page');
        Route::post('/professional-developments', [ProfessionalDevelopmentsController::class, 'store'])->name('instructor.professional-developments.store');
        Route::get('/professional-developments/file-info/{id}', [ProfessionalDevelopmentsController::class, 'getFileInfoForDevelopment'])->name('instructor.professional-developments.file-info');
        Route::get('/professional-developments/file/{id}', [ProfessionalDevelopmentsController::class, 'viewFileForDevelopment'])->name('instructor.professional-developments.view-file');
        Route::delete('/professional-developments/{development}', [ProfessionalDevelopmentsController::class, 'destroy'])->name('instructor.professional-developments.destroy');
    });
});
