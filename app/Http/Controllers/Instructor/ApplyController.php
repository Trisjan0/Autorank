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
     * Submit the user's application for merit promotion.
     */
    public function submit()
    {
        // to do
    }
}
