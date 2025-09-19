<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Services\DataSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Illuminate\Http\JsonResponse;

class EvaluationsController extends Controller
{
    /**
     * Display a paginated list of evaluations with search and "load more".
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
     * Store created evaluation in Google Drive and the database.
     */
    public function storeEvaluation(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'category' => 'required|string|in:student,supervisor',
                'title' => 'required|string|max:255',
                'publish_date' => 'nullable|date',
                'score' => 'required|numeric|min:0',
                'evaluation_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            ]);

            $user = Auth::user();
            $file = $request->file('evaluation_file');

            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->refreshToken($user->google_refresh_token);
            $service = new Google_Service_Drive($client);

            $mainFolderName = 'Autorank Files';
            $mainFolderId = $this->findOrCreateFolder($service, $mainFolderName, null);

            $kraFolderName = 'KRA I-A: Evaluations';
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

            Evaluation::create([
                'user_id' => $user->id,
                'category' => $validatedData['category'],
                'title' => $validatedData['title'],
                'publish_date' => $validatedData['publish_date'],
                'score' => $validatedData['score'],
                'google_drive_file_id' => $uploadedFile->id,
                'file_path' => null,
            ]);

            return response()->json(['message' => 'Evaluation uploaded successfully!'], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading evaluation: ' . $e->getMessage());
            if (empty($user->google_refresh_token)) {
                return response()->json(['message' => 'Google account not linked or permission denied. Please re-authenticate.'], 500);
            }
            return response()->json(['message' => 'An unexpected error occurred during file upload.'], 500);
        }
    }

    /**
     * Find or create a folder in Google Drive.
     * @return string|null The ID of the found or created folder, or null on failure.
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

        $folderId = $folder->id;
        if (!$folderId) {
            throw new \Exception("Failed to create or find Google Drive folder: {$folderName}");
        }

        return $folderId;
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
        $evaluation = Evaluation::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $client = new \Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->refreshToken($user->google_refresh_token);
        $service = new \Google_Service_Drive($client);

        try {
            $file = $service->files->get($evaluation->google_drive_file_id, ['fields' => 'mimeType,name']);

            return response()->json([
                'isViewable' => $this->isMimeTypeViewable($file->getMimeType()),
                'fileName'   => $file->getName(),
                'viewUrl'    => route('instructor.evaluations.view-file', $evaluation->id),
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
        $evaluation = Evaluation::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $client = new \Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->refreshToken($user->google_refresh_token);
        $service = new \Google_Service_Drive($client);

        try {
            $file = $service->files->get($evaluation->google_drive_file_id, ['fields' => 'mimeType,name']);
            $content = $service->files->get($evaluation->google_drive_file_id, ['alt' => 'media']);

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
     * Remove the specified evaluation from storage and Google Drive.
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $user = Auth::user();
        $evaluation = Evaluation::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        try {
            if ($evaluation->google_drive_file_id) {
                $client = new \Google_Client();
                $client->setClientId(env('GOOGLE_CLIENT_ID'));
                $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
                $client->refreshToken($user->google_refresh_token);
                $service = new \Google_Service_Drive($client);

                $service->files->delete($evaluation->google_drive_file_id);
            }

            $evaluation->delete();

            return response()->json(['message' => 'Evaluation deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting evaluation: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete the evaluation. Please try again.'], 500);
        }
    }
}
