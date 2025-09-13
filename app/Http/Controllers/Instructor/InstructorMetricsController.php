<?php

namespace App\Http\Controllers\Instructor;

use Illuminate\Http\Request;
use App\Models\PerformanceMetric;
use App\Models\Evaluation;
use App\Models\Material;
use App\Models\ResearchDocument;
use App\Models\ExtensionService;
use App\Models\ProfessionalDevelopment;
use App\Models\AhpCriterion;
use App\Models\AhpWeight;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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

    //store research document
    public function storeResearchDocument(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string|in:Book,Monograph,Journal,Chapter',
                'title' => 'required|string|max:255',
                'date' => 'required|date',
                'category' => 'required|string|max:255',
                'document_file' => 'required|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
            ]);

            $filePath = null;
            if ($request->hasFile('document_file')) {
                // Store the file in 'storage/app/public/research_documents'
                $filePath = $request->file('document_file')->store('research_documents', 'public');
            }

            ResearchDocument::create([
                'user_id' => Auth::id(),
                'type' => $request->type,
                'title' => $request->title,
                'date' => $request->date,
                'category' => $request->category,
                'file_path' => $filePath,
            ]);

            return redirect()->route('research-documents-page')->with('success', 'Research document uploaded successfully!');
        } catch (\Exception $e) {
            Log::error('Error uploading research document: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was a problem uploading your document. Please try again.');
        }
    }
    //store extension service
    public function storeExtensionService(Request $request)
    {
        try {
            $request->validate([
                'service_type' => 'required|string|in:Institution,Community,Extension Involvement',
                'title' => 'required|string|max:255',
                'date' => 'required|date',
                'evidence_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:10240', // Max 10MB
            ]);

            $filePath = null;
            if ($request->hasFile('evidence_file')) {
                // Store the file in 'storage/app/public/extension_services'
                $filePath = $request->file('evidence_file')->store('extension_services', 'public');
            }

            ExtensionService::create([
                'user_id' => Auth::id(),
                'service_type' => $request->service_type,
                'title' => $request->title,
                'date' => $request->date,
                'file_path' => $filePath,
            ]);

            return redirect()->route('extension-services-page')->with('success', 'Extension service uploaded successfully!');
        } catch (\Exception $e) {
            Log::error('Error uploading extension service: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was a problem uploading your evidence. Please try again.');
        }
    }

    //store professional development
    public function storeProfessionalDevelopment(Request $request)
    {
        try {
            $request->validate([
                'category' => 'required|string|in:Professional Organization Involvement,Continuing Development,Awards,Experience',
                'title' => 'required|string|max:255',
                'date' => 'required|date',
                'evidence_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:10240', // Max 10MB
            ]);

            $filePath = null;
            if ($request->hasFile('evidence_file')) {
                // Store the file in 'storage/app/public/professional_developments'
                $filePath = $request->file('evidence_file')->store('professional_developments', 'public');
            }

            ProfessionalDevelopment::create([
                'user_id' => Auth::id(),
                'category' => $request->category,
                'title' => $request->title,
                'date' => $request->date,
                'file_path' => $filePath,
            ]);

            return redirect()->route('professional-developments-page')->with('success', 'Professional development evidence uploaded successfully!');
        } catch (\Exception $e) {
            Log::error('Error uploading professional development evidence: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was a problem uploading your evidence. Please try again.');
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
