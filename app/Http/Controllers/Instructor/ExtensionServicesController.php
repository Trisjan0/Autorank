<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesGoogleDrive;
use App\Models\ExtensionService;
use App\Services\DataSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ExtensionServicesController extends Controller
{
    use ManagesGoogleDrive;

    /**
     * Display a paginated list of extension services.
     */
    public function index(Request $request, DataSearchService $searchService)
    {
        $perPage = 5;
        $query = ExtensionService::where('user_id', Auth::id())->orderBy('created_at', 'desc');
        $searchableColumns = ['title', 'service_type'];
        $searchTerm = $request->input('search');
        $searchService->applySearch($query, $searchTerm, $searchableColumns);

        if ($request->ajax()) {
            $offset = $request->input('offset', 0);
            $extension_services = (clone $query)->skip($offset)->take($perPage)->get();
            $html = '';
            foreach ($extension_services as $service) {
                $html .= view('partials._extension_services_table_row', ['service' => $service])->render();
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
        $extension_services = $query->take($perPage)->get();
        $initialHasMore = ($perPage < $totalMatching);

        return view('instructor.extension-services-page', compact('extension_services', 'initialHasMore', 'perPage'));
    }

    /**
     * Store a new extension service.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'service_type' => 'required|string|in:Institution,Community,Extension Involvement',
                'date' => 'required|date',
                'evidence_file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,jpg,png|max:10240', // 10MB max
            ]);

            // Upload the file to Google Drive and get the file ID
            $googleDriveFileId = $this->uploadFileToGoogleDrive($request, 'evidence_file', 'KRA III: Extension Services');

            // Create the new ExtensionService record
            $service = ExtensionService::create(array_merge($validatedData, [
                'user_id' => Auth::id(),
                'google_drive_file_id' => $googleDriveFileId,
                'filename' => $request->file('evidence_file')->getClientOriginalName(), // Store original filename
            ]));

            // Render the partial view for the new table row
            $newRowHtml = view('partials._extension_services_table_row', ['service' => $service])->render();

            // Return a successful JSON response with the new row's HTML
            return response()->json([
                'success' => true,
                'message' => 'Extension service uploaded successfully!',
                'newRowHtml' => $newRowHtml
            ], 201);
        } catch (ValidationException $e) {
            // Return validation errors
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log the error for debugging and return a generic error message
            Log::error('Extension Service Upload Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified extension service.
     *
     * @param \App\Models\ExtensionService $service The service instance to delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ExtensionService $service): JsonResponse
    {
        // Authorization check: Ensure the user owns the service record.
        if ($service->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the associated file from Google Drive if it exists.
            if ($service->google_drive_file_id) {
                $this->deleteFileFromGoogleDrive($service->google_drive_file_id);
            }

            // Delete the service record from the database.
            $service->delete();

            // Return a successful JSON response.
            return response()->json(['message' => 'Extension service deleted successfully.']);
        } catch (\Exception $e) {
            // Log the specific error for debugging purposes.
            Log::error('Extension Service Deletion Failed: ' . $e->getMessage());

            // Return a generic, user-friendly error message.
            return response()->json(['message' => 'Failed to delete the extension service. Please try again later.'], 500);
        }
    }

    public function getFileInfoForService($id)
    {
        return $this->getFileInfo($id, ExtensionService::class, 'instructor.extension-services.view-file');
    }

    public function viewFileForService($id, Request $request)
    {
        return $this->viewFile($id, ExtensionService::class, $request);
    }
}
