<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesGoogleDrive;
use App\Models\Evaluation;
use App\Services\DataSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class EvaluationsController extends Controller
{
    use ManagesGoogleDrive;

    /**
     * Display a paginated list of evaluations.
     */
    public function index(Request $request, DataSearchService $searchService)
    {
        $perPage = 5;
        $query = Evaluation::where('user_id', Auth::id())->orderBy('created_at', 'desc');
        $searchableColumns = ['title', 'category', 'score'];
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

    /**
     * Store a new evaluation and return a JSON response.
     */
    public function storeEvaluation(Request $request): JsonResponse
    {
        // Sanitize publish_date input
        if ($request->input('publish_date') === '') {
            $request->merge(['publish_date' => null]);
        }

        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'category' => 'required|string|in:student,supervisor',
                'title' => 'required|string|max:255',
                'publish_date' => 'nullable|date',
                'score' => 'required|numeric|min:0',
                'evaluation_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120', // 5MB max
            ]);

            // Upload the file to Google Drive and get the file ID
            $googleDriveFileId = $this->uploadFileToGoogleDrive($request, 'evaluation_file', 'KRA I-A: Evaluations');

            // Create the new Evaluation record
            $evaluation = Evaluation::create(array_merge($validatedData, [
                'user_id' => Auth::id(),
                'google_drive_file_id' => $googleDriveFileId,
                'filename' => $request->file('evaluation_file')->getClientOriginalName(), // Store original filename
            ]));

            // Render the partial view for the new table row
            $newRowHtml = view('partials._evaluations_table_row', ['evaluation' => $evaluation])->render();

            // Return a successful JSON response with the new row's HTML
            return response()->json([
                'success' => true,
                'message' => 'Evaluation uploaded successfully!',
                'newRowHtml' => $newRowHtml
            ], 201);
        } catch (ValidationException $e) {
            // Return validation errors
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log the error for debugging and return a generic error message
            Log::error('Evaluation Upload Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified evaluation.
     *
     * @param \App\Models\Evaluation $evaluation The evaluation instance to delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Evaluation $evaluation): JsonResponse
    {
        // Authorization check: Ensure the user owns the evaluation.
        if ($evaluation->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the associated file from Google Drive if it exists.
            if ($evaluation->google_drive_file_id) {
                $this->deleteFileFromGoogleDrive($evaluation->google_drive_file_id);
            }

            // Delete the evaluation record from the database.
            $evaluation->delete();

            // Return a successful JSON response.
            return response()->json(['message' => 'Evaluation deleted successfully.']);
        } catch (\Exception $e) {
            // Log the specific error for debugging purposes.
            Log::error('Evaluation Deletion Failed: ' . $e->getMessage());

            // Return a generic, user-friendly error message.
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
