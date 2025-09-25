<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesGoogleDrive;
use App\Models\ProfessionalDevelopment;
use App\Services\DataSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ProfessionalDevelopmentController extends Controller
{
    use ManagesGoogleDrive;

    /**
     * Centralized method to get all dropdown options for KRA IV.
     */
    private function getProfessionalDevelopmentOptions(): array
    {
        return [
            // Options for Involvement in Professional Organizations
            'po_membership_types' => ['Member', 'Life Member', 'Fellow'],

            // Options for Continuing Professional Education & Training
            'pt_types' => [
                'Post-Doctoral Program',
                'Doctoral Degree',
                'Master\'s Degree',
                'Training / Seminar / Workshop',
                'Conference / Forum / Symposium'
            ],

            // Options for Awards and Recognitions
            'pa_levels' => ['Institutional / University', 'Regional', 'National', 'International'],
        ];
    }

    public function index(Request $request, DataSearchService $searchService)
    {
        $perPage = 5;
        $userId = Auth::id();

        if ($request->ajax()) {
            $criterion = $request->input('criterion', 'prof-organizations');
            $searchTerm = $request->input('search');
            $offset = $request->input('offset', 0);

            $query = ProfessionalDevelopment::where('user_id', $userId)->where('criterion', $criterion)->orderBy('created_at', 'desc');

            $searchableColumns = [
                'prof-organizations' => ['title', 'membership_type', 'role'],
                'prof-training'      => ['title', 'type', 'organizer', 'level'],
                'prof-awards'        => ['title', 'awarding_body', 'level'],
            ];

            if ($searchTerm && isset($searchableColumns[$criterion])) {
                $searchService->applySearch($query, $searchTerm, $searchableColumns[$criterion]);
            }

            $totalMatching = (clone $query)->count();
            $items = $query->skip($offset)->take($perPage)->get();
            $html = '';
            $partialName = 'partials._' . str_replace('-', '_', $criterion) . '_table_row';

            if (view()->exists($partialName)) {
                foreach ($items as $item) {
                    $html .= view($partialName, ['item' => $item])->render();
                }
            }

            return response()->json([
                'html'       => $html,
                'hasMore'    => ($offset + $perPage) < $totalMatching,
                'nextOffset' => $offset + $perPage,
            ]);
        }

        $profOrganizationsData = ProfessionalDevelopment::where('user_id', $userId)->where('criterion', 'prof-organizations')->orderBy('created_at', 'desc');

        $professionalDevelopmentOptions = $this->getProfessionalDevelopmentOptions();

        return view('instructor.professional-development-page', [
            'profOrganizationsData' => (clone $profOrganizationsData)->take($perPage)->get(),
            'profTrainingData'      => ProfessionalDevelopment::where('user_id', $userId)->where('criterion', 'prof-training')->orderBy('created_at', 'desc')->take($perPage)->get(),
            'profAwardsData'        => ProfessionalDevelopment::where('user_id', $userId)->where('criterion', 'prof-awards')->orderBy('created_at', 'desc')->take($perPage)->get(),
            'perPage'                       => $perPage,
            'initialHasMore'                => $profOrganizationsData->count() > $perPage,
            'professionalDevelopmentOptions' => $professionalDevelopmentOptions,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $criterion = $request->input('criterion');
            $validatedData = $this->validateRequest($request, $criterion, $user->id);

            $kraFolderName = 'KRA IV: Professional Development';
            $folderNameMap = [
                'prof-organizations' => 'Involvement in Professional Organizations',
                'prof-training'      => 'Continuing Professional Education & Training',
                'prof-awards'        => 'Awards and Recognitions',
            ];
            $subFolderName = $folderNameMap[$criterion] ?? ucfirst(str_replace('-', ' ', $criterion));

            $dataToCreate = [
                'user_id' => $user->id,
                'criterion' => $criterion,
            ];

            // Unset checkbox values so they don't get added to the database
            unset($validatedData['is_officer']);

            foreach ($validatedData as $key => $value) {
                if (!$request->hasFile($key)) {
                    $dataToCreate[$key] = $value;
                }
            }

            if ($request->hasFile('proof_file')) {
                $fileId = $this->uploadFileToGoogleDrive($request, 'proof_file', $kraFolderName, $subFolderName);
                $dataToCreate['google_drive_file_id'] = $fileId;
                $dataToCreate['filename'] = $request->file('proof_file')->getClientOriginalName();
            }

            ProfessionalDevelopment::create($dataToCreate);

            return response()->json(['success' => true, 'message' => 'Successfully uploaded!'], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Professional Development Upload Failed: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return response()->json(['message' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    private function validateRequest(Request $request, string $criterion, int $userId): array
    {
        $rules = [];
        $options = $this->getProfessionalDevelopmentOptions();

        if ($criterion === 'prof-organizations') {
            $rules = [
                'title'           => ['required', 'string', 'max:255'],
                'membership_type' => ['required', Rule::in($options['po_membership_types'])],
                'is_officer'      => 'nullable|boolean',
                'role'            => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::requiredIf(fn() => $request->has('is_officer')),
                ],
                'start_date'      => 'required|date|before_or_equal:today',
                'end_date'        => 'required|date|after_or_equal:start_date',
                'proof_file'      => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            ];
        } elseif ($criterion === 'prof-training') {
            $nonDegreeTypes = ['Training / Seminar / Workshop', 'Conference / Forum / Symposium'];
            $rules = [
                'title'        => ['required', 'string', 'max:255'],
                'type'         => ['required', Rule::in($options['pt_types'])],
                'organizer'    => 'required|string|max:255',
                'start_date'   => 'required|date|before_or_equal:today',
                'end_date'     => 'required|date|after_or_equal:start_date',
                'hours'        => [
                    'nullable',
                    'integer',
                    'min:1',
                    Rule::requiredIf(fn() => in_array($request->input('type'), $nonDegreeTypes)),
                ],
                'level'        => [
                    'nullable',
                    'string',
                    Rule::requiredIf(fn() => !in_array($request->input('type'), $nonDegreeTypes)),
                ],
                'proof_file'   => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            ];
        } elseif ($criterion === 'prof-awards') {
            $rules = [
                'title'         => ['required', 'string', 'max:255'],
                'awarding_body' => 'required|string|max:255',
                'level'         => ['required', Rule::in($options['pa_levels'])],
                'end_date'      => ['required', 'date', 'before_or_equal:today'],
                'proof_file'    => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            ];
        }

        return $request->validate($rules);
    }

    public function destroy(ProfessionalDevelopment $professionalDevelopment): JsonResponse
    {
        if ($professionalDevelopment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        try {
            if ($professionalDevelopment->google_drive_file_id) {
                $this->deleteFileFromGoogleDrive($professionalDevelopment->google_drive_file_id);
            }
            $professionalDevelopment->delete();
            return response()->json(['message' => 'Item deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Professional Development Deletion Failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete the item. Please try again later.'], 500);
        }
    }

    public function getFileInfo($id)
    {
        $professionalDevelopment = ProfessionalDevelopment::findOrFail($id);

        if ($professionalDevelopment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$professionalDevelopment->google_drive_file_id) {
            return response()->json(['message' => 'No file associated with this record.'], 404);
        }

        $viewUrl = route('instructor.professional-development.view-file', ['id' => $id]);

        $fileInfoResponse = $this->getFileInfoById($professionalDevelopment->google_drive_file_id, $viewUrl);
        $fileInfoData = $fileInfoResponse->getData(true);

        $recordData = $this->formatRecordDataForViewer($professionalDevelopment);

        $responseData = array_merge($fileInfoData, ['recordData' => $recordData]);

        return response()->json($responseData);
    }

    public function viewFile($id, Request $request)
    {
        $professionalDevelopment = ProfessionalDevelopment::findOrFail($id);

        if ($professionalDevelopment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $this->viewFileById($professionalDevelopment->google_drive_file_id, $request);
    }

    /**
     * Formats a ProfessionalDevelopment record's data for the file viewer.
     *
     * @param \App\Models\ProfessionalDevelopment $professionalDevelopment
     * @return array
     */
    private function formatRecordDataForViewer(ProfessionalDevelopment $professionalDevelopment): array
    {
        $data = [];
        switch ($professionalDevelopment->criterion) {
            case 'prof-organizations':
                $data = [
                    'Organization Name' => $professionalDevelopment->title,
                    'Membership Type' => $professionalDevelopment->membership_type,
                    'Start Date' => Carbon::parse($professionalDevelopment->start_date)->format('F j, Y'),
                    'End Date' => Carbon::parse($professionalDevelopment->end_date)->format('F j, Y'),
                ];
                if ($professionalDevelopment->role) {
                    $data['Role (as Officer)'] = $professionalDevelopment->role;
                }
                break;

            case 'prof-training':
                $data = [
                    'Title of Training/Degree' => $professionalDevelopment->title,
                    'Type' => $professionalDevelopment->type,
                    'Organizer/Institution' => $professionalDevelopment->organizer,
                    'Start Date' => Carbon::parse($professionalDevelopment->start_date)->format('F j, Y'),
                    'Completion Date' => Carbon::parse($professionalDevelopment->end_date)->format('F j, Y'),
                ];
                if ($professionalDevelopment->hours) {
                    $data['Number of Hours'] = $professionalDevelopment->hours;
                }
                if ($professionalDevelopment->level) {
                    $data['Level'] = $professionalDevelopment->level;
                }
                break;

            case 'prof-awards':
                $data = [
                    'Award Title' => $professionalDevelopment->title,
                    'Awarding Body' => $professionalDevelopment->awarding_body,
                    'Level' => $professionalDevelopment->level,
                    'Date Awarded' => Carbon::parse($professionalDevelopment->end_date)->format('F j, Y'),
                ];
                break;
        }

        $data['Date Uploaded'] = $professionalDevelopment->created_at->format('F j, Y, g:i A');
        return $data;
    }
}
