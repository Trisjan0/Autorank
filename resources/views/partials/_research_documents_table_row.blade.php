<tr id="document-row-{{ $document->id }}">
    <td>{{ $document->id }}</td>
    <td>{{ $document->title }}</td>
    <td>{{ $document->type }}</td>
    <td>{{ $document->category }}</td>
    <td>{{ $document->created_at->format('F j, Y') }}</td>
    <td>{{ $document->publish_date ? \Carbon\Carbon::parse($document->publish_date)->format('F j, Y') : 'N/A' }}</td>
    <td>
        @if($document->google_drive_file_id)
        <button class="btn btn-primary view-file-btn"
            data-filename="{{ $document->title }}"
            data-info-url="{{ route('instructor.research-documents.file-info', $document->id) }}">
            <i class="fa-solid fa-up-right-from-square" style="color: #ffffff;"></i>&nbsp;View File
        </button>
        @else
        <span class="table-data-secondary-text">No File</span>
        @endif
        &nbsp;
        <button class="btn btn-danger delete-btn"
            data-delete-url="{{ route('instructor.research-documents.destroy', $document->id) }}"
            data-item-title="{{ $document->title }}">
            <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
        </button>
    </td>
</tr>