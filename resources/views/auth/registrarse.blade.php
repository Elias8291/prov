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
<style>
.modal-overlay {
    position: fixed; top:0; left:0; width:100vw; height:100vh;
    background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 10000;
}
.modal-content {
    background: #fff; border-radius: 8px; padding: 30px 40px; text-align: center;
    box-shadow: 0 4px 24px rgba(0,0,0,0.20);
    max-width: 90vw;
}
.modal-content h2 { color: #28a745; margin-bottom: 15px; }
#closeSuccessModal.btn {
    background: #28a745; color: #fff; border: none; padding: 8px 28px; border-radius: 4px; margin-top: 18px;
    font-size: 1rem; cursor: pointer;
}
#closeSuccessModal.btn:hover { background: #218838; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('registration_success'))
        // Mostrar el modal de éxito
        document.getElementById('successModal').style.display = 'flex';
        // Permitir cerrar el modal
        document.getElementById('closeSuccessModal').onclick = function() {
            document.getElementById('successModal').style.display = 'none';
        };
        // Cerrar al hacer clic fuera
        document.getElementById('successModal').onclick = function(e) {
            if (e.target === this) this.style.display = 'none';
        }
    @endif
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // ========== File Upload Handling ==========
        const fileInput = document.getElementById('register-file');
        const pdfDataContainer = document.querySelector('.pdf-data-container');
        const fileLabel = document.querySelector('.custom-file-upload span');
        const fileUploadContainer = document.querySelector('.custom-file-upload');
        const registerForm = document.getElementById('registerForm');
        const passwordInput = document.getElementById('password-input');
        const confirmPasswordInput = document.getElementById('password-confirm-input');

        // Function to process PDF file
        async function processPDF(file, fileName) {
            @@ - 119, 7 + 137, 7 @@ class = "email-input"
            placeholder = "CONFIRME CONTRASEÑA"
            required >
                tokenInput.type = 'hidden';
            tokenInput.id = 'secure_data_token';
            tokenInput.name = 'secure_data_token';
            document.getElementById('registerForm').appendChild(tokenInput);
            registerForm.appendChild(tokenInput);
        }
        tokenInput.value = token;

        @@ - 161, 12 + 179, 81 @@ class = "email-input"
        placeholder = "CONFIRME CONTRASEÑA"
        required >
    }
    });

    // Check for temporary file on page load
    @if (session('temp_sat_file_path'))
        fetch('{{ asset('storage/' . session('temp_sat_file_path')) }}')
        // ========== Password Validation ==========

        // Function to validate passwords match
        function validatePasswords() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            // Get/create error message element
            let errorElement = confirmPasswordInput.parentElement.nextElementSibling;
            if (!errorElement || !errorElement.classList.contains('error-message')) {
                errorElement = document.createElement('label');
                errorElement.className = 'error-message';
                errorElement.style.color = '#F44336';
                errorElement.style.fontSize = '0.9rem';
                errorElement.style.marginTop = '5px';
                errorElement.style.display = 'none';
                confirmPasswordInput.parentElement.after(errorElement);
            }

            // Check if passwords match only if both fields have values
            if (confirmPassword && password !== confirmPassword) {
                errorElement.textContent = 'Las contraseñas no coinciden';
                errorElement.style.display = 'block';
                return false;
            } else {
                errorElement.style.display = 'none';
                return true;
            }
        }

        // Add event listeners for password validation
        confirmPasswordInput.addEventListener('blur', validatePasswords);
        confirmPasswordInput.addEventListener('input', function() {
            if (confirmPasswordInput.dataset.blurred) {
                validatePasswords();
            }
        });

        confirmPasswordInput.addEventListener('focus', function() {
            confirmPasswordInput.dataset.blurred = 'true';
        });

        // Validate form on submit
        registerForm.addEventListener('submit', function(e) {
            if (!validatePasswords()) {
                e.preventDefault();
            }
        });

        // ========== Password Toggle Visibility ==========
        document.querySelectorAll('.password-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const passwordField = this.previousElementSibling;
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    this.innerHTML =
                        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
                } else {
                    passwordField.type = 'password';
                    this.innerHTML =
                        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                }
            });
        });

        // ========== Check for Temporary File ==========
        // This part would normally be handled by Blade directives
        const tempFilePath = document.querySelector('input[name="temp_sat_file_path"]')?.value;
        const tempFileName = document.querySelector('input[name="temp_sat_file_name"]')?.value;

        if (tempFilePath) {
            fetch(tempFilePath)
                .then(response => response.blob())
                .then(blob => {
                    const fileName = '{{ session('temp_sat_file_name', 'Subir archivo PDF') }}';
                    const fileName = tempFileName || 'Subir archivo PDF';
                    const file = new File([blob], fileName, {
                        type: 'application/pdf'
                    });
                    @@ - 181, 6 + 268, 105 @@ class = "email-input"
                    placeholder = "CONFIRME CONTRASEÑA"
                    required >
                        fileUploadContainer.classList.remove('file-selected');
                    pdfDataContainer.style.display = 'none';
                });
        @endif
    }

    // ========== Helper Functions ==========
    // These functions would normally be defined elsewhere or imported

    // Create a modal with loading animation
    function createModal(options = {}) {
        const modalElement = document.createElement('div');
        modalElement.className = 'modal-overlay';
        modalElement.style.position = 'fixed';
        modalElement.style.top = '0';
        modalElement.style.left = '0';
        modalElement.style.width = '100vw';
        modalElement.style.height = '100vh';
        modalElement.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        modalElement.style.display = 'flex';
        modalElement.style.justifyContent = 'center';
        modalElement.style.alignItems = 'center';
        modalElement.style.zIndex = '9999';

        if (options.html) {
            modalElement.innerHTML = options.html;
        }

        document.body.appendChild(modalElement);
        return modalElement;
    }

    // Create a spinner animation
    function createSpinner() {
        return `
            <div class="spinner-container" style="background: white; border-radius: 8px; padding: 20px; text-align: center;">
                <div class="spinner" style="border: 4px solid rgba(0, 0, 0, 0.1); width: 40px; height: 40px; border-radius: 50%; border-left-color: #09f; animation: spin 1s linear infinite; margin: 0 auto;"></div>
                <p style="margin-top: 15px; color: #333;">Procesando documento...</p>
                <style>
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                </style>
            </div>
        `;
    }

    // Function to extract QR code from PDF - placeholder
    async function extractQRCodeFromPDF(file) {
        // This would normally make an API call or process the PDF
        // For now, we'll simulate a delay
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve({
                    qrUrl: 'https://example.com/qr'
                });
            }, 1000);
        });
    }

    // Function to scrape SAT data - placeholder
    async function scrapeSATData(qrUrl) {
        // This would normally make an API call
        // For now, we'll simulate a delay
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve({
                    email: 'example@example.com',
                    rfc: 'EXAMPLE123456'
                });
            }, 500);
        });
    }

    // Function to secure data - placeholder
    async function secureExtractedData(pdfData, satData) {
    // This would normally generate a secure token
    return 'secured_token_' + Date.now();
    }
    }); // Password Toggle Visibility
    document.querySelectorAll('.password-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const passwordField = this.previousElementSibling;
            const svg = this.querySelector('svg');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                svg.innerHTML = `
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            `;
                svg.classList.remove('show-password');
                svg.classList.add('hide-password');
            } else {
                passwordField.type = 'password';
                svg.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            `;
                svg.classList.remove('hide-password');
                svg.classList.add('show-password');
            }
        });
    });
</script>
