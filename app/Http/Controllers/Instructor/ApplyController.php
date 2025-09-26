<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Credential;
use App\Models\Instruction;
use App\Models\Research;
use App\Models\Extension;
use App\Models\ProfessionalDevelopment;
use App\Models\Position;
use App\Models\Application;
use App\Models\ApplicationSubmission;
use Illuminate\Support\Facades\Log;

class ApplyController extends Controller
{
    /**
     * Check if the authenticated user has uploaded all required documents.
     */
    public function checkCompleteness(Request $request)
    {
        $user = Auth::user();
        $missing = [];

        if (Credential::where('user_id', $user->id)->count() === 0) {
            $missing[] = 'Credentials';
        }
        if (Instruction::where('user_id', $user->id)->count() === 0) {
            $missing[] = 'KRA I: Instruction';
        }
        if (Research::where('user_id', $user->id)->count() === 0) {
            $missing[] = 'KRA II: Research';
        }
        if (Extension::where('user_id', $user->id)->count() === 0) {
            $missing[] = 'KRA III: Extension';
        }
        if (ProfessionalDevelopment::where('user_id', $user->id)->count() === 0) {
            $missing[] = 'KRA IV: Professional Development';
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

        // Validation 1: Position availability.
        if (!$position->is_available || $position->available_slots <= 0) {
            return response()->json(['success' => false, 'message' => 'Sorry, this position is no longer available.'], 400);
        }

        // Validation 2: Existing application.
        $existingApplication = Application::where('user_id', $user->id)
            ->where('position_id', $position->id)
            ->whereIn('status', ['Pending Evaluation', 'Evaluated']) // Check for active applications
            ->exists();

        if ($existingApplication) {
            return response()->json(['success' => false, 'message' => 'You already have an active application for this position.'], 409);
        }

        // --- CORE LOGIC WRAPPED IN A DATABASE TRANSACTION FOR SAFETY ---
        try {
            DB::transaction(function () use ($user, $position) {
                // Step 1: Create the main application record.
                $application = Application::create([
                    'user_id' => $user->id,
                    'position_id' => $position->id,
                    'status' => 'Pending Evaluation',
                ]);

                // Step 2: "Snapshot" all submissions by linking them in the pivot table.
                $submissionTypes = [
                    Instruction::class,
                    Research::class,
                    Extension::class,
                    ProfessionalDevelopment::class,
                ];

                foreach ($submissionTypes as $modelClass) {
                    // Get all submissions for this user that are not yet part of another application.
                    $submissions = $modelClass::where('user_id', $user->id)
                        ->where('status', 'For Submission')
                        ->get();

                    foreach ($submissions as $submission) {
                        // Create the link in our pivot table.
                        ApplicationSubmission::create([
                            'application_id' => $application->id,
                            'submission_id' => $submission->id,
                            'submission_type' => $modelClass,
                        ]);

                        // Update the status of the original submission to "lock" it to this application.
                        $submission->status = 'Under Review';
                        $submission->save();
                    }
                }

                // Step 3: Update the position availability.
                $position->decrement('available_slots');
                if ($position->available_slots <= 0) {
                    $position->is_available = false;
                    $position->save();
                }
            });
        } catch (\Exception $e) {
            Log::error('Application Submission Failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred during submission. Please try again.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully! Reloading the page...',
            'redirect_url' => route('dashboard')
        ]);
    }
}
