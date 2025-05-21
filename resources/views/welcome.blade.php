@extends('layouts.app')

@section('content')
<div class="container">
    <div class="left-section">
        <div class="carousel-wrapper">
            <div class="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="{{ asset('assets/imagenes/carrousel1.png') }}" alt="Imagen 1">
                        <div class="carousel-text">
                            <h2>Inscripción Simplificada</h2>
                            <p>Regístrate en minutos y comienza a participar en licitaciones públicas de manera ágil y segura.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('assets/imagenes/carrousel2.png') }}" alt="Imagen 2">
                        <div class="carousel-text">
                            <h2>Renovación sin Complicaciones</h2>
                            <p>Mantén tus datos actualizados fácilmente y renueva tu registro cuando lo necesites.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('assets/imagenes/carrousel3.png') }}" alt="Imagen 3">
                        <div class="carousel-text">
                            <h2>Validación de Documentos</h2>
                            <p>Nuestros operadores verifica tus documentos rápidamente para que puedas comenzar a operar.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('assets/imagenes/carrousel4.png') }}" alt="Imagen 4">
                        <div class="carousel-text">
                            <h2>Seguimiento en Tiempo Real</h2>
                            <p>Consulta el estado de tu registro, documentos y procesos desde cualquier lugar.</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control prev" onclick="moveSlide(-1)">❮</button>
                <button class="carousel-control next" onclick="moveSlide(1)">❯</button>
                <div class="carousel-dots">
                    <span class="carousel-dot active" onclick="goToSlide(0)"></span>
                    <span class="carousel-dot" onclick="goToSlide(1)"></span>
                    <span class="carousel-dot" onclick="goToSlide(2)"></span>
                    <span class="carousel-dot" onclick="goToSlide(3)"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="right-section">
        <div class="forms-container">
            <div class="form-page welcome-form active" id="welcomeForm">
                <img src="{{ asset('assets/imagenes/logoAdminsitracion.png') }}" alt="Logo" class="logo-img">
                <h1 class="welcome-title-animation">Bienvenido</h1>
                <p class="welcome-subtitle">Padrón de Proveedores de Oaxaca</p>
                <p class="welcome-description">Portal oficial para el registro, actualización y renovación de proveedores del Estado de Oaxaca.</p>
                <div class="welcome-buttons">
                    <button type="button" class="btn" id="goToLoginBtn">Iniciar Sesión</button>
                    <button type="button" class="btn btn-secondary" id="goToRegisterBtn">Registrarse</button>
                </div>
            </div>

            @include('auth.login')
            @include('auth.forgot-password')
            @include('auth.registrarse')
            @include('auth.set-password')
        </div>
    </div>
</div>
<!-- Modal de éxito -->
<div id="successModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div class="modal-icon success">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.7649 14.1003 1.98232 16.07 2.85999" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 4L12 14.01L9 11.01" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h3>¡Registro exitoso!</h3>
        <p id="successMessage"></p>
        <button class="btn" id="successModalBtn">Aceptar</button>
    </div>
</div>

<!-- Modal de error -->
<div id="errorModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div class="modal-icon error">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 8V12M12 16H12.01M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="#F44336" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h3>Error en el registro</h3>
        <p id="errorMessage"></p>
        <button class="btn" id="errorModalBtn">Aceptar</button>
    </div>
</div>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show success modal if needed
    const showSuccessModal = "{{ session('show_success_modal') }}" === "1";
    if (showSuccessModal) {
        const modal = document.getElementById('successModal');
        const message = document.getElementById('successMessage');
        message.textContent = "{{ session('message') }}";
        modal.style.display = "block";
    }
    
    // Show error modal if needed
    const showErrorModal = "{{ session('show_error_modal') }}" === "1";
    if (showErrorModal) {
        const modal = document.getElementById('errorModal');
        const message = document.getElementById('errorMessage');
        message.textContent = "{{ session('error') }}";
        modal.style.display = "block";
    }
    
    // Handle redirection to registration form Step 2 after error
    const showRegisterForm = "{{ session('show_register_form') }}" === "1";
    if (showRegisterForm) {
        // Hide welcome form
        const welcomeForm = document.getElementById('welcomeForm');
        if (welcomeForm) welcomeForm.classList.remove('active');
        
        // Show registration form
        const registerForm = document.querySelector('.register-form');
        if (registerForm) registerForm.classList.add('active');
        
        // Go directly to step 2
        const step1 = document.getElementById('registerFormStep1');
        const step2 = document.getElementById('registerFormStep2');
        if (step1) step1.style.display = 'none';
        if (step2) step2.style.display = 'block';
        
        // If back button needs to be shown for step 2
        const backBtn = document.getElementById('backFromRegisterStep1Btn');
        if (backBtn) backBtn.style.display = 'block';
    }
    
    // Close modals
    document.querySelectorAll('.close-modal').forEach(function(btn) {
        btn.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });
    
    // Modal action buttons
    document.getElementById('successModalBtn').addEventListener('click', function() {
        document.getElementById('successModal').style.display = 'none';
    });
    
    document.getElementById('errorModalBtn').addEventListener('click', function() {
        document.getElementById('errorModal').style.display = 'none';
    });
});
</script>