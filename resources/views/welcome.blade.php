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
        <span class="close-modal">×</span>
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
        <span class="close-modal">×</span>
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    const welcomeForm = document.getElementById('welcomeForm');
    const loginForm = document.getElementById('loginForm');
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    const setPasswordForm = document.getElementById('setPasswordForm');
    const errorModal = document.getElementById('errorModal');
    const errorMessage = document.getElementById('errorMessage');
    const successModal = document.getElementById('successModal');
    const successMessage = document.getElementById('successMessage');
    const goToRegisterBtn = document.getElementById('goToRegisterBtn');
    const goToLoginBtn = document.getElementById('goToLoginBtn');

    // Session variables from Laravel
    const showRegisterForm = {{ session('show_register_form') ? 'true' : 'false' }};
    const showRegisterStep1 = {{ session('show_register_step1') ? 'true' : 'false' }};
    const showErrorModal = {{ session('show_error_modal') ? 'true' : 'false' }};
    const showSuccessModal = {{ session('show_success_modal') ? 'true' : 'false' }};
    const errorText = "{{ session('error') }}";
    const successText = "{{ session('message') }}";
    const tempFileName = "{{ session('temp_sat_file_name') }}";
    const showPdfDataOnLoad = {{ session('pdf_data_error') ? 'true' : 'false' }};

    // Helper function to show a specific form and hide others
    const showForm = (formToShow) => {
        [welcomeForm, loginForm, forgotPasswordForm, registerForm, setPasswordForm].forEach(form => {
            if (form) form.classList.remove('active');
        });
        if (formToShow) formToShow.classList.add('active');
    };

    // Show the registration form if there’s an error or user clicked register
    if (showRegisterForm && showRegisterStep1) {
        showForm(registerForm);
        const pdfDataContainer = document.querySelector('.pdf-data-container');
        const fileLabel = document.querySelector('.custom-file-upload span');
        const fileUploadContainer = document.querySelector('.custom-file-upload');
        if (showPdfDataOnLoad && pdfDataContainer) {
            pdfDataContainer.style.display = 'block';
            if (tempFileName && fileLabel && fileUploadContainer) {
                fileLabel.textContent = tempFileName;
                fileUploadContainer.classList.add('file-selected');
            }
        }
    } else if (showSuccessModal) {
        showForm(welcomeForm);
    } else {
        showForm(welcomeForm);
    }

    // Show error modal if there’s an error
    if (showErrorModal && errorText && errorModal && errorMessage) {
        errorMessage.textContent = errorText;
        errorModal.style.display = 'block';
    }

    // Show success modal if registration was successful
    if (showSuccessModal && successText && successModal && successMessage) {
        successMessage.textContent = successText;
        successModal.style.display = 'block';
    }

    // Highlight fields with validation errors
    const highlightErrorFields = () => {
        const emailError = document.querySelector('label.error-message[for="email-input"]');
        const passwordError = document.querySelector('label.error-message[for="password-input"]');
        const passwordConfirmError = document.querySelector('label.error-message[for="password-confirm-input"]');

        if (emailError) {
            const emailInput = document.getElementById('email-input');
            if (emailInput) {
                emailInput.classList.add('input-error');
                emailInput.addEventListener('focus', () => {
                    emailInput.classList.remove('input-error');
                }, { once: true });
            }
        }

        if (passwordError) {
            const passwordInput = document.getElementById('password-input');
            if (passwordInput) {
                passwordInput.classList.add('input-error');
                passwordInput.addEventListener('focus', () => {
                    passwordInput.classList.remove('input-error');
                }, { once: true });
            }
        }

        if (passwordConfirmError) {
            const passwordConfirmInput = document.getElementById('password-confirm-input');
            if (passwordConfirmInput) {
                passwordConfirmInput.classList.add('input-error');
                passwordConfirmInput.addEventListener('focus', () => {
                    passwordConfirmInput.classList.remove('input-error');
                }, { once: true });
            }
        }

        // Focus on the first error field
        const errorFields = document.querySelectorAll('input.input-error');
        if (errorFields.length > 0) {
            errorFields[0].focus();
        }
    };

    // Apply error highlighting if validation errors exist
    highlightErrorFields();

    // Button to show registration form
    if (goToRegisterBtn) {
        goToRegisterBtn.addEventListener('click', () => {
            showForm(registerForm);
        });
    }

    // Button to show login form
    if (goToLoginBtn) {
        goToLoginBtn.addEventListener('click', () => {
            showForm(loginForm);
        });
    }

    // Close modals
    const closeModalButtons = document.querySelectorAll('.close-modal, #errorModalBtn, #successModalBtn');
    closeModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (errorModal) errorModal.style.display = 'none';
            if (successModal) successModal.style.display = 'none';
            if (showRegisterForm && showRegisterStep1) {
                showForm(registerForm);
            } else {
                showForm(welcomeForm);
            }
        });
    });

    // File input handling (assuming auth.registrarse has similar structure to previous form)
    const fileInput = document.getElementById('register-file');
    const pdfDataContainer = document.querySelector('.pdf-data-container');
    const fileLabel = document.querySelector('.custom-file-upload span');
    const fileUploadContainer = document.querySelector('.custom-file-upload');
    const viewExampleBtn = document.getElementById('viewExampleBtnStep1');

    if (fileInput) {
        fileInput.addEventListener('change', async () => {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                if (file.type !== 'application/pdf') {
                    showError('El archivo debe ser un PDF válido.');
                    fileInput.value = '';
                    if (fileLabel) fileLabel.textContent = 'Subir archivo PDF';
                    if (fileUploadContainer) fileUploadContainer.classList.remove('file-selected');
                    if (pdfDataContainer) pdfDataContainer.style.display = 'none';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    showError('El archivo excede el tamaño máximo de 5MB.');
                    fileInput.value = '';
                    if (fileLabel) fileLabel.textContent = 'Subir archivo PDF';
                    if (fileUploadContainer) fileUploadContainer.classList.remove('file-selected');
                    if (pdfDataContainer) pdfDataContainer.style.display = 'none';
                    return;
                }

                if (fileLabel) fileLabel.textContent = file.name;
                const loading = createModal({ html: createSpinner() });
                const minimumDelay = 2000;
                const startTime = Date.now();

                try {
                    const pdfData = await extractQRCodeFromPDF(file);
                    const satData = await scrapeSATData(pdfData.qrUrl);

                    const token = await secureExtractedData(pdfData, satData);
                    let tokenInput = document.getElementById('secure_data_token');
                    if (!tokenInput) {
                        tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.id = 'secure_data_token';
                        tokenInput.name = 'secure_data_token';
                        registerForm.appendChild(tokenInput);
                    }
                    tokenInput.value = token;

                    const elapsedTime = Date.now() - startTime;
                    const remainingTime = Math.max(0, minimumDelay - elapsedTime);

                    setTimeout(() => {
                        if (fileUploadContainer) fileUploadContainer.classList.add('file-selected');
                        updatePDFDataPreview(pdfData, satData);
                        if (pdfDataContainer) pdfDataContainer.style.display = 'block';
                        document.body.removeChild(loading);
                    }, remainingTime);
                } catch (error) {
                    const elapsedTime = Date.now() - startTime;
                    const remainingTime = Math.max(0, minimumDelay - elapsedTime);

                    setTimeout(() => {
                        showError(error.message);
                        fileInput.value = '';
                        if (fileLabel) fileLabel.textContent = 'Subir archivo PDF';
                        if (fileUploadContainer) fileUploadContainer.classList.remove('file-selected');
                        if (pdfDataContainer) pdfDataContainer.style.display = 'none';
                        document.body.removeChild(loading);
                    }, remainingTime);
                }
            } else {
                if (fileLabel) fileLabel.textContent = 'Subir archivo PDF';
                if (fileUploadContainer) fileUploadContainer.classList.remove('file-selected');
                if (pdfDataContainer) pdfDataContainer.style.display = 'none';
            }
        });
    }

    // Password visibility toggles
    document.querySelectorAll('.password-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.closest('.password-input-container').querySelector('input');
            if (input.type === 'password') {
                input.type = 'text';
                this.querySelector('.password-toggle-icon').innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M1 1L23 23" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                `;
            } else {
                input.type = 'password';
                this.querySelector('.password-toggle-icon').innerHTML = `
                    <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                `;
            }
        });
    });

    // View example PDF
    if (viewExampleBtn) {
        viewExampleBtn.addEventListener('click', () => {
            window.open('/assets/pdf/ejemplo_sat.pdf', '_blank');
        });
    }

    // Client-side email validation
    const emailInput = document.getElementById('email-input');
    if (emailInput) {
        emailInput.addEventListener('blur', () => {
            if (emailInput.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.toLowerCase())) {
                let errorLabel = document.querySelector('.email-section .error-message');
                if (!errorLabel) {
                    errorLabel = document.createElement('label');
                    errorLabel.className = 'error-message';
                    errorLabel.setAttribute('for', 'email-input');
                    errorLabel.style.color = '#F44336';
                    errorLabel.style.fontSize = '0.9rem';
                    errorLabel.style.marginTop = '5px';
                    errorLabel.style.display = 'block';
                    errorLabel.textContent = 'Por favor ingresa una dirección de correo válida.';
                    emailInput.parentNode.appendChild(errorLabel);
                }
                emailInput.classList.add('input-error');
            } else {
                const errorLabel = document.querySelector('.email-section .error-message');
                if (errorLabel && errorLabel.textContent === 'Por favor ingresa una dirección de correo válida.') {
                    errorLabel.remove();
                }
                emailInput.classList.remove('input-error');
            }
        });
    }

    // Client-side password confirmation validation
    const passwordInput = document.getElementById('password-input');
    const passwordConfirmInput = document.getElementById('password-confirm-input');
    if (passwordInput && passwordConfirmInput) {
        passwordConfirmInput.addEventListener('blur', () => {
            if (passwordConfirmInput.value && passwordInput.value !== passwordConfirmInput.value) {
                let errorLabel = document.querySelector('.password-confirm-section .error-message');
                if (!errorLabel) {
                    errorLabel = document.createElement('label');
                    errorLabel.className = 'error-message';
                    errorLabel.setAttribute('for', 'password-confirm-input');
                    errorLabel.style.color = '#F44336';
                    errorLabel.style.fontSize = '0.9rem';
                    errorLabel.style.marginTop = '5px';
                    errorLabel.style.display = 'block';
                    errorLabel.textContent = 'Las contraseñas no coinciden.';
                    passwordConfirmInput.parentNode.parentNode.appendChild(errorLabel);
                }
                passwordConfirmInput.classList.add('input-error');
            } else {
                const errorLabel = document.querySelector('.password-confirm-section .error-message');
                if (errorLabel && errorLabel.textContent === 'Las contraseñas no coinciden.') {
                    errorLabel.remove();
                }
                passwordConfirmInput.classList.remove('input-error');
            }
        });
    }
});

// Assuming updatePDFDataPreview is defined elsewhere, included for completeness
function updatePDFDataPreview(pdfData, satData) {
    const isExpired = pdfData.estatus === 'Vencido';
    
    const documentStatus = document.getElementById('document-status');
    const warningBadge = document.getElementById('warning-badge');
    const pdfDataCard = document.getElementById('pdf-data-card');
    
    if (documentStatus) documentStatus.textContent = `DOCUMENTO ${isExpired ? 'VENCIDO' : 'VÁLIDO'}`;
    if (warningBadge) warningBadge.style.display = isExpired ? 'inline-flex' : 'none';
    if (pdfDataCard) pdfDataCard.classList.toggle('expired', isExpired);

    const emailInput = document.getElementById('email-input');
    if (emailInput && satData.email) {
        emailInput.value = satData.email.toLowerCase();
    }

    const viewSatDataBtn = document.getElementById('viewSatDataBtn');
    if (viewSatDataBtn) {
        viewSatDataBtn.disabled = !satData || satData.extractedData.length === 0;
        viewSatDataBtn.addEventListener('click', async () => {
            const loading = document.getElementById('sat-data-loading');
            if (loading) loading.style.display = 'block';
            viewSatDataBtn.disabled = true;
            try {
                showSATDataModal(satData, pdfData.qrUrl);
            } catch (error) {
                showError(`Error al mostrar datos SAT: ${error.message}`);
            } finally {
                if (loading) loading.style.display = 'none';
                viewSatDataBtn.disabled = !satData || satData.extractedData.length === 0;
            }
        }, { once: true });
    }
}

// Placeholder for showError function (should be defined elsewhere)
function showError(message) {
    const errorModal = document.getElementById('errorModal');
    const errorMessage = document.getElementById('errorMessage');
    if (errorModal && errorMessage) {
        errorMessage.textContent = message;
        errorModal.style.display = 'block';
    }
}

// Placeholder for createModal and createSpinner (should be defined elsewhere)
function createModal({ html }) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.display = 'block';
    modal.innerHTML = html;
    document.body.appendChild(modal);
    return modal;
}

function createSpinner() {
    return '<div class="spinner"></div>';
}
</script>
@endsection