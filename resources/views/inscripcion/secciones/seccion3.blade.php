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
        .form-group {
            position: relative;
            margin-bottom: 15px;
        }

        .form-control {
            border: 1px solid #ced4da;
            padding: 8px;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        .form-control.valid {
            border-color: #28a745 !important;
        }

        .form-control.invalid {
            border-color: #dc3545 !important;
        }

        .formulario__input-error {
            color: #dc3545;
            font-size: 0.85em;
            margin-top: 5px;
            display: none;
        }

        .form-group.invalid .formulario__input-error {
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

        /* Layout styles */
        .horizontal-group {
            display: flex;
            gap: 15px;
        }

        .half-width {
            flex: 1;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
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

            .horizontal-group {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <form action="{{ route('inscripcion.procesar', ['seccion' => 3]) }}" method="POST" id="formulario3">
        @csrf
        <div class="form-section" id="form-step-3">
            <input type="hidden" name="seccion" value="3">
            <h4><i class="fas fa-building"></i> Datos de Constitución (Persona Moral)</h4>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--numero_escritura">
                    <label class="form-label" for="numero_escritura">Número de Escritura</label>
                    <input type="text" id="numero_escritura" name="numero_escritura" class="form-control"
                        placeholder="Ej: 1234 o 1234/2024" maxlength="15" value="{{ $datosPrevios['numero_escritura'] ?? '' }}">
                    <p class="formulario__input-error">Debe contener de 1 a 10 dígitos, opcionalmente seguido de / y un año de 4 dígitos (ej: 1234, 1234/2024).</p>
                </div>
                <div class="half-width form-group" id="formulario__grupo--nombre_notario">
                    <label class="form-label" for="nombre_notario">Nombre del Notario</label>
                    <input type="text" id="nombre_notario" name="nombre_notario" class="form-control"
                        placeholder="Ej: Lic. Juan Pérez González" maxlength="100" value="{{ $datosPrevios['nombre_notario'] ?? '' }}">
                    <p class="formulario__input-error">El nombre del notario debe contener solo letras y espacios (máx. 100 caracteres).</p>
                </div>
            </div>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--entidad_federativa">
                    <label class="form-label" for="entidad_federativa">Entidad Federativa</label>
                    <select id="entidad_federativa" name="entidad_federativa" class="form-control" required>
                        <option value="">Seleccione un estado</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado['id'] }}" {{ isset($datosPrevios['entidad_federativa']) && $datosPrevios['entidad_federativa'] == $estado['id'] ? 'selected' : '' }}>
                                {{ $estado['nombre'] }}
                            </option>
                        @endforeach
                    </select>
                    <p class="formulario__input-error">Por favor, seleccione una entidad federativa.</p>
                </div>
                <div class="half-width form-group" id="formulario__grupo--fecha_constitucion">
                    <label class="form-label" for="fecha_constitucion">Fecha de Constitución</label>
                    <input type="date" id="fecha_constitucion" name="fecha_constitucion" class="form-control" required
                        value="{{ $datosPrevios['fecha_constitucion'] ?? '' }}">
                    <p class="formulario__input-error">Por favor, seleccione una fecha válida (no futura).</p>
                </div>
            </div>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--numero_notario">
                    <label class="form-label" for="numero_notario">Número de Notario</label>
                    <input type="text" id="numero_notario" name="numero_notario" class="form-control"
                        placeholder="Ej: 123" maxlength="10" value="{{ $datosPrevios['numero_notario'] ?? '' }}">
                    <p class="formulario__input-error">El número de notario debe contener solo números (máx. 10 dígitos).</p>
                </div>
                <div class="half-width"></div> <!-- Espacio vacío para mantener el diseño -->
            </div>
            <h4><i class="fas fa-file-contract"></i> Datos de Inscripción en el Registro Público</h4>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--numero_registro">
                    <label class="form-label" for="numero_registro">Número de Registro o Folio Mercantil</label>
                    <input type="text" id="numero_registro" name="numero_registro" class="form-control"
                        placeholder="Ej: 0123456789 o FME123456789" maxlength="14" value="{{ $datosPrevios['numero_registro'] ?? '' }}">
                    <p class="formulario__input-error">Debe contener de 9 a 14 caracteres: 0 a 3 letras iniciales seguidas de 6 a 14 dígitos (ej: 0123456789, FME123456789).</p>
                </div>
                <div class="half-width form-group" id="formulario__grupo--fecha_inscripcion">
                    <label class="form-label" for="fecha_inscripcion">Fecha de Inscripción</label>
                    <input type="date" id="fecha_inscripcion" name="fecha_inscripcion" class="form-control" required
                        value="{{ $datosPrevios['fecha_inscripcion'] ?? '' }}">
                    <p class="formulario__input-error">Por favor, seleccione una fecha válida (no anterior a la fecha de constitución).</p>
                </div>
            </div>
            <div id="form-errors" class="alert-danger" style="display: none;"></div>
        </div>
        <div class="form-buttons">
            <button type="button" class="btn btn-secondary" onclick="window.history.back();">Anterior</button>
            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Siguiente</button>
        </div>
    </form>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    $(document).ready(function() {
        // Validation rules for each field
        const validationRules = {
            numero_escritura: {
                pattern: /^\d{1,10}(\/\d{4})?$/,
                message: "Debe contener de 1 a 10 dígitos, opcionalmente seguido de / y un año de 4 dígitos (ej: 1234, 1234/2024).",
                required: false,
                label: "Número de Escritura"
            },
            nombre_notario: {
                pattern: /^[A-Za-z\s]{1,100}$/,
                message: "El nombre del notario debe contener solo letras y espacios (máx. 100 caracteres).",
                required: false,
                label: "Nombre del Notario"
            },
            entidad_federativa: {
                pattern: /.+/,
                message: "Por favor, seleccione una entidad federativa.",
                required: true,
                label: "Entidad Federativa"
            },
            fecha_constitucion: {
                pattern: /.+/,
                message: "Por favor, seleccione una fecha válida (no futura).",
                required: true,
                label: "Fecha de Constitución"
            },
            numero_notario: {
                pattern: /^\d{1,10}$/,
                message: "El número de notario debe contener solo números (máx. 10 dígitos).",
                required: false,
                label: "Número de Notario"
            },
            numero_registro: {
                pattern: /^(?:[A-Za-z]{0,3}\d{6,14}|\d{9,14})$/,
                message: "Debe contener de 9 a 14 caracteres: 0 a 3 letras iniciales seguidas de 6 a 14 dígitos (ej: 0123456789, FME123456789).",
                required: false,
                label: "Número de Registro o Folio Mercantil"
            },
            fecha_inscripcion: {
                pattern: /.+/,
                message: "Por favor, seleccione una fecha válida (no anterior a la fecha de constitución).",
                required: true,
                label: "Fecha de Inscripción"
            }
        };

        // Track which fields have been interacted with
        const interactedFields = new Set();

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

        // Function to validate a single field (returns validity without updating UI)
        function isFieldValid(field) {
            const fieldName = field.attr('id');
            if (!validationRules[fieldName]) {
                return true; // Skip inapplicable fields
            }

            const rules = validationRules[fieldName];
            const value = field.val()?.trim();

            if (rules.required && (!value || value === '')) {
                return false;
            }
            if (value && rules.pattern && !rules.pattern.test(value)) {
                return false;
            }
            if (!rules.required && value && rules.pattern && !rules.pattern.test(value)) {
                return false; // For optional fields
            }

            // Additional date validation
            if (['fecha_constitucion', 'fecha_inscripcion'].includes(fieldName)) {
                if (value) {
                    const date = new Date(value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    if (date > today) {
                        return false; // Date cannot be in the future
                    }
                    if (fieldName === 'fecha_inscripcion' && $('#fecha_constitucion').val()) {
                        const constitucionDate = new Date($('#fecha_constitucion').val());
                        if (date < constitucionDate) {
                            return false; // Inscription date cannot be before constitution date
                        }
                    }
                }
            }

            // Additional validation for numero_escritura year
            if (fieldName === 'numero_escritura' && value.includes('/')) {
                const year = parseInt(value.split('/')[1], 10);
                const currentYear = new Date().getFullYear();
                if (year < 1900 || year > currentYear) {
                    return false; // Year must be between 1900 and current year
                }
            }

            return true;
        }

        // Function to update field UI (borders and optional error message)
        function updateFieldUI(field, showError = false) {
            const fieldName = field.attr('id');
            if (!validationRules[fieldName]) {
                field.closest('.form-group').removeClass('valid invalid');
                field.removeClass('valid invalid');
                field.closest('.form-group').find('.formulario__input-error').hide();
                return true; // Skip inapplicable fields
            }

            const group = field.closest('.form-group');
            const rules = validationRules[fieldName];
            const isValid = isFieldValid(field);

            if (!interactedFields.has(fieldName) && !showError) {
                // If field hasn't been interacted with, don't show validation state
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
                    let errorMessage = rules.message;
                    if (fieldName === 'fecha_inscripcion' && $('#fecha_constitucion').val()) {
                        const inscriptionDate = new Date(field.val());
                        const constitucionDate = new Date($('#fecha_constitucion').val());
                        if (inscriptionDate < constitucionDate) {
                            errorMessage = "La fecha de inscripción no puede ser anterior a la fecha de constitución.";
                        }
                    } else if (fieldName === 'numero_escritura' && value.includes('/')) {
                        const year = parseInt(value.split('/')[1], 10);
                        const currentYear = new Date().getFullYear();
                        if (year < 1900 || year > currentYear) {
                            errorMessage = `El año debe estar entre 1900 y ${currentYear} (ej: 1234/2024).`;
                        }
                    }
                    group.find('.formulario__input-error').text(errorMessage).show();
                } else {
                    group.find('.formulario__input-error').hide();
                }
            }

            return isValid;
        }

        // Function to check if all validated fields are valid and update submit button
        function checkAllFields() {
            let allValid = true;
            const fields = [
                '#numero_escritura',
                '#nombre_notario',
                '#entidad_federativa',
                '#fecha_constitucion',
                '#numero_notario',
                '#numero_registro',
                '#fecha_inscripcion'
            ];

            fields.forEach(function(fieldSelector) {
                const field = $(fieldSelector);
                if (field.length && !field.is(':hidden')) {
                    if (!isFieldValid(field)) {
                        allValid = false;
                    }
                }
            });

            $('#submit-btn').prop('disabled', !allValid);
        }

        // Function to display form errors in an alert
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

        // Set max date for date inputs to today
        const today = new Date().toISOString().split('T')[0];
        $('#fecha_constitucion, #fecha_inscripcion').attr('max', today);

        // Set fecha_constitucion change listener to update min date for fecha_inscripcion
        $('#fecha_constitucion').on('change', function() {
            const fechaConstitucion = this.value;
            $('#fecha_inscripcion').attr('min', fechaConstitucion);
            if (interactedFields.has('fecha_inscripcion')) {
                updateFieldUI($('#fecha_inscripcion'), true);
            }
            checkAllFields();
        });

        // Real-time input filtering
        $('#numero_escritura').on('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            const value = $(this).val();
            const cursorPos = this.selectionStart;

            // Allow digits, and '/' only if followed by year digits and not already present
            if (!/[0-9]/.test(char)) {
                if (char === '/' && !value.includes('/') && cursorPos >= 1 && cursorPos <= 10) {
                    // Allow '/' if not already present and after 1-10 digits
                    return true;
                }
                e.preventDefault();
            }
        });

        $('#numero_notario').on('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            if (!/[0-9]/.test(char)) {
                e.preventDefault();
            }
        });

        $('#numero_registro').on('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            const value = $(this).val();
            const cursorPos = this.selectionStart;

            // Allow letters in the first 3 positions, digits afterward
            if (cursorPos < 3 && /[A-Za-z]/.test(char)) {
                return true;
            }
            if (/[0-9]/.test(char)) {
                return true;
            }
            e.preventDefault();
        });

        $('#nombre_notario').on('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            if (!/[A-Za-z\s]/.test(char)) {
                e.preventDefault();
            }
        });

        // Real-time validation on input/change
        $('#numero_escritura, #nombre_notario, #numero_notario, #numero_registro').on('input', function() {
            const fieldName = $(this).attr('id');
            interactedFields.add(fieldName); // Mark as interacted
            updateFieldUI($(this), true); // Show errors
            checkAllFields();
        });

        $('#entidad_federativa, #fecha_constitucion, #fecha_inscripcion').on('change', function() {
            const fieldName = $(this).attr('id');
            interactedFields.add(fieldName); // Mark as interacted
            updateFieldUI($(this), true); // Show errors
            checkAllFields();
        });

        // Delayed error message display for text inputs
        const showErrorMessage = debounce(function(field) {
            const fieldName = field.attr('id');
            interactedFields.add(fieldName); // Mark as interacted
            updateFieldUI(field, true); // Show error after pause
            checkAllFields();
        }, 500);

        $('#numero_escritura, #nombre_notario, #numero_notario, #numero_registro').on('input', function() {
            showErrorMessage($(this));
        });

        // Validate all fields on form submission
        $('#formulario3').on('submit', function(e) {
            const fields = [
                '#numero_escritura',
                '#nombre_notario',
                '#entidad_federativa',
                '#fecha_constitucion',
                '#numero_notario',
                '#numero_registro',
                '#fecha_inscripcion'
            ];

            let hasErrors = false;
            const errors = [];

            fields.forEach(function(fieldSelector) {
                const field = $(fieldSelector);
                if (field.length && !field.is(':hidden')) {
                    const fieldName = field.attr('id');
                    interactedFields.add(fieldName); // Mark as interacted
                    if (!updateFieldUI(field, true)) {
                        hasErrors = true;
                        let errorMessage = validationRules[fieldName].message;
                        if (fieldName === 'fecha_inscripcion' && $('#fecha_constitucion').val()) {
                            const inscriptionDate = new Date(field.val());
                            const constitucionDate = new Date($('#fecha_constitucion').val());
                            if (inscriptionDate < constitucionDate) {
                                errorMessage = "La fecha de inscripción no puede ser anterior a la fecha de constitución.";
                            }
                        } else if (fieldName === 'numero_escritura' && field.val().includes('/')) {
                            const year = parseInt(field.val().split('/')[1], 10);
                            const currentYear = new Date().getFullYear();
                            if (year < 1900 || year > currentYear) {
                                errorMessage = `El año debe estar entre 1900 y ${currentYear} (ej: 1234/2024).`;
                            }
                        }
                        errors.push({
                            label: validationRules[fieldName].label,
                            message: errorMessage
                        });
                    }
                }
            });

            displayFormErrors(errors);

            if (hasErrors) {
                e.preventDefault();
                checkAllFields();
            }
        });

        // Trigger initial min date for fecha_inscripcion if fecha_constitucion is pre-filled
        if ($('#fecha_constitucion').val()) {
            $('#fecha_inscripcion').attr('min', $('#fecha_constitucion').val());
        }

        // Initial check for submit button state (no UI validation)
        checkAllFields();
    });
    </script>
</body>
</html>