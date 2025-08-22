<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformanceMetric;
use App\Models\AhpCriterion;
use App\Models\AhpWeight;
use App\Models\User; // Add the User model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstructorMetricsController extends Controller
{
    /**
     * Show the form for entering performance metrics.
     */
    public function showForm()
    {
        $criteria = AhpCriterion::whereNull('parent_id')->get();
        $metrics = Auth::user()->performanceMetrics->pluck('value', 'name');

        return view('instructor.metrics-form', compact('criteria', 'metrics'));
    }

    /**
     * Store the performance metrics submitted by the instructor and calculate their AHP score.
     */
    public function store(Request $request)
    {
        // Define validation rules for the metrics.
        $validatedData = $request->validate([
            'instruction_score' => 'required|numeric|between:0,100',
            'research_count' => 'required|integer|min:0',
            'extension_activities' => 'required|integer|min:0',
            'professional_dev_hours' => 'required|integer|min:0',
        ]);

        $user = Auth::user();

        DB::transaction(function () use ($validatedData, $user) {
            // Delete and store new metrics
            $user->performanceMetrics()->delete();

            $user->performanceMetrics()->create([
                'type' => 'Instruction',
                'name' => 'Instruction Score',
                'value' => $validatedData['instruction_score'],
            ]);

            $user->performanceMetrics()->create([
                'type' => 'Research',
                'name' => 'Research Count',
                'value' => $validatedData['research_count'],
            ]);

            $user->performanceMetrics()->create([
                'type' => 'Extension',
                'name' => 'Extension Activities',
                'value' => $validatedData['extension_activities'],
            ]);

            $user->performanceMetrics()->create([
                'type' => 'Professional Development',
                'name' => 'Professional Development Hours',
                'value' => $validatedData['professional_dev_hours'],
            ]);

            // After saving the metrics, calculate and save the AHP score
            $this->calculateAndSaveAhpScore($user);
        });

        return redirect()->back()->with('success', 'Your performance metrics have been saved and your score has been updated!');
    }

    /**
     * Calculate the AHP score for a given user and save it.
     * This method is a helper to keep the store() method clean.
     *
     * @param \App\Models\User $user
     */
    protected function calculateAndSaveAhpScore(User $user)
    {
        // Define maximum possible scores for normalization. These can be stored in a config file
        // or a dedicated database table later for easier admin management.
        $maxScores = [
            'Instruction Score' => 100, // Score is 0-100
            'Research Count' => 50, // Example: max of 50 publications
            'Extension Activities' => 20, // Example: max of 20 activities
            'Professional Development Hours' => 100, // Example: max of 100 hours
        ];

        // Fetch the performance metrics and AHP weights
        $userMetrics = $user->performanceMetrics->pluck('value', 'name');
        $ahpWeights = AhpWeight::with('criterion')->get()->keyBy(function ($item) {
            return $item->criterion->name;
        })->pluck('weight');

        $finalAhpScore = 0;

        // Iterate through each AHP criterion to calculate the score
        if ($ahpWeights->isNotEmpty() && $userMetrics->isNotEmpty()) {
            foreach ($ahpWeights as $criterionName => $weight) {
                // Find the corresponding metric value
                $metricValue = $userMetrics[$this->getMetricNameForCriterion($criterionName)] ?? 0;

                // Get the max score for normalization
                $maxScore = $maxScores[$this->getMetricNameForCriterion($criterionName)] ?? 1;

                // Normalize the metric value to a 0-1 scale
                $normalizedScore = ($metricValue > 0 && $maxScore > 0) ? min($metricValue / $maxScore, 1) : 0;

                // Add the weighted score to the final total
                $finalAhpScore += ($normalizedScore * $weight);
            }
        }

        // Save the final calculated AHP score to the user's record
        $user->ahp_score = $finalAhpScore;
        $user->save();
    }

    /**
     * Helper method to map AHP criterion names to metric names.
     * This is a simple way to handle the mapping for now.
     */
    protected function getMetricNameForCriterion(string $criterionName): string
    {
        return match ($criterionName) {
            'Instruction' => 'Instruction Score',
            'Research' => 'Research Count',
            'Extension' => 'Extension Activities',
            'Professional Development' => 'Professional Development Hours',
            default => '',
        };
    }
}
