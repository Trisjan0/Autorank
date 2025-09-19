<?php

namespace App\Http\Controllers\Concerns;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait ManagesGoogleDrive
{
    /**
     * Uploads a file to a specific Google Drive folder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $fileInputName The name of the file input from the form.
     * @param  string  $kraFolderName The name of the KRA-specific folder in Google Drive.
     * @return string The ID of the uploaded Google Drive file.
     */
    protected function uploadFileToGoogleDrive(Request $request, string $fileInputName, string $kraFolderName): string
    {
        $user = Auth::user();
        $file = $request->file($fileInputName);

        $service = $this->getGoogleDriveService();

        $mainFolderId = $this->findOrCreateFolder($service, 'Autorank Files');
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

        return $uploadedFile->id;
    }

    /**
     * Deletes a file from Google Drive.
     *
     * @param  string  $fileId The Google Drive file ID.
     * @return void
     */
    protected function deleteFileFromGoogleDrive(string $fileId): void
    {
        $service = $this->getGoogleDriveService();
        $service->files->delete($fileId);
    }

    /**
     * Get file metadata to determine if it's viewable.
     */
    public function getFileInfo($id, $model, $routeName): JsonResponse
    {
        $item = $this->findItem($model, $id);
        $service = $this->getGoogleDriveService();

        try {
            $file = $service->files->get($item->google_drive_file_id, ['fields' => 'mimeType,name']);
            return response()->json([
                'isViewable' => $this->isMimeTypeViewable($file->getMimeType()),
                'fileName'   => $file->getName(),
                'viewUrl'    => route($routeName, $item->id),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching file metadata: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch file info.'], 404);
        }
    }

    /**
     * Stream a file from Google Drive for viewing or download.
     */
    public function viewFile($id, $model, Request $request)
    {
        $item = $this->findItem($model, $id);
        $service = $this->getGoogleDriveService();

        try {
            $file = $service->files->get($item->google_drive_file_id, ['fields' => 'mimeType,name']);
            $content = $service->files->get($item->google_drive_file_id, ['alt' => 'media']);
            $disposition = $request->query('download') ? 'attachment' : 'inline';

            return response($content->getBody(), 200)
                ->header('Content-Type', $file->getMimeType())
                ->header('Content-Disposition', $disposition . '; filename="' . $file->getName() . '"');
        } catch (\Exception $e) {
            Log::error('Error fetching file: ' . $e->getMessage());
            return abort(404, 'Unable to fetch file.');
        }
    }

    /**
     * Get an authenticated Google Drive service instance.
     */
    private function getGoogleDriveService(): Google_Service_Drive
    {
        $user = Auth::user();
        if (empty($user->google_refresh_token)) {
            throw new \Exception('Google account not linked or permission denied.');
        }
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->refreshToken($user->google_refresh_token);
        return new Google_Service_Drive($client);
    }

    /**
     * Find or create a folder in Google Drive.
     */
    private function findOrCreateFolder(Google_Service_Drive $service, string $folderName, ?string $parentId = null): string
    {
        $query = "mimeType='application/vnd.google-apps.folder' and name='$folderName' and trashed=false";
        if ($parentId) {
            $query .= " and '$parentId' in parents";
        }
        $response = $service->files->listFiles(['q' => $query, 'fields' => 'files(id)']);
        if (count($response->getFiles()) > 0) {
            return $response->getFiles()[0]->getId();
        }
        $folderMetadata = new Google_Service_Drive_DriveFile(['name' => $folderName, 'mimeType' => 'application/vnd.google-apps.folder']);
        if ($parentId) {
            $folderMetadata->setParents([$parentId]);
        }
        $folder = $service->files->create($folderMetadata, ['fields' => 'id']);
        return $folder->id;
    }

    /**
     * Check if a MIME type is viewable in a browser.
     */
    private function isMimeTypeViewable(string $mimeType): bool
    {
        return in_array($mimeType, [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'text/plain',
        ]);
    }

    /**
     * Find the relevant item from the database.
     */
    private function findItem($modelClass, $id)
    {
        return $modelClass::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
    }
}
