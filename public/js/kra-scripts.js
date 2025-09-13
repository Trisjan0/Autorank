document.addEventListener('DOMContentLoaded', () => {
    /*
    |--------------------------------------------------------------------------
    | FOR KRA I-A: EVALUATIONS MODAL -- START
    |--------------------------------------------------------------------------
    */
    const uploadEvalModal = document.getElementById('uploadEvaluationModal');

    if (uploadEvalModal) {
        const openUploadModalBtn = document.getElementById('upload-evaluations-button');
        const closeUploadModalBtn = document.getElementById('closeUploadEvalModalBtn');
        const evalForm = document.getElementById('upload-evaluations-form');
        const uploadEvaluationInitialStep = document.getElementById('uploadEvaluationInitialStep');
        const proceedToEvalConfirmationBtn = document.getElementById('proceedToEvalConfirmationBtn');
        const evalModalMessages = document.getElementById('eval-modal-messages');
        const uploadEvaluationConfirmationStep = document.getElementById('uploadEvaluationConfirmationStep');
        const backToEvalSelectionBtn = document.getElementById('backToEvalSelectionBtn');
        const confirmUploadEvalBtn = document.getElementById('confirmUploadEvalBtn');
        const evalConfirmationMessageArea = document.getElementById('evalConfirmationMessageArea');
        const evalFinalStatusMessageArea = document.getElementById('evalFinalStatusMessageArea');

        const pageRefreshDelay = 1250; // Consistent delay for user to read messages

        function showUploadModal(step = 'initial') {
            uploadEvalModal.style.display = 'flex';
            document.body.classList.add('modal-open');
            showUploadStep(step);
        }

        function hideUploadModal() {
            uploadEvalModal.style.display = 'none';
            document.body.classList.remove('modal-open');
            // Clear all messages and reset state when closing
            if (evalModalMessages) evalModalMessages.innerHTML = '';
            if (evalFinalStatusMessageArea) evalFinalStatusMessageArea.innerHTML = '';
            if (evalConfirmationMessageArea) evalConfirmationMessageArea.innerHTML = '';
            // Re-enable buttons if they were disabled
            if (confirmUploadEvalBtn) confirmUploadEvalBtn.disabled = false;
            if (backToEvalSelectionBtn) backToEvalSelectionBtn.disabled = false;
            if (closeUploadModalBtn) closeUploadModalBtn.disabled = false;
        }

        function showUploadStep(step) {
            if (uploadEvaluationInitialStep && uploadEvaluationConfirmationStep) {
                if (step === 'initial') {
                    uploadEvaluationInitialStep.style.display = 'block';
                    uploadEvaluationConfirmationStep.style.display = 'none';
                } else if (step === 'confirmation') {
                    uploadEvaluationInitialStep.style.display = 'none';
                    uploadEvaluationConfirmationStep.style.display = 'block';
                }
            }
        }

        if (openUploadModalBtn) {
            openUploadModalBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (evalForm) evalForm.reset();
                showUploadModal('initial');
            });
        }

        if (closeUploadModalBtn) {
            closeUploadModalBtn.addEventListener('click', hideUploadModal);
        }

        if (uploadEvalModal) {
            uploadEvalModal.addEventListener('click', function(event) {
                if (event.target === uploadEvalModal) {
                    hideUploadModal();
                }
            });
        }

        if (proceedToEvalConfirmationBtn) {
            proceedToEvalConfirmationBtn.addEventListener('click', function() {
                if (evalModalMessages) evalModalMessages.innerHTML = '';
                if (!evalForm.checkValidity()) {
                    if (evalModalMessages) evalModalMessages.innerHTML = '<div class="alert-danger">Please fill out all required fields.</div>';
                    return;
                }

                // Get form values to show in the confirmation message
                const title = document.getElementById('eval-title').value;
                const categoryRadio = evalForm.querySelector('input[name="category"]:checked');
                const score = document.getElementById('eval-score').value;
                const fileInput = document.getElementById('evaluation_file');
                const publishDateInput = document.getElementById('eval-publish-date');

                const category = categoryRadio ? categoryRadio.value : 'N/A';
                const fileName = fileInput.files.length > 0 ? fileInput.files[0].name : 'No file selected';
                const publishDate = publishDateInput.value ? publishDateInput.value : 'N/A';

                // Populate confirmation message
                evalConfirmationMessageArea.innerHTML = `Please confirm the following details:<br><br>
                    <strong>Title:</strong> ${title}<br>
                    <strong>Category:</strong> ${category}<br>
                    <strong>Publish Date:</strong> ${publishDate}<br>
                    <strong>Score:</strong> ${score}<br>
                    <strong>File:</strong> ${fileName}`;

                showUploadStep('confirmation');
            });
        }

        // *** MODIFIED TO MATCH REFERENCE PATTERN ***
        if (confirmUploadEvalBtn) {
            confirmUploadEvalBtn.addEventListener('click', async function() {
                const url = evalForm.getAttribute('action');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const formData = new FormData(evalForm);

                // Disable buttons to prevent multiple submissions
                confirmUploadEvalBtn.disabled = true;
                if (backToEvalSelectionBtn) backToEvalSelectionBtn.disabled = true;
                if (closeUploadModalBtn) closeUploadModalBtn.disabled = true;

                evalFinalStatusMessageArea.innerHTML = '<div class="alert-info">Uploading... Please wait.</div>';

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    });

                    const responseData = await response.json();

                    if (response.ok) {
                        evalFinalStatusMessageArea.innerHTML = `<div class="alert-success">${responseData.message}</div>`;
                        setTimeout(() => {
                            hideUploadModal();
                            window.location.reload(); // Reload page to show new data
                        }, pageRefreshDelay);
                    } else {
                        let errorMessage = 'An error occurred. Please try again.';
                        if (response.status === 422 && responseData.errors) {
                            errorMessage = '<div class="alert-danger"><ul>';
                            for (const key in responseData.errors) {
                                errorMessage += `<li>${responseData.errors[key][0]}</li>`;
                            }
                            errorMessage += '</ul></div>';
                        } else if (responseData.message) {
                            errorMessage = `<div class="alert-danger">${responseData.message}</div>`;
                        }
                        evalFinalStatusMessageArea.innerHTML = errorMessage;

                        // Re-enable buttons on error
                        confirmUploadEvalBtn.disabled = false;
                        if (backToEvalSelectionBtn) backToEvalSelectionBtn.disabled = false;
                        if (closeUploadModalBtn) closeUploadModalBtn.disabled = false;
                    }
                } catch (error) {
                    console.error('AJAX Error:', error);
                    evalFinalStatusMessageArea.innerHTML = `<div class="alert-danger">Network error or unexpected response: ${error.message}</div>`;

                    // Re-enable buttons on network error
                    confirmUploadEvalBtn.disabled = false;
                    if (backToEvalSelectionBtn) backToEvalSelectionBtn.disabled = false;
                    if (closeUploadModalBtn) closeUploadModalBtn.disabled = false;
                }
            });
        }


        if (backToEvalSelectionBtn) {
            backToEvalSelectionBtn.addEventListener('click', function() {
                showUploadStep('initial');
                // Clear messages and re-enable buttons
                if (evalModalMessages) evalModalMessages.innerHTML = '';
                if (evalFinalStatusMessageArea) evalFinalStatusMessageArea.innerHTML = '';
                if (confirmUploadEvalBtn) confirmUploadEvalBtn.disabled = false;
                if (backToEvalSelectionBtn) backToEvalSelectionBtn.disabled = false;
                if (closeUploadModalBtn) closeUploadModalBtn.disabled = false; // Ensure close is enabled
            });
        }
    }
    /*
    |--------------------------------------------------------------------------
    | FOR KRA I-A: EVALUATIONS MODAL -- END
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | FOR KRA I-A: EVALUATIONS (LOAD MORE & SEARCH) -- START
    |--------------------------------------------------------------------------
    */
    const loadMoreBtn = document.getElementById('loadMoreEvaluationsBtn');
    const tableBody = document.getElementById('evaluations-table-body');
    const searchForm = document.getElementById('evaluations-search-form');

    if (loadMoreBtn && tableBody && searchForm) {
        const searchInput = searchForm.querySelector('input[name="search"]');
        const searchBtnIcon = document.getElementById('eval-search-btn-icon');
        let isLoading = false;
        let isFiltered = false;

        async function loadEvaluations(isSearch = false) {
            if (isLoading) return;
            isLoading = true;

            if (isSearch) {
                tableBody.innerHTML = '';
                loadMoreBtn.dataset.currentOffset = '0';
            }

            const offset = parseInt(loadMoreBtn.dataset.currentOffset);
            const searchTerm = searchInput.value;

            loadMoreBtn.disabled = true;
            loadMoreBtn.textContent = 'Loading...';

            try {
                const url = `/evaluations?ajax=true&offset=${offset}&search=${encodeURIComponent(searchTerm)}`;
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                
                const data = await response.json();
                tableBody.insertAdjacentHTML('beforeend', data.html);
                loadMoreBtn.dataset.currentOffset = data.nextOffset;
                loadMoreBtn.style.display = data.hasMore ? 'block' : 'none';
                
                if (tableBody.children.length === 0) {
                    const noResultsRowHTML = '<tr id="no-evaluations-row"><td colspan="8" style="text-align: center;">No evaluations found.</td></tr>';
                    tableBody.innerHTML = noResultsRowHTML;
                    loadMoreBtn.style.display = 'none';
                }
            } catch (error) {
                console.error('Error loading evaluations:', error);
                alert('Failed to load evaluations. Please try again.');
            } finally {
                isLoading = false;
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = 'Load More +';
            }
        }

        searchForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (isFiltered) {
                searchInput.value = '';
                await loadEvaluations(true);
                
                searchBtnIcon.classList.remove('fa-xmark');
                searchBtnIcon.classList.add('fa-magnifying-glass');
                isFiltered = false;
            } else {
                const searchTerm = searchInput.value.trim();
                if (searchTerm.length === 0) return;

                await loadEvaluations(true);
                
                searchBtnIcon.classList.remove('fa-magnifying-glass');
                searchBtnIcon.classList.add('fa-xmark');
                isFiltered = true;
            }
        });

        loadMoreBtn.addEventListener('click', () => {
            loadEvaluations(false);
        });
    }
    /*
    |--------------------------------------------------------------------------
    | FOR KRA I-A: EVALUATIONS (LOAD MORE & SEARCH) -- END
    |--------------------------------------------------------------------------
    */
});