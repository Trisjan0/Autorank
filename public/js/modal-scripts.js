/**
 * @file Manages all reusable modals for the application.
 * This script provides global functions for showing a confirmation modal
 * and initializing all modal-related event listeners on a page.
 */

/**
 * Displays a confirmation modal with custom options and an action to perform.
 * This is the new, flexible way to show a confirmation dialog from any script.
 *
 * @param {object} options - The configuration for the modal.
 * @param {string} options.title - The title to display in the modal header.
 * @param {string} options.body - The text or HTML to display in the modal body.
 * @param {string} [options.confirmText='Confirm'] - The text for the confirm button.
 * @param {Function} options.onConfirm - The function to execute when the user clicks the confirm button.
 */
function showConfirmationModal({ title, body, confirmText = 'Confirm', onConfirm }) {
    const modal = document.getElementById('confirmationModal');
    if (!modal) {
        console.error('Confirmation modal not found. Ensure the _action_modals.blade.php partial is included on this page.');
        return;
    }

    // Get references to all the modal's parts
    const modalTitle = document.getElementById('confirmationModalTitle');
    const modalText = document.getElementById('confirmationModalText');
    const confirmBtn = document.getElementById('confirmActionBtn');
    const cancelBtn = document.getElementById('cancelConfirmationBtn');
    const statusMessage = document.getElementById('confirmation-final-status-message-area');

    // Populate the modal with the new content
    modalTitle.textContent = title;
    modalText.innerHTML = body; // Use innerHTML to allow for formatted text
    confirmBtn.textContent = confirmText;

    // Reset the modal to a clean state
    statusMessage.innerHTML = '';
    confirmBtn.disabled = false;
    cancelBtn.style.display = 'inline-block';

    // This is a crucial step to prevent old event listeners from firing.
    // We clone the button to remove any previous click handlers before adding the new one.
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

    // Add the new action for this specific confirmation
    newConfirmBtn.addEventListener('click', async () => {
        newConfirmBtn.disabled = true;
        cancelBtn.disabled = true;
        statusMessage.innerHTML = `<div class="alert-info">Processing...</div>`;

        try {
            await onConfirm(); // Execute the action provided
        } catch (error) {
            statusMessage.innerHTML = `<div class="alert-danger">${error.message}</div>`;
            newConfirmBtn.disabled = false;
            cancelBtn.disabled = false;
        }
    });

    document.body.classList.add('modal-open');
    modal.style.display = 'flex';
}

/**
 * Hides the main confirmation modal.
 */
function hideConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    if (modal) {
        document.body.classList.remove('modal-open');
        modal.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    /**
     * This function sets up all the initial event listeners for the modals on the page.
     * It is exposed globally so that other scripts can call it if needed.
     */
    function initializeActionModals() {
        // --- File Viewer Modal ---
        const fileViewerModal = document.getElementById('fileViewerModal');
        if (fileViewerModal) {
            const iframe = document.getElementById('fileViewerIframe');
            const modalLabel = document.getElementById('fileViewerModalLabel');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const loader = fileViewerModal.querySelector('.loader-container');
            const feedbackContainer = document.getElementById('fileViewerFeedback');
            const downloadBtn = document.getElementById('fileViewerDownloadBtn');
            const slider = document.getElementById('fileViewerSlider');
            const prevBtn = document.getElementById('prevFileBtn');
            const nextBtn = document.getElementById('nextFileBtn');
            const counter = document.getElementById('fileCounter');
            const detailsContent = document.getElementById('file-details-content');
            const toggleDetailsBtn = document.getElementById('toggleDetailsBtn');
            const detailsPanel = fileViewerModal.querySelector('.file-details-panel');

            let files = [];
            let currentIndex = 0;

            const openFileViewer = () => {
                fileViewerModal.classList.remove('modal-container--hidden');
                document.body.classList.add('modal-open');
                detailsPanel?.classList.remove('file-details-panel--hidden');
                toggleDetailsBtn?.classList.add('active');
            };

            const closeFileViewer = () => {
                fileViewerModal.classList.add('modal-container--hidden');
                document.body.classList.remove('modal-open');
                if (iframe) iframe.src = 'about:blank';
                if (slider) slider.style.display = 'none';
                if (detailsContent) detailsContent.innerHTML = '';
                detailsPanel?.classList.remove('file-details-panel--hidden');
                toggleDetailsBtn?.classList.add('active');
            };

            const loadFile = async (fileInfo) => {
                if (!fileInfo) return;
                loader.style.display = 'flex';
                iframe.style.display = 'none';
                feedbackContainer.style.display = 'none';
                modalLabel.textContent = `Loading: ${fileInfo.filename}`;
                if (detailsContent) detailsContent.innerHTML = '';

                try {
                    const response = await fetch(fileInfo.infoUrl);
                    if (!response.ok) throw new Error('Failed to fetch file info.');
                    const data = await response.json();

                    if (detailsContent && data.recordData) {
                        let detailsHtml = '';
                        for (const key in data.recordData) {
                            detailsHtml += `<div class="file-details-content-item"><strong>${key}</strong><span>${data.recordData[key]}</span></div>`;
                        }
                        detailsContent.innerHTML = detailsHtml;
                    }

                    modalLabel.textContent = `Viewing: ${fileInfo.filename}`;
                    iframe.onload = () => {
                        loader.style.display = 'none';
                        iframe.style.display = 'block';
                    };

                    if (data.isViewable) {
                        iframe.src = `${data.viewUrl}#toolbar=1`;
                    } else {
                        downloadBtn.href = `${data.viewUrl}?download=true`;
                        feedbackContainer.style.display = 'flex';
                        loader.style.display = 'none';
                    }
                } catch (error) {
                    modalLabel.textContent = 'Error';
                    if (feedbackContainer) feedbackContainer.querySelector('p').textContent = 'Could not load the file.';
                    if (downloadBtn) downloadBtn.style.display = 'none';
                    if (feedbackContainer) feedbackContainer.style.display = 'flex';
                    loader.style.display = 'none';
                }
            };

            const updateSlider = () => {
                if (counter) counter.textContent = `${currentIndex + 1} / ${files.length}`;
                if (prevBtn) prevBtn.disabled = currentIndex === 0;
                if (nextBtn) nextBtn.disabled = currentIndex === files.length - 1;
                loadFile(files[currentIndex]);
            };

            prevBtn?.addEventListener('click', () => { if (currentIndex > 0) { currentIndex--; updateSlider(); } });
            nextBtn?.addEventListener('click', () => { if (currentIndex < files.length - 1) { currentIndex++; updateSlider(); } });
            
            if (toggleDetailsBtn && !toggleDetailsBtn.dataset.bound) {
            toggleDetailsBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                console.log('Before:', detailsPanel.className);
                detailsPanel.classList.toggle('file-details-panel--hidden');
                toggleDetailsBtn.classList.toggle('active');
                console.log('After:', detailsPanel.className);
            });
            toggleDetailsBtn.dataset.bound = "true"; // prevent double binding
            }


            document.body.addEventListener('click', (event) => {
                const viewButton = event.target.closest('.view-file-btn');
                if (viewButton) {
                    files = [];
                    if (viewButton.dataset.infoUrl) files.push({ infoUrl: viewButton.dataset.infoUrl, filename: viewButton.dataset.filename });
                    if (viewButton.dataset.infoUrlStudent) files.push({ infoUrl: viewButton.dataset.infoUrlStudent, filename: viewButton.dataset.filenameStudent });
                    if (viewButton.dataset.infoUrlSupervisor) files.push({ infoUrl: viewButton.dataset.infoUrlSupervisor, filename: viewButton.dataset.filenameSupervisor });

                    if (files.length > 0) {
                        openFileViewer();
                        if (slider) slider.style.display = files.length > 1 ? 'flex' : 'none';
                        currentIndex = 0;
                        updateSlider();
                    }
                }
            });

            closeModalBtn?.addEventListener('click', closeFileViewer);
            fileViewerModal.addEventListener('click', (e) => { if (e.target === fileViewerModal) closeFileViewer(); });
        }

        // --- Confirmation Modal ---
        const confirmationModal = document.getElementById('confirmationModal');
        if (confirmationModal) {
            const closeBtn = document.getElementById('closeConfirmationModalBtn');
            const cancelBtn = document.getElementById('cancelConfirmationBtn');

            // General closing listeners
            closeBtn?.addEventListener('click', hideConfirmationModal);
            cancelBtn?.addEventListener('click', hideConfirmationModal);
            confirmationModal.addEventListener('click', (e) => {
                if (e.target === confirmationModal) hideConfirmationModal();
            });

            document.body.addEventListener('click', (event) => {
                const actionButton = event.target.closest('.confirm-action-btn');
                if (actionButton) {
                    showConfirmationModal({
                        title: actionButton.dataset.modalTitle,
                        body: actionButton.dataset.modalText,
                        confirmText: actionButton.dataset.confirmButtonText,
                        onConfirm: async () => {
                            const actionUrl = actionButton.dataset.actionUrl;
                            if (!actionUrl) return;

                            const response = await fetch(actionUrl, {
                                method: actionButton.dataset.method || 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json'
                                },
                            });

                            const data = await response.json();
                            if (!response.ok) throw new Error(data.message || 'An error occurred.');

                            // Handle success (remove item, reload data, etc.)
                            const itemToRemove = actionButton.closest('tr');
                            if (itemToRemove) itemToRemove.remove();
                            if (typeof window.loadData === 'function') window.loadData(true);

                            document.getElementById('confirmation-final-status-message-area').innerHTML = `<div class="alert-success">${data.message}</div>`;
                            setTimeout(hideConfirmationModal, 850);
                        }
                    });
                }
            });
        }
    }

    // Run the initialization function once the DOM is ready.
    initializeActionModals();

    // Expose the function globally so other scripts (like kra-scripts.js) can call it.
    window.initializeActionModals = initializeActionModals;
});