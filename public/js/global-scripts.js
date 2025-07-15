document.addEventListener('DOMContentLoaded', () => {
    // For Copying Texts to Clipboard
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
    // --- End of Copying Texts to Clipboard ---


    // For Toggling the Hidden Menu
    const nav = document.getElementById('hidden-menu');
    const menuToggleButton = document.getElementById('menu-toggle-button');
    const toggleMenuIcon = document.querySelector('.fa-bars');

    if (nav && menuToggleButton) {
        nav.classList.remove('is-active');
        menuToggleButton.setAttribute('aria-expanded', 'false');
        nav.setAttribute('aria-hidden', 'true');
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

        menuToggleButton.setAttribute('aria-expanded', isActive);
        nav.setAttribute('aria-hidden', !isActive);
    }

    if (menuToggleButton) {
        menuToggleButton.addEventListener('click', toggleMenu);
    }

    if (nav && menuToggleButton) {
        nav.addEventListener('mouseleave', function() {
            if (nav.classList.contains('is-active')) {
                toggleMenu();
            }
        });
    }
    // --- End of Toggling the Hidden Menu ---


    // For Going Back a Page
    function goBack() {
        history.back();
    }

    window.goBack = goBack;
    // --- End of Going Back a Page ---
});