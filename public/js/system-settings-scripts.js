document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    /*
    |--------------------------------------------------------------------------
    | Handlers for Theme and Color
    |--------------------------------------------------------------------------
    */
    const darkModeToggle = document.getElementById('darkModeToggle');
    const primaryColorInput = document.getElementById('primaryColor');
    const resetColorsBtn = document.getElementById('resetColorsBtn');

    // Event listener for the dark mode toggle (User Preference)
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', (event) => {
            const isDarkMode = event.target.checked;
            document.body.classList.toggle('dark-mode', isDarkMode);
            saveUserTheme(isDarkMode ? 'dark' : 'light');
        });
    }

    // Event listener for the primary color input (Global Setting)
    if (primaryColorInput) {
        primaryColorInput.addEventListener('input', debounce((event) => {
            const newColor = event.target.value;
            document.documentElement.style.setProperty('--primaryColor', newColor);
            savePrimaryColor(newColor);
        }, 500));
    }

    // Event listener for the reset button (Global Setting)
    if (resetColorsBtn) {
        resetColorsBtn.addEventListener('click', () => {
            const defaultColor = '#262626';
            document.documentElement.style.setProperty('--primaryColor', defaultColor);
            if (primaryColorInput) {
                primaryColorInput.value = defaultColor;
            }
            resetPrimaryColor();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | API Functions to Save Settings
    |--------------------------------------------------------------------------
    */

    /** Saves the user's personal theme choice. */
    async function saveUserTheme(themeValue) {
        try {
            await fetch('/user/preference/theme', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ theme: themeValue })
            });
        } catch (error) {
            console.error('Error saving user theme:', error);
        }
    }

    /** Saves the new global primary color. */
    async function savePrimaryColor(colorValue) {
        // NOTE: Make sure your route matches this URL
        const url = '/system-settings/primary-color';
        try {
            await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ primary_color: colorValue })
            });
        } catch (error) {
            console.error('Error saving primary color:', error);
        }
    }

    /** Resets the global primary color in the database. */
    async function resetPrimaryColor() {
        // NOTE: Make sure your route matches this URL
        const url = '/system-settings/theme/reset';
        try {
            await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
        } catch (error) {
            console.error('Error resetting theme color:', error);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Functions
    |--------------------------------------------------------------------------
    */

    /** Debounce function to limit how often a function is called. */
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Logo Upload Modal Logic (Unchanged)
    |--------------------------------------------------------------------------
    */
    const modal = document.getElementById('change-website-logo-modal');
    if (modal) {
        const openBtn = document.getElementById('upload-logo-button');
        const form = modal.querySelector('.kra-upload-form');
        const closeBtn = modal.querySelector('.close-modal-btn');
        const initialStep = modal.querySelector('.initial-step');
        const confirmationStep = modal.querySelector('.confirmation-step');
        const proceedBtn = modal.querySelector('.proceed-btn');
        const backBtn = modal.querySelector('.back-btn');
        const confirmBtn = modal.querySelector('.confirm-btn');
        const messages = {
            initial: modal.querySelector('.modal-messages'),
            confirmation: modal.querySelector('.confirmation-message-area'),
            finalStatus: modal.querySelector('.final-status-message-area'),
        };

        const previewContainer = modal.querySelector('#logoConfirmPreviewContainer');
        const previewImage = modal.querySelector('#logoConfirmPreview');

        const showStep = (step) => {
            initialStep.style.display = (step === 'initial') ? 'block' : 'none';
            confirmationStep.style.display = (step === 'confirmation') ? 'block' : 'none';
        };
        const hideModal = () => {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
            Object.values(messages).forEach(el => el && (el.innerHTML = ''));
            form.reset();
            [confirmBtn, backBtn, closeBtn].forEach(btn => btn && (btn.disabled = false));
            previewImage.src = '';
            previewContainer.style.display = 'none';
            showStep('initial');
        };

        openBtn?.addEventListener('click', () => {
            modal.style.display = 'flex';
            document.body.classList.add('modal-open');
        });

        closeBtn?.addEventListener('click', hideModal);
        modal.addEventListener('click', (e) => (e.target === modal) && hideModal());

        backBtn?.addEventListener('click', () => { 
            showStep('initial'); 
            messages.finalStatus.innerHTML = ''; 
            previewImage.src = '';
            previewContainer.style.display = 'none';
        });

        proceedBtn?.addEventListener('click', () => {
            if (messages.initial) messages.initial.innerHTML = '';
            if (!form.checkValidity()) {
                if (messages.initial) {
                    messages.initial.innerHTML = '<div class="alert-danger">Please select a file.</div>';
                }
                return;
            }

            const formData = new FormData(form);
            let confirmationHtml = 'Please confirm the following details:<br><br>';

            formData.forEach((value, key) => {
                if (key.startsWith('_')) return;
                const input = form.querySelector(`[name="${key}"]`);
                if (!input || input.type === 'hidden' || input.disabled) return;
                const label = input.closest('.form-group')?.querySelector('[data-label],label')?.dataset.label || key;
                let displayValue = (value instanceof File) ? value.name : value;
                confirmationHtml += `<strong>${label}:</strong> ${displayValue}<br>`;

                if (value instanceof File && value.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewContainer.style.display = 'block';
                    };
                    reader.readAsDataURL(value);
                }
            });

            messages.confirmation.innerHTML = confirmationHtml;
            showStep('confirmation');
        });

        confirmBtn?.addEventListener('click', async () => {
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
                if (!response.ok) {
                    let errorMsg = data.message || 'Error.';
                    if (response.status === 422 && data.errors) {
                        errorMsg = Object.values(data.errors).map(err => `<p>${err[0]}</p>`).join('');
                    }
                    throw new Error(errorMsg);
                }
                messages.finalStatus.innerHTML = `<div class="alert-success">${data.message}</div>`;
                setTimeout(() => { hideModal(); window.location.reload(); }, 1500);
            } catch (error) {
                messages.finalStatus.innerHTML = `<div class="alert-danger">${error.message}</div>`;
                [confirmBtn, backBtn, closeBtn].forEach(btn => btn.disabled = false);
            }
        });
    }
});