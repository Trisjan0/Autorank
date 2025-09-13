<?php

namespace App\Http\Controllers\Instructor;

use Illuminate\Http\Request;
use App\Models\ResearchDocument;
use App\Services\DataSearchService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ResearchDocumentsController extends Controller
{
    /**
     * Display a paginated list of research documents.
     */
    public function index(Request $request, DataSearchService $searchService)
    {
        $perPage = 5;

        $query = ResearchDocument::where('user_id', Auth::id())->orderBy('created_at', 'desc');

        $searchableColumns = ['title', 'type', 'category'];
        $searchTerm = $request->input('search');

        $searchService->applySearch($query, $searchTerm, $searchableColumns);

        if ($request->ajax()) {
            $offset = $request->input('offset', 0);
            $documents = (clone $query)->skip($offset)->take($perPage)->get();

            $html = '';
            foreach ($documents as $document) {
                $html .= view('partials._research_documents_table_row', ['document' => $document])->render();
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
        $documents = $query->take($perPage)->get();
        $initialHasMore = ($perPage < $totalMatching);

        return view('instructor.research-documents-page', [
            'research_documents' => $documents,
            'initialHasMore' => $initialHasMore,
            'perPage' => $perPage
        ]);
    }

    /**
     * Store a new research document.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'type' => 'required|string|in:Book,Monograph,Journal,Chapter',
                'title' => 'required|string|max:255',
                'date' => 'required|date',
                'category' => 'required|string|max:255',
                'document_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            ]);

            $filePath = null;
            if ($request->hasFile('document_file')) {
                $filePath = $request->file('document_file')->store('research_documents', 'public');
            }

            ResearchDocument::create([
                'user_id' => Auth::id(),
                'type' => $validatedData['type'],
                'title' => $validatedData['title'],
                'date' => $validatedData['date'],
                'category' => $validatedData['category'],
                'file_path' => $filePath,
            ]);

            return response()->json(['message' => 'Research document uploaded successfully!'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading research document: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
}
