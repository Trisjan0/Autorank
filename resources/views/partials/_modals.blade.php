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
<div class="modal-container" id="confirmationModal" style="display: none;">
    <div class="confirmation-modal-box">
        <div class="confirmation-modal-header">
            <i class="fa-solid fa-xmark" id="closeConfirmationModalBtn"></i>
        </div>
        <div class="confirmation-modal-body">
            <h1 class="confirmation-modal-title" id="confirmationModalTitle"></h1>
            <p id="confirmationModalText"></p>
            <div id="confirmation-final-status-message-area" class="mt-2"></div>
        </div>
        <div class="confirmation-modal-actions">
            <button type="button" class="btn-cancel" id="cancelConfirmationBtn">Cancel</button>
            <button type="button" class="btn-confirm" id="confirmActionBtn"></button>
        </div>
    </div>
</div>