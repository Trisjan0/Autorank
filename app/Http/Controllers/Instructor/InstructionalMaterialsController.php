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
            $validatedData = $request->validate([
                'title'         => 'required|string|max:255',
                'category'      => 'required|string',
                'type'          => 'required|string|max:255',
                'material_file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,jpg,png|max:10240', // 10MB for consistency
            ]);

            $googleDriveFileId = $this->uploadFileToGoogleDrive(
                $request,
                'material_file',
                'KRA I-B: Instructional Materials',
                $validatedData['category']
            );

            $material = Material::create([
                'user_id'              => Auth::id(),
                'title'                => $validatedData['title'],
                'category'             => $validatedData['category'],
                'type'                 => $validatedData['type'],
                'google_drive_file_id' => $googleDriveFileId,
                'filename'             => $request->file('material_file')->getClientOriginalName(),
                'sub_cat1_score'       => null,
            ]);

            $newRowHtml = view('partials._instructional_materials_table_row', ['material' => $material])->render();

            return response()->json([
                'success'    => true,
                'message'    => 'Material uploaded successfully! This will soon be scored by an Evaluator.',
                'newRowHtml' => $newRowHtml,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Material Upload Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified material.
     */
    public function destroy(Material $material): JsonResponse
    {
        if ($material->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            if ($material->google_drive_file_id) {
                $this->deleteFileFromGoogleDrive($material->google_drive_file_id);
            }

            $material->delete();

            return response()->json(['message' => 'Material deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Material Deletion Failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete the material. Please try again later.'], 500);
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
