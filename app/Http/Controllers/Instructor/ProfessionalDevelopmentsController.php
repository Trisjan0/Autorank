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
        if ($request->input('publish_date') === '') {
            $request->merge(['publish_date' => null]);
        }

        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'category' => 'required|string|in:Professional Organization Involvement,Continuing Development,Awards,Experience',
                'publish_date' => 'nullable|date',
                'evidence_file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,jpg,png|max:10240',
            ]);

            $googleDriveFileId = $this->uploadFileToGoogleDrive($request, 'evidence_file', 'KRA IV: Professional Development');

            ProfessionalDevelopment::create(array_merge($validatedData, [
                'user_id' => Auth::id(),
                'google_drive_file_id' => $googleDriveFileId,
            ]));

            return response()->json(['message' => 'Professional development evidence uploaded successfully!'], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified development record.
     */
    public function destroy($id): JsonResponse
    {
        $development = ProfessionalDevelopment::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        try {
            if ($development->google_drive_file_id) {
                $this->deleteFileFromGoogleDrive($development->google_drive_file_id);
            }
            $development->delete();
            return response()->json(['message' => 'Professional development record deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete the record: ' . $e->getMessage()], 500);
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
