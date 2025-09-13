<tr>
    <td>{{ $material->id }}</td>
    <td>{{ $material->title }}</td>
    <td>{{ Str::title(str_replace('_', ' ', $material->category)) }}</td>
    <td>{{ \Carbon\Carbon::parse($material->date)->format('m/d/Y') }}</td>
    <td>{{ $material->type }}</td>
    <td>
        <div class="action-container">
            @if($material->file_path)
            <button><a href="{{ asset('storage/' . $material->file_path) }}" target="_blank">View File</a></button>
            @else
            N/A
            @endif
            <button>Edit</button>
        </div>
    </td>
</tr>