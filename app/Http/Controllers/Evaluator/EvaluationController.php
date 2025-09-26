<?php

namespace App\Http\Controllers\Evaluator;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationSubmission;
use App\Models\Instruction;
use App\Models\Research;
use App\Models\Extension;
use App\Models\ProfessionalDevelopment;
use App\Services\AHPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    /**
     * Display the main dashboard for evaluators, showing all applications pending evaluation.
     */
    public function index()
    {
        // Fetch applications that are ready for evaluation, along with the applicant's info.
        $applications = Application::with('user', 'position')
            ->where('status', 'Pending Evaluation')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('evaluator.applications-dashboard', compact('applications'));
    }

    /**
     * Display the summary/details of a single application.
     */
    public function showApplication(Application $application)
    {
        // Eager load relationships for efficiency
        $application->load('user', 'position');

        // Get the counts of submissions for each KRA from the pivot table
        $submissionCounts = ApplicationSubmission::where('application_id', $application->id)
            ->select('submission_type', DB::raw('count(*) as total'))
            ->groupBy('submission_type')
            ->pluck('total', 'submission_type');

        // Map the counts to a more friendly key for the view
        $kraCounts = [
            'KRA I: Instruction' => $submissionCounts[Instruction::class] ?? 0,
            'KRA II: Research' => $submissionCounts[Research::class] ?? 0,
            'KRA III: Extension' => $submissionCounts[Extension::class] ?? 0,
            'KRA IV: Professional Development' => $submissionCounts[ProfessionalDevelopment::class] ?? 0,
        ];

        return view('evaluator.application-details', compact('application', 'kraCounts'));
    }

    /**
     * Display the list of specific submissions for a given KRA within an application.
     */
    public function showApplicationKra(Application $application, string $kra_slug)
    {
        $kraModelMap = [
            'instruction' => Instruction::class,
            'research' => Research::class,
            'extension' => Extension::class,
            'professional-development' => ProfessionalDevelopment::class,
        ];

        if (!array_key_exists($kra_slug, $kraModelMap)) {
            abort(404, 'KRA not found.');
        }

        $modelClass = $kraModelMap[$kra_slug];

        // 1. Get the IDs of the submissions linked to this application for this specific KRA model.
        $submissionIds = ApplicationSubmission::where('application_id', $application->id)
            ->where('submission_type', $modelClass)
            ->pluck('submission_id');

        // 2. Fetch only those specific submissions from the corresponding KRA table.
        $submissions = $modelClass::whereIn('id', $submissionIds)->get();

        // 3. Pass the data onto the blade for view
        return view('evaluator.kra-evaluation-page', [
            'application' => $application,
            'kra_slug' => $kra_slug,
            'submissions' => $submissions,
        ]);
    }

    /**
     * Store the score for a single submission.
     */
    public function scoreSubmission(Request $request, string $kra_slug, int $submission_id)
    {
        $request->validate(['score' => 'required|numeric|min:0']);

        $kraModelMap = [
            'instruction' => Instruction::class,
            'research' => Research::class,
            'extension' => Extension::class,
            'professional-development' => ProfessionalDevelopment::class,
        ];

        if (!array_key_exists($kra_slug, $kraModelMap)) {
            return response()->json(['message' => 'Invalid KRA specified.'], 400);
        }

        $modelClass = $kraModelMap[$kra_slug];
        $submission = $modelClass::find($submission_id);

        if (!$submission) {
            return response()->json(['message' => 'Submission not found.'], 404);
        }

        try {
            $submission->score = $request->input('score');
            $submission->status = 'Scored';
            $submission->save();

            return response()->json(['success' => true, 'message' => 'Score saved successfully.']);
        } catch (\Exception $e) {
            Log::error("Failed to save score for {$kra_slug} submission {$submission_id}: " . $e->getMessage());
            return response()->json(['message' => 'An error occurred while saving the score.'], 500);
        }
    }

    /**
     * Calculate the final AHP score for the application.
     *
     * @param Application $application
     * @param AHPService $ahpService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function calculateFinalScore(Application $application, AHPService $ahpService)
    {
        try {
            DB::transaction(function () use ($application, $ahpService) {
                // Step 1: Sum up all individual scores and save them to the application record.
                $this->updateApplicationKraScores($application);

                // Step 2: Reload the application model to get the freshly saved KRA scores.
                $application->refresh();

                // Step 3: Use the AHPService to calculate the final weighted score.
                $finalScore = $ahpService->calculateGeneralScore($application);

                // Step 4: Update the application with the final score and new status.
                $application->final_score = $finalScore;
                $application->status = 'Evaluated'; // Or 'Pending Admin Review'
                $application->save();
            });

            return redirect()->route('evaluator.applications.show', $application->id)
                ->with('success', 'Final score has been calculated successfully!');
        } catch (\Exception $e) {
            Log::error("Failed to calculate final score for Application ID {$application->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during the final score calculation.');
        }
    }

    /**
     * Helper function to sum scores from individual submissions and update the application model.
     *
     * @param Application $application
     */
    private function updateApplicationKraScores(Application $application)
    {
        $kraModelMap = [
            'kra1_score' => Instruction::class,
            'kra2_score' => Research::class,
            'kra3_score' => Extension::class,
            'kra4_score' => ProfessionalDevelopment::class,
        ];

        $totalScores = [];

        foreach ($kraModelMap as $scoreColumn => $modelClass) {
            // Get all submission IDs for this application and KRA type
            $submissionIds = ApplicationSubmission::where('application_id', $application->id)
                ->where('submission_type', $modelClass)
                ->pluck('submission_id');

            // Sum the 'score' column for those submissions
            $totalScores[$scoreColumn] = $modelClass::whereIn('id', $submissionIds)->sum('score');
        }

        // Update the application record with the summed scores
        $application->update($totalScores);
    }
}
