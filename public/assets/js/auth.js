
document.addEventListener('DOMContentLoaded', function() {
    // Botones para cambiar de formulario
    document.getElementById('goToLoginBtn').addEventListener('click', function() {
        document.querySelectorAll('.form-page').forEach(f => f.classList.remove('active'));
        document.getElementById('loginForm').classList.add('active');
    });
    document.getElementById('goToRegisterBtn').addEventListener('click', function() {
        document.querySelectorAll('.form-page').forEach(f => f.classList.remove('active'));
        document.getElementById('registerFormStep1').classList.add('active');
    });

    // Botón de "¿No tienes cuenta? Regístrate"
    let goToRegisterFromLoginBtn = document.getElementById('goToRegisterFromLoginBtn');
    if(goToRegisterFromLoginBtn){
        goToRegisterFromLoginBtn.addEventListener('click', function() {
            document.querySelectorAll('.form-page').forEach(f => f.classList.remove('active'));
            document.getElementById('registerFormStep1').classList.add('active');
        });
    }

    // Botón de regreso desde login
    let backFromLoginBtn = document.getElementById('backFromLoginBtn');
    if(backFromLoginBtn){
        backFromLoginBtn.addEventListener('click', function() {
            document.querySelectorAll('.form-page').forEach(f => f.classList.remove('active'));
            document.getElementById('welcomeForm').classList.add('active');
        });
    }

});


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
            ODA
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