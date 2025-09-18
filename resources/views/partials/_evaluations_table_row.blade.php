<tr id="evaluation-row-{{ $evaluation->id }}">
    <td>{{ $evaluation->id }}</td>
    <td>{{ $evaluation->title }}</td>
    <td>{{ ucfirst($evaluation->category) }}</td>
    <td>{{ $evaluation->created_at->format('F j, Y') }}</td>
    <td>{{ $evaluation->publish_date ? \Carbon\Carbon::parse($evaluation->publish_date)->format('F j, Y') : 'N/A' }}</td>
    <td>{{ rtrim(rtrim(number_format($evaluation->score, 2), '0'), '.') }}</td>
    <td>
        @if($evaluation->google_drive_file_id)
        <button class="btn btn-primary view-file-btn"
            data-evaluationid="{{ $evaluation->id }}"
            data-filename="{{ $evaluation->title }}"
            data-info-url="{{ route('instructor.evaluations.file-info', $evaluation->id) }}"> <i class="fa-solid fa-up-right-from-square" style="color: #ffffff;"></i>&nbsp;View File
        </button>
        @else
        <span class="table-data-secondary-text">No File</span>
        @endif
        &nbsp;
        <button class="btn btn-danger delete-evaluation-btn"
            data-delete-url="{{ route('instructor.evaluations.destroy', $evaluation->id) }}"
            data-evaluation-title="{{ $evaluation->title }}">
            <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
        </button>
    </td>
</tr>