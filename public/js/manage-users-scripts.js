document.addEventListener('DOMContentLoaded', () => {
    /*
    |--------------------------------------------------------------------------
    | TABLE TOGGLE (Roles <-> Faculty Ranks)
    |--------------------------------------------------------------------------
    */
    const actionSelect = document.getElementById('action-select');
    const rolesTable = document.getElementById('manage-roles-table');
    const ranksTable = document.getElementById('manage-faculty-rank-table');

    function toggleTables() {
        rolesTable.style.display = 'none';
        ranksTable.style.display = 'none';

        if (actionSelect.value === 'manage-roles') {
            rolesTable.style.display = 'block';
        } else if (actionSelect.value === 'manage-faculty-rank') {
            ranksTable.style.display = 'block';
        }
    }

    toggleTables();
    actionSelect.addEventListener('change', toggleTables);

    /*
    |--------------------------------------------------------------------------
    | FOR MANAGING USER ROLES (MODAL)
    |--------------------------------------------------------------------------
    */
    const updateRoleModal = document.getElementById('updateRoleModal');

    if (updateRoleModal) {
        const updateRoleForm = document.getElementById('updateRoleForm');
        const modalUserName = document.getElementById('modal-user-name');
        const modalUserId = document.getElementById('modal-user-id');
        const modalMessages = document.getElementById('role-modal-message');
        const modalRolesRadioButtonsContainer = document.getElementById('modal-roles-radio-buttons');
        const closeUpdateRoleModalBtn = document.getElementById('closeUpdateRoleModalBtn');

        const updateRoleInitialStep = document.getElementById('updateRoleInitialStep');
        const updateRoleConfirmationStep = document.getElementById('updateRoleConfirmationStep');
        const confirmUpdateRoleBtn = document.getElementById('confirmUpdateRoleBtn');
        const backToSelectionBtn = document.getElementById('backToSelectionBtn');
        const confirmationMessageArea = document.getElementById('confirmationMessageArea');
        const finalStatusMessageArea = document.getElementById('finalStatusMessageArea');

        const pageRefreshDelay = 1250;

        let currentSelectedUserId = null;
        let currentSelectedUserName = null;
        let currentSelectedRoleName = null;
        let currentUserCurrentRoleName = null;

        // Displays the role update modal and controls which step is visible.
        function showModal(step = 'initial') {
            if (updateRoleModal) {
                updateRoleModal.style.display = 'flex'; // Make the modal visible
                document.body.classList.add('modal-open'); // Add class to body to prevent scrolling
                showStep(step); // Show the specified step
            }
        }

        // Hides the role update modal.
        function hideModal() {
            if (updateRoleModal) {
                updateRoleModal.style.display = 'none'; // Hide the modal
                document.body.classList.remove('modal-open'); // Remove class from body
                // Clear all messages and reset state when closing
                modalMessages.innerHTML = '';
                finalStatusMessageArea.innerHTML = '';
                confirmationMessageArea.innerHTML = '';
                // Re-enable buttons if they were disabled
                if (confirmUpdateRoleBtn) confirmUpdateRoleBtn.disabled = false;
                if (backToSelectionBtn) backToSelectionBtn.disabled = false;
                if (closeUpdateRoleModalBtn) closeUpdateRoleModalBtn.disabled = false;
            }
        }

        // Controls which step of the modal is visible
        function showStep(step) {
            if (updateRoleInitialStep && updateRoleConfirmationStep) {
                if (step === 'initial') {
                    updateRoleInitialStep.style.display = 'block';
                    updateRoleConfirmationStep.style.display = 'none';
                } else if (step === 'confirmation') {
                    updateRoleInitialStep.style.display = 'none';
                    updateRoleConfirmationStep.style.display = 'block';
                }
            }
        }


        // Event listener for opening the role update modal.
        document.addEventListener('click', function(event) {
            if (event.target.matches('.update-role-btn')) {
                const button = event.target;
                currentSelectedUserId = button.dataset.userId;
                currentSelectedUserName = button.dataset.userName;
                const userRoles = JSON.parse(button.dataset.userRoles);
                const currentUserRoleId = userRoles.length > 0 ? userRoles[0] : null;
                currentUserCurrentRoleName = button.dataset.currentRoleName; // Get the user's current role name

                // Populate modal fields with the clicked user's data
                modalUserId.value = currentSelectedUserId;
                modalUserName.textContent = currentSelectedUserName;
                modalMessages.innerHTML = ''; // Clear any messages from previous modal interactions
                finalStatusMessageArea.innerHTML = ''; // Clear any previous final status

                // Select the correct radio button based on the user's current role
                modalRolesRadioButtonsContainer.querySelectorAll('input[type="radio"]').forEach(radio => {
                    radio.checked = (parseInt(radio.value) === currentUserRoleId);
                });

                // Reset internal variables for the next interaction
                currentSelectedRoleName = null;

                showModal('initial'); // Always start with the initial selection step
            }
        });

        // Event listeners for closing the modal (via specific buttons or clicking outside)
        if (closeUpdateRoleModalBtn) {
            closeUpdateRoleModalBtn.addEventListener('click', hideModal); // Close button click
        }

        if (updateRoleModal) {
            // Close modal if click occurs directly on the modal's background (outside its content)
            updateRoleModal.addEventListener('click', function(event) {
                if (event.target === updateRoleModal) {
                    hideModal();
                }
            });
        }

        // Handle initial form submission (move to confirmation step)
        if (updateRoleForm) {
            updateRoleForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                modalMessages.innerHTML = ''; // Clear previous messages

                const selectedRadio = modalRolesRadioButtonsContainer.querySelector('input[type="radio"]:checked');

                if (!selectedRadio) {
                    modalMessages.innerHTML = '<div class="alert-danger">Please select a role to proceed.</div>';
                    return;
                }

                currentSelectedRoleName = selectedRadio.dataset.roleName; // Get the selected role's human-readable name

                // Populate confirmation message
                confirmationMessageArea.innerHTML = `You are about to change <strong>${currentSelectedUserName}'s</strong> role from <strong>${currentUserCurrentRoleName || 'N/A'}</strong> to <strong>${currentSelectedRoleName}</strong>.<br><br>Do you want to proceed?`;

                showStep('confirmation'); // Move to the confirmation step
            });
        }

        // Handle confirmation button click (perform actual AJAX update)
        if (confirmUpdateRoleBtn) {
            confirmUpdateRoleBtn.addEventListener('click', async function() {
                const url = `/manage-users/${currentSelectedUserId}/update-roles`;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const selectedRadio = modalRolesRadioButtonsContainer.querySelector('input[type="radio"]:checked');
                const selectedRoleId = selectedRadio ? selectedRadio.value : null;

                const dataToSend = {
                    user_id: currentSelectedUserId,
                    roles: selectedRoleId ? [parseInt(selectedRoleId)] : []
                };

                // Disable buttons to prevent multiple submissions
                if (confirmUpdateRoleBtn) confirmUpdateRoleBtn.disabled = true;
                if (backToSelectionBtn) backToSelectionBtn.disabled = true;
                if (closeUpdateRoleModalBtn) closeUpdateRoleModalBtn.disabled = true; // Disable modal close during process

                finalStatusMessageArea.innerHTML = '<div class="alert-info">Updating role... Please wait.</div>'; // Show loading message

                try {
                    const response = await fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(dataToSend)
                    });

                    const responseData = await response.json();

                    if (response.ok) {
                        finalStatusMessageArea.innerHTML = `<div class="alert-success">${responseData.message}</div>`;

                        // --- LOGIC FOR HANDLING ADMIN'S OWN ROLE CHANGE & REDIRECT ---
                        if (responseData.redirect_url) {
                            console.log('Admin role updated. Redirecting to login page...');
                            setTimeout(() => {
                                window.location.href = responseData.redirect_url;
                            }, pageRefreshDelay); // Give user enough time to read success before redirect
                            return;
                        }

                        // --- Logic for when another user's role was updated ---
                        const rolesTd = document.getElementById(`roles-${currentSelectedUserId}`);
                        if (rolesTd && responseData.newRolesHtml) {
                            rolesTd.innerHTML = responseData.newRolesHtml;
                        }

                        // Update Role Assigned At and By in the table if available in response
                        const assignedAtTd = document.getElementById(`assigned-at-${currentSelectedUserId}`);
                        const assignedByTd = document.getElementById(`assigned-by-${currentSelectedUserId}`);

                        if (assignedAtTd && responseData.newRoleAssignedAt) {
                            assignedAtTd.textContent = responseData.newRoleAssignedAt;
                        }
                        if (assignedByTd && responseData.newRoleAssignedBy) {
                            assignedByTd.textContent = responseData.newRoleAssignedBy;
                        }

                        // Update the button's data attributes
                        const updateButton = document.querySelector(`.update-role-btn[data-user-id="${currentSelectedUserId}"]`);
                        if (updateButton) {
                            updateButton.dataset.userRoles = JSON.stringify([responseData.newRoleId]);
                            updateButton.dataset.currentRoleName = responseData.newRoleName;
                        }

                        setTimeout(() => {
                            hideModal();
                        }, pageRefreshDelay); // Keep modal open to show success

                    } else { // Server responded with a non-2xx status
                        let errorMessage = 'An error occurred. Please try again.';
                        if (response.status === 422) {
                            const errors = responseData.errors;
                            errorMessage = '<div class="alert-danger"><ul>';
                            for (const key in errors) {
                                errorMessage += `<li>${errors[key][0]}</li>`;
                            }
                            errorMessage += '</ul></div>';
                        } else if (responseData.message) {
                            errorMessage = `<div class="alert-danger">${responseData.message}</div>`;
                        }
                        finalStatusMessageArea.innerHTML = errorMessage;
                        console.error('Server error response:', responseData);

                        // Re-enable buttons if there's an error and no redirect
                        if (confirmUpdateRoleBtn) confirmUpdateRoleBtn.disabled = false;
                        if (backToSelectionBtn) backToSelectionBtn.disabled = false;
                        if (closeUpdateRoleModalBtn) closeUpdateRoleModalBtn.disabled = false;
                    }
                } catch (error) { // Catch block for network errors or issues parsing the JSON response
                    console.error('AJAX Error:', error);
                    finalStatusMessageArea.innerHTML = `<div class="alert-danger">Network error or unexpected response: ${error.message}</div>`;

                    // Re-enable buttons on network error
                    if (confirmUpdateRoleBtn) confirmUpdateRoleBtn.disabled = false;
                    if (backToSelectionBtn) backToSelectionBtn.disabled = false;
                    if (closeUpdateRoleModalBtn) closeUpdateRoleModalBtn.disabled = false;
                }
            });
        }

        // Back button on confirmation step
        if (backToSelectionBtn) {
            backToSelectionBtn.addEventListener('click', function() {
                showStep('initial');
                // Clear messages and re-enable buttons when going back
                modalMessages.innerHTML = '';
                finalStatusMessageArea.innerHTML = '';
                if (confirmUpdateRoleBtn) confirmUpdateRoleBtn.disabled = false;
                if (backToSelectionBtn) backToSelectionBtn.disabled = false;
                if (closeUpdateRoleModalBtn) closeUpdateRoleModalBtn.disabled = false;
            });
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MANAGING FACULTY RANK (MODAL + AJAX)
    |--------------------------------------------------------------------------
    */
    const facultyRankModal = document.getElementById('updateFacultyRankModal');
    if (facultyRankModal) {
        const form = facultyRankModal.querySelector('form');
        const modalUserName = facultyRankModal.querySelector('#modal-user-name');
        const modalUserId = facultyRankModal.querySelector('#modal-user-id');
        const selectInput = facultyRankModal.querySelector('select');

        const messageArea = facultyRankModal.querySelector('#role-modal-message');
        const confirmBtn = facultyRankModal.querySelector('#confirmUpdateRoleBtn');
        const backBtn = facultyRankModal.querySelector('#backToSelectionBtn');
        const closeBtn = facultyRankModal.querySelector('#closeUpdateRoleModalBtn');

        const initialStep = facultyRankModal.querySelector('#updateRoleInitialStep');
        const confirmationStep = facultyRankModal.querySelector('#updateRoleConfirmationStep');
        const confirmationMessageArea = facultyRankModal.querySelector('#confirmationMessageArea');
        const finalStatusMessageArea = facultyRankModal.querySelector('#finalStatusMessageArea');

        const pageRefreshDelay = 1250;
        let currentSelectedUserId = null;
        let currentSelectedUserName = null;
        let selectedRank = null;

        // --- Show & Hide ---
        function showModal(step = 'initial') {
            facultyRankModal.style.display = 'flex';
            document.body.classList.add('modal-open');
            showStep(step);
        }
        function hideModal() {
            facultyRankModal.style.display = 'none';
            document.body.classList.remove('modal-open');
            messageArea.innerHTML = '';
            confirmationMessageArea.innerHTML = '';
            finalStatusMessageArea.innerHTML = '';
            confirmBtn.disabled = false;
            backBtn.disabled = false;
            closeBtn.disabled = false;
        }
        function showStep(step) {
            if (step === 'initial') {
                initialStep.style.display = 'block';
                confirmationStep.style.display = 'none';
            } else {
                initialStep.style.display = 'none';
                confirmationStep.style.display = 'block';
            }
        }

        // --- Open modal ---
        document.addEventListener('click', (e) => {
            if (e.target.matches('.update-faculty-rank-btn')) {
                const btn = e.target;
                currentSelectedUserId = btn.dataset.userId;
                currentSelectedUserName = btn.dataset.userName;

                modalUserId.value = currentSelectedUserId;
                modalUserName.textContent = currentSelectedUserName;
                showModal('initial');
            }
        });

        // --- Close modal ---
        closeBtn.addEventListener('click', hideModal);
        facultyRankModal.addEventListener('click', (e) => {
            if (e.target === facultyRankModal) hideModal();
        });

        // --- Step 1: Validate & go to confirmation ---
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            selectedRank = selectInput.value;

            if (!selectedRank) {
                messageArea.innerHTML = '<div class="alert-danger">Please select a faculty rank.</div>';
                return;
            }

            confirmationMessageArea.innerHTML =
                `You are about to change <strong>${currentSelectedUserName}</strong>'s faculty rank to <strong>${selectedRank}</strong>.<br><br>Do you want to proceed?`;
            showStep('confirmation');
        });

        // --- Step 2: Confirm & send AJAX ---
        confirmBtn.addEventListener('click', async () => {
            const url = `/users/${currentSelectedUserId}/update-faculty-rank`;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            confirmBtn.disabled = true;
            backBtn.disabled = true;
            closeBtn.disabled = true;
            finalStatusMessageArea.innerHTML = '<div class="alert-info">Updating faculty rank... Please wait.</div>';

            try {
                const response = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ faculty_rank: selectedRank }),
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Error updating rank.');

                finalStatusMessageArea.innerHTML = `<div class="alert-success">${data.message}</div>`;

                // Update the table inline (no reload)
                const rankTd = document.getElementById(`rank-${currentSelectedUserId}`);
                const assignedAtTd = document.getElementById(`rank-assigned-at-${currentSelectedUserId}`);
                const assignedByTd = document.getElementById(`rank-assigned-by-${currentSelectedUserId}`);

                if (rankTd) rankTd.textContent = data.newFacultyRank;
                if (assignedAtTd) assignedAtTd.textContent = data.newRankAssignedAt;
                if (assignedByTd) assignedByTd.textContent = data.newRankAssignedBy;

                // Also update button’s dataset
                const updateBtn = document.querySelector(`.update-faculty-rank-btn[data-user-id="${currentSelectedUserId}"]`);
                if (updateBtn) updateBtn.dataset.currentRank = data.newFacultyRank;

                setTimeout(() => {
                    hideModal();
                }, pageRefreshDelay);
            } catch (err) {
                finalStatusMessageArea.innerHTML = `<div class="alert-danger">${err.message}</div>`;
                confirmBtn.disabled = false;
                backBtn.disabled = false;
                closeBtn.disabled = false;
            }
        });

        // --- Step 3: Back button ---
        backBtn.addEventListener('click', () => {
            showStep('initial');
            confirmationMessageArea.innerHTML = '';
            finalStatusMessageArea.innerHTML = '';
            confirmBtn.disabled = false;
            backBtn.disabled = false;
            closeBtn.disabled = false;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD MORE + SEARCH (ROLES + FACULTY RANKS)
    |--------------------------------------------------------------------------
    */
    async function setupTable(tableId, loadMoreBtnId, searchFormSelector) {
        const tableBody = document.querySelector(`#${tableId} tbody`);
        const loadMoreBtn = document.getElementById(loadMoreBtnId);
        const searchForm = document.querySelector(searchFormSelector);

        if (!tableBody || !loadMoreBtn || !searchForm) return;

        const searchInput = searchForm.querySelector('input[name="search"]');
        let isLoading = false;

        async function loadData(isSearch = false) {
            if (isLoading) return;
            isLoading = true;

            if (isSearch) {
                tableBody.innerHTML = '';
                loadMoreBtn.dataset.currentOffset = '0';
            }

            const offset = parseInt(loadMoreBtn.dataset.currentOffset || '0');
            const searchTerm = searchInput.value;

            loadMoreBtn.disabled = true;
            loadMoreBtn.textContent = 'Loading...';

            try {
                const url = `${window.location.pathname}?ajax=true&offset=${offset}&search=${encodeURIComponent(searchTerm)}`;
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!response.ok) throw new Error(`HTTP error: ${response.status}`);

                const data = await response.json();
                if (isSearch) tableBody.innerHTML = '';
                if (data.html) tableBody.insertAdjacentHTML('beforeend', data.html);

                loadMoreBtn.dataset.currentOffset = data.nextOffset;
                loadMoreBtn.style.display = data.hasMore ? 'block' : 'none';

                if (tableBody.children.length === 0) {
                    const colspan = document.querySelector(`#${tableId} thead th`).length;
                    tableBody.innerHTML = `<tr><td colspan="${colspan}" style="text-align:center;">No results found.</td></tr>`;
                    loadMoreBtn.style.display = 'none';
                }
            } catch (err) {
                console.error('Error loading data:', err);
            } finally {
                isLoading = false;
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = 'Load More +';
            }
        }

        // attach handlers
        searchForm.addEventListener('submit', (e) => { e.preventDefault(); loadData(true); });
        loadMoreBtn.addEventListener('click', () => loadData(false));
    }

    // Setup both tables
    setupTable('manage-roles-table', 'loadMoreUsersBtn', '#search-form'); 
    setupTable('manage-faculty-rank-table', 'loadMoreUsersBtn', '#search-form');
});