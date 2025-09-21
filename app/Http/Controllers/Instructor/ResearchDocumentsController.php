<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesGoogleDrive;
use App\Models\ResearchDocument;
use App\Services\DataSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ResearchDocumentsController extends Controller
{
    use ManagesGoogleDrive;

    /**
     * Display a paginated list of research documents.
     */
    public function index(Request $request, DataSearchService $searchService)
    {
        $perPage = 5;
        $query = ResearchDocument::where('user_id', Auth::id())->orderBy('created_at', 'desc');
        $searchableColumns = ['title', 'type', 'category', 'publish_date'];
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
    public function store(Request $request): JsonResponse
    {
        // Sanitize publish_date input
        if ($request->input('publish_date') === '') {
            $request->merge(['publish_date' => null]);
        }

        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'type' => 'required|string|in:Book,Monograph,Journal,Chapter',
                'title' => 'required|string|max:255',
                'publish_date' => 'nullable|date',
                'category' => 'required|string|max:255',
                'document_file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            ]);

            // Upload the file to Google Drive and get the file ID
            $googleDriveFileId = $this->uploadFileToGoogleDrive($request, 'document_file', 'KRA II: Research Outputs');

            // Create the new ResearchDocument record
            $document = ResearchDocument::create(array_merge($validatedData, [
                'user_id' => Auth::id(),
                'google_drive_file_id' => $googleDriveFileId,
                'filename' => $request->file('document_file')->getClientOriginalName(), // Store original filename
            ]));

            // Render the partial view for the new table row
            $newRowHtml = view('partials._research_documents_table_row', ['document' => $document])->render();

            // Return a successful JSON response with the new row's HTML
            return response()->json([
                'success' => true,
                'message' => 'Research document uploaded successfully!',
                'newRowHtml' => $newRowHtml
            ], 201);
        } catch (ValidationException $e) {
            // Return validation errors
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log the error for debugging and return a generic error message
            Log::error('Research Document Upload Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified research document.
     *
     * @param \App\Models\ResearchDocument $document The document instance to delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ResearchDocument $document): JsonResponse
    {
        // Authorization check: Ensure the user owns the document.
        if ($document->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the associated file from Google Drive if it exists.
            if ($document->google_drive_file_id) {
                $this->deleteFileFromGoogleDrive($document->google_drive_file_id);
            }

            // Delete the document record from the database.
            $document->delete();

            // Return a successful JSON response.
            return response()->json(['message' => 'Research document deleted successfully.']);
        } catch (\Exception $e) {
            // Log the specific error for debugging purposes.
            Log::error('Research Document Deletion Failed: ' . $e->getMessage());

            // Return a generic, user-friendly error message.
            return response()->json(['message' => 'Failed to delete the research document. Please try again later.'], 500);
        }
    }

    public function getFileInfoForDocument($id)
    {
        return $this->getFileInfo($id, ResearchDocument::class, 'instructor.research-documents.view-file');
    }

    public function viewFileForDocument($id, Request $request)
    {
        return $this->viewFile($id, ResearchDocument::class, $request);
    }
}
