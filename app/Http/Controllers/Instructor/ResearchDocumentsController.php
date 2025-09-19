<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\ResearchDocument;
use App\Services\DataSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Illuminate\Http\JsonResponse;

class ResearchDocumentsController extends Controller
{
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
     * Store a new research document in Google Drive and the database.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'type' => 'required|string|in:Book,Monograph,Journal,Chapter',
                'title' => 'required|string|max:255',
                'publish_date' => 'nullable|date',
                'category' => 'required|string|max:255',
                'document_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            ]);

            $user = Auth::user();
            $file = $request->file('document_file');

            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->refreshToken($user->google_refresh_token);
            $service = new Google_Service_Drive($client);

            $mainFolderName = 'Autorank Files';
            $mainFolderId = $this->findOrCreateFolder($service, $mainFolderName, null);

            $kraFolderName = 'KRA II: Research Outputs';
            $kraFolderId = $this->findOrCreateFolder($service, $kraFolderName, $mainFolderId);

            $fileName = time() . '_' . $file->getClientOriginalName();
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $fileName,
                'parents' => [$kraFolderId]
            ]);

            $content = file_get_contents($file->getRealPath());
            $uploadedFile = $service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $file->getClientMimeType(),
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);

            ResearchDocument::create([
                'user_id' => $user->id,
                'type' => $validatedData['type'],
                'title' => $validatedData['title'],
                'publish_date' => $validatedData['publish_date'],
                'category' => $validatedData['category'],
                'google_drive_file_id' => $uploadedFile->id,
                'file_path' => null,
            ]);

            return response()->json(['message' => 'Research document uploaded successfully!'], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading research document: ' . $e->getMessage());
            if (empty($user->google_refresh_token)) {
                return response()->json(['message' => 'Google account not linked or permission denied. Please re-authenticate.'], 500);
            }
            return response()->json(['message' => 'An unexpected error occurred during file upload.'], 500);
        }
    }

    /**
     * Find or create a folder in Google Drive.
     */
    private function findOrCreateFolder(Google_Service_Drive $service, string $folderName, ?string $parentId = null): ?string
    {
        $query = "mimeType='application/vnd.google-apps.folder' and name='$folderName' and trashed=false";
        if ($parentId) {
            $query .= " and '$parentId' in parents";
        }

        $response = $service->files->listFiles(['q' => $query, 'fields' => 'files(id)']);

        if (count($response->getFiles()) > 0) {
            return $response->getFiles()[0]->getId();
        }

        $folderMetadata = new Google_Service_Drive_DriveFile([
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder'
        ]);
        if ($parentId) {
            $folderMetadata->setParents([$parentId]);
        }

        $folder = $service->files->create($folderMetadata, ['fields' => 'id']);
        return $folder->id;
    }

    /**
     * Helper function to check if a MIME type is viewable in a browser.
     */
    private function isMimeTypeViewable(string $mimeType): bool
    {
        $viewableMimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'text/plain',
        ];
        return in_array($mimeType, $viewableMimeTypes);
    }

    /**
     * Get file metadata to determine if it's viewable.
     */
    public function getFileInfo($id): JsonResponse
    {
        $user = Auth::user();
        $document = ResearchDocument::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $client = new \Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->refreshToken($user->google_refresh_token);
        $service = new \Google_Service_Drive($client);

        try {
            $file = $service->files->get($document->google_drive_file_id, ['fields' => 'mimeType,name']);
            return response()->json([
                'isViewable' => $this->isMimeTypeViewable($file->getMimeType()),
                'fileName'   => $file->getName(),
                'viewUrl'    => route('instructor.research-documents.view-file', $document->id),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching file metadata from Google Drive: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch file info.'], 404);
        }
    }

    /**
     * Stream a file from Google Drive for inline viewing or force download.
     */
    public function viewFile($id, Request $request)
    {
        $user = Auth::user();
        $document = ResearchDocument::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $client = new \Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->refreshToken($user->google_refresh_token);
        $service = new \Google_Service_Drive($client);

        try {
            $file = $service->files->get($document->google_drive_file_id, ['fields' => 'mimeType,name']);
            $content = $service->files->get($document->google_drive_file_id, ['alt' => 'media']);
            $disposition = $request->query('download') ? 'attachment' : 'inline';

            return response($content->getBody(), 200)
                ->header('Content-Type', $file->getMimeType())
                ->header('Content-Disposition', $disposition . '; filename="' . $file->getName() . '"');
        } catch (\Exception $e) {
            Log::error('Error fetching file from Google Drive: ' . $e->getMessage());
            return abort(404, 'Unable to fetch file.');
        }
    }

    /**
     * Remove the specified research document from storage and Google Drive.
     */
    public function destroy($id): JsonResponse
    {
        $user = Auth::user();
        $document = ResearchDocument::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        try {
            if ($document->google_drive_file_id) {
                $client = new \Google_Client();
                $client->setClientId(env('GOOGLE_CLIENT_ID'));
                $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
                $client->refreshToken($user->google_refresh_token);
                $service = new \Google_Service_Drive($client);
                $service->files->delete($document->google_drive_file_id);
            }

            $document->delete();

            return response()->json(['message' => 'Research document deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting research document: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete the research document. Please try again.'], 500);
        }
    }
}
