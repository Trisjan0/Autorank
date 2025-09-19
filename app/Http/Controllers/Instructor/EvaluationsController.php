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
     * Store a new evaluation.
     */
    public function storeEvaluation(Request $request): JsonResponse
    {
        if ($request->input('publish_date') === '') {
            $request->merge(['publish_date' => null]);
        }

        try {
            $validatedData = $request->validate([
                'category' => 'required|string|in:student,supervisor',
                'title' => 'required|string|max:255',
                'publish_date' => 'nullable|date',
                'score' => 'required|numeric|min:0',
                'evaluation_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            ]);

            $googleDriveFileId = $this->uploadFileToGoogleDrive($request, 'evaluation_file', 'KRA I-A: Evaluations');

            Evaluation::create(array_merge($validatedData, [
                'user_id' => Auth::id(),
                'google_drive_file_id' => $googleDriveFileId,
            ]));

            return response()->json(['message' => 'Evaluation uploaded successfully!'], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified evaluation.
     */
    public function destroy($id): JsonResponse
    {
        $evaluation = Evaluation::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        try {
            if ($evaluation->google_drive_file_id) {
                $this->deleteFileFromGoogleDrive($evaluation->google_drive_file_id);
            }
            $evaluation->delete();
            return response()->json(['message' => 'Evaluation deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete the evaluation: ' . $e->getMessage()], 500);
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
