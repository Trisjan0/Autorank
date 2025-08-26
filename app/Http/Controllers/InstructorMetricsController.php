<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformanceMetric;
use App\Models\Evaluation;
use App\Models\Material;
use App\Models\AhpCriterion;
use App\Models\AhpWeight;
use App\Models\User;
use Illuminate\Support\Facades\Log;
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
     * Store created evaluation
     */
    public function storeEvaluation(Request $request)
    {
        try {
            $request->validate([
                'category' => 'required|string|in:student,supervisor',
                'title' => 'required|string|max:255',
                'date' => 'required|date',
                'score' => 'required|numeric|min:0',
                'evaluation_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|min:100|max:5120',
            ]);

            $filePath = null;
            if ($request->hasFile('evaluation_file')) {
                $filePath = $request->file('evaluation_file')->store('evaluations', 'public');
            }

            Evaluation::create([
                'user_id' => Auth::id(),
                'category' => $request->category,
                'title' => $request->title,
                'date' => $request->date,
                'score' => $request->score,
                'file_path' => $filePath,
            ]);

            return redirect()->route('evaluations-page')->with('success', 'Evaluation uploaded successfully!');
        } catch (\Exception $e) {
            Log::error('Error uploading evaluation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was a problem uploading your evaluation. Please try again.');
        }
    }
    //store material
    public function storeMaterial(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'material_file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,jpg,png|max:10240', // Max 10MB
            ]);

            $filePath = null;
            if ($request->hasFile('material_file')) {
                // Store the file in 'storage/app/public/materials'
                $filePath = $request->file('material_file')->store('materials', 'public');
            }

            Material::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $filePath,
            ]);

            return redirect()->route('evaluations-page')->with('success', 'Material uploaded successfully!');
        } catch (\Exception $e) {
            Log::error('Error uploading material: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was a problem uploading your material. Please try again.');
        }
    }


    /**
     * Calculate the AHP score for a given user and save it.
     */
    protected function calculateAndSaveAhpScore(User $user)
    {
        $maxScores = [
            'Instruction Score' => 100,
            'Research Count' => 50,
            'Extension Activities' => 20,
            'Professional Development Hours' => 100,
        ];

        $userMetrics = $user->performanceMetrics->pluck('value', 'name');
        $ahpWeights = AhpWeight::with('criterion')->get()->keyBy(function ($item) {
            return $item->criterion->name;
        })->pluck('weight');

        $finalAhpScore = 0;

        if ($ahpWeights->isNotEmpty() && $userMetrics->isNotEmpty()) {
            foreach ($ahpWeights as $criterionName => $weight) {
                $metricValue = $userMetrics[$this->getMetricNameForCriterion($criterionName)] ?? 0;
                $maxScore = $maxScores[$this->getMetricNameForCriterion($criterionName)] ?? 1;
                $normalizedScore = ($metricValue > 0 && $maxScore > 0) ? min($metricValue / $maxScore, 1) : 0;
                $finalAhpScore += ($normalizedScore * $weight);
            }
        }

        $user->ahp_score = $finalAhpScore;
        $user->save();
    }

    /**
     * Helper method to map AHP criterion names to metric names.
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
