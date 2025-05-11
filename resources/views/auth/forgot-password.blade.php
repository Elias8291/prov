<div class="form-page forgot-form" id="forgotForm">
    <button class="back-btn" id="backFromForgotBtn">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
        Atrás
    </button>

    <img src="{{ asset('assets/imagenes/logoAdminsitracion.png') }}" alt="Logo" class="logo-img">
    <h1>Recuperar Contraseña</h1>
    <p>Ingresa tu RFC y correo para recuperar tu contraseña</p>

    <form method="POST">
        @csrf
        <div class="input-group">
            <label for="forgot-rfc">RFC*</label>
            <input type="text" id="forgot-rfc" name="rfc" placeholder="Ingresa tu RFC" required>
        </div>

        <div class="input-group">
            <label for="forgot-email">Correo Electrónico*</label>
            <input type="email" id="forgot-email" name="email" placeholder="ejemplo@correo.com" required>
        </div>

        <button type="submit" class="btn" id="recoverBtn">Recuperar Contraseña</button>
    </form>
</div>