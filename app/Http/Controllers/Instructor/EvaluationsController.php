<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesGoogleDrive;
use App\Models\Evaluation;
use App\Services\DataSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class EvaluationsController extends Controller
{
    use ManagesGoogleDrive;

    public function index(Request $request, DataSearchService $searchService)
    {
        $perPage = 5;
        $query = Evaluation::where('user_id', Auth::id())->orderBy('created_at', 'desc');
        $searchableColumns = ['title', 'category', 'type', 'sub_cat1_score', 'sub_cat2_score'];
        $searchTerm = $request->input('search');
        $searchService->applySearch($query, $searchTerm, $searchableColumns);

        if ($request->ajax()) {
            $offset = $request->input('offset', 0);
            $evaluations = (clone $query)->skip($offset)->take($perPage)->get();
            $html = '';
            foreach ($evaluations as $evaluation) {
                $html .= view('partials._evaluations_table_row', ['evaluation' => $evaluation])->render();
            }
            $totalMatching = (clone $query)->count();
            $hasMore = ($offset + $perPage) < $totalMatching;
            return response()->json([
                'html'       => $html,
                'hasMore'    => $hasMore,
                'nextOffset' => $offset + $perPage,
            ]);
        }

        $totalMatching = (clone $query)->count();
        $evaluations = $query->take($perPage)->get();
        $initialHasMore = ($perPage < $totalMatching);

        return view('instructor.evaluations-page', compact('evaluations', 'initialHasMore', 'perPage'));
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $validCategories = ['Teaching Effectiveness', 'Thesis, Dissertation, and Mentorship Services'];
            $category = $request->input('category');

            $rules = [
                'category' => ['required', 'string', Rule::in($validCategories)],
                'title' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'publish_date' => 'required|date',
                'evaluation_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120',
                'score' => ['nullable', 'numeric', 'min:0'],
            ];

            if ($category === 'Teaching Effectiveness') {
                $existingScore = Evaluation::where('user_id', $user->id)->sum('sub_cat1_score');
                $maxTotal = 60;
                $pointsAvailable = max(0, $maxTotal - $existingScore);
                $rules['score'] = ['required', 'numeric', 'min:0', 'max:' . $pointsAvailable];
            } elseif ($category === 'Thesis, Dissertation, and Mentorship Services') {
                $rules['score'] = ['prohibited'];
            }

            $validatedData = $request->validate($rules, [
                'score.max' => 'The score exceeds the maximum available points for this category.'
            ]);

            $submittedScore = (float)($validatedData['score'] ?? 0);
            $scoreToSave = 0;
            $excessScore = 0;

            if ($category === 'Teaching Effectiveness') {
                $existingScore = Evaluation::where('user_id', $user->id)->sum('sub_cat1_score');
                $maxTotal = 60;
                $pointsAvailable = max(0, $maxTotal - $existingScore);
                $scoreToSave = min($submittedScore, $pointsAvailable);
                $excessScore = $submittedScore - $scoreToSave;
            } elseif ($category === 'Thesis, Dissertation, and Mentorship Services') {
                $scoreToSave = null;
            }

            $googleDriveFileId = $this->uploadFileToGoogleDrive(
                $request,
                'evaluation_file',
                'KRA I-A: Evaluations',
                $category
            );

            $dataToCreate = [
                'user_id' => $user->id,
                'category' => $validatedData['category'],
                'title' => $validatedData['title'],
                'type' => $validatedData['type'],
                'publish_date' => $validatedData['publish_date'],
                'google_drive_file_id' => $googleDriveFileId,
                'filename' => $request->file('evaluation_file')->getClientOriginalName(),
                'sub_cat1_score' => null,
                'sub_cat2_score' => null,
            ];

            if ($category === 'Teaching Effectiveness') {
                $dataToCreate['sub_cat1_score'] = $scoreToSave;
            } elseif ($category === 'Thesis, Dissertation, and Mentorship Services') {
                $dataToCreate['sub_cat2_score'] = null;
            }

            Evaluation::create($dataToCreate);

            if ($category === 'Teaching Effectiveness') {
                $message = 'Evaluation uploaded successfully!';
            } elseif ($category === 'Thesis, Dissertation, and Mentorship Services') {
                $message = 'Evaluation uploaded successfully! This will soon be scored by an Evaluator.';
            }

            if ($excessScore > 0) {
                $message .= " You have reached the maximum score for this category. Excess of " . $excessScore . " points was not added.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Evaluation Upload Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }


    public function destroy(Evaluation $evaluation): JsonResponse
    {
        if ($evaluation->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            if ($evaluation->google_drive_file_id) {
                $this->deleteFileFromGoogleDrive($evaluation->google_drive_file_id);
            }

            $evaluation->delete();

            return response()->json(['message' => 'Evaluation deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Evaluation Deletion Failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete the evaluation. Please try again later.'], 500);
        }
    }

    public function getFileInfoForEvaluation($id)
    {
        return $this->getFileInfo($id, Evaluation::class, 'instructor.evaluations.view-file');
    }

    public function viewFileForEvaluation($id, Request $request)
    {
        return $this->viewFile($id, Evaluation::class, $request);
    }
}
