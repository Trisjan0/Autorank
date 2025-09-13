<?php

namespace App\Http\Controllers\Instructor;

use Illuminate\Http\Request;
use App\Models\ProfessionalDevelopment;
use App\Services\DataSearchService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProfessionalDevelopmentsController extends Controller
{
    /**
     * Display a paginated list of professional developments.
     */
    public function index(Request $request, DataSearchService $searchService)
    {
        $perPage = 5;

        $query = ProfessionalDevelopment::where('user_id', Auth::id())->orderBy('created_at', 'desc');

        $searchableColumns = ['title', 'category'];
        $searchTerm = $request->input('search');

        $searchService->applySearch($query, $searchTerm, $searchableColumns);

        if ($request->ajax()) {
            $offset = $request->input('offset', 0);
            $developments = (clone $query)->skip($offset)->take($perPage)->get();

            $html = '';
            foreach ($developments as $development) {
                $html .= view('partials._professional_developments_table_row', ['development' => $development])->render();
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
        $developments = $query->take($perPage)->get();
        $initialHasMore = ($perPage < $totalMatching);

        return view('instructor.professional-developments-page', [
            'professional_developments' => $developments,
            'initialHasMore' => $initialHasMore,
            'perPage' => $perPage
        ]);
    }

    /**
     * Store a new professional development entry.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'category' => 'required|string|in:Professional Organization Involvement,Continuing Development,Awards,Experience',
                'title' => 'required|string|max:255',
                'date' => 'required|date',
                'evidence_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:10240',
            ]);

            $filePath = null;
            if ($request->hasFile('evidence_file')) {
                $filePath = $request->file('evidence_file')->store('professional_developments', 'public');
            }

            ProfessionalDevelopment::create([
                'user_id' => Auth::id(),
                'category' => $validatedData['category'],
                'title' => $validatedData['title'],
                'date' => $validatedData['date'],
                'file_path' => $filePath,
            ]);

            return response()->json(['message' => 'Professional development evidence uploaded successfully!'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading professional development evidence: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
}
