<tr id="material-row-{{ $material->id }}">
    <td>{{ $material->id }}</td>
    <td>{{ $material->title }}</td>
    <td>{{ Str::title(str_replace('_', ' ', $material->category)) }}</td>
    <td>{{ $material->created_at->format('F j, Y') }}</td>
    <td>{{ $material->type }}</td>
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
        <button class="btn btn-danger delete-btn"
            data-delete-url="{{ route('instructor.instructional-materials.destroy', $material->id) }}"
            data-item-title="{{ $material->title }}">
            <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
        </button>
    </td>
</tr>