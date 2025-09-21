<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Concerns\ManagesGoogleDrive;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class CredentialController extends Controller
{
    use ManagesGoogleDrive;

    /**
     * Store a new credential.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'credential_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:10240', // 10MB max
            ]);

            $googleDriveFileId = $this->uploadFileToGoogleDrive($request, 'credential_file', 'Credentials');

            $credential = Credential::create([
                'user_id' => Auth::id(),
                'title' => $validatedData['title'],
                'google_drive_file_id' => $googleDriveFileId,
                'filename' => $request->file('credential_file')->getClientOriginalName(),
            ]);

            $newRowHtml = view('partials._credential_table_row', ['credential' => $credential])->render();

            return response()->json([
                'success' => true,
                'message' => 'Credential uploaded successfully!',
                'newRowHtml' => $newRowHtml
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Credential Upload Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified credential.
     */
    public function destroy(Credential $credential): JsonResponse
    {
        if ($credential->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            if ($credential->google_drive_file_id) {
                $this->deleteFileFromGoogleDrive($credential->google_drive_file_id);
            }
            $credential->delete();
            return response()->json(['message' => 'Credential deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Credential Deletion Failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete credential. Please try again.'], 500);
        }
    }

    /**
     * Get file metadata for a credential.
     */
    public function getFileInfoForCredential(Credential $credential): JsonResponse
    {
        return $this->getFileInfo($credential->id, Credential::class, 'credentials.view-file');
    }

    /**
     * Stream a credential file from Google Drive.
     */
    public function viewFileForCredential(Credential $credential, Request $request)
    {
        return $this->viewFile($credential->id, Credential::class, $request);
    }
}
