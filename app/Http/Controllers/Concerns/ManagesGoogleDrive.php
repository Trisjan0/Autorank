<?php

namespace App\Http\Controllers\Concerns;

// Use the modern, namespaced Google API classes
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait ManagesGoogleDrive
{
    protected function uploadFileToGoogleDrive(Request $request, string $fileInputName, string $kraFolderName, ?string $subFolderName = null): string
    {
        $file = $request->file($fileInputName);
        $service = $this->getGoogleDriveService();
        $mainFolderId = $this->findOrCreateFolder($service, 'Autorank Files');
        $kraFolderId = $this->findOrCreateFolder($service, $kraFolderName, $mainFolderId);
        $targetFolderId = $subFolderName ? $this->findOrCreateFolder($service, $subFolderName, $kraFolderId) : $kraFolderId;
        $fileName = time() . '_' . $file->getClientOriginalName();
        $fileMetadata = new DriveFile([
            'name' => $fileName,
            'parents' => [$targetFolderId]
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

    protected function deleteFileFromGoogleDrive(string $fileId): void
    {
        try {
            $service = $this->getGoogleDriveService();
            $service->files->delete($fileId);
        } catch (\Exception $e) {
            if ($e->getCode() == 404) {
                Log::info('Attempted to delete a Google Drive file that was already gone.', ['file_id' => $fileId]);
            } else {
                throw $e;
            }
        }
    }

    protected function getFileInfo($recordId, $modelClass, $viewRouteName)
    {
        $item = $modelClass::findOrFail($recordId);
        return $this->getFileInfoById($item->google_drive_file_id, route($viewRouteName, ['id' => $recordId]));
    }

    protected function getFileInfoById($fileId, $viewUrl)
    {
        if (!$fileId) {
            return response()->json(['message' => 'File not found for this record.'], 404);
        }
        try {
            $service = $this->getGoogleDriveService();
            $file = $service->files->get($fileId, ['fields' => 'mimeType']);
            $isViewable = in_array($file->getMimeType(), ['application/pdf', 'image/jpeg', 'image/png']);
            return response()->json(['isViewable' => $isViewable, 'viewUrl' => $viewUrl]);
        } catch (\Exception $e) {
            Log::error('Google Drive file info retrieval failed: ' . $e->getMessage());
            return response()->json(['message' => 'Could not retrieve file information.'], 500);
        }
    }

    protected function viewFile($recordId, $modelClass, Request $request)
    {
        $item = $modelClass::findOrFail($recordId);
        return $this->viewFileById($item->google_drive_file_id, $request);
    }

    protected function viewFileById($fileId, Request $request)
    {
        if (!$fileId) abort(404, 'File ID not found.');
        try {
            $service = $this->getGoogleDriveService();
            $response = $service->files->get($fileId, ['alt' => 'media']);
            $content = $response->getBody()->getContents();
            $fileMeta = $service->files->get($fileId, ['fields' => 'mimeType, name']);
            return response($content, 200, [
                'Content-Type' => $fileMeta->getMimeType(),
                'Content-Disposition' => 'inline; filename="' . $fileMeta->getName() . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Google Drive file viewing failed: ' . $e->getMessage());
            abort(500, 'Could not retrieve the file.');
        }
    }

    private function getGoogleDriveService(): Drive
    {
        $user = Auth::user();
        if (empty($user->google_refresh_token)) {
            throw new \Exception('Google account not linked or permission denied.');
        }
        $client = new Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->refreshToken($user->google_refresh_token);
        return new Drive($client);
    }

    private function findOrCreateFolder(Drive $service, string $folderName, ?string $parentId = null): string
    {
        $query = "mimeType='application/vnd.google-apps.folder' and name='$folderName' and trashed=false";
        if ($parentId) {
            $query .= " and '$parentId' in parents";
        }
        $response = $service->files->listFiles(['q' => $query, 'fields' => 'files(id)']);
        if (count($response->getFiles()) > 0) {
            return $response->getFiles()[0]->getId();
        }
        $folderMetadata = new DriveFile(['name' => $folderName, 'mimeType' => 'application/vnd.google-apps.folder']);
        if ($parentId) {
            $folderMetadata->setParents([$parentId]);
        }
        $folder = $service->files->create($folderMetadata, ['fields' => 'id']);
        return $folder->id;
    }
}
