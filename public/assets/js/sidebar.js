const menuToggle = document.getElementById('menu-toggle');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebar-overlay');
let isMobile = window.innerWidth <= 992;
const userProfile = document.querySelector('.user-profile');

userProfile.addEventListener('click', function(e) {
    e.stopPropagation();
    userProfile.classList.toggle('active');
});

document.addEventListener('click', function(e) {
    if (!userProfile.contains(e.target)) {
        userProfile.classList.remove('active');
    }
});

function checkMobile() {
    isMobile = window.innerWidth <= 992;
    if (!isMobile) {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    }
}

menuToggle.addEventListener('click', () => {
    if (isMobile) {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
    }
});

sidebarOverlay.addEventListener('click', () => {
    sidebar.classList.remove('active');
    sidebarOverlay.classList.remove('active');
});

document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', () => {
        if (isMobile) {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        }
    });
});

window.addEventListener('resize', checkMobile);

checkMobile();