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
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="password-toggle-icon">
                            <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
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

<script>
   
document.addEventListener('DOMContentLoaded', function() {
    @if (session('show_login'))
        document.querySelectorAll('.form-page').forEach(f => f.classList.remove('active'));
        document.getElementById('loginForm').classList.add('active');
    @elseif (session('show_register'))
        document.querySelectorAll('.form-page').forEach(f => f.classList.remove('active'));
        document.getElementById('registerFormStep1').classList.add('active');
    @endif
});
    // Password toggle functionality
    document.addEventListener('DOMContentLoaded', () => {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('login-password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                // Toggle the password input type between 'password' and 'text'
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle the icon appearance (optional)
                this.classList.toggle('show-password');
            });
        }
    });
</script>