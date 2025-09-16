{{-- In resources/views/partials/_evaluations_table_row.blade.php --}}
<tr id="evaluation-row-{{ $evaluation->id }}">
    <td>{{ $evaluation->id }}</td>
    <td>{{ $evaluation->title }}</td>
    <td>{{ ucfirst($evaluation->category) }}</td>
    <td>{{ $evaluation->created_at->format('F j, Y') }}</td>
    <td>{{ $evaluation->publish_date ? \Carbon\Carbon::parse($evaluation->publish_date)->format('F j, Y') : 'N/A' }}</td>
    <td>{{ rtrim(rtrim(number_format($evaluation->score, 2), '0'), '.') }}</td>

    <td>
        @if($evaluation->google_drive_file_id)
        <button class="view-file-btn"
            data-fileid="{{ $evaluation->google_drive_file_id }}"
            data-filename="{{ $evaluation->title }}">
            <i class="fa-solid fa-up-right-from-square" style="color: #ffffff;"></i>
            <span>&nbsp;View File</span>
        </button>
        @else
        <span class="table-data-secondary-text">No File</span>
        @endif
    </td>
</tr>