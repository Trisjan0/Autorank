<?php

namespace App\Http\Controllers\Instructor;

use Illuminate\Http\Request;
use App\Models\ExtensionService;
use App\Services\DataSearchService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ExtensionServicesController extends Controller
{
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
            $services = (clone $query)->skip($offset)->take($perPage)->get();

            $html = '';
            foreach ($services as $service) {
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
        $services = $query->take($perPage)->get();
        $initialHasMore = ($perPage < $totalMatching);

        return view('instructor.extension-services-page', [
            'extension_services' => $services,
            'initialHasMore' => $initialHasMore,
            'perPage' => $perPage
        ]);
    }

    /**
     * Store a new extension service.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'service_type' => 'required|string|in:Institution,Community,Extension Involvement',
                'title' => 'required|string|max:255',
                'date' => 'required|date',
                'evidence_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:10240',
            ]);

            $filePath = null;
            if ($request->hasFile('evidence_file')) {
                $filePath = $request->file('evidence_file')->store('extension_services', 'public');
            }

            ExtensionService::create([
                'user_id' => Auth::id(),
                'service_type' => $validatedData['service_type'],
                'title' => $validatedData['title'],
                'date' => $validatedData['date'],
                'file_path' => $filePath,
            ]);

            return response()->json(['message' => 'Extension service uploaded successfully!'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading extension service: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
}
