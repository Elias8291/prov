<meta name="csrf-token" content="{{ csrf_token() }}">
<form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
    @csrf
    @if ($errors->any() && !$errors->has('email') && !$errors->has('password') && !$errors->has('password_confirmation'))
        <div class="alert alert-danger"
            style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; margin-bottom: 15px; border-radius: 4px;">
            <strong>Error:</strong>
            <ul style="margin: 5px 0 0 20px; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-page register-form inactive" id="registerFormStep1">
        <button class="back-btn" id="backFromRegisterBtn">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            Atrás
        </button>

        <img src="{{ asset('assets/imagenes/logoAdminsitracion.png') }}" alt="Logo" class="logo-img">
        <h1>Regístrate</h1>
        <p>Registro en el <span class="system-name">Padrón de Proveedores de Oaxaca</span></p>
        <div class="input-group">
            <div class="file-input-header">
                <label for="register-file">Constancia del SAT (PDF)*</label>
                <button type="button" class="small-btn outline" id="viewExampleBtnStep1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ffffff"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Ver ejemplo
                </button>
            </div>
            <label class="custom-file-upload" for="register-file">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="12" y1="18" x2="12" y2="12"></line>
                    <line x1="9" y1="15" x2="15" y2="15"></line>
                </svg>
                <span class="file-name">{{ session('temp_sat_file_name', 'Subir archivo PDF') }}</span>
                <small>Tamaño máximo: 5MB</small>
            </label>
            <input type="file" id="register-file" name="sat_file" accept="application/pdf" required>
        </div>
        <div class="pdf-data-container" style="display: {{ session('temp_sat_file_path') ? 'block' : 'none' }};">
            <div class="success-card" id="pdf-data-card">
                <button type="button" class="small-btn outline" id="viewSatDataBtn" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Ver Datos del SAT
                </button>
                <div class="email-section">
                    <p class="name-display"><strong>Correo Electrónico:</strong></p>
                    <input type="email" name="email" id="email-input" class="email-input"
                        placeholder="INGRESE CORREO" value="{{ old('email') }}" required>
                    @error('email')
                        <label class="error-message"
                            style="color: #F44336; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</label>
                    @enderror
                </div>
                <div class="password-section">
                    <p class="name-display"><strong>Contraseña:</strong></p>
                    <div class="password-input-container">
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password-input" class="email-input"
                                placeholder="INGRESE CONTRASEÑA" required>
                            <button type="button" class="password-toggle">
                                <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                    </path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <label class="error-message"
                            style="color: #F44336; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</label>
                    @enderror
                </div>
                <div class="password-confirm-section">
                    <p class="name-display"><strong>Confirmar Contraseña:</strong></p>
                    <div class="password-input-container">
                        <div class="password-wrapper">
                            <input type="password" name="password_confirmation" id="password-confirm-input"
                                class="email-input" placeholder="CONFIRME CONTRASEÑA" required>
                            <button type="button" class="password-toggle">
                                <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                    </path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @error('password_confirmation')
                        <label class="error-message"
                            style="color: #F44336; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</label>
                    @enderror
                </div>
                <input type="hidden" name="secure_data_token" id="secure_data_token"
                    value="{{ old('secure_data_token') }}">
            </div>
            <button type="submit" class="btn" id="registerBtn">Registrarse</button>
        </div>
    </div>
</form>

<!-- Modal de éxito al enviar correo de confirmación -->
<div id="successModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <h2>¡Registro Exitoso!</h2>
        <p>Te hemos enviado un correo de verificación.<br>
        Por favor revisa tu bandeja de entrada y sigue las instrucciones para activar tu cuenta.</p>
        <button id="closeSuccessModal" class="btn">Cerrar</button>
    </div>
</div>

