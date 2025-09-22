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
        if (Credential::where('user_id', $user->id)->count() === 0) {
            $missing[] = 'Credentials';
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

        //
        // --- TODO: DATABASE LOGIC GOES HERE ---
        // 1. Check if user has already applied for this position.
        // 2. Create a new record in the 'applications' table.
        //

        // For now, just return a success message.
        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully!',
            // 'redirect_url' => route('some.confirmation.page') // Optional: redirect after success
        ]);
    }
}
