{{-- This file controls the display of a single row in the evaluations table --}}
<tr id="evaluations-row-{{ $evaluation->id }}">
    <td>{{ $evaluation->id }}</td>
    <td>{{ Str::title($evaluation->title) }}</td>
    <td>{{ Str::title($evaluation->category) }}</td>
    <td>{{ $evaluation->created_at->format('m/d/Y') }}</td>
    <td>
        @if($evaluation->publish_date)
        {{ \Carbon\Carbon::parse($evaluation->publish_date)->format('m/d/Y') }}
        @else
        N/A
        @endif
    </td>
    <td>{{ $evaluation->score }}</td>
    <td>
        <div class="action-container">
            @if($evaluation->file_path)
            <button><a href="{{ asset('storage/' . $evaluation->file_path) }}" target="_blank">View File</a></button>
            @else
            N/A
            @endif
            <button id="edit-evaluations-btn" data-evaluation-id="{{ $evaluation->id }}">Edit</button>
        </div>
    </td>
</tr>