// For Toggling the Sidebar > Main Menu
let nav = document.getElementById('hidden-menu');
let toggleMenuIcon = document.querySelector('.fa-bars');
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
}