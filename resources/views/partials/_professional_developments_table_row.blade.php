<tr>
    <td>{{ $development->id }}</td>
    <td>{{ $development->title }}</td>
    <td>{{ $development->category }}</td>
    <td>{{ \Carbon\Carbon::parse($development->date)->format('m/d/Y') }}</td>
    <td>
        <div class="action-container">
            @if($development->file_path)
            <button><a href="{{ asset('storage/' . $development->file_path) }}" target="_blank">View File</a></button>
            @else
            N/A
            @endif
            <button>Edit</button>
        </div>
    </td>
</tr>