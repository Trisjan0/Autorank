<tr id="material-row-{{ $material->id }}">
    <td>{{ $material->id }}</td>
    <td>{{ $material->title }}</td>
    <td>{{ $material->category }}</td>
    <td>{{ $material->type }}</td>
    <td>{{ $material->created_at->format('m/d/y') }}</td>
    <td>{{ $material->score ? rtrim(rtrim(number_format($material->score, 2), '0'), '.') : 'TBE' }}</td>
    <td>
        @if($material->google_drive_file_id)
        <button class="btn btn-primary view-file-btn"
            data-materialid="{{ $material->id }}"
            data-filename="{{ $material->title }}"
            data-info-url="{{ route('instructor.instructional-materials.file-info', $material->id) }}">
            <i class="fa-solid fa-up-right-from-square" style="color: #ffffff;"></i>&nbsp;View File
        </button>
        @else
        <span class="table-data-secondary-text">No File</span>
        @endif
        &nbsp;
        <button class="btn btn-danger confirm-action-btn"
            data-action-url="{{ route('instructor.instructional-materials.destroy', $material->id) }}"
            data-modal-title="Confirm Deletion"
            data-modal-text="This action will delete the file from the system and Google Drive. It cannot be undone."
            data-item-title="{{ $material->title }}"
            data-confirm-button-text="Delete">
            <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
        </button>
    </td>
</tr>