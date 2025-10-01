<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;

class ApplyController extends Controller
{
    /**
     * Performs a pre-check to see if the user has submissions for all KRAs within the current draft application.
     * This is intended to be called via AJAX.
     */
    public function checkSubmissions(Request $request)
    {
        $user = Auth::user();
        $missing = [];

        // Find the user's current draft application
        $draftApplication = $user->applications()->where('status', 'draft')->first();

        if (!$draftApplication) {
            // If there's no draft application, it means they haven't uploaded anything yet for this cycle.
            $missing = [
                ['name' => 'KRA I: Instruction', 'route' => route('instructor.instructional-page')],
                ['name' => 'KRA II: Research', 'route' => route('instructor.research-page')],
                ['name' => 'KRA III: Extension', 'route' => route('instructor.extension-page')],
                ['name' => 'KRA IV: Professional Development', 'route' => route('instructor.professional-development-page')],
            ];
            return response()->json(['success' => false, 'missing' => $missing]);
        }

        // Check for submissions linked to the specific draft application
        if ($draftApplication->instructions()->count() === 0) {
            $missing[] = ['name' => 'KRA I: Instruction', 'route' => route('instructor.instructional-page')];
        }
        if ($draftApplication->researches()->count() === 0) {
            $missing[] = ['name' => 'KRA II: Research', 'route' => route('instructor.research-page')];
        }
        if ($draftApplication->extensions()->count() === 0) {
            $missing[] = ['name' => 'KRA III: Extension', 'route' => route('instructor.extension-page')];
        }
        if ($draftApplication->professionalDevelopments()->count() === 0) {
            $missing[] = ['name' => 'KRA IV: Professional Development', 'route' => route('instructor.professional-development-page')];
        }

        if (empty($missing)) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'missing' => $missing]);
        }
    }

    /**
     * Submits the user's draft application for evaluation.
     */
    public function submitEvaluation(Request $request)
    {
        $user = Auth::user();

        // Check for an existing application that is already pending evaluation to prevent duplicates
        if ($user->applications()->where('status', 'pending evaluation')->exists()) {
            return redirect()->route('profile-page')->with('error', 'You already have an application pending evaluation.');
        }

        // Find the user's draft application
        $draftApplication = $user->applications()->where('status', 'draft')->first();

        if (!$draftApplication) {
            return redirect()->route('profile-page')->with('error', 'No draft application found to submit.');
        }

        // Update the status of the draft application to 'pending evaluation'
        $draftApplication->status = 'pending evaluation';
        $draftApplication->save();

        return redirect()->route('profile-page')->with('success', 'Your CCE documents have been successfully submitted for evaluation!');
    }
}
