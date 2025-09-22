document.addEventListener('DOMContentLoaded', () => {
    /*
    |--------------------------------------------------------------------------
    | UI LOGIC HELPER
    |--------------------------------------------------------------------------
    */
    const MQL = 1130;
    function updateContainerAlignment() {
        if (window.innerWidth <= MQL) { return; }

        const loadMoreBtn = document.getElementById('load-more-credentials-btn');
        const container = document.querySelector('.mini-load-more-container');

        if (loadMoreBtn && container) {
            const isVisible = loadMoreBtn.style.display !== 'none';
            
            if (isVisible) {
                container.classList.add('space-between');
            } else {
                container.classList.remove('space-between');
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DYNAMIC DATA LOADING (SEARCH & LOAD MORE)
    |--------------------------------------------------------------------------
    */
    const loadMoreBtn = document.getElementById('load-more-credentials-btn');
    const tableBody = document.getElementById('credentials-table-body');
    const searchForm = document.getElementById('credentials-search-form');
    let isLoading = false;

    window.loadData = async function(isSearch = false) {
        if (isLoading) return;
        isLoading = true;

        if (isSearch) {
            tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Loading...</td></tr>';
            if (loadMoreBtn) {
                loadMoreBtn.dataset.currentOffset = '0';
            }
        }

        const offset = loadMoreBtn ? parseInt(loadMoreBtn.dataset.currentOffset, 10) : 0;
        const searchInput = searchForm.querySelector('input[name="search"]');
        const searchTerm = searchInput.value;

        if (loadMoreBtn) {
            loadMoreBtn.disabled = true;
            loadMoreBtn.textContent = 'Loading...';
        }

        try {
            const url = `/profile?ajax=true&offset=${offset}&search=${encodeURIComponent(searchTerm)}`;
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            
            if (isSearch) {
                tableBody.innerHTML = ''; // Clear loading message
            }

            const noResultsRow = document.getElementById('no-results-row');
            if (noResultsRow) noResultsRow.remove();

            tableBody.insertAdjacentHTML('beforeend', data.html);

            if (loadMoreBtn) {
                loadMoreBtn.dataset.currentOffset = data.nextOffset;
                loadMoreBtn.style.display = data.hasMore ? 'block' : 'none';
            }

            updateContainerAlignment(); 
            
            if (tableBody.children.length === 0) {
                const colspan = tableBody.closest('table').querySelectorAll('thead th').length;
                tableBody.innerHTML = `<tr id="no-results-row"><td colspan="${colspan}" style="text-align: center;">No items found.</td></tr>`;
                if (loadMoreBtn) loadMoreBtn.style.display = 'none';
            }
        } catch (error) {
            console.error('Error loading data:', error);
            tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Failed to load data. Please try again.</td></tr>';
        } finally {
            isLoading = false;
            if (loadMoreBtn) {
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = 'Load More +';
            }

            updateContainerAlignment(); 
        }
    }

    if (tableBody && searchForm) {
        const searchInput = searchForm.querySelector('input[name="search"]');
        const searchBtnIcon = document.getElementById('credentials-search-btn-icon');

        const updateSearchIcon = () => {
            if (searchInput.value.trim() !== '') {
                searchBtnIcon.classList.remove('fa-magnifying-glass');
                searchBtnIcon.classList.add('fa-xmark');
            } else {
                searchBtnIcon.classList.remove('fa-xmark');
                searchBtnIcon.classList.add('fa-magnifying-glass');
            }
        };
        updateSearchIcon();

        searchForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await window.loadData(true);
            updateSearchIcon();
        });

        searchBtnIcon.addEventListener('click', (e) => {
            if (searchBtnIcon.classList.contains('fa-xmark')) {
                e.preventDefault();
                searchInput.value = '';
                updateSearchIcon();
                window.loadData(true);
            }
        });

        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', () => window.loadData(false));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CREDENTIAL UPLOAD MODAL
    |--------------------------------------------------------------------------
    */
    const uploadModal = document.getElementById('credential-upload-modal');

    if (uploadModal) {
        const openBtn = document.getElementById('upload-credential-button');
        const closeBtn = document.getElementById('credential-modal-close-btn');
        const form = document.getElementById('credential-upload-form');
        const initialStep = document.getElementById('credential-modal-initial-step');
        const confirmationStep = document.getElementById('credential-modal-confirmation-step');
        const proceedBtn = document.getElementById('credential-proceed-btn');
        const backBtn = document.getElementById('credential-back-btn');
        const confirmBtn = document.getElementById('credential-confirm-btn');
        const messages = {
            initial: document.getElementById('credential-modal-messages'),
            confirmation: document.getElementById('credential-confirmation-area'),
            finalStatus: document.getElementById('credential-final-status-area'),
        };
        const pageRefreshDelay = 1250;

        const showStep = (step) => {
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
            form.reset();
            [confirmBtn, backBtn, closeBtn].forEach(btn => { if (btn) btn.disabled = false; });
        };

        if (openBtn) openBtn.addEventListener('click', showModal);
        if (closeBtn) closeBtn.addEventListener('click', hideModal);
        uploadModal.addEventListener('click', (e) => { if (e.target === uploadModal) hideModal(); });
        if (backBtn) backBtn.addEventListener('click', () => {
            showStep('initial');
            if (messages.finalStatus) messages.finalStatus.innerHTML = '';
        });

        if (proceedBtn) {
            proceedBtn.addEventListener('click', () => {
                if (messages.initial) messages.initial.innerHTML = '';
                if (!form.checkValidity()) {
                    if (messages.initial) messages.initial.innerHTML = '<div class="alert-danger">Please fill out all required fields.</div>';
                    return;
                }
                let confirmationHtml = 'Please confirm the following details:<br><br>';
                const formData = new FormData(form);
                formData.forEach((value, key) => {
                    const input = form.querySelector(`[name="${key}"]`);
                    const label = input.getAttribute('data-label') || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    let displayValue = (value instanceof File) ? value.name : value;
                    confirmationHtml += `<strong>${label}:</strong> ${displayValue}<br>`;
                });
                messages.confirmation.innerHTML = confirmationHtml;
                showStep('confirmation');
            });
        }

        if (confirmBtn) {
            confirmBtn.addEventListener('click', async () => {
                const url = form.getAttribute('action');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const formData = new FormData(form);
                
                [confirmBtn, backBtn, closeBtn].forEach(btn => btn.disabled = true);
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
                        
                        if (typeof window.loadData === 'function') {
                            window.loadData(true);
                        }

                        setTimeout(hideModal, pageRefreshDelay);
                    } else {
                        let errorMsg = data.message || 'An unknown error occurred.';
                        if (response.status === 422 && data.errors) {
                            errorMsg = Object.values(data.errors).map(err => `<p>${err[0]}</p>`).join('');
                        }
                        messages.finalStatus.innerHTML = `<div class="alert-danger">${errorMsg}</div>`;
                        [confirmBtn, backBtn, closeBtn].forEach(btn => btn.disabled = false);
                    }
                } catch (error) {
                    messages.finalStatus.innerHTML = `<div class="alert-danger">Network error: ${error.message}</div>`;
                    [confirmBtn, backBtn, closeBtn].forEach(btn => btn.disabled = false);
                }
            });
        }
    }

    updateContainerAlignment(); 
});