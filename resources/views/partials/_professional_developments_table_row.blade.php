<tr id="development-row-{{ $development->id }}">
    <td>{{ $development->id }}</td>
    <td>{{ $development->title }}</td>
    <td>{{ $development->category }}</td>
    <td>{{ $development->publish_date ? \Carbon\Carbon::parse($development->publish_date)->format('F j, Y') : 'N/A' }}</td>
    <td>{{ $development->created_at->format('F j, Y') }}</td>
    <td>
        @if($development->google_drive_file_id)
        <button class="btn btn-primary view-file-btn"
            data-filename="{{ $development->title }}"
            data-info-url="{{ route('instructor.professional-developments.file-info', $development->id) }}">
            <i class="fa-solid fa-up-right-from-square" style="color: #ffffff;"></i>&nbsp;View File
        </button>
        @else
        <span class="table-data-secondary-text">No File</span>
        @endif
        &nbsp;
        <button class="btn btn-danger delete-btn"
            data-delete-url="{{ route('instructor.professional-developments.destroy', $development->id) }}"
            data-item-title="{{ $development->title }}">
            <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
        </button>
    </td>
</tr>