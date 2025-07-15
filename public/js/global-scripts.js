// For Toggling the Hidden Menu
let nav = document.getElementById('hidden-menu');
let toggleMenuIcon = document.querySelector('.fa-bars');
let mobileProfileMenu = document.querySelectorAll('.profile-menu');
nav.style.display = "none";

function toggleMenu(){
    if (nav.style.display === "none") {
        nav.style.display = "flex";
        toggleMenuIcon.classList.remove('fa-bars');
        toggleMenuIcon.classList.add('fa-times');
    } else {
        nav.style.display = "none";
        toggleMenuIcon.classList.remove('fa-times');
        toggleMenuIcon.classList.add('fa-bars');
    }

    // Hide the menu once the user's cursor leaves the container for the menu
    nav.addEventListener('mouseleave', function() {

    if (nav.style.display === "flex") {
            nav.style.display = "none";
            toggleMenuIcon.classList.remove('fa-times');
            toggleMenuIcon.classList.add('fa-bars');
        }
    });
}

// For Copying Texts to Clipboard
function copyInstructorsName() {
    const usernameElement = document.getElementById('username');

    if (usernameElement) {
        const textToCopy = usernameElement.innerText;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(textToCopy)
        }
    }
}

function copyInstructorNumber() {
    const instructorsNumberElement = document.getElementById('instructorsNumber');

    if (instructorsNumberElement) {
        const textToCopy = instructorsNumberElement.innerText;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(textToCopy)
        }
    }
}

// For Going Back a Page
function goBack() {
    history.back();
}