<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Credential;
use App\Models\Evaluation;
use App\Models\Material;
use App\Models\ResearchDocument;
use App\Models\ExtensionService;
use App\Models\ProfessionalDevelopment;
use App\Models\Position;
use App\Models\Application;

class ApplyController extends Controller
{
    /**
     * Check if the authenticated user has uploaded all required documents.
     */
    public function checkCompleteness(Request $request)
    {
        $user = Auth::user();
        $missing = [];

        // Check for at least one credential
        if (Credential::where('user_id', $user->id)->count() < 2) {
            $missing[] = 'Credentials (at least 2)';
        }
        // Check for KRA I-A
        if (Evaluation::where('user_id', $user->id)->count() === 0) {
            $missing[] = 'KRA I-A: Evaluations';
        }
        // Check for KRA I-B
        if (Material::where('user_id', $user->id)->count() === 0) {
            $missing[] = 'KRA I-B: Instructional Materials';
        }
        // Check for KRA II
        if (ResearchDocument::where('user_id', $user->id)->count() === 0) {
            $missing[] = 'KRA II: Research Documents';
        }
        // Check for KRA III
        if (ExtensionService::where('user_id', $user->id)->count() === 0) {
            $missing[] = 'KRA III: Extension Services';
        }
        // Check for KRA IV
        if (ProfessionalDevelopment::where('user_id', $user->id)->count() === 0) {
            $missing[] = 'KRA IV: Professional Developments';
        }

        if (empty($missing)) {
            return response()->json(['complete' => true, 'message' => 'All required documents are present.']);
        }

        return response()->json([
            'complete' => false,
            'missing' => $missing,
            'message' => 'You have missing documents in the following categories.'
        ]);
    }

    /**
     * Submit the user's application for a specific position.
     */
    public function submit(Request $request, Position $position)
    {
        $user = Auth::user();

        // Validation 1: Check if the position is still available.
        if (!$position->is_available || $position->available_slots <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, this position is no longer available.'
            ], 400);
        }

        // Validation 2: Check if the user has already applied for this specific position.
        $existingApplication = Application::where('user_id', $user->id)
            ->where('position_id', $position->id)
            ->exists();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this position.'
            ], 409); // 409 Conflict status code
        }

        // All checks passed, create the application.
        Application::create([
            'user_id' => $user->id,
            'position_id' => $position->id,
            'applicant_name' => $user->name, // Snapshot the user's name
            'applicant_current_rank' => $user->rank ?? 'Not Specified', // Snapshot the user's rank
            'status' => 'submitted',
        ]);

        // Decrement the available slots for the position
        $position->decrement('available_slots');

        // If slots are now zero, mark the position as unavailable
        if ($position->available_slots <= 0) {
            $position->is_available = false;
            $position->save();
        }

        // Return a success response with a redirect URL
        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully! Reloading the page...',
            'redirect_url' => route('dashboard')
        ]);
    }
}
