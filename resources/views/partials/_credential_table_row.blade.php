<tr id="credential-{{ $credential->id }}">
    <td>{{ $credential->title }}</td>
    <td>{{ $credential->filename }}</td>
    <td>{{ $credential->created_at->format('F d, Y') }}</td>
    <td>
        <div class="action-buttons">
            <button
                class="btn btn-info view-file-btn"
                data-info-url="{{ route('credentials.file-info', $credential) }}"
                data-filename="{{ $credential->filename }}">
                View File
            </button>
            <button
                class="btn btn-danger delete-btn"
                data-item-title="{{ $credential->title }}"
                data-delete-url="{{ route('credentials.destroy', $credential) }}">
                <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
            </button>
        </div>
    </td>
</tr>