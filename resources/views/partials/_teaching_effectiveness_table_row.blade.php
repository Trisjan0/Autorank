<tr data-id="{{ $item->id }}">
    <td>{{ $item->id }}</td>
    <td>{{ $item->academic_period }}</td>
    <td>{{ $item->student_score }}</td>
    <td>{{ $item->supervisor_score }}</td>
    <td>{{ $item->total_score }}</td>
    <td>{{ $item->created_at->format('F d, Y') }}</td>
    <td>
        <div class="action-buttons">
            {{-- This single button now holds info for both files --}}
            <button
                class="btn btn-info view-file-btn"
                data-info-url-student="{{ route('instructor.instruction.file-info', ['id' => $item->id, 'fileType' => 'student']) }}"
                data-filename-student="{{ $item->student_proof_filename }}"
                @if($item->supervisor_proof_filename)
                data-info-url-supervisor="{{ route('instructor.instruction.file-info', ['id' => $item->id, 'fileType' => 'supervisor']) }}"
                data-filename-supervisor="{{ $item->supervisor_proof_filename }}"
                @endif
                >
                View Files
            </button>
            <button
                class="btn btn-danger confirm-action-btn"
                data-action-url="{{ route('instructor.instruction.destroy', ['instruction' => $item->id]) }}"
                data-modal-title="Confirm Deletion"
                data-modal-text="This will delete the record and its associated file(s) from Google Drive. This action cannot be undone."
                data-item-title="{{ $item->academic_period }}"
                data-confirm-button-text="Delete">
                <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
            </button>
        </div>
    </td>
</tr>