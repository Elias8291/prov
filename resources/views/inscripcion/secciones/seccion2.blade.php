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

        .data-field {
            display: block;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
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
    <form id="formulario2" action="{{ route('inscripcion.procesar_seccion') }}" method="POST">
        @csrf
        <input type="hidden" name="action" value="next"> <!-- To differentiate Siguiente vs Anterior -->
        <div class="form-section" id="form-step-2">
            <h4><i class="fas fa-map-marker-alt"></i> Domicilio</h4>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--codigo_postal">
                    <label class="form-label data-label">Código Postal</label>
                    @if (Auth::user()->hasRole('solicitante') && isset($direccion) && $direccion->codigo_postal)
                        <span class="data-field" id="codigo_postal_display">{{ str_pad($direccion->codigo_postal, 5, '0', STR_PAD_LEFT) }}</span>
                        <input type="hidden" id="codigo_postal" name="codigo_postal" value="{{ str_pad($direccion->codigo_postal, 5, '0', STR_PAD_LEFT) }}">
                    @else
                        <input type="text" id="codigo_postal" name="codigo_postal" class="form-control" 
                               placeholder="Ej: 12345" required pattern="[0-9]{4,5}" maxlength="5" 
                               value="{{ old('codigo_postal', str_pad($datosPrevios['codigo_postal'] ?? '', 5, '0', STR_PAD_LEFT)) }}">
                        <p class="formulario__input-error">El código postal debe contener 4 o 5 dígitos numéricos.</p>
                    @endif
                </div>
                <div class="half-width form-group" id="formulario__grupo--estado">
                    <label class="form-label data-label">Estado</label>
                    @if (Auth::user()->hasRole('solicitante') && isset($datosPrevios['estado']))
                        <span class="data-field" id="estado_display">{{ $datosPrevios['estado'] }}</span>
                        <input type="hidden" id="estado" name="estado" value="{{ $datosPrevios['estado'] }}">
                    @else
                        <input type="text" id="estado" name="estado" class="form-control" 
                               placeholder="Ej: Jalisco" readonly required 
                               value="{{ old('estado', $datosPrevios['estado'] ?? '') }}">
                        <p class="formulario__input-error">El estado debe contener solo letras y espacios, máximo 100 caracteres.</p>
                    @endif
                </div>
            </div>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--municipio">
                    <label class="form-label data-label">Municipio</label>
                    @if (Auth::user()->hasRole('solicitante') && isset($datosPrevios['municipio']))
                        <span class="data-field" id="municipio_display">{{ $datosPrevios['municipio'] }}</span>
                        <input type="hidden" id="municipio" name="municipio" value="{{ $datosPrevios['municipio'] }}">
                    @else
                        <input type="text" id="municipio" name="municipio" class="form-control" 
                               placeholder="Ej: Guadalajara" readonly required 
                               value="{{ old('municipio', $datosPrevios['municipio'] ?? '') }}">
                        <p class="formulario__input-error">El municipio debe contener solo letras y espacios, máximo 100 caracteres.</p>
                    @endif
                </div>
                <div class="half-width form-group" id="formulario__grupo--colonia">
                    <label class="form-label" for="colonia">Asentamiento</label>
                    <select id="colonia" name="colonia" class="form-control" required>
                        <option value="">Seleccione un Asentamiento</option>
                        @if (isset($datosPrevios['colonia']))
                            <option value="{{ $datosPrevios['colonia'] }}" selected>{{ $datosPrevios['colonia'] }}</option>
                        @endif
                    </select>
                    <p class="formulario__input-error">Debe seleccionar un asentamiento.</p>
                </div>
            </div>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--calle">
                    <label class="form-label" for="calle">Calle</label>
                    <input type="text" id="calle" name="calle" class="form-control" 
                           placeholder="Ej: Av. Principal" required maxlength="100" 
                           value="{{ old('calle', $datosPrevios['calle'] ?? '') }}">
                    <p class="formulario__input-error">La calle debe contener letras, números o espacios, máximo 100 caracteres.</p>
                </div>
                <div class="half-width form-group" id="formulario__grupo--numero_exterior">
                    <label class="form-label" for="numero_exterior">Número Exterior</label>
                    <input type="text" id="numero_exterior" name="numero_exterior" class="form-control" 
                           placeholder="Ej: 123 o S/N" required maxlength="10" pattern="[A-Za-z0-9\/]+" 
                           value="{{ old('numero_exterior', $datosPrevios['numero_exterior'] ?? '') }}">
                    <p class="formulario__input-error">El número exterior debe contener letras, números o /, entre 1 y 10 caracteres.</p>
                </div>
            </div>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--numero_interior">
                    <label class="form-label" for="numero_interior">Número Interior (Opcional)</label>
                    <input type="text" id="numero_interior" name="numero_interior" class="form-control" 
                           placeholder="Ej: 5A" maxlength="10" pattern="[A-Za-z0-9]+" 
                           value="{{ old('numero_interior', $datosPrevios['numero_interior'] ?? '') }}">
                    <p class="formulario__input-error">El número interior debe contener letras o números, máximo 10 caracteres.</p>
                </div>
                <div class="half-width form-group" id="formulario__grupo--entre_calle_1">
                    <label class="form-label" for="entre_calle_1">Entre Calle 1</label>
                    <input type="text" id="entre_calle_1" name="entre_calle_1" class="form-control" 
                           placeholder="Ej: Calle Independencia" required maxlength="100" pattern="[A-Za-z0-9\s]+" 
                           value="{{ old('entre_calle_1', $datosPrevios['entre_calle_1'] ?? '') }}">
                    <p class="formulario__input-error">Entre calle 1 debe contener letras, números o espacios, máximo 100 caracteres.</p>
                </div>
            </div>
            <div class="form-group" id="formulario__grupo--entre_calle_2">
                <label class="form-label" for="entre_calle_2">Entre Calle 2</label>
                <input type="text" id="entre_calle_2" name="entre_calle_2" class="form-control" 
                       placeholder="Ej: Calle Morelos" required maxlength="100" pattern="[A-Za-z0-9\s]+" 
                       value="{{ old('entre_calle_2', $datosPrevios['entre_calle_2'] ?? '') }}">
                <p class="formulario__input-error">Entre calle 2 debe contener letras, números o espacios, máximo 100 caracteres.</p>
            </div>
        </div>
        <div class="form-buttons">
            <button type="button" class="btn btn-secondary" onclick="goToPreviousSection()">Anterior</button>
            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Siguiente</button>
        </div>
    </form>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
  $(document).ready(function() {
    // Validation rules for each field
    const validationRules = {
        codigo_postal: {
            pattern: /^\d{4,5}$/,
            message: "El código postal debe contener 4 o 5 dígitos numéricos.",
            required: true,
            label: "Código Postal"
        },
        estado: {
            pattern: /^[A-Za-z\s]{1,100}$/,
            message: "El estado debe contener solo letras y espacios, máximo 100 caracteres.",
            required: true,
            label: "Estado"
        },
        municipio: {
            pattern: /^[A-Za-z\s]{1,100}$/,
            message: "El municipio debe contener solo letras y espacios, máximo 100 caracteres.",
            required: true,
            label: "Municipio"
        },
        colonia: {
            pattern: /.+/,
            message: "Debe seleccionar un asentamiento.",
            required: true,
            label: "Asentamiento"
        },
        calle: {
            pattern: /^[A-Za-z0-9\s]{1,100}$/,
            message: "La calle debe contener letras, números o espacios, máximo 100 caracteres.",
            required: true,
            label: "Calle"
        },
        numero_exterior: {
            pattern: /^[A-Za-z0-9\/]{1,10}$/,
            message: "El número exterior debe contener letras, números o /, entre 1 y 10 caracteres.",
            required: true,
            label: "Número Exterior"
        },
        numero_interior: {
            pattern: /^[A-Za-z0-9]{1,10}$/,
            message: "El número interior debe contener letras o números, máximo 10 caracteres.",
            required: false,
            label: "Número Interior"
        },
        entre_calle_1: {
            pattern: /^[A-Za-z0-9\s]{1,100}$/,
            message: "Entre calle 1 debe contener letras, números o espacios, máximo 100 caracteres.",
            required: true,
            label: "Entre Calle 1"
        },
        entre_calle_2: {
            pattern: /^[A-Za-z0-9\s]{1,100}$/,
            message: "Entre calle 2 debe contener letras, números o espacios, máximo 100 caracteres.",
            required: true,
            label: "Entre Calle 2"
        }
    };

    // Fields exempt from validation (green if filled, no errors)
    const exemptFields = ['estado'];

    // Fields always green, no validation
    const alwaysGreenFields = ['calle', 'municipio'];

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

    // Function to check if a field is applicable based on role and visibility
    function isFieldApplicable(fieldName) {
        const isSolicitante = {{ auth()->check() && auth()->user()->hasRole('solicitante') ? 'true' : 'false' }};
        if (isSolicitante && ['codigo_postal', 'estado', 'municipio'].includes(fieldName)) {
            return false; // Display-only for solicitante
        }
        return true;
    }

    // Function to validate a single field (returns validity without updating UI)
    function isFieldValid(field) {
        const fieldName = field.attr('id');
        if (!validationRules[fieldName] || !isFieldApplicable(fieldName)) {
            return true; // Skip inapplicable fields
        }

        const rules = validationRules[fieldName];
        const value = field.val()?.trim();

        if (alwaysGreenFields.includes(fieldName)) {
            return true; // Always green fields are always valid
        }

        if (exemptFields.includes(fieldName)) {
            return !rules.required || (value && (!rules.pattern || rules.pattern.test(value)));
        }

        if (rules.required && (!value || value === '')) {
            return false;
        }
        if (value && rules.pattern && !rules.pattern.test(value)) {
            return false;
        }
        if (!rules.required && value && rules.pattern && !rules.pattern.test(value)) {
            return false; // For optional fields like numero_interior
        }
        return true;
    }

    // Function to update field UI (borders and optional error message)
    function updateFieldUI(field, showError = false) {
        const fieldName = field.attr('id');
        if (!validationRules[fieldName] || !isFieldApplicable(fieldName)) {
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

        if (alwaysGreenFields.includes(fieldName)) {
            group.addClass('valid');
            field.addClass('valid');
            group.find('.formulario__input-error').hide();
        } else if (exemptFields.includes(fieldName)) {
            const value = field.val()?.trim();
            const isExemptValid = !rules.required || (value && (!rules.pattern || rules.pattern.test(value)));
            group.addClass(isExemptValid ? 'valid' : 'invalid');
            field.addClass(isExemptValid ? 'valid' : 'invalid');
            group.find('.formulario__input-error').hide();
        } else if (isValid) {
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

    // Function to check if all validated fields are valid and update submit button
    function checkAllFields() {
        let allValid = true;
        const fields = [
            '#codigo_postal',
            '#estado',
            '#municipio',
            '#colonia',
            '#calle',
            '#numero_exterior',
            '#numero_interior',
            '#entre_calle_1',
            '#entre_calle_2'
        ];

        fields.forEach(function(fieldSelector) {
            const field = $(fieldSelector);
            if (field.length && !field.is(':hidden') && isFieldApplicable(field.attr('id'))) {
                if (!isFieldValid(field)) {
                    allValid = false;
                }
            }
        });

        $('#submit-btn').prop('disabled', !allValid);
    }

    // Function to handle "Anterior" button click
    window.goToPreviousSection = function() {
        $('#formulario2 input[name="action"]').val('previous');
        $('#formulario2').submit();
    };

    // Real-time input filtering
    $('#codigo_postal').on('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/[0-9]/.test(char)) {
            e.preventDefault();
        }
    });

    $('#numero_exterior').on('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/[A-Za-z0-9\/]/.test(char)) {
            e.preventDefault();
        }
    });

    $('#numero_interior').on('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/[A-Za-z0-9]/.test(char)) {
            e.preventDefault();
        }
    });

    $('#calle, #entre_calle_1, #entre_calle_2').on('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/[A-Za-z0-9\s]/.test(char)) {
            e.preventDefault();
        }
    });

    // Real-time validation on input/change
    $('#codigo_postal, #calle, #numero_exterior, #numero_interior, #entre_calle_1, #entre_calle_2').on('input', function() {
        const fieldName = $(this).attr('id');
        interactedFields.add(fieldName); // Mark as interacted
        updateFieldUI($(this), true); // Show errors
        checkAllFields();
    });

    $('#colonia').on('change', function() {
        const fieldName = $(this).attr('id');
        interactedFields.add(fieldName); // Mark as interacted
        updateFieldUI($(this), true); // Show errors
        checkAllFields();
    });

    // Delayed error message display
    const showErrorMessage = debounce(function(field) {
        const fieldName = field.attr('id');
        if (!alwaysGreenFields.includes(fieldName) && !exemptFields.includes(fieldName)) {
            interactedFields.add(fieldName); // Mark as interacted
            updateFieldUI(field, true); // Show error after pause
        }
        checkAllFields();
    }, 500);

    $('#calle, #numero_exterior, #numero_interior, #entre_calle_1, #entre_calle_2').on('input', function() {
        showErrorMessage($(this));
    });

    // Handle postal code AJAX validation with debounce
    const validatePostalCode = debounce(function(codigoPostal, input) {
        const fieldName = input.attr('id');
        interactedFields.add(fieldName); // Mark as interacted

        if (codigoPostal.length >= 4 && codigoPostal.match(/^\d{4,5}$/)) {
            console.log('Sending AJAX request for postal code:', codigoPostal); // Depuración
            $.ajax({
                url: '{{ route("inscripcion.obtener-datos-direccion") }}', // Corregido: coma añadida
                method: 'POST',
                data: {
                    codigo_postal: codigoPostal,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('AJAX response:', response); // Depuración
                    if (response.success) {
                        $('#estado').val(response.estado);
                        $('#estado_display').text(response.estado);
                        $('#municipio').val(response.municipio);
                        $('#municipio_display').text(response.municipio);
                        $('#colonia').empty().append('<option value="">Seleccione un Asentamiento</option>');
                        $.each(response.asentamientos, function(index, asentamiento) {
                            $('#colonia').append(
                                $('<option>', {
                                    value: asentamiento.nombre,
                                    text: asentamiento.nombre
                                })
                            );
                        });

                        // Update UI only if fields have been interacted with
                        if (interactedFields.has('estado')) {
                            updateFieldUI($('#estado'), true);
                        } else {
                            updateFieldUI($('#estado'), false);
                        }
                        if (interactedFields.has('municipio')) {
                            updateFieldUI($('#municipio'), true);
                        } else {
                            updateFieldUI($('#municipio'), false);
                        }
                        if (interactedFields.has('colonia')) {
                            updateFieldUI($('#colonia'), true);
                        } else {
                            updateFieldUI($('#colonia'), false);
                        }
                        updateFieldUI(input, true);
                    } else {
                        updateFieldUI(input, true);
                        $('#estado').val('');
                        $('#estado_display').text('');
                        $('#municipio').val('');
                        $('#municipio_display').text('');
                        $('#colonia').empty().append('<option value="">Seleccione un Asentamiento</option>');
                        if (interactedFields.has('estado')) {
                            updateFieldUI($('#estado'), true);
                        } else {
                            updateFieldUI($('#estado'), false);
                        }
                        if (interactedFields.has('municipio')) {
                            updateFieldUI($('#municipio'), true);
                        } else {
                            updateFieldUI($('#municipio'), false);
                        }
                        if (interactedFields.has('colonia')) {
                            updateFieldUI($('#colonia'), true);
                        } else {
                            updateFieldUI($('#colonia'), false);
                        }
                    }
                    checkAllFields();
                },
                error: function(xhr) {
                    console.error('Error fetching address data:', xhr); // Depuración
                    updateFieldUI(input, true);
                    $('#estado').val('');
                    $('#estado_display').text('');
                    $('#municipio').val('');
                    $('#municipio_display').text('');
                    $('#colonia').empty().append('<option value="">Seleccione un Asentamiento</option>');
                    if (interactedFields.has('estado')) {
                        updateFieldUI($('#estado'), true);
                    } else {
                        updateFieldUI($('#estado'), false);
                    }
                    if (interactedFields.has('municipio')) {
                        updateFieldUI($('#municipio'), true);
                    } else {
                        updateFieldUI($('#municipio'), false);
                    }
                    if (interactedFields.has('colonia')) {
                        updateFieldUI($('#colonia'), true);
                    } else {
                        updateFieldUI($('#colonia'), false);
                    }
                    checkAllFields();
                }
            });
        } else {
            updateFieldUI(input, true);
            $('#estado').val('');
            $('#estado_display').text('');
            $('#municipio').val('');
            $('#municipio_display').text('');
            $('#colonia').empty().append('<option value="">Seleccione un Asentamiento</option>');
            if (interactedFields.has('estado')) {
                updateFieldUI($('#estado'), true);
            } else {
                updateFieldUI($('#estado'), false);
            }
            if (interactedFields.has('municipio')) {
                updateFieldUI($('#municipio'), true);
            } else {
                updateFieldUI($('#municipio'), false);
            }
            if (interactedFields.has('colonia')) {
                updateFieldUI($('#colonia'), true);
            } else {
                updateFieldUI($('#colonia'), false);
            }
            checkAllFields();
        }
    }, 500);

    $('#codigo_postal').on('input', function() {
        const fieldName = $(this).attr('id');
        interactedFields.add(fieldName); // Mark as interacted
        let value = $(this).val();
        value = value.replace(/[^0-9]/g, '');
        $(this).val(value);
        console.log('Validating postal code:', value); // Depuración
        validatePostalCode($(this).val(), $(this));
    });

    // Validate all fields on form submission
    $('#formulario2').on('submit', function(e) {
        const fields = [
            '#codigo_postal',
            '#estado',
            '#municipio',
            '#colonia',
            '#calle',
            '#numero_exterior',
            '#numero_interior',
            '#entre_calle_1',
            '#entre_calle_2'
        ];

        let hasErrors = false;
        fields.forEach(function(fieldSelector) {
            const field = $(fieldSelector);
            if (field.length && !field.is(':hidden') && isFieldApplicable(field.attr('id'))) {
                const fieldName = field.attr('id');
                interactedFields.add(fieldName); // Mark as interacted
                if (!updateFieldUI(field, true)) {
                    hasErrors = true;
                }
            }
        });

        if (hasErrors) {
            e.preventDefault();
            checkAllFields();
        }
    });

    // Trigger postal code validation for pre-filled value without UI update
    if ($('#codigo_postal').val()) {
        const codigoPostal = $('#codigo_postal').val();
        if (codigoPostal.length >= 4 && codigoPostal.match(/^\d{4,5}$/)) {
            console.log('Validating pre-filled postal code:', codigoPostal); // Depuración
            $.ajax({
                url: '{{ route("inscripcion.obtener-datos-direccion") }}', // Corregido: coma añadida
                method: 'POST',
                data: {
                    codigo_postal: codigoPostal,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('AJAX response for pre-filled postal code:', response); // Depuración
                    if (response.success) {
                        $('#estado').val(response.estado);
                        $('#estado_display').text(response.estado);
                        $('#municipio').val(response.municipio);
                        $('#municipio_display').text(response.municipio);
                        $('#colonia').empty().append('<option value="">Seleccione un Asentamiento</option>');
                        $.each(response.asentamientos, function(index, asentamiento) {
                            $('#colonia').append(
                                $('<option>', {
                                    value: asentamiento.nombre,
                                    text: asentamiento.nombre
                                })
                            );
                        });
                        // Do not update UI to avoid red/green borders
                    }
                    checkAllFields();
                },
                error: function(xhr) {
                    console.error('Error fetching address data for pre-filled postal code:', xhr); // Depuración
                    checkAllFields();
                }
            });
        }
    }

    // Initial check for submit button state (no UI validation)
    checkAllFields();
});
    </script>
</body>
</html>