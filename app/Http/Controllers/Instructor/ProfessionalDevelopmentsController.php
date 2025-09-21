<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesGoogleDrive;
use App\Models\ProfessionalDevelopment;
use App\Services\DataSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ProfessionalDevelopmentsController extends Controller
{
    use ManagesGoogleDrive;

    /**
     * Display a paginated list of professional developments.
     */
    public function index(Request $request, DataSearchService $searchService)
    {
        $perPage = 5;
        $query = ProfessionalDevelopment::where('user_id', Auth::id())->orderBy('created_at', 'desc');
        $searchableColumns = ['title', 'category'];
        $searchTerm = $request->input('search');
        $searchService->applySearch($query, $searchTerm, $searchableColumns);

        if ($request->ajax()) {
            $offset = $request->input('offset', 0);
            $professional_developments = (clone $query)->skip($offset)->take($perPage)->get();
            $html = '';
            foreach ($professional_developments as $development) {
                $html .= view('partials._professional_developments_table_row', ['development' => $development])->render();
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
        $professional_developments = $query->take($perPage)->get();
        $initialHasMore = ($perPage < $totalMatching);

        return view('instructor.professional-developments-page', compact('professional_developments', 'initialHasMore', 'perPage'));
    }

    /**
     * Store a new professional development entry.
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
                'title' => 'required|string|max:255',
                'category' => 'required|string|in:Professional Organization Involvement,Continuing Development,Awards,Experience',
                'publish_date' => 'nullable|date',
                'evidence_file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,jpg,png|max:10240', // 10MB max
            ]);

            // Upload the file to Google Drive and get the file ID
            $googleDriveFileId = $this->uploadFileToGoogleDrive($request, 'evidence_file', 'KRA IV: Professional Development');

            // Create the new ProfessionalDevelopment record
            $development = ProfessionalDevelopment::create(array_merge($validatedData, [
                'user_id' => Auth::id(),
                'google_drive_file_id' => $googleDriveFileId,
                'filename' => $request->file('evidence_file')->getClientOriginalName(), // Store original filename
            ]));

            // Render the partial view for the new table row
            $newRowHtml = view('partials._professional_developments_table_row', ['development' => $development])->render();

            // Return a successful JSON response with the new row's HTML
            return response()->json([
                'success' => true,
                'message' => 'Professional development evidence uploaded successfully!',
                'newRowHtml' => $newRowHtml
            ], 201);
        } catch (ValidationException $e) {
            // Return validation errors
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log the error for debugging and return a generic error message
            Log::error('Professional Development Upload Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified professional development record.
     *
     * @param \App\Models\ProfessionalDevelopment $development The development record instance to delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ProfessionalDevelopment $development): JsonResponse
    {
        // Authorization check: Ensure the user owns the development record.
        if ($development->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the associated file from Google Drive if it exists.
            if ($development->google_drive_file_id) {
                $this->deleteFileFromGoogleDrive($development->google_drive_file_id);
            }

            // Delete the development record from the database.
            $development->delete();

            // Return a successful JSON response.
            return response()->json(['message' => 'Professional development record deleted successfully.']);
        } catch (\Exception $e) {
            // Log the specific error for debugging purposes.
            Log::error('Professional Development Deletion Failed: ' . $e->getMessage());

            // Return a generic, user-friendly error message.
            return response()->json(['message' => 'Failed to delete the record. Please try again later.'], 500);
        }
    }

    public function getFileInfoForDevelopment($id)
    {
        return $this->getFileInfo($id, ProfessionalDevelopment::class, 'instructor.professional-developments.view-file');
    }

    public function viewFileForDevelopment($id, Request $request)
    {
        return $this->viewFile($id, ProfessionalDevelopment::class, $request);
    }
}
