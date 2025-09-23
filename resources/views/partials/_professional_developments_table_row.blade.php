<tr id="development-row-{{ $development->id }}">
    <td>{{ $development->id }}</td>
    <td>{{ $development->title }}</td>
    <td>{{ $development->category }}</td>
    <td>{{ $development->type }}</td>
    <td>{{ $development->publish_date ? \Carbon\Carbon::parse($development->publish_date)->format('m/d/y') : 'N/A' }}</td>
    <td>{{ $development->created_at->format('m/d/y') }}</td>
    <td>
        @php
        $scoreToDisplay = $development->sub_cat1_score
        ?? $development->sub_cat2_score
        ?? $development->sub_cat3_score;
        @endphp
        {{ $scoreToDisplay === null || $scoreToDisplay == 0 
            ? 'TBE' 
            : rtrim(rtrim(number_format($scoreToDisplay, 2), '0'), '.') }}
    </td>
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
        <button class="btn btn-danger confirm-action-btn"
            data-action-url="{{ route('instructor.professional-developments.destroy', $development->id) }}"
            data-modal-title="Confirm Deletion"
            data-modal-text="This action will delete the file from the system and Google Drive. It cannot be undone."
            data-item-title="{{ $development->title }}"
            data-confirm-button-text="Delete">
            <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
        </button>
    </td>
</tr>