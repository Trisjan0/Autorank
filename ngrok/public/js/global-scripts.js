document.addEventListener('DOMContentLoaded', () => {
    /*
    |--------------------------------------------------------------------------
    | FOR COPYING TEXTS TO CLIPBOARD -- START
    |--------------------------------------------------------------------------
    */
    const copyToast = document.getElementById('copyToast');

    function showCopiedToast(duration = 1250) {
        if (!copyToast) {
            console.error('Toast element with ID "copyToast" not found.');
            return;
        }

        copyToast.classList.add('show');

        setTimeout(() => {
            copyToast.classList.remove('show');
        }, duration);
    }

    async function copyInstructorsName() {
        const usernameElement = document.getElementById('username');

        if (usernameElement) {
            const textToCopy = usernameElement.innerText;

            // For older browsers
            if (navigator.clipboard && navigator.clipboard.writeText) {
                try {
                    await navigator.clipboard.writeText(textToCopy);
                    showCopiedToast();
                } catch (err) {
                    console.error('Failed to copy instructor\'s name: ', err);
                }
            }
        }
    }

    async function copyInstructorNumber() {
        const instructorsNumberElement = document.getElementById('instructorsNumber');

        if (instructorsNumberElement) {
            const textToCopy = instructorsNumberElement.innerText;

            // For older browsers
            if (navigator.clipboard && navigator.clipboard.writeText) {
                try {
                    await navigator.clipboard.writeText(textToCopy);
                    showCopiedToast();
                } catch (err) {
                    console.error('Failed to copy instructor\'s number: ', err);
                }
            }
        }
    }

    window.copyInstructorsName = copyInstructorsName;
    window.copyInstructorNumber = copyInstructorNumber;
    /*
    |--------------------------------------------------------------------------
    | FOR COPYING TEXTS TO CLIPBOARD -- END
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | FOR TOGGLING HIDDEN MENU -- START
    |--------------------------------------------------------------------------
    */
    const nav = document.getElementById('hidden-menu');
    const menuToggleButton = document.getElementById('menu-toggle-button');
    const toggleMenuIcon = document.querySelector('.fa-bars');

    if (nav && menuToggleButton) {
        nav.classList.remove('is-active');
    }

    function toggleMenu() {
        if (!nav || !menuToggleButton || !toggleMenuIcon) {
            console.error('Menu or toggle icon element not found for toggling.');
            return;
        }

        const isActive = nav.classList.toggle('is-active');

        if (isActive) {
            toggleMenuIcon.classList.remove('fa-bars');
            toggleMenuIcon.classList.add('fa-times');
        } else {
            toggleMenuIcon.classList.remove('fa-times');
            toggleMenuIcon.classList.add('fa-bars');
        }

    }

    if (menuToggleButton) {
        menuToggleButton.addEventListener('click', toggleMenu);
    }

    // Closes the menu when a cursor leaves the menu container
    if (nav && menuToggleButton) {
        nav.addEventListener('mouseleave', function() {
            if (nav.classList.contains('is-active')) {
                toggleMenu();
            }
        });
    }
    /*
    |--------------------------------------------------------------------------
    | FOR TOGGLING HIDDEN MENU -- END
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | FOR GOING BACK TO THE PREVIOUS PAGE -- START
    |--------------------------------------------------------------------------
    */
    function goBack() {
        history.back();
    }

    window.goBack = goBack;
    /*
    |--------------------------------------------------------------------------
    | FOR GOING BACK TO THE PREVIOUS PAGE -- START
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | FOR ADDING / EDITING PHONE NUMBER -- START
    |--------------------------------------------------------------------------
    */
    const openModalBtn = document.getElementById('openPhoneNumberModalBtn');
    const phoneNumberModal = document.getElementById('phoneNumberModal');
    const closePhoneNumberModalBtn = document.getElementById('closePhoneNumberModalBtn');
    const body = document.body;

    const phoneInputStep = document.getElementById('phoneInputStep');
    const otpInputStep = document.getElementById('otpInputStep');

    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const phoneInputField = document.getElementById('phoneInput');
    const otpInputField = document.getElementById('otpInput');
    const phoneModalForm = document.getElementById('phoneNumberUpdateForm');

    // Resets the modal on page load and whenever the modal's opened or closed
    function resetPhoneNumberModal() {
        if (phoneInputStep && otpInputStep) {
            phoneInputStep.classList.remove('hidden-step');
            otpInputStep.classList.remove('visible-step');
            otpInputStep.classList.add('hidden-step');
        }
        if (phoneInputField) phoneInputField.value = '';
        if (otpInputField) otpInputField.value = '';
    }

    resetPhoneNumberModal();

    if (openModalBtn) {
        openModalBtn.addEventListener('click', function(event) {
            event.preventDefault();
            if (phoneNumberModal) {
                phoneNumberModal.style.display = 'flex';
                body.classList.add('modal-open');
                resetPhoneNumberModal();
            }
        });
    }

    if (closePhoneNumberModalBtn) {
        closePhoneNumberModalBtn.addEventListener('click', function() {
            if (phoneNumberModal) {
                phoneNumberModal.style.display = 'none';
                body.classList.remove('modal-open');
                resetPhoneNumberModal();
            }
        });
    }

    // Closes modal if clicked outside of it
    if (phoneNumberModal) {
        phoneNumberModal.addEventListener('click', function(event) {
            if (event.target === phoneNumberModal) {
                phoneNumberModal.style.display = 'none';
                body.classList.remove('modal-open');
                resetPhoneNumberModal();
            }
        });
    }

    // --- Phone Number Input Filtering ---
    if (phoneInputField) {
        phoneInputField.addEventListener('focus', function() {
            if (this.value === '') {
                this.value = '09';
            }
        });

        phoneInputField.addEventListener('input', function() {
            let currentValue = this.value.replace(/[^0-9]/g, '');
            if (currentValue.length > 0 && !currentValue.startsWith('09')) {
                this.value = '09' + currentValue.substring(currentValue.startsWith('0') ? 1 : 0);
            } else if (currentValue.length === 0) {
                this.value = '';
            } else {
                this.value = currentValue;
            }
            if (this.value.length > 11) {
                this.value = this.value.substring(0, 11);
            }
        });

        phoneInputField.addEventListener('paste', function(event) {
            event.preventDefault();
            const pastedText = (event.clipboardData || window.clipboardData).getData('text');
            let cleanPastedText = pastedText.replace(/[^0-9]/g, '');

            if (!cleanPastedText.startsWith('09') && cleanPastedText.length > 0) {
                 cleanPastedText = '09' + cleanPastedText.substring(cleanPastedText.startsWith('0') ? 1 : 0);
            }
            if (cleanPastedText.length > 11) {
                cleanPastedText = cleanPastedText.substring(0, 11);
            }
            this.value = cleanPastedText;
        });
    }

    // --- OTP Input Filtering ---
    if (otpInputField) {
        otpInputField.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 6) {
                this.value = this.value.substring(0, 6);
            }
        });
    }

    // --- Step 1: Send OTP Button Click ---
    if (sendOtpBtn) {
        sendOtpBtn.addEventListener('click', async function(event) {
            event.preventDefault();

            const phoneNumber = phoneInputField.value;

            // Client-side validation for exactly 11 digits and starting with "09"
            if (!/^09[0-9]{9}$/.test(phoneNumber)) {
                alert('Please enter a valid 11-digit phone number starting with 09 (e.g., 09171234567).');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch('/profile/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        phone_number: phoneNumber
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to send OTP.');
                }

                const data = await response.json();
                console.log('Success:', data.message);

                alert('OTP sent successfully to ' + phoneNumber + '!');

                // If 'otp' is returned in dev mode, log it for convenience
                if (data.otp) {
                    console.log('DEV MODE OTP: ', data.otp);
                }

                // Hide phone input step and show OTP input step using classes
                if (phoneInputStep && otpInputStep) {
                    phoneInputStep.classList.add('hidden-step');

                    otpInputStep.classList.remove('hidden-step');
                }
                otpInputField.focus();

            } catch (error) {
                console.error('Error sending OTP:', error);
                alert('Error sending OTP: ' + error.message);
            }
        });
    }

    // --- Step 2: Verify OTP & Save Phone Number ---
    if (phoneModalForm) {
        phoneModalForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            if (otpInputStep && otpInputStep.classList.contains('hidden-step')) {
                return;
            }

            const otpCode = otpInputField.value;
            const phoneNumber = phoneInputField.value;

            if (!/^[0-9]{6}$/.test(otpCode)) {
                alert('Please enter a valid 6-digit OTP.');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                // Send OTP and phone number to verify and save
                const response = await fetch('/profile/verify-phone-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        phone_number: phoneNumber, // Send phone number for verification context
                        otp_code: otpCode
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'OTP verification failed.');
                }

                const data = await response.json();
                console.log('Success:', data.message);
                alert(data.message);
                window.location.reload(); // Reload page on final success

            } catch (error) {
                console.error('Error verifying OTP:', error);
                alert('Error verifying OTP: ' + error.message);
            }
        });
    }
    /*
    |--------------------------------------------------------------------------
    | FOR ADDING / EDITING PHONE NUMBER -- END
    |--------------------------------------------------------------------------
    */
});
