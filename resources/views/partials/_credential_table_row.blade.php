<tr data-id="{{ $credential->id }}">
    <td>{{ $credential->title }}</td>
    <td>{{ $credential->type }}</td>
    <td>{{ $credential->filename }}</td>
    <td>{{ $credential->created_at->format('F d, Y') }}</td>
    <td>
        <div class="action-buttons">
            <button
                class="btn btn-info view-file-btn"
                data-info-url="{{ route('credentials.file-info', $credential) }}"
                data-filename="{{ $credential->filename }}">
                View File
            </button>
            <button
                class="btn btn-danger confirm-action-btn"
                data-action-url="{{ route('credentials.destroy', $credential) }}"
                data-modal-title="Confirm Deletion"
                data-modal-text="This action will delete the credential file from the system and Google Drive. It cannot be undone."
                data-item-title="{{ $credential->title }}"
                data-confirm-button-text="Delete">
                <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
            </button>
        </div>
    </td>
</tr>