document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.details-button').forEach(button => {
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            let content = this.nextElementSibling;
            const isVisible = content.style.display === 'block';
            document.querySelectorAll('.dropdown-content').forEach(c => {
                c.style.display = 'none';
            });
            if (!isVisible) {
                content.style.display = 'block';
            }
        });
    });

    window.addEventListener('click', function (e) {
        document.querySelectorAll('.dropdown-content').forEach(content => {
            content.style.display = 'none';
        });
    });

    document.querySelectorAll('.toggle-button').forEach(button => {
        button.addEventListener('click', async function () {
            const url = this.dataset.url;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                if (data.success) {
                    // Get the ID from the button's ID attribute
                    const positionId = this.id.split('-').pop();
                    
                    // Find the corresponding badge and button
                    const badge = document.getElementById(`badge-${positionId}`);
                    const toggleButton = document.getElementById(`toggle-button-${positionId}`);

                    // Update UI based on the new status from the server
                    if (data.is_available) {
                        badge.textContent = 'Available';
                        badge.classList.remove('unavailable');
                        badge.classList.add('available');
                        
                        toggleButton.textContent = 'Set Unavailable';
                        toggleButton.classList.remove('set-available');
                        toggleButton.classList.add('set-unavailable');
                    } else {
                        badge.textContent = 'Unavailable';
                        badge.classList.remove('available');
                        badge.classList.add('unavailable');

                        toggleButton.textContent = 'Set Available';
                        toggleButton.classList.remove('set-unavailable');
                        toggleButton.classList.add('set-available');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    });
});