@extends('layouts.app')

@section('content')
<div class="container">
    <div class="left-section">
        <div class="carousel-wrapper">
            <div class="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="{{ asset('assets/imagenes/carrousel_1.webp') }}" alt="Imagen 1">
                        <div class="carousel-text">
                            <h2>Inscripción Simplificada</h2>
                            <p>Regístrate en minutos y comienza a participar en licitaciones públicas de manera ágil y segura.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('assets/imagenes/carrousel_2.webp') }}" alt="Imagen 2">
                        <div class="carousel-text">
                            <h2>Renovación sin Complicaciones</h2>
                            <p>Mantén tus datos actualizados fácilmente y renueva tu registro cuando lo necesites.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('assets/imagenes/carrousel3.webp') }}" alt="Imagen 3">
                        <div class="carousel-text">
                            <h2>Validación de Documentos</h2>
                            <p>Nuestros operadores verifica tus documentos rápidamente para que puedas comenzar a operar.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('assets/imagenes/carrousel4.webp') }}" alt="Imagen 4">
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
 <x-custom-modal
        modal-id="registrationSuccessModal"
        title="¡Registro Exitoso!"
        message="{{ session('message') ?? '¡Usuario registrado correctamente! Por favor, revisa tu correo electrónico para confirmar tu cuenta. Tienes <strong>72 horas</strong> para completar la confirmación.' }}"
        :show-modal="session('show_success_modal', false)"
    />
@endsection

