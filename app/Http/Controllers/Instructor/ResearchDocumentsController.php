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
        $searchableColumns = ['title', 'category', 'type'];
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
                'title'         => 'required|string|max:255',
                'type'          => 'required|string|max:255',
                'category'      => 'required|string|in:Research Outputs,Inventions,Creative Works,Papers Presented in Conferences',
                'publish_date'  => 'nullable|date',
                'document_file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,jpg,png|max:10240',
            ]);

            $googleDriveFileId = $this->uploadFileToGoogleDrive(
                $request,
                'document_file',
                'KRA II: Research, Invention, and Creative Work',
                $validatedData['category']
            );

            $document = ResearchDocument::create([
                'user_id'              => Auth::id(),
                'title'                => $validatedData['title'],
                'type'                 => $validatedData['type'],
                'category'             => $validatedData['category'],
                'publish_date'         => $validatedData['publish_date'],
                'google_drive_file_id' => $googleDriveFileId,
                'filename'             => $request->file('document_file')->getClientOriginalName(),
                'sub_cat1_score'       => null,
                'sub_cat2_score'       => null,
                'sub_cat3_score'       => null,
                'sub_cat4_score'       => null,
            ]);

            $newRowHtml = view('partials._research_documents_table_row', ['document' => $document])->render();

            return response()->json([
                'success'    => true,
                'message'    => 'Research document uploaded successfully! This will soon be scored by an Evaluator.',
                'newRowHtml' => $newRowHtml,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Research Document Upload Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    public function destroy(ResearchDocument $document): JsonResponse
    {
        if ($document->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            if ($document->google_drive_file_id) {
                $this->deleteFileFromGoogleDrive($document->google_drive_file_id);
            }

            $document->delete();
            return response()->json(['message' => 'Research document deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Research Document Deletion Failed: ' . $e->getMessage());
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
