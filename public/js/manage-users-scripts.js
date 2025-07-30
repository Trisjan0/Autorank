document.addEventListener('DOMContentLoaded', () => {
    /*
    |--------------------------------------------------------------------------
    | FOR MANAGING USER ROLES (MODAL) -- START
    |--------------------------------------------------------------------------
    */
    const updateRoleModal = document.getElementById('updateRoleModal');
    const updateRoleForm = document.getElementById('updateRoleForm');
    const modalUserName = document.getElementById('modal-user-name');
    const modalUserId = document.getElementById('modal-user-id');
    const modalMessages = document.getElementById('modal-messages');
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
    const noUsersRow = document.getElementById('no-users-row');

    // Asynchronously loads more user data from the server via AJAX.
    // This function is triggered by the "Load More" button or a search submission.
    async function loadMoreUsers() {
        // Get the current offset for pagination from the button's data attribute
        const currentOffset = parseInt(loadMoreUsersBtn.dataset.currentOffset);
        // Get the current search term from the search input field
        const searchTerm = searchForm.querySelector('input[name="search"]').value;

        // Apply a loading state to the "Load More" button to provide user feedback
        loadMoreUsersBtn.disabled = true; // Disable the button to prevent multiple clicks
        loadMoreUsersBtn.textContent = 'Loading...'; // Change button text to indicate loading

        try {
            // Construct the URL for the AJAX request to the /manage-users endpoint
            // It includes an 'ajax' parameter to signal the server, the current offset, and the search term
            const url = `/manage-users?ajax=true&offset=${currentOffset}&search=${encodeURIComponent(searchTerm)}`;
            
            // Send the AJAX request using the Fetch API
            const response = await fetch(url, {
                headers: {
                    // Include this header so Laravel's Request::ajax() method can identify the request
                    'X-Requested-With': 'XMLHttpRequest' 
                }
            });

            // Check if the HTTP response status is not OK (e.g., 404, 500)
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Parse the JSON response body
            const data = await response.json();

            // If the response contains HTML for new user rows
            if (data.html) {
                // Before appending new rows, check if the "No users found" row exists and remove it.
                // This ensures it doesn't stay if results are loaded.
                if (noUsersRow) {
                    noUsersRow.remove();
                }
                // Append the new HTML rows to the user table body
                userTableBody.insertAdjacentHTML('beforeend', data.html);
            }

            // Update the 'currentOffset' data attribute on the button for the next load
            loadMoreUsersBtn.dataset.currentOffset = data.nextOffset;

            // Determine whether to show or hide the "Load More" button
            // If 'hasMore' is false (no more users to load), hide the button
            if (!data.hasMore) {
                loadMoreUsersBtn.style.display = 'none';
            } else {
                // Otherwise, ensure the button is visible
                loadMoreUsersBtn.style.display = 'block'; 
            }

        } catch (error) {
            // Catch and log any errors during the fetch operation
            console.error('Error loading more users:', error);
            // Provide a user-friendly alert message
            alert('Failed to load more users. Please try again.');
        } finally {
            // Finally block always executes, regardless of try/catch outcome
            // Re-enable the button and reset its text to the default
            loadMoreUsersBtn.disabled = false;
            loadMoreUsersBtn.textContent = 'Load More +';
        }
    }

    // Attach event listener to the "Load More" button if it exists
    if (loadMoreUsersBtn) {
        loadMoreUsersBtn.addEventListener('click', loadMoreUsers);
    }

    // Handles the submission of the user search form.
    // This function clears existing results and loads new ones based on the search term.
    if (searchForm) {
        searchForm.addEventListener('submit', async function(e) {
            e.preventDefault(); // Prevent the default form submission (it causes a full page reload)

            // Clear all existing rows from the user table body
            userTableBody.innerHTML = '';
            
            // If the "No users found" row was present, remove it before loading new search results
            const initialNoUsersRow = document.getElementById('no-users-row');
            if (initialNoUsersRow) initialNoUsersRow.remove(); 

            // Reset the pagination offset to 0 for a new search query
            loadMoreUsersBtn.dataset.currentOffset = '0';
            // Ensure the "Load More" button is visible initially for a new search
            loadMoreUsersBtn.style.display = 'block'; 

            // Load the first batch of results for the new search term
            await loadMoreUsers(); // Calls the async function and waits for it to complete

            // After loading, if no users were found (table body is still empty)
            if (userTableBody.children.length === 0) {
                // Create a new row to display the "No users found" message
                const newNoUsersRow = document.createElement('tr');
                newNoUsersRow.id = 'no-users-row'; // Assign the ID for future reference
                // Add the message spanning all 5 columns of the table
                newNoUsersRow.innerHTML = '<td colspan="7" style="text-align: center;">No users found.</td>';
                // Append this row to the table body
                userTableBody.appendChild(newNoUsersRow);
                // Hide the "Load More" button if no users are found for the search
                loadMoreUsersBtn.style.display = 'none'; 
            }
        });
    }
    /*
    |--------------------------------------------------------------------------
    | FOR MANAGING USERS (LOAD MORE & SEARCH) -- END
    |--------------------------------------------------------------------------
    */
});
