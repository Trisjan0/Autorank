{{-- FILE VIEWER MODAL --}}
<div class="modal-container modal-container--hidden" id="fileViewerModal">
    <div class="modal-content modal-fullscreen">
        <div class="modal-header">
            <h5 class="modal-title" id="fileViewerModalLabel">Viewing File</h5>
            <button class="close-modal-btn" id="closeModalBtn">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="loader-container">
                <div class="loader">
                    <div class="bar1"></div>
                    <div class="bar2"></div>
                    <div class="bar3"></div>
                    <div class="bar4"></div>
                    <div class="bar5"></div>
                    <div class="bar6"></div>
                    <div class="bar7"></div>
                    <div class="bar8"></div>
                    <div class="bar9"></div>
                    <div class="bar10"></div>
                    <div class="bar11"></div>
                    <div class="bar12"></div>
                </div>
            </div>
            <div class="file-feedback-container" id="fileViewerFeedback" style="display: none;">
                <p>This file type cannot be previewed directly on the website.</p>
                <a href="#" id="fileViewerDownloadBtn" class="btn btn-primary" download>Download File</a>
            </div>
            <iframe id="fileViewerIframe" src="" frameborder="0"></iframe>
        </div>
    </div>
</div>

{{-- DELETE CONFIRMATION MODAL --}}
<div class="modal-container" id="deleteConfirmationModal" style="display: none;">
    <div class="delete-modal-box">
        <div class="delete-modal-header">
            <i class="fa-solid fa-xmark" id="closeDeleteModalBtn"></i>
        </div>
        <div class="delete-modal-body">
            <h1 class="delete-modal-title">Confirm Deletion</h1>
            <p id="deleteModalText">Are you sure you want to delete this item? This action cannot be undone.</p>
            <div id="delete-final-status-message-area" class="mt-2"></div>
        </div>
        <div class="delete-modal-actions">
            <button type="button" class="btn-cancel" id="cancelDeleteBtn">Cancel</button>
            <button type="button" class="btn-delete" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>