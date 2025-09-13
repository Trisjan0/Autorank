<?php

namespace App\Http\Controllers\Instructor;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Services\DataSearchService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EvaluationsController extends Controller
{
    /**
     * Display a paginated list of evaluations with search and "load more".
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\DataSearchService $searchService
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request, DataSearchService $searchService)
    {
        $perPage = 5; // Number of items to load initially and per "load more"

        // Base query for evaluations belonging to the logged-in user
        $query = Evaluation::where('user_id', Auth::id())->orderBy('created_at', 'desc');

        // Define which columns on the Evaluation model are searchable
        $searchableColumns = ['title', 'category', 'score'];
        $searchTerm = $request->input('search');

        // Use the reusable service to apply the search filter
        $searchService->applySearch($query, $searchTerm, $searchableColumns);

        // Handle AJAX requests for searching and "Load More"
        if ($request->ajax()) {
            $offset = $request->input('offset', 0);
            $evaluations = (clone $query)->skip($offset)->take($perPage)->get();

            $html = '';
            foreach ($evaluations as $evaluation) {
                // Render each row using a Blade partial for consistency
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

        // Handle the initial page load
        $totalMatching = (clone $query)->count();
        $evaluations = $query->take($perPage)->get();
        $initialHasMore = ($perPage < $totalMatching);

        return view('instructor.evaluations-page', compact('evaluations', 'initialHasMore', 'perPage'));
    }

    /**
     * Store created evaluation
     */
    public function storeEvaluation(Request $request)
    {
        try {
            // Your validation rules remain the same
            $validatedData = $request->validate([
                'category' => 'required|string|in:student,supervisor',
                'title' => 'required|string|max:255',
                'publish_date' => 'nullable|date',
                'score' => 'required|numeric|min:0',
                // IMPORTANT: Make sure the file name in your frontend form is 'evaluation_file'
                'evaluation_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            ]);

            $filePath = null;
            if ($request->hasFile('evaluation_file')) {
                $filePath = $request->file('evaluation_file')->store('evaluations', 'public');
            }

            Evaluation::create([
                'user_id' => Auth::id(),
                'category' => $validatedData['category'],
                'title' => $validatedData['title'],
                'publish_date' => $validatedData['publish_date'],
                'score' => $validatedData['score'],
                'file_path' => $filePath,
            ]);

            // Instead of redirecting, return a success JSON response
            return response()->json(['message' => 'Evaluation uploaded successfully!'], 201); // 201 Created

        } catch (ValidationException $e) {
            // If validation fails, return a 422 JSON response with the errors
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // For any other server error, log it and return a 500 JSON response
            Log::error('Error uploading evaluation: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
}
