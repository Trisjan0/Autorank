document.addEventListener('DOMContentLoaded', () => {
    const uploadModal = document.getElementById('credential-upload-modal');

    if (uploadModal) {
        // --- Modal Element Selectors ---
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

        // --- Modal Control Functions ---
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

        // --- Event Listeners ---
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
                        const tableBody = document.getElementById('credentials-table-body');
                        if (tableBody && data.newRowHtml) {
                            const noResultsRow = document.getElementById('no-credentials-row');
                            if (noResultsRow) noResultsRow.remove();
                            tableBody.insertAdjacentHTML('afterbegin', data.newRowHtml);
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
});