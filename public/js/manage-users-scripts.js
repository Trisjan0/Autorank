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
    const cancelUpdateRoleBtn = document.getElementById('cancelUpdateRoleBtn');

    // Displays the role update modal.
    function showModal() {
        if (updateRoleModal) {
            updateRoleModal.style.display = 'flex'; // Make the modal visible
            document.body.classList.add('modal-open'); // Add class to body to prevent scrolling
        }
    }

    // Hides the role update modal.
    function hideModal() {
        if (updateRoleModal) {
            updateRoleModal.style.display = 'none'; // Hide the modal
            document.body.classList.remove('modal-open'); // Remove class from body
        }
    }

    // Event listener for opening the role update modal.
    // This is a delegated event listener attached to the document.
    // It's used because 'update-role-btn' elements might be dynamically loaded
    // (e.g., via "Load More" functionality on the manage-users page).
    document.addEventListener('click', function(event) {
        // Check if the clicked element (or its parent) matches a button with class 'update-role-btn'
        if (event.target.matches('.update-role-btn')) {
            const button = event.target;
            // Retrieve user data from data attributes on the button
            const userId = button.dataset.userId;
            const userName = button.dataset.userName;
            // Parse user roles (expected as a JSON string) to get the current role ID
            const userRoles = JSON.parse(button.dataset.userRoles);
            const currentUserRoleId = userRoles.length > 0 ? userRoles[0] : null; // Get the ID of the first role

            // Populate modal fields with the clicked user's data
            modalUserId.value = userId;
            modalUserName.textContent = userName;
            modalMessages.innerHTML = ''; // Clear any messages from previous modal interactions

            // Select the correct radio button based on the user's current role
            modalRolesRadioButtonsContainer.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.checked = (parseInt(radio.value) === currentUserRoleId);
            });

            showModal(); // Display the modal
        }
    });

    // Event listeners for closing the modal (via specific buttons or clicking outside)
    if (closeUpdateRoleModalBtn) {
        closeUpdateRoleModalBtn.addEventListener('click', hideModal); // Close button click
    }
    if (cancelUpdateRoleBtn) {
        cancelUpdateRoleBtn.addEventListener('click', hideModal); // Cancel button click
    }
    if (updateRoleModal) {
        // Close modal if click occurs directly on the modal's background (outside its content)
        updateRoleModal.addEventListener('click', function(event) {
            if (event.target === updateRoleModal) {
                hideModal();
            }
        });
    }

    // Handle modal form submission via AJAX for role updates
    if (updateRoleForm) {
        updateRoleForm.addEventListener('submit', async function(e) {
            e.preventDefault(); // Prevent the default form submission (it causes a full page reload)

            const userId = modalUserId.value; // Get the user ID from the hidden input
            // Construct the API endpoint URL for updating the user's roles
            const url = `/manage-users/${userId}/update-roles`;
            // Get the CSRF token from the meta tag for secure POST/PUT requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Find the currently selected role radio button
            const selectedRadio = modalRolesRadioButtonsContainer.querySelector('input[type="radio"]:checked');
            // Get its value (role ID) or null if none is selected
            const selectedRoleId = selectedRadio ? selectedRadio.value : null;

            // Prepare the data to be sent in the request body
            // 'roles' is expected to be an array of role IDs by the Laravel controller's syncRoles method
            const dataToSend = {
                user_id: userId, // Include user_id, though it's also in the URL path
                roles: selectedRoleId ? [parseInt(selectedRoleId)] : [] // Send an array, parse ID to integer
            };

            try {
                // Send the AJAX request using the Fetch API
                const response = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json', // Indicate sending JSON
                        'Accept': 'application/json',       // Expecting JSON response
                        'X-CSRF-TOKEN': csrfToken          // Include CSRF token for security
                    },
                    body: JSON.stringify(dataToSend) // Convert data to JSON string for the body
                });

                const responseData = await response.json(); // Parse the JSON response from the server

                if (response.ok) { // Check if the HTTP status code is in the 200-299 range (success)
                    modalMessages.innerHTML = `<div class="alert-success">${responseData.message}</div>`; // Display success message in modal

                    // --- LOGIC FOR HANDLING ADMIN'S OWN ROLE CHANGE & REDIRECT ---
                    // If the server response includes a 'redirect_url', it means the admin changed their own role
                    // and has been logged out on the server-side.
                    if (responseData.redirect_url) {
                        console.log('Admin role updated. Redirecting to login page...');
                        // Add a small delay to allow the user to read the success message
                        setTimeout(() => {
                            // Redirect the browser to the specified URL (your sign-in page)
                            window.location.href = responseData.redirect_url;
                        }, 1500);
                        return; // Stop further execution of this JavaScript function
                    }

                    // --- Logic for when another user's role was updated ---
                    // If no 'redirect_url' is present, it means an admin updated another user's role.
                    // In this case, update the specific table row's roles badge dynamically without a full page reload.
                    const rolesTd = document.getElementById(`roles-${userId}`); // Find the table cell for roles
                    if (rolesTd && responseData.newRolesHtml) {
                        rolesTd.innerHTML = responseData.newRolesHtml; // Update its content with the new roles HTML
                    }

                    console.log('Another user\'s role updated. Hiding modal...');
                    // Hide the modal after a delay, allowing the user to see the update
                    setTimeout(() => {
                        hideModal();
                    }, 1500);

                } else { // Server responded with a non-2xx status (e.g., 422 Unprocessable Entity for validation, 500 Internal Server Error)
                    let errorMessage = 'An error occurred. Please try again.';
                    // If it's a validation error (HTTP 422), format and display specific error messages
                    if (response.status === 422) {
                        const errors = responseData.errors; // Get validation errors object
                        errorMessage = '<div class="alert-danger"><ul>';
                        for (const key in errors) {
                            // Append each validation error message
                            errorMessage += `<li>${errors[key][0]}</li>`;
                        }
                        errorMessage += '</ul></div>';
                    } else if (responseData.message) {
                        // If a general error message is provided by the server
                        errorMessage = `<div class="alert-danger">${responseData.message}</div>`;
                    }
                    modalMessages.innerHTML = errorMessage; // Display the error message in the modal
                    console.error('Server error response:', responseData); // Log the full error response
                }
            } catch (error) { // Catch block for network errors or issues parsing the JSON response
                console.error('AJAX Error:', error); // Log the technical error
                modalMessages.innerHTML = `<div class="alert-danger">Network error or unexpected response: ${error.message}</div>`; // Display a user-friendly error
            }
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
                newNoUsersRow.innerHTML = '<td colspan="5" style="text-align: center;">No users found.</td>';
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
