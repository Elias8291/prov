<style>
    /* Main Button Styling */
    .navigation-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        background-color: #9d2449;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }

    /* Icon spacing */
    .navigation-button i {
        margin-left: 8px;
    }

    .navigation-button i:first-child {
        margin-left: 0;
        margin-right: 8px;
    }

    /* Hover state */
    .navigation-button:hover {
        background-color: #821f3d;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        transform: translateY(-1px);
    }

    /* Active/clicked state */
    .navigation-button:active {
        background-color: #6e1a33;
        transform: translateY(1px);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    /* Disabled state */
    .navigation-button:disabled {
        background-color: #a0aec0;
        cursor: not-allowed;
        opacity: 0.7;
        box-shadow: none;
    }

    /* For "Finalizar" button */
    .navigation-button.is-confirmation {
        background-color: #38c172;
    }

    .navigation-button.is-confirmation:hover {
        background-color: #2d9d5b;
    }

    /* Objeto Social styling */
    #formulario__grupo--objeto_social {
        margin-top: 15px;
    }

    #formulario__grupo--objeto_social .form-control {
        resize: vertical;
        min-height: 100px;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .form-group.has-error .form-control {
        border-color: #dc3545;
    }

    .formulario__input-error {
        color: #dc3545;
        font-size: 0.85em;
        margin-top: 5px;
        display: none;
    }

    .form-group.has-error .formulario__input-error {
        display: block;
    }

    /* Match form-step-2 validation styles */
    .form-control.valid {
        border-color: #28a745 !important;
    }

    .form-control.invalid {
        border-color: #dc3545 !important;
    }

    .form-group.valid .form-control {
        border-color: #28a745 !important;
    }

    .form-group.invalid .form-control {
        border-color: #dc3545 !important;
    }

    /* Actividades seleccionadas styling */
    .actividades-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 10px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        min-height: 40px;
        align-items: center;
    }

    .actividad-seleccionada {
        display: inline-flex;
        align-items: center;
        background-color: #f1f1f1;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 14px;
    }

    .actividad-seleccionada span {
        margin-right: 8px;
    }

    .remove-activity {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 14px;
        line-height: 1;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .remove-activity:hover {
        background-color: #c82333;
        transform: scale(1.1);
    }

    .remove-activity:active {
        background-color: #bd2130;
        transform: scale(0.95);
    }
</style>

<form id="formulario1" method="POST" action="{{ route('inscripcion.procesar') }}" enctype="multipart/form-data">
    @csrf
    <!-- Sección para subir Constancia de Situación Fiscal, visible solo para admin -->
    @if (auth()->check() && auth()->user()->hasRole('admin'))
        <div class="form-section" id="constancia-upload-section">
            <h4><i class="fas fa-file-pdf"></i> Subir Constancia de Situación Fiscal</h4>
            <div class="form-group full-width" id="formulario__grupo--constancia">
                <label class="form-label" for="constancia_upload">
                    <span>Seleccionar Constancia de Situación Fiscal</span>
                    <span class="file-desc">Formato PDF, máximo 5MB</span>
                </label>
                <input type="file" id="constancia_upload" name="constancia_upload" class="form-control" accept="application/pdf">
                <p class="formulario__input-error"></p>
                <div class="pdf-preview-container" id="upload-feedback" style="display: none;">
                    <i class="fas fa-file-pdf pdf-icon"></i>
                    <span class="pdf-name upload-success">PDF subido correctamente</span>
                    <button class="view-pdf-btn preview-pdf" id="preview-pdf" title="Ver PDF">
                        <i class="fas fa-eye"></i> Ver PDF
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Resto del formulario -->
    <div class="form-section" id="form-step-1">
        <h4><i class="fas fa-building"></i> Datos Generales</h4>
        <div class="form-group horizontal-group">
            <div class="half-width">
                <label class="form-label data-label">Tipo de Proveedor</label>
                @if (Auth::user()->hasRole('solicitante'))
                    <span class="data-field">{{ $tipoPersona ?? 'No disponible' }}</span>
                @else
                    <select name="tipo_persona" id="tipo_persona" class="form-control">
                        <option value="">Seleccione un tipo</option>
                        <option value="Física" {{ old('tipo_persona', $tipoPersona ?? '') === 'Física' ? 'selected' : '' }}>Física</option>
                        <option value="Moral" {{ old('tipo_persona', $tipoPersona ?? '') === 'Moral' ? 'selected' : '' }}>Moral</option>
                    </select>
                    <p class="formulario__input-error"></p>
                @endif
            </div>
            <div class="half-width">
                <label class="form-label data-label">RFC</label>
                @if (Auth::user()->hasRole('solicitante'))
                    <span class="data-field">{{ Auth::user()->rfc ?? 'No disponible' }}</span>
                @else
                    <input type="text" name="rfc" id="rfc" class="form-control" placeholder="Ej. XAXX010101000" maxlength="13" pattern="[A-Z0-9]{12,13}" value="{{ old('rfc') }}">
                    <p class="formulario__input-error"></p>
                @endif
            </div>
        </div>
        <!-- CURP field, shown for solicitante when tipo_persona is Física -->
        @if ($mostrarCurp)
            <div class="form-group" id="curp-field">
                <label class="form-label data-label">CURP</label>
                <span class="data-field">{{ $datosPrevios['curp'] ?? 'No disponible' }}</span>
            </div>
        @endif
        <!-- Campos visibles solo para revisor -->
        @if (Auth::user()->hasRole('revisor'))
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--razon_social">
                    <label class="form-label" for="razon_social">Razón Social</label>
                    <input type="text" id="razon_social" name="razon_social" class="form-control" maxlength="100" pattern="[A-Za-z\s&.,0-9]+" value="{{ old('razon_social') }}">
                    <p class="formulario__input-error"></p>
                </div>
                <div class="half-width form-group" id="formulario__grupo--correo_electronico">
                    <label class="form-label" for="correo_electronico">Correo Electrónico</label>
                    <input type="email" id="correo_electronico" name="correo_electronico" class="form-control" value="{{ old('correo_electronico') }}">
                    <p class="formulario__input-error"></p>
                </div>
            </div>
        @endif
        <!-- Objeto Social field, shown only for Moral providers -->
        @if (($tipoPersona ?? '') === 'Moral' || Auth::user()->hasRole('revisor'))
            <div class="form-group full-width" id="formulario__grupo--objeto_social" style="{{ ($tipoPersona ?? '') !== 'Moral' && !Auth::user()->hasRole('revisor') ? 'display: none;' : '' }}">
                <label class="form-label" for="objeto_social">Objeto Social</label>
                <textarea id="objeto_social" name="objeto_social" class="form-control" rows="4" maxlength="500" placeholder="Describa el objeto social de la empresa">{{ old('objeto_social', $datosPrevios['objeto_social'] ?? '') }}</textarea>
                <p class="formulario__input-error"></p>
            </div>
        @endif
        <div class="form-group full-width" id="formulario__grupo--sectores">
            <label class="form-label">Sectores</label>
            <select name="sectores" id="sectores" class="form-control">
                <option value="">Seleccione un sector</option>
                @foreach ($sectores as $sector)
                    <option value="{{ $sector['id'] }}" {{ old('sectores') == $sector['id'] ? 'selected' : '' }}>{{ $sector['nombre'] }}</option>
                @endforeach
            </select>
            <p class="formulario__input-error"></p>
        </div>
        <div class="form-group full-width" id="formulario__grupo--actividades">
            <label class="form-label">Actividades</label>
            <select name="actividad" id="actividad" class="form-control">
                <option value="">Seleccione una actividad</option>
            </select>
            <p class="formulario__input-error"></p>
        </div>
        <div class="form-group full-width" id="actividades-seleccionadas-container">
            <label class="form-label">Actividades Seleccionadas</label>
            <div id="actividades-seleccionadas" class="actividades-container">
                <!-- Actividades seleccionadas se añadirán aquí dinámicamente -->
            </div>
            <input type="hidden" name="actividades_seleccionadas" id="actividades_seleccionadas_input">
            <p class="formulario__input-error"></p>
        </div>
        <div class="horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--contacto_telefono">
                <label class="form-label" for="contacto_telefono">Teléfono de Contacto</label>
                <input type="tel" id="contacto_telefono" name="contacto_telefono" class="form-control" pattern="[0-9]{10}" maxlength="10" inputmode="numeric" value="{{ old('contacto_telefono') }}">
                <p class="formulario__input-error"></p>
            </div>
            <div class="half-width form-group" id="formulario__grupo--contacto_web">
                <label class="form-label" for="contacto_web">Página Web (opcional)</label>
                <input type="url" id="contacto_web" name="contacto_web" class="form-control" placeholder="https://www.ejemplo.com" value="{{ old('contacto_web') }}">
                <p class="formulario__input-error"></p>
            </div>
        </div>
        <h4><i class="fas fa-address-card"></i> Datos de Contacto</h4>
        <span>Persona encargada de recibir solicitudes y requerimientos</span>
        <div class="form-group" id="formulario__grupo--contacto_nombre">
            <label class="form-label" for="contacto_nombre">Nombre Completo</label>
            <input type="text" id="contacto_nombre" name="contacto_nombre" class="form-control" maxlength="40" pattern="[A-Za-z\s]+" value="{{ old('contacto_nombre') }}">
            <p class="formulario__input-error"></p>
        </div>
        <div class="form-group" id="formulario__grupo--contacto_cargo">
            <label class="form-label" for="contacto_cargo">Cargo o Puesto</label>
            <input type="text" id="contacto_cargo" name="contacto_cargo" class="form-control" maxlength="50" pattern="[A-Za-z\s]+" value="{{ old('contacto_cargo') }}">
            <p class="formulario__input-error"></p>
        </div>
        <div class="form-group" id="formulario__grupo--contacto_correo">
            <label class="form-label" for="contacto_correo">Correo Electrónico</label>
            <input type="email" id="contacto_correo" name="contacto_correo" class="form-control" value="{{ old('contacto_correo') }}">
            <p class="formulario__input-error"></p>
        </div>
        <div class="form-group" id="formulario__grupo--contacto_telefono_2">
            <label class="form-label" for="contacto_telefono_2">Teléfono de Contacto</label>
            <input type="tel" id="contacto_telefono_2" name="contacto_telefono_2" class="form-control" pattern="[0-9]{10}" maxlength="10" inputmode="numeric" value="{{ old('contacto_telefono_2') }}">
            <p class="formulario__input-error"></p>
        </div>
        <div class="form-group form-actions">
            <div class="button-group">
                @if ($seccion > 1)
                    <button type="submit" name="retroceder" value="1" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Anterior
                    </button>
                @endif
                <button type="submit" id="btnSiguiente" class="navigation-button {{ $isConfirmationSection ?? false ? 'is-confirmation' : '' }}" disabled>
                    @if ($isConfirmationSection ?? false)
                        <i class="fas fa-check"></i> Finalizar
                    @else
                        Siguiente <i class="fas fa-arrow-right"></i>
                    @endif
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Validation rules for each field
    const validationRules = {
        tipo_persona: {
            pattern: /^(Física|Moral)$/,
            message: 'Debe seleccionar un tipo de proveedor (Física o Moral).',
            required: true,
            label: 'Tipo de Proveedor'
        },
        rfc: {
            pattern: /^[A-Z0-9]{12,13}$/,
            message: 'El RFC debe tener 12 o 13 caracteres alfanuméricos.',
            required: true,
            label: 'RFC'
        },
        razon_social: {
            pattern: /^[A-Za-z\s&.,0-9]{1,100}$/,
            message: 'La razón social debe contener letras, números, espacios o &., (máx. 100 caracteres).',
            required: true,
            label: 'Razón Social'
        },
        correo_electronico: {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Debe ingresar un correo electrónico válido (ej: usuario@dominio.com).',
            required: true,
            label: 'Correo Electrónico'
        },
        objeto_social: {
            pattern: /^[\s\S]{1,500}$/,
            message: 'El objeto social es obligatorio y debe tener hasta 500 caracteres.',
            required: true, // Conditional, checked in isFieldValid
            label: 'Objeto Social'
        },
        sectores: {
            pattern: /.+/,
            message: 'Debe seleccionar un sector.',
            required: true,
            label: 'Sectores'
        },
        actividad: {
            pattern: /.+/,
            message: 'Debe seleccionar una actividad.',
            required: false, // Not directly validated, actividades_seleccionadas instead
            label: 'Actividad'
        },
        actividades_seleccionadas: {
            validator: function(value) {
                try {
                    const activities = JSON.parse(value || '[]');
                    return activities.length > 0;
                } catch (e) {
                    return false;
                }
            },
            message: 'Debe seleccionar al menos una actividad.',
            required: true,
            label: 'Actividades Seleccionadas'
        },
        contacto_telefono: {
            pattern: /^\d{10}$/,
            message: 'El teléfono debe tener exactamente 10 dígitos numéricos.',
            required: true,
            label: 'Teléfono de Contacto'
        },
        contacto_web: {
            pattern: /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?$/,
            message: 'La página web debe ser una URL válida (ej: https://ejemplo.com) o dejar en blanco.',
            required: false,
            label: 'Página Web'
        },
        contacto_nombre: {
            pattern: /^[A-Za-z\s]{1,40}$/,
            message: 'El nombre solo puede contener letras y espacios, máximo 40 caracteres.',
            required: true,
            label: 'Nombre Completo (Contacto)'
        },
        contacto_cargo: {
            pattern: /^[A-Za-z\s]{1,50}$/,
            message: 'El cargo solo puede contener letras y espacios, máximo 50 caracteres.',
            required: true,
            label: 'Cargo o Puesto'
        },
        contacto_correo: {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Debe ingresar un correo electrónico válido (ej: usuario@dominio.com).',
            required: true,
            label: 'Correo Electrónico (Contacto)'
        },
        contacto_telefono_2: {
            pattern: /^\d{10}$/,
            message: 'El teléfono debe tener exactamente 10 dígitos numéricos.',
            required: true,
            label: 'Teléfono de Contacto (Persona)'
        },
        constancia_upload: {
            validator: function(fileInput) {
                if (!fileInput.files || !fileInput.files[0]) {
                    return false; // Required for admin
                }
                const file = fileInput.files[0];
                return file.type === 'application/pdf' && file.size <= 5 * 1024 * 1024; // Max 5MB
            },
            message: 'Debe subir un archivo PDF de máximo 5MB.',
            required: true, // Only for admin
            label: 'Constancia de Situación Fiscal'
        }
    };

    // Debounce function for delayed error messages
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

    // Function to check if a field is applicable based on role and tipo_persona
    function isFieldApplicable(fieldName) {
        const isAdmin = {{ auth()->check() && auth()->user()->hasRole('admin') ? 'true' : 'false' }};
        const isRevisor = {{ auth()->check() && auth()->user()->hasRole('revisor') ? 'true' : 'false' }};
        const isSolicitante = {{ auth()->check() && auth()->user()->hasRole('solicitante') ? 'true' : 'false' }};
        const tipoPersona = $('#tipo_persona').val() || '{{ $tipoPersona ?? '' }}';

        if (fieldName === 'constancia_upload' && !isAdmin) {
            return false;
        }
        if (['razon_social', 'correo_electronico'].includes(fieldName) && !isRevisor) {
            return false;
        }
        if (fieldName === 'objeto_social' && !isRevisor && tipoPersona !== 'Moral') {
            return false;
        }
        if (['tipo_persona', 'rfc'].includes(fieldName) && isSolicitante) {
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
        let value = field.val()?.trim();
        if (fieldName === 'constancia_upload') {
            return rules.validator(field[0]);
        }
        if (fieldName === 'actividades_seleccionadas') {
            value = $('#actividades_seleccionadas_input').val();
            return rules.validator(value);
        }
        if (rules.required && (!value || value === '')) {
            return false;
        }
        if (value && rules.pattern && !rules.pattern.test(value)) {
            return false;
        }
        if (!rules.required && value && rules.pattern && !rules.pattern.test(value)) {
            return false; // For optional fields like contacto_web
        }
        return true;
    }

    // Function to update field UI (borders and error message)
    function updateFieldUI(field, showError = false) {
        const fieldName = field.attr('id');
        if (!validationRules[fieldName] || !isFieldApplicable(fieldName)) {
            field.closest('.form-group').removeClass('has-error invalid valid');
            field.removeClass('invalid valid');
            field.closest('.form-group').find('.formulario__input-error').hide();
            return true; // Skip inapplicable fields
        }

        const group = field.closest('.form-group');
        const rules = validationRules[fieldName];
        const isValid = isFieldValid(field);

        if (isValid) {
            group.removeClass('has-error invalid').addClass('valid');
            field.removeClass('invalid').addClass('valid');
            group.find('.formulario__input-error').hide();
        } else {
            group.removeClass('valid').addClass('has-error invalid');
            field.removeClass('valid').addClass('invalid');
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
            '#tipo_persona',
            '#rfc',
            '#razon_social',
            '#correo_electronico',
            '#objeto_social',
            '#sectores',
            '#actividades_seleccionadas_input',
            '#contacto_telefono',
            '#contacto_web',
            '#contacto_nombre',
            '#contacto_cargo',
            '#contacto_correo',
            '#contacto_telefono_2',
            '#constancia_upload'
        ];

        fields.forEach(function(fieldSelector) {
            const field = $(fieldSelector);
            if (field.length && !field.is(':hidden') && isFieldApplicable(field.attr('id'))) {
                if (!isFieldValid(field)) {
                    allValid = false;
                }
            }
        });

        // Enable/disable submit button
        $('#btnSiguiente').prop('disabled', !allValid);
    }

    // Input restrictions
    $('#contacto_telefono, #contacto_telefono_2').on('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/[0-9]/.test(char)) {
            e.preventDefault();
        }
    });

    $('#contacto_nombre, #contacto_cargo').on('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/[A-Za-z\s]/.test(char)) {
            e.preventDefault();
        }
    });

    $('#rfc').on('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/[A-Z0-9]/.test(char)) {
            e.preventDefault();
        }
    });

    $('#razon_social').on('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/[A-Za-z\s&.,0-9]/.test(char)) {
            e.preventDefault();
        }
    });

    // Real-time border updates and error display
    $('#tipo_persona, #rfc, #razon_social, #correo_electronico, #objeto_social, #sectores, #contacto_telefono, #contacto_web, #contacto_nombre, #contacto_cargo, #contacto_correo, #contacto_telefono_2').on('input change', function() {
        updateFieldUI($(this), true); // Always show errors
        checkAllFields();
    });

    // Special handling for constancia_upload
    $('#constancia_upload').on('change', function() {
        const isValid = isFieldValid($(this));
        updateFieldUI($(this), true); // Show error immediately
        if (isValid) {
            $('#upload-feedback').show();
        } else {
            $('#upload-feedback').hide();
        }
        checkAllFields();
    });

    // Special handling for actividades_seleccionadas
    const actividadesSeleccionadasInput = $('#actividades_seleccionadas_input');
    const observer = new MutationObserver(function() {
        updateFieldUI(actividadesSeleccionadasInput, true); // Show error immediately
        checkAllFields();
    });
    observer.observe(document.getElementById('actividades-seleccionadas'), { childList: true, subtree: true });

    // Delayed error message display for non-select/file fields
    const showErrorMessage = debounce(function(field) {
        updateFieldUI(field, true); // Show error after pause
        checkAllFields();
    }, 500);

    $('#rfc, #razon_social, #correo_electronico, #objeto_social, #contacto_telefono, #contacto_web, #contacto_nombre, #contacto_cargo, #contacto_correo, #contacto_telefono_2').on('input', function() {
        showErrorMessage($(this));
    });

    // Immediate error for select fields
    $('#tipo_persona, #sectores').on('change', function() {
        updateFieldUI($(this), true); // Show error immediately
        checkAllFields();
    });

    // Activity selection and objeto_social logic
    const sectorSelect = $('#sectores');
    const actividadSelect = $('#actividad');
    const actividadesSeleccionadas = $('#actividades-seleccionadas');
    const tipoPersonaSelect = $('#tipo_persona');
    const objetoSocialGroup = $('#formulario__grupo--objeto_social');
    const selectedActivities = [];

    function updateActividades(actividades) {
        actividadSelect.html('<option value="">Seleccione una actividad</option>');
        actividades.forEach(actividad => {
            const option = $('<option>', {
                value: actividad.id,
                text: actividad.nombre
            });
            actividadSelect.append(option);
        });
    }

    function isActivitySelected(id) {
        return selectedActivities.some(activity => activity.id === id);
    }

    function removeActivity(id) {
        const index = selectedActivities.findIndex(activity => activity.id === id);
        if (index !== -1) {
            selectedActivities.splice(index, 1);
            updateActivityDisplay();
        }
    }

    function updateActivityDisplay() {
        actividadesSeleccionadas.html('');
        if (selectedActivities.length === 0) {
            actividadesSeleccionadas.html('<span>Sin actividad seleccionada</span>');
            actividadesSeleccionadasInput.val('');
            updateFieldUI(actividadesSeleccionadasInput, true); // Trigger validation
            return;
        }
        selectedActivities.forEach(activity => {
            const activityElement = $('<div>', { class: 'actividad-seleccionada' });
            const activityText = $('<span>', { text: activity.name });
            const removeButton = $('<button>', {
                class: 'remove-activity',
                html: '×',
                'data-id': activity.id,
                title: 'Eliminar actividad'
            }).on('click', function() {
                removeActivity(activity.id);
            });
            activityElement.append(activityText).append(removeButton);
            actividadesSeleccionadas.append(activityElement);
        });
        actividadesSeleccionadasInput.val(JSON.stringify(selectedActivities.map(a => a.id)));
        updateFieldUI(actividadesSeleccionadasInput, true); // Trigger validation
    }

    function addSelectedActivity() {
        const selectedOption = actividadSelect[0].options[actividadSelect[0].selectedIndex];
        if (!selectedOption || selectedOption.value === '') {
            return;
        }
        const activityId = selectedOption.value;
        const activityName = selectedOption.textContent;
        if (isActivitySelected(activityId)) {
            return;
        }
        selectedActivities.push({
            id: activityId,
            name: activityName
        });
        updateActivityDisplay();
        actividadSelect.val('');
    }

    function toggleObjetoSocial() {
        if (tipoPersonaSelect.length && objetoSocialGroup.length) {
            const isMoral = tipoPersonaSelect.val() === 'Moral';
            const isRevisor = {{ auth()->check() && auth()->user()->hasRole('revisor') ? 'true' : 'false' }};
            objetoSocialGroup.css('display', isMoral || isRevisor ? 'block' : 'none');
            updateFieldUI($('#objeto_social'), true);
            checkAllFields();
        }
    }

    sectorSelect.on('change', function() {
        const sectorId = this.value;
        if (!sectorId) {
            actividadSelect.html('<option value="">Seleccione una actividad</option>');
            selectedActivities.length = 0;
            updateActivityDisplay();
            return;
        }
        $.ajax({
            url: `/inscripcion/actividades?sector_id=${sectorId}`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(data) {
                if (data.error) {
                    console.error(data.error);
                    actividadSelect.html('<option value="">Error al cargar actividades</option>');
                    selectedActivities.length = 0;
                    updateActivityDisplay();
                    return;
                }
                updateActividades(data.actividades);
            },
            error: function(error) {
                console.error('Error:', error);
                actividadSelect.html('<option value="">Error al cargar actividades</option>');
                selectedActivities.length = 0;
                updateActivityDisplay();
            }
        });
    });

    actividadSelect.on('change', function() {
        if (this.value) {
            addSelectedActivity();
        }
    });

    // Initialize objeto_social visibility and validation
    toggleObjetoSocial();
    tipoPersonaSelect.on('change', toggleObjetoSocial);

    // Initial validation on page load
    const fields = [
        '#tipo_persona',
        '#rfc',
        '#razon_social',
        '#correo_electronico',
        '#objeto_social',
        '#sectores',
        '#actividades_seleccionadas_input',
        '#contacto_telefono',
        '#contacto_web',
        '#contacto_nombre',
        '#contacto_cargo',
        '#contacto_correo',
        '#contacto_telefono_2',
        '#constancia_upload'
    ];
    fields.forEach(function(fieldSelector) {
        const field = $(fieldSelector);
        if (field.length && !field.is(':hidden') && isFieldApplicable(field.attr('id'))) {
            updateFieldUI(field, true); // Show errors on load if invalid
        }
    });

    // Update submit button state
    checkAllFields();

    // Handle pre-selected activities (e.g., from old() or session)
    const preSelectedActivities = $('#actividades_seleccionadas_input').val();
    if (preSelectedActivities) {
        try {
            const activities = JSON.parse(preSelectedActivities);
            activities.forEach(id => {
                // Fetch activity name if needed (mocked here)
                selectedActivities.push({ id: id, name: `Actividad ${id}` });
            });
            updateActivityDisplay();
        } catch (e) {
            console.error('Error parsing pre-selected activities:', e);
        }
    }
});
</script>