<tr id="service-row-{{ $service->id }}">
    <td>{{ $service->id }}</td>
    <td>{{ $service->title }}</td>
    <td>{{ $service->service_type }}</td>
    <td>{{ \Carbon\Carbon::parse($service->date)->format('F j, Y') }}</td>
    <td>
        @if($service->google_drive_file_id)
        <button class="btn btn-primary view-file-btn"
            data-filename="{{ $service->title }}"
            data-info-url="{{ route('instructor.extension-services.file-info', $service->id) }}">
            <i class="fa-solid fa-up-right-from-square" style="color: #ffffff;"></i>&nbsp;View File
        </button>
        @else
        <span class="table-data-secondary-text">No File</span>
        @endif
        &nbsp;
        <button class="btn btn-danger confirm-action-btn"
            data-action-url="{{ route('instructor.extension-services.destroy', $service->id) }}"
            data-modal-title="Confirm Deletion"
            data-modal-text="This action will delete the file from the system and Google Drive. It cannot be undone."
            data-item-title="{{ $service->title }}"
            data-confirm-button-text="Delete">
            <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
        </button>
    </td>
</tr>