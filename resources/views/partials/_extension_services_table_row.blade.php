<tr>
    <td>{{ $service->id }}</td>
    <td>{{ $service->title }}</td>
    <td>{{ $service->service_type }}</td>
    <td>{{ \Carbon\Carbon::parse($service->date)->format('m/d/Y') }}</td>
    <td>
        <div class="action-container">
            @if($service->file_path)
            <button><a href="{{ asset('storage/' . $service->file_path) }}" target="_blank">View File</a></button>
            @else
            N/A
            @endif
            <button>Edit</button>
        </div>
    </td>
</tr>