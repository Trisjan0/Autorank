<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController; //Page Controller import
use Illuminate\Http\Request;

// Route for the initial page load, index page load
Route::get('/', [PageController::class, 'signin'])->name('signin-page');

// Route for the Home Page
Route::get('/home', [PageController::class, 'home'])->name('home');

// Route for the Applications Page
Route::get('/applications', [PageController::class, 'showApplicationsPage'])->name('application-page');

// Route for the Profile Page
Route::get('/profile', [PageController::class, 'showProfilePage'])->name('profile-page');

// Route for the Research Documents Page
Route::get('/research-documents', [PageController::class, 'showResearchDocumentsPage'])->name('research-documents-page');

// Route for the Evaluations Page
Route::get('/evaluations', [PageController::class, 'showEvaluationsPage'])->name('evaluations-page');

// Route for the Evaluations Page
Route::get('/event-participations', [PageController::class, 'showEventParticipationsPage'])->name('event-participations-page');

// Route for Google OAuth
Route::get('/auth/google/redirect', [PageController::class, 'redirectGoogleAuth'])->name('auth.google.redirect');

Route::get('/auth/google/callback', [PageController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Redirect Route after Loggin Out
Route::post('/signin', [PageController::class, 'signin'])->name('logout');
