<div class="form-page login-form" id="loginForm">
    <button class="back-btn" id="backFromLoginBtn">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
        Atrás
    </button>

    <img src="{{ asset('assets/imagenes/logoAdminsitracion.png') }}" alt="Logo" class="logo-img">
    <h1>Iniciar Sesión</h1>
    <p>Accede a tu cuenta del <span class="system-name">Padrón de Proveedores</span></p>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="input-group">
            <label for="login-rfc">RFC*</label>
            <input type="text" id="login-rfc" name="rfc" 
                   value="{{ old('rfc') }}" 
                   placeholder="Ingresa tu RFC" 
                   required autofocus>
            @error('rfc')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="input-group">
            <label for="login-password">Contraseña*</label>
            <div class="password-wrapper">
                <input type="password" id="login-password" name="password" 
                       placeholder="••••••••" required>
                <span class="toggle-password" id="togglePassword">
                    <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/>
                    </svg>
                    <svg class="eye-slash-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                        <path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z" fill="currentColor"/>
                    </svg>
                </span>
            </div>
            @error('password')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn" id="loginBtn">Iniciar Sesión</button>

        <div class="link-text">
            <button type="button" class="link-button" id="forgotPasswordBtn">Olvidé mi contraseña</button>
            <br>
            ¿No tienes una cuenta? <button type="button" class="link-button"
                id="goToRegisterFromLoginBtn">Regístrate</button>
        </div>
    </form>
</div>

<scrip>
    @if (session('show_login'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.form-page').forEach(f => f.classList.remove('active'));
            document.getElementById('loginForm').classList.add('active');
        });
    </script>
@endif
</scrip>