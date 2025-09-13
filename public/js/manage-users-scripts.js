document.addEventListener('DOMContentLoaded', () => {
    /*
    |--------------------------------------------------------------------------
    | FOR MANAGING USER ROLES (MODAL) -- START
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

                        console.log('Another user\'s role updated. Modal will close and page reload shortly...');
                        setTimeout(() => {
                            hideModal();
                            window.location.reload(); // Reload the entire page to ensure all data is fresh
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
    | FOR MANAGING USER ROLES (MODAL) -- END
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | FOR MANAGING USERS (LOAD MORE & SEARCH) -- START
    |--------------------------------------------------------------------------
    */
    const loadMoreUsersBtn = document.getElementById('loadMoreUsersBtn');
    const userTableBody = document.getElementById('user-table-body');
    const searchForm = document.getElementById('search-form');
    
    if (!loadMoreUsersBtn || !userTableBody || !searchForm) {
        return;
    }

    const searchInput = searchForm.querySelector('input[name="search"]');
    const searchBtnIcon = document.getElementById('search-btn-icon'); // Get the icon

    let isLoading = false;
    let isFiltered = false; // State variable to track search status

    async function loadUsers(isSearch = false) {
        if (isLoading) return;
        isLoading = true;

        if (isSearch) {
            userTableBody.innerHTML = '';
            loadMoreUsersBtn.dataset.currentOffset = '0';
        }

        const offset = parseInt(loadMoreUsersBtn.dataset.currentOffset);
        const searchTerm = searchInput.value;

        loadMoreUsersBtn.disabled = true;
        loadMoreUsersBtn.textContent = 'Loading...';

        try {
            const url = `/manage-users?ajax=true&offset=${offset}&search=${encodeURIComponent(searchTerm)}`;
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            userTableBody.insertAdjacentHTML('beforeend', data.html);
            loadMoreUsersBtn.dataset.currentOffset = data.nextOffset;
            loadMoreUsersBtn.style.display = data.hasMore ? 'block' : 'none';
            
            if (userTableBody.children.length === 0) {
                const noUsersRowHTML = '<tr id="no-users-row"><td colspan="7" style="text-align: center;">No users found.</td></tr>';
                userTableBody.innerHTML = noUsersRowHTML;
                loadMoreUsersBtn.style.display = 'none';
            }
        } catch (error) {
            console.error('Error loading users:', error);
            alert('Failed to load users. Please try again.');
        } finally {
            isLoading = false;
            loadMoreUsersBtn.disabled = false;
            loadMoreUsersBtn.textContent = 'Load More +';
        }
    }

    searchForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // If the button is in "reset" mode (isFiltered is true)
        if (isFiltered) {
            searchInput.value = ''; // Clear the input
            await loadUsers(true);  // Reload the table
            
            // Change icon back to search glass
            searchBtnIcon.classList.remove('fa-xmark');
            searchBtnIcon.classList.add('fa-magnifying-glass');
            isFiltered = false; // Update state
        } 
        // If the button is in "search" mode
        else {
            const searchTerm = searchInput.value.trim();
            if (searchTerm.length === 0) return; // Don't search if input is empty

            await loadUsers(true); // Perform the search
            
            // Change icon to an 'X'
            searchBtnIcon.classList.remove('fa-magnifying-glass');
            searchBtnIcon.classList.add('fa-xmark');
            isFiltered = true; // Update state
        }
    });

    // Event listener for the "Load More" button
    loadMoreUsersBtn.addEventListener('click', () => {
        loadUsers(false);
    });
    /*
    |--------------------------------------------------------------------------
    | FOR MANAGING USERS (LOAD MORE & SEARCH) -- START
    |--------------------------------------------------------------------------
    */
});