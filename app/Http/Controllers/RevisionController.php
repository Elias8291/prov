<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Muestra errores y mensajes --}}
@if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
    @csrf
    <div class="form-page register-form active" id="registerFormStep1">
        <img src="{{ asset('assets/imagenes/logoAdminsitracion.png') }}" alt="Logo" class="logo-img">
        <h1>Regístrate</h1>
        <p>Registro en el <span class="system-name">Padrón de Proveedores de Oaxaca</span></p>
        <div class="input-group">
            <div class="file-input-header">
                <label for="register-file">Constancia del SAT (PDF)*</label>
                <button type="button" class="small-btn outline" id="viewExampleBtnStep1">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 10V12.6667C14 13.0203 13.8595 13.3594 13.6095 13.6095C13.3594 13.8595 13.0203 14 12.6667 14H3.33333C2.97971 14 2.64057 13.8595 2.39052 13.6095C2.14048 13.3594 2 13.0203 2 12.6667V3.33333C2 2.97971 2.14048 2.64057 2.39052 2.39052C2.64057 2.14048 2.97971 2 3.33333 2H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M10 2H14V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M9.3335 6.66667L14.0002 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Ver ejemplo
                </button>
            </div>
            <label class="custom-file-upload" for="register-file">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M14 2V8H20" stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M16 13H8" stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M16 17H8" stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M10 9H9H8" stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Subir archivo PDF</span>
                <small>Tamaño máximo: 5MB</small>
            </label>
            <input type="file" id="register-file" name="sat_file" accept="application/pdf" required>
        </div>
        <div class="pdf-data-container" style="display: none;">
            <div class="success-card" id="pdf-data-card">
                <div class="success-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.7649 14.1003 1.98232 16.07 2.85999" stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M22 4L12 14.01L9 11.01" stroke="#9F1F4F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <h3 id="document-status">DOCUMENTO</h3>
                        <div class="warning-badge" id="warning-badge" style="display: none;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 8V12M12 16H12.01M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            VENCIDO
                        </div>
                    </div>
                    <div class="sat-actions">
                        <button class="small-btn" id="viewSatDataBtn" type="button" disabled>VER MÁS DATOS</button>
                        <div id="sat-data-loading" style="display: none;">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </div>
                <div class="info-message">
                    <p>Por favor, ingrese su correo electrónico y establezca una contraseña.</p>
                </div>
                <div class="email-section">
                    <p class="name-display"><strong>Correo Electrónico:</strong></p>
                    <input type="email" name="email" id="email-input" class="email-input" placeholder="INGRESE CORREO" value="{{ old('email') }}" required>
                    @error('email')
                        <label class="error-message" style="color: #F44336; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</label>
                    @enderror
                </div>
                <div class="password-section">
                    <p class="name-display"><strong>Contraseña:</strong></p>
                    <div class="password-input-container" style="position: relative;">
                        <input type="password" name="password" id="password-input" class="email-input" placeholder="INGRESE CONTRASEÑA" required>
                        <button type="button" class="password-toggle" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="password-toggle-icon">
                                <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <label class="error-message" style="color: #F44336; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</label>
                    @enderror
                </div>
                <div class="password-confirm-section">
                    <p class="name-display"><strong>Confirmar Contraseña:</strong></p>
                    <div class="password-input-container" style="position: relative;">
                        <input type="password" name="password_confirmation" id="password-confirm-input" class="email-input" placeholder="CONFIRME CONTRASEÑA" required>
                        <button type="button" class="password-toggle" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="password-toggle-icon">
                                <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <label class="error-message" style="color: #F44336; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</label>
                    @enderror
                </div>
                <input type="hidden" name="secure_data_token" id="secure_data_token" value="{{ old('secure_data_token') }}">
            </div>
              <button type="submit" class="btn" id="registerBtn">Registrarse</button>
        </div>
       
    </div>
</form>

<script>
 document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('register-file');
    const pdfDataContainer = document.querySelector('.pdf-data-container');
    const viewExampleBtn = document.getElementById('viewExampleBtnStep1');
    const fileLabel = document.querySelector('.custom-file-upload span');
    const fileUploadContainer = document.querySelector('.custom-file-upload');
    
    // Check if we need to show the PDF data container automatically due to previous errors
    const showPdfDataOnLoad = {{ session('pdf_data_error') ? 'true' : 'false' }};
    const tempFileName = "{{ session('temp_sat_file_name') }}";
    
    // Helper function to highlight fields with errors
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
    };
    
    if (showPdfDataOnLoad) {
        // When there was an error, automatically show the PDF data section
        pdfDataContainer.style.display = 'block';
        
        // If we have a filename from the session, update the label
        if (tempFileName) {
            fileLabel.textContent = tempFileName;
            fileUploadContainer.classList.add('file-selected');
        }
        
        // Enable the view data button if appropriate
        const viewSatDataBtn = document.getElementById('viewSatDataBtn');
        if (viewSatDataBtn) {
            viewSatDataBtn.disabled = false;
            viewSatDataBtn.addEventListener('click', () => {
                showError('Los datos no están disponibles. Por favor, vuelva a subir el archivo si desea ver los detalles.');
            });
        }
        
        // Highlight fields with errors
        highlightErrorFields();
        
        // Focus on the first field with error
        const errorFields = document.querySelectorAll('input.input-error');
        if (errorFields.length > 0) {
            errorFields[0].focus();
        }
    }

    fileInput?.addEventListener('change', async () => {
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            if (file.type !== 'application/pdf') {
                showError('El archivo debe ser un PDF válido.');
                fileInput.value = '';
                fileLabel.textContent = 'Subir archivo PDF';
                fileUploadContainer.classList.remove('file-selected');
                pdfDataContainer.style.display = 'none';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showError('El archivo excede el tamaño máximo de 5MB.');
                fileInput.value = '';
                fileLabel.textContent = 'Subir archivo PDF';
                fileUploadContainer.classList.remove('file-selected');
                pdfDataContainer.style.display = 'none';
                return;
            }

            fileLabel.textContent = file.name;
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
                    document.getElementById('registerForm').appendChild(tokenInput);
                }
                tokenInput.value = token;

                const elapsedTime = Date.now() - startTime;
                const remainingTime = Math.max(0, minimumDelay - elapsedTime);

                setTimeout(() => {
                    fileUploadContainer.classList.add('file-selected'); 
                    updatePDFDataPreview(pdfData, satData);
                    pdfDataContainer.style.display = 'block';
                    document.body.removeChild(loading);
                }, remainingTime);
            } catch (error) {
                const elapsedTime = Date.now() - startTime;
                const remainingTime = Math.max(0, minimumDelay - elapsedTime);

                setTimeout(() => {
                    showError(error.message);
                    fileInput.value = '';
                    fileLabel.textContent = 'Subir archivo PDF';
                    fileUploadContainer.classList.remove('file-selected');
                    pdfDataContainer.style.display = 'none';
                    document.body.removeChild(loading);
                }, remainingTime);
            }
        } else {
            fileLabel.textContent = 'Subir archivo PDF';
            fileUploadContainer.classList.remove('file-selected');
            pdfDataContainer.style.display = 'none';
        }
    });

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

    viewExampleBtn?.addEventListener('click', () => {
        window.open('/assets/pdf/ejemplo_sat.pdf', '_blank');
    });
    
    // Add custom validation for email and password fields
    const emailInput = document.getElementById('email-input');
    if (emailInput) {
        emailInput.addEventListener('blur', () => {
            if (emailInput.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.toLowerCase())) {
                const errorLabel = document.querySelector('.email-section .error-message');
                if (!errorLabel) {
                    const newErrorLabel = document.createElement('label');
                    newErrorLabel.className = 'error-message';
                    newErrorLabel.setAttribute('for', 'email-input');
                    newErrorLabel.style.color = '#F44336';
                    newErrorLabel.style.fontSize = '0.9rem';
                    newErrorLabel.style.marginTop = '5px';
                    newErrorLabel.style.display = 'block';
                    newErrorLabel.textContent = 'Por favor ingresa una dirección de correo válida.';
                    emailInput.parentNode.appendChild(newErrorLabel);
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
    
    const passwordInput = document.getElementById('password-input');
    const passwordConfirmInput = document.getElementById('password-confirm-input');
    if (passwordInput && passwordConfirmInput) {
        passwordConfirmInput.addEventListener('blur', () => {
            if (passwordConfirmInput.value && passwordInput.value !== passwordConfirmInput.value) {
                const errorLabel = document.querySelector('.password-confirm-section .error-message');
                if (!errorLabel) {
                    const newErrorLabel = document.createElement('label');
                    newErrorLabel.className = 'error-message';
                    newErrorLabel.setAttribute('for', 'password-confirm-input');
                    newErrorLabel.style.color = '#F44336';
                    newErrorLabel.style.fontSize = '0.9rem';
                    newErrorLabel.style.marginTop = '5px';
                    newErrorLabel.style.display = 'block';
                    newErrorLabel.textContent = 'Las contraseñas no coinciden.';
                    passwordConfirmInput.parentNode.parentNode.appendChild(newErrorLabel);
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

// Modified updatePDFDataPreview
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
        // Enable button only if satData has valid data
        viewSatDataBtn.disabled = !satData || satData.extractedData.length === 0;
        viewSatDataBtn.addEventListener('click', async () => {
            const loading = document.getElementById('sat-data-loading');
            if (loading) loading.style.display = 'block';
            viewSatDataBtn.disabled = true;
            try {
                console.log('Intentando mostrar modal con satData:', satData); // Debugging
                showSATDataModal(satData, pdfData.qrUrl);
            } catch (error) {
                console.error('Error al mostrar modal:', error);
                showError(`Error al mostrar datos SAT: ${error.message}`);
            } finally {
                if (loading) loading.style.display = 'none';
                viewSatDataBtn.disabled = !satData || satData.extractedData.length === 0;
            }
        }, { once: true }); // Prevent multiple listeners
    }
}
</script>
