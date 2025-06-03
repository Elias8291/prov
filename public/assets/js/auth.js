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

    // Botón de regreso desde registro
    let backFromRegisterBtn = document.getElementById('backFromRegisterBtn');
    if(backFromRegisterBtn){
        backFromRegisterBtn.addEventListener('click', function() {
            document.querySelectorAll('.form-page').forEach(f => f.classList.remove('active'));
            document.getElementById('welcomeForm').classList.add('active');
        });
    }
});


