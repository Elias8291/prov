<div>
    <form id="formulario5" action="{{ $action }}" method="{{ $method }}">
        @csrf
         <div id="section-5" class="form-section">
        <div class="form-container">
            <!-- Primera columna -->
            <div class="form-column">
                <div class="form-group">
                    <h4><i class="fas fa-user-tie"></i> Datos del Apoderado o Representante Legal</h4>
                </div>
                <div class="form-group horizontal-group">
                    <div class="half-width form-group" id="formulario__grupo--nombre-apoderado">
                        <label class="form-label" for="nombre-apoderado">Nombre</label>
                        <input type="text" id="nombre-apoderado" name="nombre-apoderado" class="form-control"
                            placeholder="Ej: Lic. Juan Pérez González" value="{{ $datosPrevios['nombre-apoderado'] ?? '' }}">
                        <p class="formulario__input-error">El nombre solo puede contener letras y espacios, máximo 100 caracteres.</p>
                    </div>
                    <div class="half-width form-group" id="formulario__grupo--numero-escritura">
                        <label class="form-label" for="numero-escritura">Número de Escritura</label>
                        <input type="text" id="numero-escritura" name="numero-escritura" class="form-control"
                            placeholder="Ej: 12345" value="{{ $datosPrevios['numero-escritura'] ?? '' }}">
                        <p class="formulario__input-error">El número de escritura debe contener solo números, máximo 10 dígitos.</p>
                    </div>
                </div>
                <div class="form-group horizontal-group">
                    <div class="half-width form-group" id="formulario__grupo--nombre-notario">
                        <label class="form-label" for="nombre-notario">Nombre del Notario</label>
                        <input type="text" id="nombre-notario" name="nombre-notario" class="form-control"
                            placeholder="Ej: Lic. María López Ramírez" value="{{ $datosPrevios['nombre-notario'] ?? '' }}">
                        <p class="formulario__input-error">El nombre del notario solo puede contener letras y espacios, máximo 100 caracteres.</p>
                    </div>
                    <div class="half-width form-group" id="formulario__grupo--numero-notario">
                        <label class="form-label" for="numero-notario">Número del Notario</label>
                        <input type="text" id="numero-notario" name="numero-notario" class="form-control"
                            placeholder="Ej: 123" value="{{ $datosPrevios['numero-notario'] ?? '' }}">
                        <p class="formulario__input-error">El número del notario debe contener solo números, máximo 10 dígitos.</p>
                    </div>
                </div>
                <div class="form-group horizontal-group">
                    <div class="half-width form-group" id="formulario__grupo--entidad-federativa">
                        <label class="form-label" for="entidad-federativa">Entidad Federativa</label>
                        <select id="entidad-federativa" name="entidad-federativa" class="form-control">
                            <option value="">Seleccione un estado</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado['id'] }}" {{ isset($datosPrevios['entidad-federativa']) && $datosPrevios['entidad-federativa'] == $estado['id'] ? 'selected' : '' }}>
                                    {{ $estado['nombre'] }}
                                </option>
                            @endforeach
                        </select>
                        <p class="formulario__input-error">Por favor, seleccione una entidad federativa.</p>
                    </div>
                    <div class="half-width form-group" id="formulario__grupo--fecha-escritura">
                        <label class="form-label" for="fecha-escritura">Fecha de Escritura</label>
                        <input type="date" id="fecha-escritura" name="fecha-escritura" class="form-control" 
                            value="{{ $datosPrevios['fecha-escritura'] ?? '' }}">
                        <p class="formulario__input-error">Por favor, seleccione una fecha válida.</p>
                    </div>
                </div>
            </div>
            <!-- Segunda columna -->
            <div class="form-column">
                <div class="form-group">
                    <h4><i class="fas fa-book"></i> Datos de Inscripción en el Registro Público</h4>
                </div>
                <div class="form-group horizontal-group">
                    <div class="half-width form-group" id="formulario__grupo--numero-registro">
                        <label class="form-label" for="numero-registro">Número de Registro o Folio Mercantil</label>
                        <input type="text" id="numero-registro" name="numero-registro" class="form-control"
                            placeholder="Ej: 987654" value="{{ $datosPrevios['numero-registro'] ?? '' }}">
                        <p class="formulario__input-error">El número de registro debe contener solo números, máximo 10 dígitos.</p>
                    </div>
                    <div class="half-width form-group" id="formulario__grupo--fecha-inscripcion">
                        <label class="form-label" for="fecha-inscripcion">Fecha de Inscripción</label>
                        <input type="date" id="fecha-inscripcion" name="fecha-inscripcion" class="form-control"
                            value="{{ $datosPrevios['fecha-inscripcion'] ?? '' }}">
                        <p class="formulario__input-error">Por favor, seleccione una fecha válida.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="form-buttons">
            <button type="button" class="btn btn-secondary" onclick="window.history.back();">Anterior</button>
            <button type="submit" class="btn btn-primary" id="submit-btn">
                {{ $isConfirmationSection ? 'Confirmar' : 'Siguiente' }}
            </button>
        </div>
    </form>

    @once
        @push('styles')
       
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

    /* Responsive styles */
    @media (max-width: 576px) {
        .form-buttons {
            flex-direction: column | column;
            gap: 10px;
        }

        .btn {
            width: 100%;
        }
    }

    /* Estilos adicionales para notificaciones */
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

    .notification.info {
        border-left: 4px solid #007bff;
    }

    .notification.success {
        border-left: 4px solid #28a745;
    }

    .notification.warning {
        border-left: 4px solid #ffc107;
    }

    .notification.error {
        border-left: 4px solid #dc3545;
    }
</style>

        @endpush

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuración de validación
            const formulario = document.getElementById('formulario5');
            const inputs = {
                'nombre-apoderado': {
                    regex: /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s.,]{1,100}$/,
                    mensaje: 'El nombre solo puede contener letras y espacios, máximo 100 caracteres.',
                    required: true
                },
                'numero-escritura': {
                    regex: /^[0-9]{1,10}$/,
                    mensaje: 'El número de escritura debe contener solo números, máximo 10 dígitos.',
                    required: true
                },
                'nombre-notario': {
                    regex: /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s.,]{1,100}$/,
                    mensaje: 'El nombre del notario solo puede contener letras y espacios, máximo 100 caracteres.',
                    required: true
                },
                'numero-notario': {
                    regex: /^[0-9]{1,10}$/,
                    mensaje: 'El número del notario debe contener solo números, máximo 10 dígitos.',
                    required: true
                },
                'entidad-federativa': {
                    regex: null, // Se valida de forma diferente
                    mensaje: 'Por favor, seleccione una entidad federativa.',
                    required: true
                },
                'fecha-escritura': {
                    regex: null, // Se valida de forma diferente
                    mensaje: 'Por favor, seleccione una fecha válida.',
                    required: true
                },
                'numero-registro': {
                    regex: /^[0-9]{1,10}$/,
                    mensaje: 'El número de registro debe contener solo números, máximo 10 dígitos.',
                    required: true
                },
                'fecha-inscripcion': {
                    regex: null, // Se valida de forma diferente
                    mensaje: 'Por favor, seleccione una fecha válida.',
                    required: true
                }
            };

            const interactedFields = new Set();
            const validStates = {};

            // Cargar estados al cargar la página
            cargarEstados();
            
            // Función para cargar estados
            function cargarEstados() {
                const select = document.getElementById('entidad-federativa');
                if (!select) {
                    console.error('No se encontró el elemento con ID entidad-federativa');
                    return;
                }
                
                if (select.options.length > 1 && select.options[1].value) {
                    console.log('Los estados ya están cargados');
                    return; // Ya están cargados
                }

                fetch('/estados')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Limpiar opciones excepto la primera
                        while (select.options.length > 1) {
                            select.remove(1);
                        }

                        // Agregar estados
                        data.forEach(estado => {
                            const option = document.createElement('option');
                            option.value = estado.id;
                            option.textContent = estado.nombre;
                            select.appendChild(option);
                        });
                        
                        // Si hay un valor preseleccionado, seleccionarlo
                        if (select.dataset.selected) {
                            select.value = select.dataset.selected;
                        }
                        
                        console.log('Estados cargados exitosamente');
                    })
                    .catch(error => {
                        console.error('Error al cargar estados:', error);
                        showNotification('No se pudieron cargar los estados. Por favor, recargue la página.', 'error');
                    });
            }

            // Función para mostrar notificaciones
            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.textContent = message;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }

            // Función para validar un campo
            function validarCampo(input, mostrarError = true) {
                const fieldId = input.id;
                const fieldConfig = inputs[fieldId];
                if (!fieldConfig) return true;

                const grupo = document.getElementById(`formulario__grupo--${fieldId}`);
                const value = input.value.trim();
                let isValid = true;

                // Comprobar si es requerido
                if (fieldConfig.required && value === '') {
                    isValid = false;
                }
                
                // Si tiene valor, comprobar el patrón
                else if (value !== '' && fieldConfig.regex && !fieldConfig.regex.test(value)) {
                    isValid = false;
                }

                // Casos especiales
                if (fieldId === 'entidad-federativa' && value === '') {
                    isValid = !fieldConfig.required;
                }

                if (['fecha-escritura', 'fecha-inscripcion'].includes(fieldId)) {
                    // Validar que la fecha es válida y no es futura
                    if (value) {
                        const fechaSeleccionada = new Date(value);
                        const hoy = new Date();
                        hoy.setHours(23, 59, 59, 999); // Fin del día actual
                        
                        if (isNaN(fechaSeleccionada.getTime()) || fechaSeleccionada > hoy) {
                            isValid = false;
                        }
                    } else {
                        isValid = !fieldConfig.required;
                    }
                }

                // Actualizar UI solo si el campo se ha interactuado
                if (interactedFields.has(fieldId) || mostrarError) {
                    // Actualizar clases
                    grupo.classList.remove('valid', 'invalid');
                    input.classList.remove('valid', 'invalid');

                    if (isValid) {
                        grupo.classList.add('valid');
                        input.classList.add('valid');
                    } else {
                        grupo.classList.add('invalid');
                        input.classList.add('invalid');
                        if (mostrarError) {
                            grupo.querySelector('.formulario__input-error').style.display = 'block';
                            grupo.querySelector('.formulario__input-error').textContent = fieldConfig.mensaje;
                        }
                    }
                }

                validStates[fieldId] = isValid;
                return isValid;
            }

            // Validar todos los campos y comprobar si el formulario es válido
            function validarFormulario() {
                let formValid = true;
                
                for (const fieldId in inputs) {
                    const input = document.getElementById(fieldId);
                    if (input) {
                        const isValid = validarCampo(input, false);
                        if (!isValid) {
                            formValid = false;
                        }
                    }
                }
                
                return formValid;
            }

            // Agregar event listeners a los campos
            for (const fieldId in inputs) {
                const input = document.getElementById(fieldId);
                if (input) {
                    input.addEventListener('input', () => {
                        interactedFields.add(fieldId);
                        validarCampo(input);
                    });
                    
                    input.addEventListener('blur', () => {
                        interactedFields.add(fieldId);
                        validarCampo(input, true);
                    });
                }
            }

            // Manejar envío del formulario
            formulario.addEventListener('submit', function(event) {
                event.preventDefault();
                
                // Marcar todos los campos como interactuados
                for (const fieldId in inputs) {
                    interactedFields.add(fieldId);
                }
                
                // Validar todos los campos
                const isValid = validarFormulario();
                
                if (isValid) {
                    // Mostrar mensaje de éxito y enviar el formulario
                    this.submit();
                } else {
                    // Mostrar mensaje de error
                    showNotification('Por favor, complete correctamente todos los campos requeridos.', 'error');
                    
                    // Enfocar el primer campo con error
                    for (const fieldId in inputs) {
                        if (validStates[fieldId] === false) {
                            const input = document.getElementById(fieldId);
                            if (input) {
                                input.focus();
                                break;
                            }
                        }
                    }
                }
            });

            // Iniciar validación para detectar valores precompletados
            setTimeout(() => {
                for (const fieldId in inputs) {
                    const input = document.getElementById(fieldId);
                    if (input && input.value) {
                        validarCampo(input, false);
                    }
                }
            }, 500);
        });
        </script>
        @endpush
    @endonce
</div>