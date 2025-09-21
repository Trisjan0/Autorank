<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesGoogleDrive;
use App\Models\Material;
use App\Services\DataSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class InstructionalMaterialsController extends Controller
{
    use ManagesGoogleDrive;

    /**
     * Display a paginated list of instructional materials.
     */
    public function index(Request $request, DataSearchService $searchService)
    {
        $perPage = 5;
        $query = Material::where('user_id', Auth::id())->orderBy('created_at', 'desc');
        $searchableColumns = ['title', 'category', 'type'];
        $searchTerm = $request->input('search');
        $searchService->applySearch($query, $searchTerm, $searchableColumns);

        if ($request->ajax()) {
            $offset = $request->input('offset', 0);
            $materials = (clone $query)->skip($offset)->take($perPage)->get();
            $html = '';
            foreach ($materials as $material) {
                $html .= view('partials._instructional_materials_table_row', ['material' => $material])->render();
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
        $materials = $query->take($perPage)->get();
        $initialHasMore = ($perPage < $totalMatching);

        return view('instructor.instructional-materials-page', compact('materials', 'initialHasMore', 'perPage'));
    }

    /**
     * Store a new instructional material.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'category' => 'required|string|in:sole_author,co_author',
                'date' => 'required|date',
                'material_file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,jpg,png|max:10240', // 10MB max
            ]);

            // Upload the file to Google Drive and get the file ID
            $googleDriveFileId = $this->uploadFileToGoogleDrive($request, 'material_file', 'KRA I-B: Instructional Materials');

            // Create the new Material record
            $material = Material::create(array_merge($validatedData, [
                'user_id' => Auth::id(),
                'google_drive_file_id' => $googleDriveFileId,
                'filename' => $request->file('material_file')->getClientOriginalName(), // Store original filename
            ]));

            // Render the partial view for the new table row
            $newRowHtml = view('partials._instructional_materials_table_row', ['material' => $material])->render();

            // Return a successful JSON response with the new row's HTML
            return response()->json([
                'success' => true,
                'message' => 'Instructional Material uploaded successfully!',
                'newRowHtml' => $newRowHtml
            ], 201);
        } catch (ValidationException $e) {
            // Return validation errors
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log the error for debugging and return a generic error message
            Log::error('Instructional Material Upload Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified instructional material.
     *
     * @param \App\Models\Material $material The material instance to delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Material $material): JsonResponse
    {
        // Authorization check: Ensure the user owns the material.
        if ($material->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the associated file from Google Drive if it exists.
            if ($material->google_drive_file_id) {
                $this->deleteFileFromGoogleDrive($material->google_drive_file_id);
            }

            // Delete the material record from the database.
            $material->delete();

            // Return a successful JSON response.
            return response()->json(['message' => 'Instructional material deleted successfully.']);
        } catch (\Exception $e) {
            // Log the specific error for debugging purposes.
            Log::error('Instructional Material Deletion Failed: ' . $e->getMessage());

            // Return a generic, user-friendly error message.
            return response()->json(['message' => 'Failed to delete the instructional material. Please try again later.'], 500);
        }
    }

    public function getFileInfoForMaterial($id)
    {
        return $this->getFileInfo($id, Material::class, 'instructor.instructional-materials.view-file');
    }

    public function viewFileForMaterial($id, Request $request)
    {
        return $this->viewFile($id, Material::class, $request);
    }
}
