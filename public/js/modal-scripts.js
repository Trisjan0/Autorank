document.addEventListener('DOMContentLoaded', () => {
    /*
    |--------------------------------------------------------------------------
    | CONFIRMATION MODAL
    |--------------------------------------------------------------------------
    */
    const confirmationModal = document.getElementById('confirmationModal');
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
            } else { // It's a configuration object
                modalTitle.textContent = source.title;
                modalText.innerHTML = source.text;
                confirmBtn.textContent = source.confirmButtonText;
                actionUrl = source.actionUrl || '';
                actionMethod = source.method || 'POST';

                if (actionMethod !== 'DELETE') {
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
});