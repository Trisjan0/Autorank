document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    /*
    |--------------------------------------------------------------------------
    | Dark Mode Handler (User Preference)
    |--------------------------------------------------------------------------
    */
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', async (event) => {
            const isDarkMode = event.target.checked;
            document.body.classList.toggle('dark-mode', isDarkMode);
            try {
                await fetch('/user/preference/theme', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ theme: isDarkMode ? 'dark' : 'light' })
                });
            } catch (error) {
                console.error('Error saving theme preference:', error);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Global Color Scheme Handler (Admin Only)
    |--------------------------------------------------------------------------
    */
    const primaryColorInput = document.getElementById('primaryColor');
    const resetColorsBtn = document.getElementById('resetColorsBtn');

    if (primaryColorInput) {
        primaryColorInput.addEventListener('input', debounce((event) => {
            const newColor = event.target.value;
            document.documentElement.style.setProperty('--primaryColor', newColor);
            savePrimaryColor(newColor);
        }, 500));
    }

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

    async function savePrimaryColor(colorValue) {
        try {
            await fetch('/system-settings/primary-color', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ primary_color: colorValue })
            });
        } catch (error) {
            console.error('Error saving primary color:', error);
        }
    }

    async function resetPrimaryColor() {
        try {
            await fetch('/system-settings/theme/reset', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
        } catch (error) {
            console.error('Error resetting theme color:', error);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Google Drive Access Handlers
    |--------------------------------------------------------------------------
    */
    const revokeBtn = document.getElementById('revoke-google-access-btn');
    const allowBtn = document.getElementById('allow-google-access-btn');

    if (revokeBtn) {
        revokeBtn.addEventListener('click', (event) => {
            const button = event.currentTarget;
            showConfirmationModal({
                title: button.dataset.modalTitle,
                body: button.dataset.modalBody,
                confirmText: 'Yes, Revoke',
                onConfirm: async () => {
                    const response = await fetch(button.dataset.action, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'An error occurred.');
                    
                    document.getElementById('confirmation-final-status-message-area').innerHTML = `<div class="alert-success">${data.message}</div>`;
                    setTimeout(() => window.location.reload(), 1500);
                }
            });
        });
    }

    if (allowBtn) {
        allowBtn.addEventListener('click', (event) => {
            event.preventDefault();
            const link = event.currentTarget;
            showConfirmationModal({
                title: link.dataset.modalTitle,
                body: link.dataset.modalBody,
                confirmText: 'Yes, Continue',
                onConfirm: () => {
                    window.location.href = link.href;
                }
            });
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Logo Upload Modal Logic (Admin Only)
    |--------------------------------------------------------------------------
    */
    const logoModal = document.getElementById('change-website-logo-modal');
    if (logoModal) {
        const openBtn = document.getElementById('upload-logo-button');
        const form = logoModal.querySelector('form');
        const closeBtn = logoModal.querySelector('.close-modal-btn');
        const initialStep = logoModal.querySelector('.initial-step');
        const confirmationStep = logoModal.querySelector('.confirmation-step');
        const proceedBtn = logoModal.querySelector('.proceed-btn');
        const backBtn = logoModal.querySelector('.back-btn');
        const confirmBtn = logoModal.querySelector('.confirm-btn');
        const messages = {
            initial: logoModal.querySelector('.modal-messages'),
            confirmation: logoModal.querySelector('.confirmation-message-area'),
            finalStatus: logoModal.querySelector('.final-status-message-area'),
        };
        const previewContainer = logoModal.querySelector('#logoConfirmPreviewContainer');
        const previewImage = logoModal.querySelector('#logoConfirmPreview');

        const showStep = (step) => {
            if (initialStep) initialStep.style.display = (step === 'initial') ? 'block' : 'none';
            if (confirmationStep) confirmationStep.style.display = (step === 'confirmation') ? 'block' : 'none';
        };

        const hideModal = () => {
            logoModal.style.display = 'none';
            document.body.classList.remove('modal-open');
            Object.values(messages).forEach(el => { if (el) el.innerHTML = ''; });
            form.reset();
            [confirmBtn, backBtn, closeBtn].forEach(btn => { if (btn) btn.disabled = false; });
            if (previewImage) previewImage.src = '';
            if (previewContainer) previewContainer.style.display = 'none';
            showStep('initial');
        };

        openBtn?.addEventListener('click', () => {
            logoModal.style.display = 'flex';
            document.body.classList.add('modal-open');
        });

        closeBtn?.addEventListener('click', hideModal);
        logoModal.addEventListener('click', (e) => { if (e.target === logoModal) hideModal(); });

        backBtn?.addEventListener('click', () => { 
            showStep('initial'); 
            if (messages.finalStatus) messages.finalStatus.innerHTML = ''; 
            if (previewImage) previewImage.src = '';
            if (previewContainer) previewContainer.style.display = 'none';
        });

        proceedBtn?.addEventListener('click', () => {
            if (messages.initial) messages.initial.innerHTML = '';
            if (!form.checkValidity()) {
                if (messages.initial) messages.initial.innerHTML = '<div class="alert-danger">Please select a file.</div>';
                return;
            }

            const formData = new FormData(form);
            let confirmationHtml = 'Please confirm the following details:<br><br>';
            const logoFile = formData.get('logo');

            if (logoFile instanceof File) {
                const label = form.querySelector('[name="logo"]')?.closest('.form-group')?.querySelector('[data-label],label')?.dataset.label || 'logo';
                confirmationHtml += `<strong>${label}:</strong> ${logoFile.name}<br>`;

                if (logoFile.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (previewImage) previewImage.src = e.target.result;
                        if (previewContainer) previewContainer.style.display = 'block';
                    };
                    reader.readAsDataURL(logoFile);
                }
            }

            if (messages.confirmation) messages.confirmation.innerHTML = confirmationHtml;
            showStep('confirmation');
        });

        confirmBtn?.addEventListener('click', async () => {
            const url = form.getAttribute('action');
            const formData = new FormData(form);

            [confirmBtn, backBtn, closeBtn].forEach(btn => { if (btn) btn.disabled = true; });
            if (messages.finalStatus) messages.finalStatus.innerHTML = '<div class="alert-info">Uploading... Please wait.</div>';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: formData,
                });
                const data = await response.json();
                if (!response.ok) {
                    let errorMsg = data.message || 'An error occurred.';
                    if (response.status === 422 && data.errors) {
                        errorMsg = Object.values(data.errors).map(err => `<p>${err.join(' ')}</p>`).join('');
                    }
                    throw new Error(errorMsg);
                }
                if (messages.finalStatus) messages.finalStatus.innerHTML = `<div class="alert-success">${data.message}</div>`;
                setTimeout(() => { window.location.reload(); }, 1500);
            } catch (error) {
                if (messages.finalStatus) messages.finalStatus.innerHTML = `<div class="alert-danger">${error.message}</div>`;
                [confirmBtn, backBtn, closeBtn].forEach(btn => { if (btn) btn.disabled = false; });
            }
        });
    }

    /**
     * Utility function to delay execution of a function.
     * @param {Function} func The function to execute after the delay.
     * @param {number} delay The delay in milliseconds.
     * @returns {Function}
     */
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }
});