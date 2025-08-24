<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Credential[] $credentials
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Evaluation[] $evaluations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Material[] $materials
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ResearchDocument[] $researchDocuments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PerformanceMetric[] $performanceMetrics
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PromotionApplication[] $promotionApplications
 */

class PageController extends Controller
{
    public function signin()
    {
        return view('auth.signin-page');
    }

    public function showDashboard()
    {
        return view('dashboard');
    }

    public function showApplicationsPage()
    {
        return view('admin.application-page');
    }

    public function showProfilePage()
    {
        $user = Auth::user();

        if ($user) {
            /** @var \App\Models\User $user */ // For IDE
            $user->load('credentials');

            // For the user's own profile page, isOwnProfile will always be true
            $isOwnProfile = true;

            return view('instructor.profile-page', [
                'user' => $user,
                'isOwnProfile' => $isOwnProfile,
            ]);
        } else {
            // Redirect to signin if not logged in
            return redirect()->route('signin-page')->with('error', 'You must be logged in to view your profile.');
        }
    }
    //Controller used for showing review documents page

    public function showReviewDocumentsPage()
    {
        return view('admin.review-documents-page');
    }
    //Controller used for showing evaluations page
    public function showEvaluationsPage()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('signin-page')->with('error', 'You must be logged in to view evaluations.');
        }

        // Ensure $user is an Eloquent model instance
        $userModel = User::find($user->id);
        if ($userModel) {
            $userModel->load('evaluations', 'materials');
            return view('instructor.evaluations-page', [
                'evaluations' => $userModel->evaluations()->latest()->get(),
                'materials' => $userModel->materials()->latest()->get()
            ]);
        } else {
            return redirect()->route('signin-page')->with('error', 'User not found.');
        }
    }
    public function showResearchDocumentsPage(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('signin-page')->with('error', 'You must be logged in to view this page.');
        }

        // Get the search term from the request
        $searchTerm = $request->input('search');

        // Start the query and conditionally apply the search filter
        $research_documents = $user->research()
            ->when($searchTerm, function ($query, $searchTerm) {
                // This entire function only runs if $searchTerm is not null or empty
                return $query->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->where('title', 'like', '%' . $searchTerm . '%')
                        ->orWhere('type', 'like', '%' . $searchTerm . '%')
                        ->orWhere('category', 'like', '%' . $searchTerm . '%');
                });
            })
            ->latest()
            ->get();

        // Pass the final results to the view
        return view('instructor.research-documents-page', [
            'research_documents' => $research_documents
        ]);
    }




    public function showEventParticipationsPage()
    {
        return view('instructor.event-participations-page');
    }

    public function showAllUsersPage()
    {
        $users = User::with('roles.permissions')->get();

        return view('admin.manage-users', compact('users'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/signin');
    }
}
