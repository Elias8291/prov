<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="form-page register-form" id="registerFormStep1">
    <button class="back-btn" id="backFromRegisterStep1Btn">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
        Atrás
    </button>

    <img src="{{ asset('assets/imagenes/logoAdminsitracion.png') }}" alt="Logo" class="logo-img">
    <h1>Regístrate</h1>
    <p>Registro en el <span class="system-name">Padrón de Proveedores de Oaxaca</span></p>

    <form method="POST" action="" enctype="multipart/form-data" id="registerForm">
        @csrf
        <div class="input-group">
            <div class="file-input-header">
                <label for="register-file">Constancia del SAT (PDF)*</label>
                <button type="button" class="small-btn outline" id="viewExampleBtnStep1">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M14 10V12.6667C14 13.0203 13.8595 13.3594 13.6095 13.6095C13.3594 13.8595 13.0203 14 12.6667 14H3.33333C2.97971 14 2.64057 13.8595 2.39052 13.6095C2.14048 13.3594 2 13.0203 2 12.6667V3.33333C2 2.97971 2.14048 2.64057 2.39052 2.39052C2.64057 2.14048 2.97971 2 3.33333 2H6"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M10 2H14V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M9.3335 6.66667L14.0002 2" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Ver ejemplo
                </button>
            </div>
            <label class="custom-file-upload" for="register-file">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z"
                        stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M14 2V8H20" stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M16 13H8" stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M16 17H8" stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M10 9H9H8" stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <span>Subir archivo PDF</span>
                <small>Tamaño máximo: 5MB</small>
            </label>
            <input type="file" id="register-file" name="sat_file" accept="application/pdf" required>
        </div>

        <button type="button" class="btn" id="nextToStep2Btn">Siguiente</button>
    </form>
</div>

<div class="form-page register-form" id="registerFormStep2">
    <button class="back-btn" id="backFromRegisterStep2Btn">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
        Atrás
    </button>

    <img src="{{ asset('assets/imagenes/logoAdminsitracion.png') }}" alt="Logo" class="logo-img">
    <h1>Datos del PDF</h1>

    <div id="pdf-data-preview">
        <div class="success-card" id="pdf-data-card">
            <!-- Sección de encabezado -->
            <div class="success-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.7649 14.1003 1.98232 16.07 2.85999"
                            stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M22 4L12 14.01L9 11.01" stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <h3 id="document-status">DOCUMENTO</h3>
                    <div class="warning-badge" id="warning-badge" style="display: none;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 8V12M12 16H12.01M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        VENCIDO
                    </div>
                </div>
                <div class="sat-actions">
                    <button class="small-btn" id="viewSatDataBtn" disabled>MIS DATOS SAT</button>
                </div>
            </div>

            <!-- Información visual para el usuario -->
            <div class="info-message">
                <p>Para completar el registro, ingrese su correo electrónico y establezca una contraseña.</p>
            </div>

            <!-- Sección de correo -->
            <div class="email-section">
                <p class="name-display"><strong>Correo Electronico:</strong></p>
                <input type="email" id="email-input" class="email-input" placeholder="INGRESE CORREO">
            </div>

            <div class="password-section">
                <p class="name-display"><strong>Contraseña:</strong></p>
                <div class="password-input-container" style="position: relative;">
                    <input type="password" id="password-input" class="email-input" placeholder="INGRESE CONTRASEÑA"
                        required>
                    <button type="button" class="password-toggle"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="password-toggle-icon">
                            <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="password-confirm-section">
                <p class="name-display"><strong>Confirmar Contraseña:</strong></p>
                <div class="password-input-container" style="position: relative;">
                    <input type="password" id="password-confirm-input" class="email-input"
                        placeholder="CONFIRME CONTRASEÑA" required>
                    <button type="button" class="password-toggle"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="password-toggle-icon">
                            <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Sección de carga -->
            <div id="sat-data-loading" style="display: none;">
                <div class="spinner"></div>
            </div>
        </div>
    </div>

    <button type="button" class="btn" id="registerBtn">Registrarse</button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const successModal = document.getElementById('successModal');
        const errorModal = document.getElementById('errorModal');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');
        const closeButtons = document.querySelectorAll('.close-modal');

        const passwordToggles = document.querySelectorAll('.password-toggle');

        passwordToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');
                const icon = this.querySelector('.password-toggle-icon');

                // Toggle between password and text input type
                if (input.type === 'password') {
                    input.type = 'text';
                    // Change to "hide password" icon (eye with slash)
                    icon.innerHTML = `
                        <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    `;
                } else {
                    input.type = 'password';
                    // Change back to "show password" icon (eye)
                    icon.innerHTML = `
                        <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    `;
                }
            });
        });
        // Cerrar modal al hacer clic en X
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                successModal.style.display = 'none';
                errorModal.style.display = 'none';
            });
        });

        // Cerrar modal al hacer clic fuera de él
        window.addEventListener('click', function(event) {
            if (event.target === successModal) {
                successModal.style.display = 'none';
            }
            if (event.target === errorModal) {
                errorModal.style.display = 'none';
            }
        });

        // Manejar clic en botón de éxito
        document.getElementById('successModalBtn').addEventListener('click', function() {
            successModal.style.display = 'none';
            if (window.redirectUrl) {
                window.location.href = window.redirectUrl;
            }
        });

        // Manejar clic en botón de error
        document.getElementById('errorModalBtn').addEventListener('click', function() {
            errorModal.style.display = 'none';
        });

       document.getElementById('registerBtn').addEventListener('click', function() {
    // Deshabilitar botón para prevenir múltiples envíos
    this.disabled = true;
    this.textContent = 'Procesando...';
    const btnElement = this;

    // Recopilar datos del formulario
    const satFileInput = document.getElementById('register-file');
    const satFile = satFileInput.files[0];
    const email = document.getElementById('email-input').value.trim();
    const password = document.getElementById('password-input').value;
    const passwordConfirm = document.getElementById('password-confirm-input').value;
    const secureToken = document.getElementById('secure_data_token')?.value;

    // Validaciones básicas
    if (!password) {
        errorMessage.textContent = 'Por favor, ingrese una contraseña.';
        errorModal.style.display = 'block';
        btnElement.disabled = false;
        btnElement.textContent = 'Registrarse';
        return;
    }

    if (password !== passwordConfirm) {
        errorMessage.textContent = 'Las contraseñas no coinciden.';
        errorModal.style.display = 'block';
        btnElement.disabled = false;
        btnElement.textContent = 'Registrarse';
        return;
    }

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errorMessage.textContent = 'Por favor, ingrese un correo electrónico válido.';
        errorModal.style.display = 'block';
        btnElement.disabled = false;
        btnElement.textContent = 'Registrarse';
        return;
    }

    if (!secureToken) {
        errorMessage.textContent = 'Error de seguridad: Token de datos no encontrado. Por favor, reinicie el proceso de registro.';
        errorModal.style.display = 'block';
        btnElement.disabled = false;
        btnElement.textContent = 'Registrarse';
        return;
    }

    // Crear objeto FormData
    const formData = new FormData();
    formData.append('sat_file', satFile);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('secure_data_token', secureToken);
    formData.append('_token', document.querySelector('input[name="_token"]').value);

    // Enviar solicitud AJAX con mejor manejo de errores
    fetch('/register', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                // Manejar los distintos tipos de errores
                if (response.status === 422) {
                    // Errores de validación
                    let errorMsg = 'Error de validación: ';
                    
                    if (data.errors) {
                        // Extraer mensajes de error de cada campo
                        const errorMessages = [];
                        for (const field in data.errors) {
                            if (data.errors.hasOwnProperty(field)) {
                                errorMessages.push(data.errors[field][0]);
                            }
                        }
                        errorMsg = errorMessages.join(', ');
                    } else if (data.message) {
                        errorMsg = data.message;
                    }
                    
                    throw { displayMessage: errorMsg };
                } else if (response.status === 419) {
                    throw { displayMessage: 'Error de CSRF: Por favor, recarga la página e intenta de nuevo.' };
                } else {
                    throw { displayMessage: data.message || `Error al procesar el registro (${response.status}).` };
                }
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            let msg = data.message || 'Registro exitoso. Por favor inicia sesión desde la página principal.';

            // Eliminar etiquetas HTML del mensaje
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = msg;
            msg = tempDiv.textContent || tempDiv.innerText;

            // Mostrar modal de éxito
            successMessage.textContent = msg;
            successModal.style.display = 'block';

            // Almacenar URL de redirección
            if (data.redirect) {
                window.redirectUrl = data.redirect;
            }
        } else {
            // Mostrar modal de error
            errorMessage.textContent = data.message || 'No se pudo completar el registro.';
            errorModal.style.display = 'block';
            btnElement.disabled = false;
            btnElement.textContent = 'Registrarse';
        }
    })
    .catch(error => {
        // Mostrar mensaje en modal sin exponer detalles técnicos en la consola
        errorMessage.textContent = error.displayMessage || 'Ocurrió un error al enviar el formulario. Por favor, intenta de nuevo.';
        errorModal.style.display = 'block';
        btnElement.disabled = false;
        btnElement.textContent = 'Registrarse';
    });
});
    });
</script>
