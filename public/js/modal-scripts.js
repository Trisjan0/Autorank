document.addEventListener('DOMContentLoaded', () => {
    function initializeActionModals() {
        const fileViewerModal = document.getElementById('fileViewerModal');
        const confirmationModal = document.getElementById('confirmationModal');

        /*
        |--------------------------------------------------------------------------
        | FILE VIEWER MODAL
        |--------------------------------------------------------------------------
        */
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
                if (detailsPanel) detailsPanel.classList.remove('file-details-panel--hidden');
                if (toggleDetailsBtn) toggleDetailsBtn.classList.add('active');
            };

            const closeFileViewer = () => { 
                fileViewerModal.classList.add('modal-container--hidden'); 
                document.body.classList.remove('modal-open'); 
                iframe.src = 'about:blank';
                if (slider) slider.style.display = 'none';
                if (detailsContent) detailsContent.innerHTML = '';
                if (detailsPanel) detailsPanel.classList.remove('file-details-panel--hidden');
                if (toggleDetailsBtn) toggleDetailsBtn.classList.add('active');
            };
            
            const loadfile = async (fileInfo) => {
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
                            const value = data.recordData[key];
                            detailsHtml += `
                                <div class="file-details-content-item">
                                    <strong>${key}</strong>
                                    <span>${value}</span>
                                </div>
                            `;
                        }
                        detailsContent.innerHTML = detailsHtml;
                    }

                    modalLabel.textContent = `Viewing: ${fileInfo.filename}`;
                    iframe.addEventListener('load', () => { loader.style.display = 'none'; iframe.style.display = 'block'; }, { once: true });

                    if (data.isViewable) {
                        iframe.src = `${data.viewUrl}#toolbar=1`;
                    } else {
                        downloadBtn.href = `${data.viewUrl}?download=true`;
                        feedbackContainer.style.display = 'flex';
                        loader.style.display = 'none';
                    }
                } catch (error) {
                    modalLabel.textContent = 'Error';
                    feedbackContainer.querySelector('p').textContent = 'Could not load the file.';
                    downloadBtn.style.display = 'none';
                    feedbackContainer.style.display = 'flex';
                    loader.style.display = 'none';
                }
            };

            const updateSlider = () => {
                counter.textContent = `${currentIndex + 1} / ${files.length}`;
                prevBtn.disabled = currentIndex === 0;
                nextBtn.disabled = currentIndex === files.length - 1;
                loadfile(files[currentIndex]);
            };
            
            if (prevBtn) prevBtn.addEventListener('click', () => { if (currentIndex > 0) { currentIndex--; updateSlider(); } });
            if (nextBtn) nextBtn.addEventListener('click', () => { if (currentIndex < files.length - 1) { currentIndex++; updateSlider(); } });

            if (toggleDetailsBtn && detailsPanel) {
                toggleDetailsBtn.addEventListener('click', () => {
                    detailsPanel.classList.toggle('file-details-panel--hidden');
                    toggleDetailsBtn.classList.toggle('active');
                });
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
                        slider.style.display = files.length > 1 ? 'flex' : 'none';
                        currentIndex = 0;
                        updateSlider();
                    }
                }
            });
            closeModalBtn.addEventListener('click', closeFileViewer);
            fileViewerModal.addEventListener('click', (e) => (e.target === fileViewerModal) && closeFileViewer());
        }

        /*
        |--------------------------------------------------------------------------
        | CONFIRMATION MODAL
        |--------------------------------------------------------------------------
        */
        if (confirmationModal) {
            const closeBtn = document.getElementById('closeConfirmationModalBtn');
            const cancelBtn = document.getElementById('cancelConfirmationBtn');
            const confirmBtn = document.getElementById('confirmActionBtn');
            const modalTitle = document.getElementById('confirmationModalTitle');
            const modalText = document.getElementById('confirmationModalText');
            const statusMessage = document.getElementById('confirmation-final-status-message-area');

            let actionUrl = '';
            let actionMethod = 'DELETE';
            let itemToUpdate = null;
            let onConfirmCallback = null; // To handle custom confirm actions

            // Make this function globally accessible and more flexible
            window.showConfirmationModal = (source) => {
                // Reset state
                statusMessage.innerHTML = '';
                confirmBtn.disabled = false;
                confirmBtn.className = 'btn-confirm'; 
                cancelBtn.disabled = false;
                cancelBtn.style.display = 'inline-block'; // Show cancel button by default
                onConfirmCallback = null; // Reset callback
                itemToUpdate = null;
                actionUrl = '';
                actionMethod = 'DELETE';

                // Check if the source is a button element or a config object
                if (source.nodeType === 1) { // It's a DOM element (a button)
                    itemToUpdate = source.closest('tr') || source.closest('.position-card');
                    actionUrl = source.dataset.actionUrl;
                    actionMethod = source.dataset.method || 'DELETE';
                    modalTitle.textContent = source.dataset.modalTitle;
                    modalText.innerHTML = source.dataset.modalText;
                    confirmBtn.textContent = source.dataset.confirmButtonText;

                    if (actionMethod !== 'DELETE') {
                        confirmBtn.classList.add('btn-primary-confirm');
                    }
                } else { // It's a configuration object
                    modalTitle.textContent = source.title;
                    modalText.innerHTML = source.text;
                    confirmBtn.textContent = source.confirmButtonText;
                    actionUrl = source.actionUrl || '';
                    actionMethod = source.method || 'POST';

                    if (source.type === 'success') {
                        confirmBtn.classList.add('btn-success-confirm');
                    } else {
                        confirmBtn.classList.add('btn-primary-confirm');
                    }

                    if (!actionUrl) {
                        cancelBtn.style.display = 'none';
                        onConfirmCallback = hideConfirmationModal;
                    }
                }

                document.body.classList.add('modal-open');
                confirmationModal.style.display = 'flex';
            };

            const hideConfirmationModal = () => {
                document.body.classList.remove('modal-open');
                confirmationModal.style.display = 'none';
            };

            document.body.addEventListener('click', (event) => {
                const actionButton = event.target.closest('.confirm-action-btn');
                if (actionButton) {
                    window.showConfirmationModal(actionButton);
                }
            });

            closeBtn.addEventListener('click', hideConfirmationModal);
            cancelBtn.addEventListener('click', hideConfirmationModal);
            confirmationModal.addEventListener('click', (e) => {
                if (e.target === confirmationModal) hideConfirmationModal();
            });

            confirmBtn.addEventListener('click', async () => {
                // If a custom callback is set (for simple notifications), just run it.
                if (onConfirmCallback) {
                    onConfirmCallback();
                    return;
                }

                // If there's no action URL, do nothing.
                if (!actionUrl) {
                    hideConfirmationModal();
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                confirmBtn.disabled = true;
                cancelBtn.disabled = true;
                statusMessage.innerHTML = '<div class="alert-info">Processing...</div>';

                try {
                    const response = await fetch(actionUrl, {
                        method: actionMethod,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                        // Pass body for methods that need it (e.g., POST, PATCH)
                        body: (actionMethod !== 'GET' && actionMethod !== 'DELETE') ? JSON.stringify({}) : null,
                    });

                    const data = await response.json();
                    if (response.ok) {
                        statusMessage.innerHTML = `<div class="alert-success">${data.message}</div>`;
                        
                        if (data.redirect_url) {
                            // Redirect if the server sends a redirect URL
                            setTimeout(() => {
                            window.location.href = data.redirect_url;
                            }, 1000);
                        } else if (typeof window.updatePositionCard === "function") {
                            window.updatePositionCard(data);
                        } else if (typeof window.loadData === "function") {
                            window.loadData(true);
                        } else if (itemToUpdate) {
                            itemToUpdate.remove();
                        }

                        setTimeout(hideConfirmationModal, 850);
                    } else {
                        statusMessage.innerHTML = `<div class="alert-danger">${data.message || 'Failed to perform action.'}</div>`;
                        confirmBtn.disabled = false;
                        cancelBtn.disabled = false;
                    }
                } catch (error) {
                    console.error('Error performing action:', error);
                    statusMessage.innerHTML = `<div class="alert-danger">A network error occurred.</div>`;
                    confirmBtn.disabled = false;
                    cancelBtn.disabled = false;
                }
            });
        }
    }

    window.initializeActionModals = initializeActionModals;
});
