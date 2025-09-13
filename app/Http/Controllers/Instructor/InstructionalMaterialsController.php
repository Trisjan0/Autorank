<?php

namespace App\Http\Controllers\Instructor;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Services\DataSearchService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class InstructionalMaterialsController extends Controller
{
    /**
     * Display a paginated list of instructional materials with search and "load more".
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
                // We'll create this partial in the next step
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
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'category' => 'required|string|in:sole_author,co_author',
                'date' => 'required|date',
                'material_file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,jpg,png|max:10240',
            ]);

            $filePath = null;
            if ($request->hasFile('material_file')) {
                $filePath = $request->file('material_file')->store('materials', 'public');
            }

            Material::create([
                'user_id' => Auth::id(),
                'title' => $validatedData['title'],
                'type' => $validatedData['type'],
                'category' => $validatedData['category'],
                'date' => $validatedData['date'],
                'file_path' => $filePath,
            ]);

            return response()->json(['message' => 'Instructional Material uploaded successfully!'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading instructional material: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
}
