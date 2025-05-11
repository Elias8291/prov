document.addEventListener('DOMContentLoaded', function() {
    const formsContainer = document.querySelector('.forms-container');
    const welcomeForm = document.getElementById('welcomeForm');
    const loginForm = document.getElementById('loginForm');
    const forgotForm = document.getElementById('forgotForm');
    const registerFormStep1 = document.getElementById('registerFormStep1');
    const registerFormStep2 = document.getElementById('registerFormStep2');
    const passwordForm = document.getElementById('passwordForm');

    const navButtons = {
        goToLoginBtn: 'loginForm',
        goToRegisterBtn: 'registerFormStep1',
        backFromLoginBtn: 'welcomeForm',
        goToRegisterFromLoginBtn: 'registerFormStep1',
        forgotPasswordBtn: 'forgotForm',
        backFromForgotBtn: 'loginForm',
        backFromRegisterStep1Btn: 'welcomeForm',
        backFromRegisterStep2Btn: 'registerFormStep1',
        nextToStep2Btn: 'registerFormStep2',
        backFromPasswordFormBtn: 'loginForm'
    };

    function cleanFileUpload() {
        const fileInput = document.getElementById('register-file');
        const customUploader = document.querySelector('.custom-file-upload');
        if (fileInput) fileInput.value = '';
        if (customUploader) {
            customUploader.classList.remove('has-file');
            const span = customUploader.querySelector('span');
            if (span) span.textContent = 'Subir archivo PDF';
        }
    }

    function resetForm(formElement) {
        if (!formElement) return;
        if (formElement.id === 'loginForm' && hasLoginErrors()) {
            const errorInputs = formElement.querySelectorAll('.error, .is-invalid');
            errorInputs.forEach(input => input.classList.remove('error', 'is-invalid'));
            return;
        }
        const inputs = formElement.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.type === 'checkbox' || input.type === 'radio') input.checked = false;
            else if (input.type !== 'hidden') input.value = '';
        });
        const errorMessages = formElement.querySelectorAll('.error-message, .alert-danger, .invalid-feedback');
        errorMessages.forEach(error => error.remove());
        const errorInputs = formElement.querySelectorAll('.error, .is-invalid');
        errorInputs.forEach(input => input.classList.remove('error', 'is-invalid'));
        if (formElement.id === 'registerFormStep1' || formElement.id === 'registerFormStep2') {
            ['pdf-name', 'pdf-rfc', 'pdf-date', 'pdf-regimen', 'pdf-qr-url', 'nombre', 'tipo-persona', 'rfc', 'cp', 'direccion'].forEach(id => {
                const element = document.getElementById(id);
                if (element) element.textContent = '';
            });
            const emailInput = document.getElementById('email-input');
            if (emailInput) emailInput.value = '';
            const warningBadge = document.getElementById('warning-badge');
            if (warningBadge) warningBadge.style.display = 'none';
            const documentStatus = document.getElementById('document-status');
            if (documentStatus) documentStatus.textContent = 'DOCUMENTO';
            cleanFileUpload();
        }
        if (formElement.id === 'passwordForm') {
            const passwordInputs = formElement.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => input.value = '');
            const sessionErrors = formElement.querySelectorAll('.alert.alert-danger');
            sessionErrors.forEach(error => error.remove());
        }
        if (formElement.id === 'loginForm') {
            const loginInputs = formElement.querySelectorAll('input[type="email"], input[type="password"]');
            loginInputs.forEach(input => input.value = '');
        }
        if (formElement.id === 'forgotForm') {
            const emailInput = formElement.querySelector('input[type="email"]');
            if (emailInput) emailInput.value = '';
        }
    }

    function resetAllForms() {
        [welcomeForm, loginForm, forgotForm, registerFormStep1, registerFormStep2, passwordForm].forEach(form => {
            if (form) resetForm(form);
        });
    }

    function hasLoginErrors() {
        const errorElements = loginForm.querySelectorAll('.error-message, .alert-danger');
        const hasErrorInput = document.getElementById('has-login-errors');
        return errorElements.length > 0 || (hasErrorInput && hasErrorInput.value === 'true');
    }

    function hasPasswordResetToken() {
        return new URLSearchParams(window.location.search).has('token');
    }

    function initializeForms() {
        [welcomeForm, loginForm, forgotForm, registerFormStep1, registerFormStep2, passwordForm].forEach(form => {
            if (form) form.classList.remove('active', 'slide-in');
        });
        if (hasLoginErrors()) {
            [welcomeForm, forgotForm, registerFormStep1, registerFormStep2, passwordForm].forEach(form => {
                if (form) resetForm(form);
            });
            activateForm(loginForm);
        } else {
            resetAllForms();
            activateForm(hasPasswordResetToken() ? passwordForm : welcomeForm);
        }
    }

    function activateForm(formToShow, isBackNavigation = false) {
        if (!formToShow) return;
    
        // Get all forms in the forms-container
        const allForms = document.querySelectorAll('.form-page');
        const currentActive = document.querySelector('.form-page.active');
        const formsContainer = document.querySelector('.forms-container');
    
        // Remove active and transition classes from all forms
        allForms.forEach(form => {
            form.classList.remove('active', 'slide-in', 'slide-out', 'fade-in', 'flip-in');
        });
    
        // Reset the current form if navigating back
        if (currentActive && isBackNavigation) {
            resetForm(currentActive);
            if (formToShow.id === 'welcomeForm') resetAllForms();
        }
    
        // Activate the target form
        setTimeout(() => {
            formToShow.classList.add('active', 'slide-in');
            if (formsContainer) formsContainer.style.height = `${formToShow.scrollHeight}px`;
    
            // Update container class for PDF preview (if applicable)
            const container = document.querySelector('.container');
            if (formToShow.id === 'registerFormStep2') {
                container.classList.add('show-pdf-preview');
            } else {
                container.classList.remove('show-pdf-preview');
            }
    
            // Remove transition class after animation
            setTimeout(() => formToShow.classList.remove('slide-in'), 350);
        }, 10);
    }

    function setupNavigation() {
        Object.keys(navButtons).forEach(buttonId => {
            const button = document.getElementById(buttonId);
            if (button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (buttonId === 'nextToStep2Btn') {
                        const fileInput = document.getElementById('register-file');
                        if (!fileInput || fileInput.files.length === 0) return;
                        const event = new CustomEvent('processPDF', { detail: fileInput.files[0] });
                        document.dispatchEvent(event);
                    }
                    const targetForm = document.getElementById(navButtons[buttonId]);
                    const isBackButton = buttonId.includes('backFrom') || buttonId.includes('goToRegisterFromLogin');
                    activateForm(targetForm, isBackButton);
                    history.pushState({ form: buttonId }, '', window.location.pathname);
                });
            }
        });
    }

    function setupFileInput() {
        const fileInput = document.getElementById('register-file');
        const customFileUpload = document.querySelector('.custom-file-upload');
        if (fileInput && customFileUpload) {
            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    customFileUpload.classList.add('has-file');
                    customFileUpload.querySelector('span').textContent = fileInput.files[0].name;
                } else cleanFileUpload();
            });
        }
    }

    function setupPasswordToggle() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('login-password');
        const eyeIcon = togglePassword?.querySelector('.eye-icon');
        const eyeSlashIcon = togglePassword?.querySelector('.eye-slash-icon');
        if (togglePassword && passwordInput && eyeIcon && eyeSlashIcon) {
            togglePassword.addEventListener('click', function() {
                const isPasswordVisible = passwordInput.type === 'text';
                passwordInput.type = isPasswordVisible ? 'password' : 'text';
                eyeIcon.style.display = isPasswordVisible ? 'block' : 'none';
                eyeSlashIcon.style.display = isPasswordVisible ? 'none' : 'block';
            });
        }
    }

    setupPasswordToggle();
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.form) {
            const targetForm = document.getElementById(navButtons[e.state.form]);
            activateForm(targetForm, true);
        } else if (hasPasswordResetToken()) {
            activateForm(passwordForm, true);
        } else {
            activateForm(welcomeForm, true);
        }
    });

    initializeForms();
    setupNavigation();
    setupFileInput();

    window.addEventListener('resize', function() {
        const activeForm = document.querySelector('.form-page.active');
        if (activeForm && formsContainer) formsContainer.style.height = `${activeForm.scrollHeight}px`;
    });
});