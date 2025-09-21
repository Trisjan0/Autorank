document.addEventListener('DOMContentLoaded', () => {
    /*
    |--------------------------------------------------------------------------
    | FOR THE REUSABLE KRA MODAL & AJAX -- START
    |--------------------------------------------------------------------------
    */
    const uploadModal = document.getElementById('kra-upload-modal');

    if (uploadModal) {
        // --- Modal Element Selectors ---
        const openUploadModalBtn = document.getElementById('upload-kra-button');
        const closeUploadModalBtn = document.getElementById('kra-modal-close-btn');
        const kraForm = document.getElementById('kra-upload-form');
        const initialStep = document.getElementById('kra-modal-initial-step');
        const confirmationStep = document.getElementById('kra-modal-confirmation-step');
        const proceedBtn = document.getElementById('kra-proceed-to-confirmation-btn');
        const backBtn = document.getElementById('kra-back-to-selection-btn');
        const confirmBtn = document.getElementById('kra-confirm-upload-btn');
        const messages = {
            initial: document.getElementById('kra-modal-messages'),
            confirmation: document.getElementById('kra-confirmation-message-area'),
            finalStatus: document.getElementById('kra-final-status-message-area'),
        };
        const pageRefreshDelay = 1250;

        // --- Modal Control Functions ---
        const showStep = (step) => {
            if (!initialStep || !confirmationStep) return;
            initialStep.style.display = (step === 'initial') ? 'block' : 'none';
            confirmationStep.style.display = (step === 'confirmation') ? 'block' : 'none';
        };

        const showModal = () => {
            uploadModal.style.display = 'flex';
            document.body.classList.add('modal-open');
            showStep('initial');
        };

        const hideModal = () => {
            uploadModal.style.display = 'none';
            document.body.classList.remove('modal-open');
            Object.values(messages).forEach(el => { if (el) el.innerHTML = ''; });
            if (kraForm) kraForm.reset();
            [confirmBtn, backBtn, closeUploadModalBtn].forEach(btn => { if (btn) btn.disabled = false; });
        };

        // --- Event Listeners ---
        if (openUploadModalBtn) openUploadModalBtn.addEventListener('click', showModal);
        if (closeUploadModalBtn) closeUploadModalBtn.addEventListener('click', hideModal);
        uploadModal.addEventListener('click', (e) => { if (e.target === uploadModal) hideModal(); });
        if (backBtn) backBtn.addEventListener('click', () => {
            showStep('initial');
            if (messages.finalStatus) messages.finalStatus.innerHTML = '';
        });

        if (proceedBtn) {
            proceedBtn.addEventListener('click', () => {
                if (messages.initial) messages.initial.innerHTML = '';
                if (!kraForm.checkValidity()) {
                    if (messages.initial) messages.initial.innerHTML = '<div class="alert-danger">Please fill out all required fields.</div>';
                    return;
                }

                let confirmationHtml = 'Please confirm the following details:<br><br>';
                const formData = new FormData(kraForm);
                const processedFields = new Set();

                formData.forEach((value, key) => {
                    const input = kraForm.querySelector(`[name="${key}"]`);
                    if (!input || (input.type === 'radio' && !input.checked)) return;
                    if (processedFields.has(key)) return;

                    const label = input.getAttribute('data-label') || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    let displayValue = (value instanceof File) ? value.name : value;

                    confirmationHtml += `<strong>${label}:</strong> ${displayValue}<br>`;
                    processedFields.add(key);
                });

                messages.confirmation.innerHTML = confirmationHtml;
                showStep('confirmation');
            });
        }

        if (confirmBtn) {
            confirmBtn.addEventListener('click', async () => {
                const url = kraForm.getAttribute('action');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const publishDateInput = kraForm.querySelector('input[name="publish_date"]');

                if (publishDateInput && publishDateInput.value === '') {
                    publishDateInput.disabled = true;
                }

                const formData = new FormData(kraForm);

                if (publishDateInput) {
                    publishDateInput.disabled = false;
                }

                [confirmBtn, backBtn, closeUploadModalBtn].forEach(btn => btn.disabled = true);
                messages.finalStatus.innerHTML = '<div class="alert-info">Uploading... Please wait.</div>';

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: formData,
                    });
                    const data = await response.json();
                    if (response.ok) {
                        messages.finalStatus.innerHTML = `<div class="alert-success">${data.message}</div>`;
                        if (typeof loadData === "function") {
                           loadData(true);
                        }
                        setTimeout(hideModal, pageRefreshDelay);
                    } else {
                        let errorMsg = data.message || 'An unknown error occurred.';
                        if (response.status === 422 && data.errors) {
                            errorMsg = Object.values(data.errors).map(err => `<p>${err[0]}</p>`).join('');
                        }
                        messages.finalStatus.innerHTML = `<div class="alert-danger">${errorMsg}</div>`;
                        [confirmBtn, backBtn, closeUploadModalBtn].forEach(btn => btn.disabled = false);
                    }
                } catch (error) {
                    messages.finalStatus.innerHTML = `<div class="alert-danger">Network error: ${error.message}</div>`;
                    [confirmBtn, backBtn, closeUploadModalBtn].forEach(btn => btn.disabled = false);
                }
            });
        }
    }
    /*
    |--------------------------------------------------------------------------
    | FOR THE REUSABLE KRA MODAL & AJAX -- END
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | FOR THE REUSABLE KRA "LOAD MORE" & SEARCH -- START
    |--------------------------------------------------------------------------
    */
    const loadMoreBtn = document.getElementById('load-more-kra-btn');
    const tableBody = document.getElementById('kra-table-body');
    const searchForm = document.getElementById('kra-search-form');
    
    async function loadData(isSearch = false) {
        const isLoading = false;
        if (isLoading) return;
        
        if (isSearch) {
            const colspan = tableBody.closest('table').querySelectorAll('thead th').length;
            tableBody.innerHTML = `<tr><td colspan="${colspan}" style="text-align: center;">Loading...</td></tr>`;
            
            loadMoreBtn.dataset.currentOffset = '0';
        }
        
        const offset = parseInt(loadMoreBtn.dataset.currentOffset, 10);
        const searchInput = searchForm.querySelector('input[name="search"]');
        const searchTerm = searchInput.value;
        
        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = 'Loading...';
        
        try {
            const url = `${window.location.pathname}?ajax=true&offset=${offset}&search=${encodeURIComponent(searchTerm)}`;
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();

            if (isSearch) {
                tableBody.innerHTML = '';
            }
            
            const noResultsRow = document.getElementById('no-results-row');
            if (noResultsRow) noResultsRow.remove();
            
            tableBody.insertAdjacentHTML('beforeend', data.html);
            loadMoreBtn.dataset.currentOffset = data.nextOffset;
            loadMoreBtn.style.display = data.hasMore ? 'block' : 'none';
            
            if (tableBody.children.length === 0) {
                const colspan = tableBody.closest('table').querySelectorAll('thead th').length;
                tableBody.innerHTML = `<tr id="no-results-row"><td colspan="${colspan}" style="text-align: center;">No items found.</td></tr>`;
                loadMoreBtn.style.display = 'none';
            }
        } catch (error) {
            console.error('Error loading data:', error);
            alert('Failed to load data. Please try again.');
        } finally {
            loadMoreBtn.disabled = false;
            loadMoreBtn.textContent = 'Load More +';
        }
    }
    
    if (loadMoreBtn && tableBody && searchForm) {
        const searchInput = searchForm.querySelector('input[name="search"]');
        const searchBtnIcon = document.getElementById('kra-search-btn-icon');

        // Function to update the search icon based on the input's content
        const updateSearchIcon = () => {
            if (searchInput.value.trim() !== '') {
                searchBtnIcon.classList.remove('fa-magnifying-glass');
                searchBtnIcon.classList.add('fa-xmark');
            } else {
                searchBtnIcon.classList.remove('fa-xmark');
                searchBtnIcon.classList.add('fa-magnifying-glass');
            }
        };
        
        // Set the initial state of the icon when the page loads
        updateSearchIcon();
        
        searchForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await loadData(true);
            updateSearchIcon();
        });
        
        // Add a click listener to the icon itself for clearing
        searchBtnIcon.addEventListener('click', (e) => {
            if (searchBtnIcon.classList.contains('fa-xmark')) {
                e.preventDefault();
                searchInput.value = '';
                updateSearchIcon();
                loadData(true);
            }
        });
        
        loadMoreBtn.addEventListener('click', () => loadData(false));
    }
    /*
    |--------------------------------------------------------------------------
    | FOR THE REUSABLE KRA "LOAD MORE" & SEARCH -- END
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | REUSABLE FILE VIEWER MODAL
    |--------------------------------------------------------------------------
    */
    const fileViewerModal = document.getElementById('fileViewerModal');
    if (fileViewerModal) {
        const iframe = document.getElementById('fileViewerIframe');
        const modalLabel = document.getElementById('fileViewerModalLabel');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const loader = fileViewerModal.querySelector('.loader-container');
        const feedbackContainer = document.getElementById('fileViewerFeedback');
        const downloadBtn = document.getElementById('fileViewerDownloadBtn');

        const openFileViewerModal = () => {
            document.body.classList.add('modal-open');
            fileViewerModal.classList.remove('modal-container--hidden');
        };

        const closeFileViewerModal = () => {
            document.body.classList.remove('modal-open');
            fileViewerModal.classList.add('modal-container--hidden');
            if (iframe) {
                iframe.style.display = 'none';
                iframe.src = 'about:blank';
            }
            if (feedbackContainer) feedbackContainer.style.display = 'none';
        };

        if (iframe) {
            iframe.addEventListener('load', () => {
                if (loader) loader.style.display = 'none';
                iframe.style.display = 'block';
            });
        }

        document.body.addEventListener('click', async (event) => {
            const viewButton = event.target.closest('.view-file-btn');
            if (viewButton) {
                const infoUrl = viewButton.dataset.infoUrl;
                if (!infoUrl) return;

                openFileViewerModal();

                loader.style.display = 'flex';
                iframe.style.display = 'none';
                feedbackContainer.style.display = 'none';
                modalLabel.textContent = `Loading...`;

                try {
                    const response = await fetch(infoUrl);
                    if (!response.ok) throw new Error('Failed to fetch file info.');
                    const data = await response.json();

                    modalLabel.textContent = `Viewing: ${viewButton.dataset.filename}`;

                    if (data.isViewable) {
                        iframe.src = `${data.viewUrl}#toolbar=1`;
                    } else {
                        downloadBtn.href = `${data.viewUrl}?download=true`;
                        feedbackContainer.style.display = 'flex';
                        loader.style.display = 'none';
                    }
                } catch (error) {
                    console.error('Error viewing file:', error);
                    modalLabel.textContent = 'Error';
                    feedbackContainer.querySelector('p').textContent = 'Could not load the file.';
                    downloadBtn.style.display = 'none';
                    feedbackContainer.style.display = 'flex';
                    loader.style.display = 'none';
                }
            }
        });

        if (closeModalBtn) closeModalBtn.addEventListener('click', closeFileViewerModal);
        fileViewerModal.addEventListener('click', (e) => {
            if (e.target === fileViewerModal) closeFileViewerModal();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | REUSABLE DELETE MODAL & AJAX
    |--------------------------------------------------------------------------
    */
    const deleteModal = document.getElementById('deleteConfirmationModal');
    if (deleteModal) {
        const closeDeleteModalBtn = document.getElementById('closeDeleteModalBtn');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const deleteModalText = document.getElementById('deleteModalText');
        const deleteStatusMessage = document.getElementById('delete-final-status-message-area');

        let itemToDelete = null;
        let deleteUrl = '';

        const showDeleteModal = (button) => {
            itemToDelete = button.closest('tr');
            deleteUrl = button.dataset.deleteUrl;
            const itemTitle = button.dataset.itemTitle;

            deleteModalText.innerHTML = `This action will delete both the files uploaded to the system and Google Drive and cannot be undone.<br><br>Are you sure you want to delete "<strong>${itemTitle}</strong>"?`;
            deleteStatusMessage.innerHTML = '';
            confirmDeleteBtn.disabled = false;
            cancelDeleteBtn.disabled = false;

            document.body.classList.add('modal-open');
            deleteModal.style.display = 'flex';
        };

        const hideDeleteModal = () => {
            document.body.classList.remove('modal-open');
            deleteModal.style.display = 'none';
        };

        document.body.addEventListener('click', (event) => {
            const deleteButton = event.target.closest('.delete-btn');
            if (deleteButton) {
                showDeleteModal(deleteButton);
            }
        });

        closeDeleteModalBtn.addEventListener('click', hideDeleteModal);
        cancelDeleteBtn.addEventListener('click', hideDeleteModal);
        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) hideDeleteModal();
        });

        confirmDeleteBtn.addEventListener('click', async () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            confirmDeleteBtn.disabled = true;
            cancelDeleteBtn.disabled = true;
            deleteStatusMessage.innerHTML = '<div class="alert-info">Deleting...</div>';

            try {
                const response = await fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                });
                const data = await response.json();
                if (response.ok) {
                    deleteStatusMessage.innerHTML = `<div class="alert-success">${data.message}</div>`;
                    
                    if (typeof loadData === "function") {
                        setTimeout(() => {
                           loadData(true);
                           hideDeleteModal();
                        }, 850);
                    } else {
                        if (itemToDelete) {
                            itemToDelete.remove();
                        }
                        setTimeout(hideDeleteModal, 1000);
                    }
                } else {
                    deleteStatusMessage.innerHTML = `<div class="alert-danger">${data.message || 'Failed to delete.'}</div>`;
                    confirmDeleteBtn.disabled = false;
                    cancelDeleteBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error deleting item:', error);
                deleteStatusMessage.innerHTML = `<div class="alert-danger">A network error occurred.</div>`;
                confirmDeleteBtn.disabled = false;
                cancelDeleteBtn.disabled = false;
            }
        });
    }
});