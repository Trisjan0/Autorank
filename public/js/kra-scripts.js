document.addEventListener('DOMContentLoaded', () => {
    /*
    |--------------------------------------------------------------------------
    | REUSABLE KRA MODAL & AJAX LOGIC
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
                const formData = new FormData(kraForm);

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
                        setTimeout(() => window.location.reload(), pageRefreshDelay);
                    } else {
                        let errorMsg = data.message || 'An unknown error occurred.';
                        if (response.status === 422 && data.errors) {
                            errorMsg = '<ul>' + Object.values(data.errors).map(err => `<li>${err[0]}</li>`).join('') + '</ul>';
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
    | REUSABLE KRA "LOAD MORE" & SEARCH LOGIC (Robust Version)
    |--------------------------------------------------------------------------
    */
    const loadMoreBtn = document.getElementById('load-more-kra-btn');
    const tableBody = document.getElementById('kra-table-body');
    const searchForm = document.getElementById('kra-search-form');

    if (loadMoreBtn && tableBody && searchForm) {
        const searchInput = searchForm.querySelector('input[name="search"]');
        const searchBtnIcon = document.getElementById('kra-search-btn-icon');
        let isLoading = false;

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

        async function loadData(isSearch = false) {
            if (isLoading) return;
            isLoading = true;

            if (isSearch) {
                tableBody.innerHTML = '';
                loadMoreBtn.dataset.currentOffset = '0';
            }

            const offset = parseInt(loadMoreBtn.dataset.currentOffset, 10);
            const searchTerm = searchInput.value;

            loadMoreBtn.disabled = true;
            loadMoreBtn.textContent = 'Loading...';

            try {
                const url = `${window.location.pathname}?ajax=true&offset=${offset}&search=${encodeURIComponent(searchTerm)}`;
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                
                const data = await response.json();
                
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
                isLoading = false;
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = 'Load More +';
            }
        }

        searchForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            await loadData(true); // Always trigger a new search on submit.
            updateSearchIcon(); // Update the icon based on the result of the search.
        });

        // Add a click listener to the icon itself for clearing
        searchBtnIcon.addEventListener('click', (e) => {
             if (searchBtnIcon.classList.contains('fa-xmark')) {
                e.preventDefault(); // Prevent form submission
                searchInput.value = ''; // Clear the input
                updateSearchIcon(); // Update the icon to a magnifying glass
                loadData(true); // Reload the data with no filter
            }
        });

        loadMoreBtn.addEventListener('click', () => loadData(false));
    }
});