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
     * Display a paginated list of credentials.
     * This method handles both the initial page load and subsequent AJAX requests.
     */
    public function index(Request $request): mixed // Use mixed return type
    {
        $user = Auth::user();
        $perPage = 5;

        // Base query for user's credentials
        $credentialsQuery = $user->credentials()->latest();

        // Apply search if a search term is provided
        if ($search = $request->input('search')) {
            $credentialsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%')
                    ->orWhere('filename', 'like', '%' . $search . '%');
            });
        }

        // --- Handle AJAX requests ---
        if ($request->ajax()) {
            $offset = $request->input('offset', 0);
            $credentials = (clone $credentialsQuery)->skip($offset)->take($perPage)->get();

            $html = '';
            foreach ($credentials as $credential) {
                $html .= view('partials._credential_table_row', ['credential' => $credential])->render();
            }

            $totalMatching = (clone $credentialsQuery)->count();
            $hasMore = ($offset + $perPage) < $totalMatching;

            return response()->json([
                'html'       => $html,
                'hasMore'    => $hasMore,
                'nextOffset' => $offset + $perPage,
            ]);
        }

        // --- Handle Initial Page Load ---
        $totalMatching = (clone $credentialsQuery)->count();
        $credentials = $credentialsQuery->take($perPage)->get();
        $initialHasMore = ($perPage < $totalMatching);
        $isOwnProfile = true; // Assuming this is always the user's own profile

        // Return the full profile page view with the initial data
        return view('profile-page', compact(
            'user',
            'credentials',
            'initialHasMore',
            'perPage',
            'isOwnProfile'
        ));
    }

    /**
     * Store a new credential.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'nullable|string|max:255',
                'credential_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:10240',
            ]);

            $googleDriveFileId = $this->uploadFileToGoogleDrive($request, 'credential_file', 'Credentials');

            Credential::create([
                'user_id' => Auth::id(),
                'title' => $validatedData['title'],
                'type' => $validatedData['type'],
                'google_drive_file_id' => $googleDriveFileId,
                'filename' => $request->file('credential_file')->getClientOriginalName(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Credential uploaded successfully!',
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

    // Other methods remain the same...
    public function getFileInfoForCredential(Credential $credential): JsonResponse
    {
        return $this->getFileInfo($credential->id, Credential::class, 'credentials.view-file');
    }

    public function viewFileForCredential(Credential $credential, Request $request)
    {
        return $this->viewFile($credential->id, Credential::class, $request);
    }
}
