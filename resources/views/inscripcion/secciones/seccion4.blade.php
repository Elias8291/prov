<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Main Button Styling */
        .form-buttons {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 500;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            min-width: 120px;
        }

        .btn-primary {
            background-color: #9d2449;
            color: white;
        }

        .btn-primary:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            box-shadow: 0 2px 4px rgba(108, 117, 125, 0.2);
        }

        .btn-secondary:hover {
            background-color: #5c636a;
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
            transform: translateY(-2px);
        }

        /* Form validation styles */
        .form-group-shareholder {
            position: relative;
            margin-bottom: 15px;
        }

        .form-control-shareholder {
            border: 1px solid #ced4da;
            padding: 8px;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        .form-control-shareholder.valid {
            border-color: #28a745 !important;
        }

        .form-control-shareholder.invalid {
            border-color: #dc3545 !important;
        }

        .formulario__input-error {
            color: #dc3545;
            font-size: 0.85em;
            margin-top: 5px;
            display: none;
        }

        .form-group-shareholder.invalid .formulario__input-error {
            display: block;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        /* Shareholder card styles */
        .shareholder-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .shareholder-card.expanded {
            border-color: #9d2449;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .shareholder-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .shareholder-number {
            background: #9d2449;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .shareholder-title {
            flex: 1;
            font-weight: 500;
        }

        .shareholder-percentage {
            font-size: 14px;
            padding: 2px 8px;
            border-radius: 12px;
            background: #e9ecef;
            color: #495057;
        }

        .btn-delete-shareholder {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-add-shareholder {
            background: #9d2449;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
        }

        .percentage-input-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .percentage-suffix {
            position: absolute;
            right: 10px;
            color: #6c757d;
        }

        /* Progress bar styles */
        .progress-bar-container {
            background: #e9ecef;
            border-radius: 5px;
            height: 8px;
            width: 100%;
            overflow: hidden;
            margin-bottom: 5px;
        }

        .progress-bar {
            background: #9d2449;
            height: 100%;
            width: 0;
            transition: width 0.3s ease;
        }

        .progress-bar.complete {
            background: #28a745;
        }

        .progress-bar.danger {
            background: #dc3545;
        }

        /* Notification styles */
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 16px;
            border-radius: 5px;
            background-color: white;
            color: #333;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            opacity: 1;
            transition: opacity 0.3s;
            max-width: 300px;
        }

        .notification.error {
            border-left: 4px solid #dc3545;
        }

        /* Responsive styles */
        @media (max-width: 576px) {
            .form-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .btn {
                width: 100%;
            }
        }

        /* CSS Variables for consistency */
        :root {
            --primary-color: #9d2449;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --primary-light: #f8d7da;
            --secondary-light: #e9ecef;
            --accent-light: #d1ecf1;
            --accent-color: #0c5460;
            --secondary-color: #6c757d;
        }
    </style>
</head>
<body>
    <form id="formulario4" action="{{ route('inscripcion.procesar', ['seccion' => 4]) }}" method="POST">
        @csrf
        <div class="form-section" id="form-step-4">
            <div class="form-container">
                <div class="form-column">
                    <div class="form-header">
                        <h4><i class="fas fa-users"></i> Socios o Accionistas (Persona Moral)</h4>
                        <p class="subtitle">Agrega los socios o accionistas de la empresa</p>
                        <div class="percentage-summary">
                            <div class="progress-bar-container">
                                <div class="progress-bar" id="percentage-bar"></div>
                            </div>
                            <span id="percentage-text">0% asignado</span>
                        </div>
                    </div>

                    <div class="shareholders-container" id="shareholders-container">
                        <!-- Tarjetas de accionistas se agregan dinámicamente -->
                    </div>

                    <button type="button" id="add-shareholder" class="btn-add-shareholder">
                        <i class="fas fa-plus-circle"></i> Agregar Socio/Accionista
                    </button>
                </div>
            </div>
            <div id="form-errors" class="alert-danger" style="display: none;"></div>
        </div>
        <!-- Campo oculto para almacenar los datos de accionistas como JSON -->
        <input type="hidden" name="accionistas" id="accionistas-data">
        <div class="form-buttons">
            <button type="button" class="btn btn-secondary" onclick="window.history.back();">Anterior</button>
            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Siguiente</button>
        </div>
    </form>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    $(document).ready(function() {
        const container = $('#shareholders-container');
        const addBtn = $('#add-shareholder');
        const percentageBar = $('#percentage-bar');
        const percentageText = $('#percentage-text');
        const form = $('#formulario4');
        const accionistasDataInput = $('#accionistas-data');

        let shareholderCount = 0;
        let activeCard = null;
        let shareholdersArray = [];
        const interactedFields = new Set();

        // Validation rules for shareholder fields
        const validationRules = {
            name: {
                pattern: /^[A-Za-z\s]{1,50}$/,
                message: "El nombre debe contener solo letras y espacios (máx. 50 caracteres).",
                required: true,
                label: "Nombre(s)"
            },
            lastname1: {
                pattern: /^[A-Za-z\s]{1,50}$/,
                message: "El apellido paterno debe contener solo letras y espacios (máx. 50 caracteres).",
                required: true,
                label: "Apellido Paterno"
            },
            lastname2: {
                pattern: /^[A-Za-z\s]{0,50}$/,
                message: "El apellido materno debe contener solo letras y espacios (máx. 50 caracteres).",
                required: false,
                label: "Apellido Materno"
            },
            percentage: {
                pattern: /^(?!0(\.0{1,2})?$)\d{1,3}(\.\d{1,2})?$/,
                message: "El porcentaje debe ser un número entre 0.01 y 100 (máx. 2 decimales).",
                required: true,
                label: "Porcentaje de Acciones"
            }
        };

        // Debounce function for delayed actions
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Function to validate a single field
        function isFieldValid(field) {
            const fieldType = field.attr('id').split('-')[0];
            const rules = validationRules[fieldType];
            if (!rules) return true;

            const value = field.val()?.trim();

            if (rules.required && (!value || value === '')) {
                return false;
            }
            if (value && rules.pattern && !rules.pattern.test(value)) {
                return false;
            }
            if (!rules.required && value && rules.pattern && !rules.pattern.test(value)) {
                return false;
            }

            if (fieldType === 'percentage') {
                const numValue = parseFloat(value);
                if (numValue <= 0 || numValue > 100) {
                    return false;
                }
            }

            return true;
        }

        // Function to update field UI
        function updateFieldUI(field, showError = false) {
            const fieldId = field.attr('id');
            const fieldType = fieldId.split('-')[0];
            const rules = validationRules[fieldType];
            if (!rules) return true;

            const group = field.closest('.form-group-shareholder');
            const isValid = isFieldValid(field);

            // Only update UI if the field has been interacted with
            if (!interactedFields.has(fieldId)) {
                group.removeClass('valid invalid');
                field.removeClass('valid invalid');
                group.find('.formulario__input-error').hide();
                return isValid;
            }

            group.removeClass('valid invalid');
            field.removeClass('valid invalid');

            if (isValid) {
                group.addClass('valid');
                field.addClass('valid');
                group.find('.formulario__input-error').hide();
            } else {
                group.addClass('invalid');
                field.addClass('invalid');
                if (showError) {
                    group.find('.formulario__input-error').text(rules.message).show();
                } else {
                    group.find('.formulario__input-error').hide();
                }
            }

            return isValid;
        }

        // Function to check all fields and update submit button
        function checkAllFields() {
            let allValid = true;
            const totalPercentage = updatePercentageSummary();

            if (Math.abs(totalPercentage - 100) > 0.1) {
                allValid = false;
            }

            container.find('.form-control-shareholder').each(function() {
                if (!isFieldValid($(this))) {
                    allValid = false;
                }
            });

            $('#submit-btn').prop('disabled', !allValid);
        }

        // Function to display form errors
        function displayFormErrors(errors) {
            const errorContainer = $('#form-errors');
            errorContainer.empty().hide();

            if (errors.length > 0) {
                let errorHtml = '<strong>Error en el formulario:</strong><ul>';
                errors.forEach(function(error) {
                    errorHtml += `<li><strong>${error.label}</strong>: ${error.message}</li>`;
                });
                errorHtml += '</ul>';
                errorContainer.html(errorHtml).show();
            }
        }

        // Function to show notifications
        function showNotification(message, type = 'error') {
            const notification = $('<div>').addClass(`notification ${type}`).text(message);
            $('body').append(notification);
            setTimeout(() => {
                notification.css('opacity', '0');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Function to add a new shareholder
        function addShareholder() {
            shareholderCount++;
            const card = $('<div>').addClass('shareholder-card expanded').attr('data-id', shareholderCount);
            
            card.html(`
                <div class="shareholder-header">
                    <div class="shareholder-number">${shareholderCount}</div>
                    <div class="shareholder-title">Socio ${shareholderCount}</div>
                    <button type="button" class="btn-delete-shareholder" title="Eliminar accionista">
                        <i class="fas fa-times"></i>
                    </button>
                    <span class="shareholder-percentage">0%</span>
                </div>
                <div class="shareholder-fields">
                    <div class="form-group-shareholder">
                        <label for="name-${shareholderCount}">Nombre(s)*</label>
                        <input type="text" id="name-${shareholderCount}" class="form-control-shareholder" placeholder="Ej: Juan Carlos" required>
                        <p class="formulario__input-error"></p>
                    </div>
                    <div class="form-group-shareholder">
                        <label for="lastname1-${shareholderCount}">Apellido Paterno*</label>
                        <input type="text" id="lastname1-${shareholderCount}" class="form-control-shareholder" placeholder="Ej: González" required>
                        <p class="formulario__input-error"></p>
                    </div>
                    <div class="form-group-shareholder">
                        <label for="lastname2-${shareholderCount}">Apellido Materno</label>
                        <input type="text" id="lastname2-${shareholderCount}" class="form-control-shareholder" placeholder="Ej: López">
                        <p class="formulario__input-error"></p>
                    </div>
                    <div class="form-group-shareholder">
                        <label for="percentage-${shareholderCount}">Porcentaje de Acciones*</label>
                        <div class="percentage-input-container">
                            <input type="number" id="percentage-${shareholderCount}" 
                                   class="form-control-shareholder percentage-input" 
                                   placeholder="Ej: 50" min="0.01" max="100" step="0.01" required>
                            <span class="percentage-suffix">%</span>
                        </div>
                        <p class="formulario__input-error"></p>
                    </div>
                </div>
            `);

            container.append(card);
            activeCard = card;

            shareholdersArray.push({
                id: shareholderCount,
                nombre: '',
                apellido_paterno: '',
                apellido_materno: '',
                porcentaje: 0
            });

            updateHiddenField();
            updatePercentageSummary();
            checkAllFields();

            card.find('input').first().focus();
        }

        // Function to delete a shareholder
        function deleteShareholder(card) {
            if (container.children().length > 1) {
                card.css({ opacity: '0', transform: 'scale(0.9)' });
                setTimeout(() => {
                    const id = parseInt(card.data('id'));
                    shareholdersArray = shareholdersArray.filter(sh => sh.id !== id);
                    card.remove();
                    updateShareholderNumbers();
                    updatePercentageSummary();
                    updateHiddenField();
                    checkAllFields();
                }, 300);
            } else {
                showNotification('Debe haber al menos un socio/accionista registrado.', 'error');
            }
        }

        // Function to update shareholder numbers
        function updateShareholderNumbers() {
            container.find('.shareholder-card').each(function(index) {
                const number = index + 1;
                const card = $(this);
                card.find('.shareholder-number').text(number);
                const id = parseInt(card.data('id'));
                const shareholder = shareholdersArray.find(sh => sh.id === id);
                if (shareholder) {
                    shareholder.id = number;
                    card.data('id', number);
                }
                updateCardTitle(card);
            });
            shareholderCount = container.find('.shareholder-card').length;
            updateHiddenField();
        }

        // Function to update card title
        function updateCardTitle(card) {
            const id = parseInt(card.data('id'));
            const nameInput = card.find('input[id^="name-"]');
            const lastname1Input = card.find('input[id^="lastname1-"]');
            const lastname2Input = card.find('input[id^="lastname2-"]');
            const percentageInput = card.find('.percentage-input');

            const name = nameInput.val()?.trim() || '';
            const lastname1 = lastname1Input.val()?.trim() || '';
            const lastname2 = lastname2Input.val()?.trim() || '';
            const percentage = parseFloat(percentageInput.val() || 0).toFixed(2);

            const titleElement = card.find('.shareholder-title');
            const percentageElement = card.find('.shareholder-percentage');

            let fullname = [name, lastname1, lastname2].filter(Boolean).join(' ');
            titleElement.text(fullname || `Socio ${card.find('.shareholder-number').text()}`);

            percentageElement.text(`${percentage}%`);
            percentageElement.css({
                backgroundColor: percentage > 50 ? 'var(--primary-light)' :
                                percentage > 20 ? 'var(--accent-light)' : 'var(--secondary-light)',
                color: percentage > 50 ? 'var(--primary-color)' :
                       percentage > 20 ? 'var(--accent-color)' : 'var(--secondary-color)'
            });

            const shareholder = shareholdersArray.find(sh => sh.id === id);
            if (shareholder) {
                shareholder.nombre = name;
                shareholder.apellido_paterno = lastname1;
                shareholder.apellido_materno = lastname2;
                shareholder.porcentaje = parseFloat(percentage) || 0;
                updateHiddenField();
            }
        }

        // Function to update hidden field
        function updateHiddenField() {
            accionistasDataInput.val(JSON.stringify(shareholdersArray));
        }

        // Function to update percentage summary
        function updatePercentageSummary() {
            const inputs = container.find('.percentage-input');
            let total = 0;

            inputs.each(function() {
                total += parseFloat($(this).val()) || 0;
            });

            const percentage = Math.min(total, 100);
            percentageBar.css('width', `${percentage}%`);
            percentageBar.removeClass('complete danger');

            if (total > 100) {
                percentageText.text(`⚠️ ${total.toFixed(2)}% (Excede el 100%)`).css('color', 'var(--danger-color)');
                percentageBar.addClass('danger');
            } else if (Math.abs(total - 100) < 0.1) {
                percentageText.text(`✓ ${total.toFixed(2)}% (Completo)`).css('color', 'var(--success-color)');
                percentageBar.addClass('complete');
            } else {
                percentageText.text(`${total.toFixed(2)}% asignado`).css('color', 'var(--primary-color)');
            }

            return total;
        }

        // Function to collapse other cards
        function collapseOtherCards(activeCardId) {
            container.find('.shareholder-card').each(function() {
                if ($(this).data('id') != activeCardId) {
                    $(this).removeClass('expanded');
                }
            });
        }

        // Input filtering
        container.on('keypress', 'input[id^="name-"], input[id^="lastname1-"], input[id^="lastname2-"]', function(e) {
            const char = String.fromCharCode(e.which);
            if (!/[A-Za-z\s]/.test(char)) {
                e.preventDefault();
            }
        });

        container.on('keypress', 'input[id^="percentage-"]', function(e) {
            const char = String.fromCharCode(e.which);
            const value = $(this).val();
            if (!/[0-9.]/.test(char) || (char === '.' && value.includes('.'))) {
                e.preventDefault();
            }
        });

        // Real-time validation
        container.on('input', 'input[id^="name-"], input[id^="lastname1-"], input[id^="lastname2-"]', function() {
            const field = $(this);
            const fieldId = field.attr('id');
            interactedFields.add(fieldId);
            updateFieldUI(field, true);
            updateCardTitle(field.closest('.shareholder-card'));
            checkAllFields();
        });

        container.on('input', 'input[id^="percentage-"]', function() {
            const field = $(this);
            const fieldId = field.attr('id');
            interactedFields.add(fieldId);
            const value = parseFloat(field.val());
            if (value > 100) field.val(100);
            updateFieldUI(field, true);
            updatePercentageSummary();
            updateCardTitle(field.closest('.shareholder-card'));
            checkAllFields();
        });

        const showErrorMessage = debounce(function(field) {
            const fieldId = field.attr('id');
            if (interactedFields.has(fieldId)) {
                updateFieldUI(field, true);
            }
            checkAllFields();
        }, 500);

        container.on('input', 'input.form-control-shareholder', function() {
            showErrorMessage($(this));
        });

        // Event listeners
        addBtn.on('click', function() {
            collapseOtherCards(null);
            addShareholder();
        });

        container.on('click', '.btn-delete-shareholder', function() {
            const card = $(this).closest('.shareholder-card');
            deleteShareholder(card);
        });

        container.on('click', '.shareholder-card', function(e) {
            const card = $(this);
            if (!$(e.target).closest('.btn-delete-shareholder').length && !card.hasClass('expanded')) {
                collapseOtherCards(card.data('id'));
                card.addClass('expanded');
                activeCard = card;
            }
        });

        container.on('focusout', 'input', function(e) {
            const card = $(this).closest('.shareholder-card');
            setTimeout(() => {
                if (!card[0].contains(document.activeElement) && !$(e.target).closest('.btn-delete-shareholder').length) {
                    card.removeClass('expanded');
                }
            }, 100);
        });

        // Form submission
        form.on('submit', function(e) {
            e.preventDefault();

            const totalPercentage = updatePercentageSummary();
            let hasErrors = false;
            const errors = [];

            if (Math.abs(totalPercentage - 100) > 0.1) {
                errors.push({
                    label: "Porcentaje Total",
                    message: "El porcentaje total debe ser exactamente 100%."
                });
                hasErrors = true;
            }

            container.find('.shareholder-card').each(function(index) {
                const card = $(this);
                const id = parseInt(card.data('id'));
                const shareholder = shareholdersArray.find(sh => sh.id === id);
                const fields = [
                    { selector: `input[id="name-${id}"]`, type: 'name' },
                    { selector: `input[id="lastname1-${id}"]`, type: 'lastname1' },
                    { selector: `input[id="lastname2-${id}"]`, type: 'lastname2' },
                    { selector: `input[id="percentage-${id}"]`, type: 'percentage' }
                ];

                fields.forEach(field => {
                    const input = card.find(field.selector);
                    const fieldId = input.attr('id');
                    const isValid = isFieldValid(input);
                    const hasValue = input.val()?.trim();

                    // Only show UI errors for interacted fields or fields with invalid non-empty content
                    if (interactedFields.has(fieldId) || (hasValue && !isValid)) {
                        if (!updateFieldUI(input, true)) {
                            hasErrors = true;
                            errors.push({
                                label: `Socio ${index + 1} - ${validationRules[field.type].label}`,
                                message: validationRules[field.type].message
                            });
                        }
                    } else if (!isValid && validationRules[field.type].required) {
                        // For untouched required fields, add error without UI change
                        hasErrors = true;
                        errors.push({
                            label: `Socio ${index + 1} - ${validationRules[field.type].label}`,
                            message: validationRules[field.type].message
                        });
                    }
                });

                if (!shareholder.nombre || !shareholder.apellido_paterno || shareholder.porcentaje <= 0) {
                    hasErrors = true;
                    errors.push({
                        label: `Socio ${index + 1}`,
                        message: "Debe tener nombre, apellido paterno y un porcentaje mayor a 0."
                    });
                }
            });

            displayFormErrors(errors);

            if (hasErrors) {
                showNotification('Por favor, corrija los errores en el formulario.', 'error');
                checkAllFields();
                return;
            }

            updateHiddenField();
            form[0].submit();
        });

        // Initialize with one shareholdera
        addShareholder();
    });
    </script>
</body>
</html>