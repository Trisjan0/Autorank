document.addEventListener('DOMContentLoaded', () => {
    const startEvaluationBtn = document.getElementById('start-evaluation-btn');

    // Check if the button exists on the page
    if (!startEvaluationBtn) {
        return;
    }

    startEvaluationBtn.addEventListener('click', function () {
        // Provide immediate feedback to the user
        this.textContent = 'Checking...';
        this.disabled = true;

        // Get the URL from the data attribute on the button
        const checkUrl = this.dataset.checkUrl;

        fetch(checkUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // --- SCENARIO B: Submissions are COMPLETE ---
                    showConfirmationModal({
                        title: 'Confirm Submission',
                        body: '<p>Are you sure you want to finalize your CCE submissions for evaluation? This action cannot be undone and your submissions will be locked.</p>',
                        confirmText: 'Confirm & Submit',
                        onConfirm: () => {
                            // The onConfirm function in your modal script handles the submission process
                            document.getElementById('submit-evaluation-form').submit();
                        }
                    });

                } else {
                    // --- SCENARIO A: Submissions are INCOMPLETE ---
                    let errorHtml = '<p>You cannot proceed. Please upload at least one document for the following Key Result Areas:</p><ul class="missing-kra-list">';
                    data.missing.forEach(function(item) {
                        errorHtml += `<li><a href="${item.route}">${item.name}</a></li>`;
                    });
                    errorHtml += '</ul>';

                    showConfirmationModal({
                        title: 'Missing Submissions',
                        body: errorHtml,
                        confirmText: 'Acknowledge', // Text for the button that will be hidden
                        onConfirm: () => {
                            hideConfirmationModal(); // Simply close the modal on confirm
                        }
                    });

                    const confirmBtn = document.getElementById('confirmActionBtn');
                    if(confirmBtn) {
                       confirmBtn.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error checking submissions:', error);
                showConfirmationModal({
                    title: 'Error',
                    body: '<p>An unexpected error occurred while checking your submissions. Please try again later.</p>',
                    confirmText: 'Close',
                    onConfirm: () => {
                        hideConfirmationModal();
                    }
                });
                 const confirmBtn = document.getElementById('confirmActionBtn');
                    if(confirmBtn) {
                       confirmBtn.style.display = 'none';
                    }
            })
            .finally(() => {
                // Reset button state
                this.textContent = 'Start CCE Evaluation Process';
                this.disabled = false;
            });
    });
});