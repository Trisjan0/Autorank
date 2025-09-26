<tr data-id="{{ $item->id }}">
    <td>{{ $item->id }}</td>
    <td>{{ $item->title }}</td>
    <td>{{ $item->category }}</td>
    <td>{{ $item->type ?? 'N/A' }}</td>
    <td>{{ $item->role }}</td>
    <td>{{ \Carbon\Carbon::parse($item->publication_date)->format('F d, Y') }}</td>
    <td>{{ $item->score ?? 'TBE' }}</td>
    <td>
        <div class="action-buttons">
            <button
                class="btn btn-info view-file-btn"
                data-info-url="{{ route('instructor.instruction.file-info', ['id' => $item->id]) }}"
                data-filename="{{ $item->student_proof_filename }}">
                View File
            </button>
            <button
                class="btn btn-danger confirm-action-btn"
                data-action-url="{{ route('instructor.instruction.destroy', ['instruction' => $item->id]) }}"
                data-modal-title="Confirm Deletion"
                data-modal-text="This will delete the record and its associated file from Google Drive. This action cannot be undone."
                data-item-title="{{ $item->title }}"
                data-confirm-button-text="Delete">
                <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
            </button>
        </div>
    </td>
</tr>