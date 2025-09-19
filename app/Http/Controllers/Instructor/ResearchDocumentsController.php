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
        if ($request->input('publish_date') === '') {
            $request->merge(['publish_date' => null]);
        }

        try {
            $validatedData = $request->validate([
                'type' => 'required|string|in:Book,Monograph,Journal,Chapter',
                'title' => 'required|string|max:255',
                'publish_date' => 'nullable|date',
                'category' => 'required|string|max:255',
                'document_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            ]);

            $googleDriveFileId = $this->uploadFileToGoogleDrive($request, 'document_file', 'KRA II: Research Outputs');

            ResearchDocument::create(array_merge($validatedData, [
                'user_id' => Auth::id(),
                'google_drive_file_id' => $googleDriveFileId,
            ]));

            return response()->json(['message' => 'Research document uploaded successfully!'], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified research document.
     */
    public function destroy($id): JsonResponse
    {
        $document = ResearchDocument::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        try {
            if ($document->google_drive_file_id) {
                $this->deleteFileFromGoogleDrive($document->google_drive_file_id);
            }
            $document->delete();
            return response()->json(['message' => 'Research document deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete the research document: ' . $e->getMessage()], 500);
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
