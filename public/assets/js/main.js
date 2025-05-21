/**
 * Main JavaScript file for the Padrón de Proveedores application
 * Handles form navigation, UI interactions and PDF processing
 */

document.addEventListener('DOMContentLoaded', () => {
    // Configure PDF.js to use proper worker
    if (window.pdfjsLib) {
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
    }
    
    // Initialize navigation buttons
    initializeNavigation();
    
    // Initialize form handlers
    initializeFormHandlers();
    
    // Initialize password toggles
    initializePasswordToggles();
    
    // Initialize modals
    initializeModals();
});

/**
 * Initialize form navigation buttons
 */
function initializeNavigation() {
    const step1 = document.getElementById('registerFormStep1');
    const step2 = document.getElementById('registerFormStep2');
    const nextBtn = document.getElementById('nextToStep2Btn');
    const backBtnStep2 = document.getElementById('backFromRegisterStep2Btn');
    const backBtnStep1 = document.getElementById('backFromRegisterStep1Btn');
    const viewExampleBtn = document.getElementById('viewExampleBtnStep1');
    
    // Handle back button on step 1
    backBtnStep1?.addEventListener('click', () => {
        window.history.back();
    });
    
    // Handle back button on step 2
    backBtnStep2?.addEventListener('click', () => {
        step2.classList.remove('active');
        step1.classList.add('active');
    });
    
    // Handle view example button
    viewExampleBtn?.addEventListener('click', () => {
        window.open('/assets/pdf/ejemplo_sat.pdf', '_blank');
    });
    
    // Handle file input changes
    const fileInput = document.getElementById('register-file');
    fileInput?.addEventListener('change', () => {
        const fileLabel = document.querySelector('.custom-file-upload span');
        if (fileLabel && fileInput.files.length > 0) {
            fileLabel.textContent = fileInput.files[0].name;
            document.querySelector('.custom-file-upload').classList.add('file-selected');
        } else if (fileLabel) {
            fileLabel.textContent = 'Subir archivo PDF';
            document.querySelector('.custom-file-upload').classList.remove('file-selected');
        }
    });
    
    // Handle next button click
    nextBtn?.addEventListener('click', handleNextStep);
}

/**
 * Initialize form handlers
 */
function initializeFormHandlers() {
    const registerForm = document.getElementById('registerForm');
    
    if (registerForm) {
        // Add hidden inputs for step 2 fields
        const createHiddenInput = (name) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            return input;
        };
        
        // Add email and password hidden fields
        registerForm.appendChild(createHiddenInput('email'));
        registerForm.appendChild(createHiddenInput('password'));
        
        // Handle register button click
        document.getElementById('registerBtn')?.addEventListener('click', () => {
            // Validate password matching
            const password = document.getElementById('password-input').value;
            const passwordConfirm = document.getElementById('password-confirm-input').value;
            const email = document.getElementById('email-input').value.trim();
            
            if (!validateRegistrationForm(email, password, passwordConfirm)) {
                return;
            }
            
            // Set form values and submit
            registerForm.querySelector('input[name="email"]').value = email;
            registerForm.querySelector('input[name="password"]').value = password;
            
            // Update form attributes and submit
            registerForm.action = '/register';
            registerForm.method = 'POST';
            registerForm.submit();
        });
    }
    
    // Add email validation on blur
    const emailInput = document.getElementById('email-input');
    emailInput?.addEventListener('blur', () => {
        if (emailInput.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.toLowerCase())) {
            showError('Por favor ingresa una dirección de correo válida.');
        }
    });
}

/**
 * Validate the registration form
 */
function validateRegistrationForm(email, password, passwordConfirm) {
    if (!password) {
        showError('Por favor, ingrese una contraseña.');
        return false;
    }
    
    if (password.length < 8) {
        showError('La contraseña debe tener al menos 8 caracteres.');
        return false;
    }

    if (password !== passwordConfirm) {
        showError('Las contraseñas no coinciden.');
        return false;
    }

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showError('Por favor, ingrese un correo electrónico válido.');
        return false;
    }
    
    const secureToken = document.getElementById('secure_data_token');
    if (!secureToken || !secureToken.value) {
        showError('Error de seguridad: Token de datos no encontrado. Por favor, reinicie el proceso de registro.');
        return false;
    }
    
    return true;
}

/**
 * Initialize password toggle buttons
 */
function initializePasswordToggles() {
    const passwordToggles = document.querySelectorAll('.password-toggle');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('.password-toggle-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                    <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                    <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                `;
            }
        });
    });
}

/**
 * Initialize modal handlers
 */
function initializeModals() {
    const successModal = document.getElementById('successModal');
    const errorModal = document.getElementById('errorModal');
    const closeButtons = document.querySelectorAll('.close-modal');
    
    // Close modal on X click
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            successModal.style.display = 'none';
            errorModal.style.display = 'none';
        });
    });
    
    // Close modal on outside click
    window.addEventListener('click', function(event) {
        if (event.target === successModal) {
            successModal.style.display = 'none';
        }
        if (event.target === errorModal) {
            errorModal.style.display = 'none';
        }
    });
    
    // Success modal button handler
    document.getElementById('successModalBtn')?.addEventListener('click', function() {
        successModal.style.display = 'none';
        if (window.redirectUrl) {
            window.location.href = window.redirectUrl;
        }
    });
    
    // Error modal button handler
    document.getElementById('errorModalBtn')?.addEventListener('click', function() {
        errorModal.style.display = 'none';
    });
}

/**
 * Handle next step button click
 */
async function handleNextStep() {
    const fileInput = document.getElementById('register-file');
    const file = fileInput?.files[0];
    const step1 = document.getElementById('registerFormStep1');
    const step2 = document.getElementById('registerFormStep2');
    
    if (!validatePdfFile(file)) {
        return;
    }
    
    const loading = createModal({ html: createSpinner() });
    const minimumDelay = 2000;
    const startTime = Date.now();
    
    try {
        const pdfData = await extractQRCodeFromPDF(file);
        const satData = await scrapeSATData(pdfData.qrUrl);
        
        const elapsedTime = Date.now() - startTime;
        const remainingTime = Math.max(0, minimumDelay - elapsedTime);
        
        setTimeout(() => {
            step1.classList.remove('active');
            step2.classList.add('active');
            updatePDFDataPreview(pdfData, satData);
            document.body.removeChild(loading);
        }, remainingTime);
    } catch (error) {
        const elapsedTime = Date.now() - startTime;
        const remainingTime = Math.max(0, minimumDelay - elapsedTime);
        
        setTimeout(() => {
            showError(error.message);
            document.body.removeChild(loading);
        }, remainingTime);
    }
}

/**
 * Validate PDF file before processing
 */
function validatePdfFile(file) {
    if (!file) {
        showError('Debe subir un archivo PDF de la Constancia del SAT para continuar.');
        return false;
    }

    if (file.type !== 'application/pdf') {
        showError('El archivo debe ser un PDF válido.');
        return false;
    }

    if (file.size > 5 * 1024 * 1024) {
        showError('El archivo excede el tamaño máximo de 5MB.');
        return false;
    }
    
    return true;
}

/**
 * Update PDF data preview and secure data
 */
function updatePDFDataPreview(pdfData, satData) {
    const isExpired = pdfData.estatus === 'Vencido';
    
    // Update document status UI
    const documentStatus = document.getElementById('document-status');
    const warningBadge = document.getElementById('warning-badge');
    const pdfDataCard = document.getElementById('pdf-data-card');
    
    if (documentStatus) documentStatus.textContent = `DOCUMENTO ${isExpired ? 'VENCIDO' : 'VÁLIDO'}`;
    if (warningBadge) warningBadge.style.display = isExpired ? 'inline-flex' : 'none';
    if (pdfDataCard) pdfDataCard.classList.toggle('expired', isExpired);

    // Pre-fill email if available
    const emailInput = document.getElementById('email-input');
    if (emailInput && satData.email) {
        emailInput.value = satData.email.toLowerCase();
    }

    // Set up SAT data view button
    const viewSatDataBtn = document.getElementById('viewSatDataBtn');
    if (viewSatDataBtn) {
        viewSatDataBtn.disabled = false;
        viewSatDataBtn.addEventListener('click', () => {
            const loading = document.getElementById('sat-data-loading');
            if (loading) loading.style.display = 'block';
            viewSatDataBtn.disabled = true;
            
            try {
                showSATDataModal(satData, pdfData.qrUrl);
            } catch (error) {
                showError(`Error al mostrar datos SAT: ${error.message}`);
            } finally {
                if (loading) loading.style.display = 'none';
                viewSatDataBtn.disabled = false;
            }
        });
    }
    
    // Secure extracted data on the server
    secureExtractedData(pdfData, satData)
        .then(token => {
            // Create or update hidden token field
            let tokenInput = document.getElementById('secure_data_token');
            if (!tokenInput) {
                tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.id = 'secure_data_token';
                tokenInput.name = 'secure_data_token';
                document.getElementById('registerForm').appendChild(tokenInput);
            }
            tokenInput.value = token;
        })
        .catch(error => showError(`Error al asegurar datos: ${error.message}`));
}

/**
 * Show error message in modal
 */
function showError(message) {
    const errorModal = document.getElementById('errorModal');
    const errorMessage = document.getElementById('errorMessage');
    
    if (errorMessage && errorModal) {
        errorMessage.textContent = message;
        errorModal.style.display = 'block';
    } else {
        alert(message);
    }
}