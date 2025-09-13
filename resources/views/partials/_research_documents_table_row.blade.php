<tr>
    <td>{{ $document->id }}</td>
    <td>{{ $document->title }}</td>
    <td>{{ $document->type }}</td>
    <td>{{ $document->category }}</td>
    <td>
        <div class="action-container">
            @if($document->file_path)
            <button><a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">View File</a></button>
            @else
            N/A
            @endif
            <button>Edit</button>
        </div>
    </td>
</tr>