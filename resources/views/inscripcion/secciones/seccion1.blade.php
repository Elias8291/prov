<form id="formulario1">
    <!-- Sección para subir Constancia de Situación Fiscal, visible solo para admin -->
    @if(auth()->check() && auth()->user()->hasRole('admin'))
    <div class="form-section" id="constancia-upload-section">
        <h4><i class="fas fa-file-pdf"></i> Subir Constancia de Situación Fiscal</h4>
        <div class="form-group full-width" id="formulario__grupo--constancia">
            <label class="form-label" for="constancia_upload">
                <span>Seleccionar Constancia de Situación Fiscal</span>
                <span class="file-desc">Formato PDF, máximo 5MB</span>
            </label>
            <input type="file" id="constancia_upload" name="constancia_upload" class="form-control"
                accept="application/pdf" required>
            <p class="formulario__input-error">Debe seleccionar un archivo en formato PDF.</p>
            <!-- Contenedor para confirmación de subida y vista previa -->
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
                    <span class="data-field">{{ Auth::user()->solicitante->tipo_persona ?? 'No disponible' }}</span>
                @else
                    <select name="tipo_persona" id="tipo_persona" class="form-control" required>
                        <option value="">Seleccione un tipo</option>
                        <option value="Física">Física</option>
                        <option value="Moral">Moral</option>
                    </select>
                    <p class="formulario__input-error">Debe seleccionar 'Física' o 'Moral'.</p>
                @endif
            </div>
            <div class="half-width">
                <label class="form-label data-label">RFC</label>
                @if (Auth::user()->hasRole('solicitante'))
                    <span class="data-field">{{ Auth::user()->rfc ?? 'No disponible' }}</span>
                @else
                    <input type="text" name="rfc" id="rfc" class="form-control"
                        placeholder="Ej. XAXX010101000" required maxlength="13" pattern="[A-Z0-9]{12,13}">
                    <p class="formulario__input-error">El RFC debe tener 12 o 13 caracteres alfanuméricos.</p>
                @endif
            </div>
        </div>
        <!-- Campos visibles solo para revisor -->
        @if (Auth::user()->hasRole('revisor'))
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--razon_social">
                    <label class="form-label" for="razon_social">Razón Social</label>
                    <input type="text" id="razon_social" name="razon_social" class="form-control" required
                        maxlength="100" pattern="[A-Za-z\s&.,0-9]+">
                    <p class="formulario__input-error">La razón social debe contener solo letras, números, espacios y
                        caracteres (&,.,).</p>
                </div>
                <div class="half-width form-group" id="formulario__grupo--correo_electronico">
                    <label class="form-label" for="correo_electronico">Correo Electrónico</label>
                    <input type="email" id="correo_electronico" name="correo_electronico" class="form-control"
                        required>
                    <p class="formulario__input-error">El correo debe tener un formato válido (ej. usuario@dominio.com).
                    </p>
                </div>
            </div>
        @endif
        <div class="form-group full-width" id="formulario__grupo--sectores">
            <label class="form-label">Sectores</label>
            <select name="sectores" id="sectores" class="form-control">
                <option value="">Seleccione un sector</option>
            </select>
            <p class="formulario__input-error">Debe seleccionar al menos un sector.</p>
        </div>
        <div class="form-group full-width" id="formulario__grupo--actividades">
            <label class="form-label">Actividades</label>
            <select name="actividad" id="actividad" class="form-control" required>
                <option value="">Seleccione una actividad</option>
            </select>
            <p class="formulario__input-error">Debe seleccionar al menos una actividad.</p>
        </div>
        <div class="form-group full-width" id="actividades-seleccionadas-container">
            <label class="form-label">Actividades Seleccionadas</label>
            <div id="actividades-seleccionadas" class="actividades-container">
                <!-- Actividades seleccionadas se añadirán aquí dinámicamente -->
            </div>
        </div>
        <!-- CURP field, initially hidden for revisor, shown dynamically if tipo_persona is Física -->
        @if (Auth::user()->hasRole('revisor') || (Auth::user()->hasRole('solicitante') && Auth::user()->solicitante && Auth::user()->solicitante->tipo_persona == 'Física'))
            <div class="form-group" id="curp-field" style="display: none;">
                <label class="form-label data-label">CURP</label>
                <span class="data-field" id="curp-value">{{ Auth::user()->solicitante->curp ?? 'No disponible' }}</span>
            </div>
        @endif
        <div class="horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--contacto_telefono">
                <label class="form-label" for="contacto_telefono">Teléfono de Contacto</label>
                <input type="tel" id="contacto_telefono" name="contacto_telefono" class="form-control" required
                    pattern="[0-9]{10}">
                <p class="formulario__input-error">El teléfono debe contener exactamente 10 dígitos numéricos.</p>
            </div>
            <div class="half-width form-group" id="formulario__grupo--contacto_web">
                <label class="form-label" for="contacto_web">Página Web (opcional)</label>
                <input type="url" id="contacto_web" name="contacto_web" class="form-control"
                    placeholder="https://www.ejemplo.com">
                <p class="formulario__input-error">La URL debe ser válida (ej. https://www.empresa.com) o dejar en
                    blanco.</p>
            </div>
        </div>
        <h4><i class="fas fa-address-card"></i> Datos de Contacto</h4>
        <span>Persona encargada de recibir solicitudes y requerimientos</span>
        <div class="form-group" id="formulario__grupo--contacto_nombre">
            <label class="form-label" for="contacto_nombre">Nombre Completo</label>
            <input type="text" id="contacto_nombre" name="contacto_nombre" class="form-control" required
                maxlength="40" pattern="[A-Za-z\s]+">
            <p class="formulario__input-error">El nombre debe contener solo letras y espacios, máximo 40 caracteres.
            </p>
        </div>
        <div class="form-group" id="formulario__grupo--contacto_cargo">
            <label class="form-label" for="contacto_cargo">Cargo o Puesto</label>
            <input type="text" id="contacto_cargo" name="contacto_cargo" class="form-control" required
                maxlength="50" pattern="[A-Za-z\s]+">
            <p class="formulario__input-error">El cargo debe contener solo letras y espacios, máximo 50 caracteres.</p>
        </div>
        <div class="form-group" id="formulario__grupo--contacto_correo">
            <label class="form-label" for="contacto_correo">Correo Electrónico</label>
            <input type="email" id="contacto_correo" name="contacto_correo" class="form-control" required>
            <p class="formulario__input-error">El correo debe tener un formato válido (ej. usuario@dominio.com).</p>
        </div>
        <div class="form-group" id="formulario__grupo--contacto_telefono_2">
            <label class="form-label" for="contacto_telefono_2">Teléfono de Contacto </label>
            <input type="tel" id="contacto_telefono_2" name="contacto_telefono_2" class="form-control" required
                pattern="[0-9]{10}">
            <p class="formulario__input-error">El teléfono debe contener exactamente 10 dígitos numéricos.</p>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Función para actualizar visibilidad de campos según tipo_persona
    function updateFormVisibility(tipoPersona) {
        const curpField = document.getElementById('curp-field');
        if (curpField) {
            curpField.style.display = tipoPersona === 'Física' ? 'block' : 'none';
        }
    }

    // 2. Función para autocompletar campos del formulario con datos del PDF
    function autocompleteFormFields(pdfData, satData) {
        const isRevisor = !!document.getElementById('constancia_upload');
        if (!isRevisor) return;

        const fieldMappings = {
            tipo_persona: pdfData.tipo || '',
            rfc: pdfData.rfc || '',
            razon_social: satData.razonSocial?.toUpperCase() || '',
            correo_electronico: satData.email?.toUpperCase() || ''
        };

        Object.entries(fieldMappings).forEach(([fieldId, value]) => {
            const element = document.getElementById(fieldId);
            if (element) {
                if (element.tagName === 'INPUT') {
                    element.value = value;
                } else if (element.tagName === 'SELECT') {
                    element.value = value;
                }
            }
        });

        if (pdfData.tipo === 'Física' && satData.curp) {
            const curpField = document.querySelector('.data-field#curp-value');
            if (curpField) {
                curpField.textContent = satData.curp.toUpperCase() || 'No disponible';
            }
        }

        // Disparar evento change para tipo_persona
        if (pdfData.tipo) {
            const tipoPersonaSelect = document.getElementById('tipo_persona');
            if (tipoPersonaSelect) {
                tipoPersonaSelect.value = pdfData.tipo;
                updateFormVisibility(pdfData.tipo);
                const changeEvent = new Event('change', { bubbles: true });
                tipoPersonaSelect.dispatchEvent(changeEvent);
            }
        }
    }

    // 3. Función para autocompletar dirección en Formulario 2 (si existe)
    function populateFormulario2AddressFields(satData) {
        const isSolicitante = @json(Auth::user()->hasRole('solicitante'));
        const fields = {
            codigo_postal: satData.cp || '',
            calle: satData.nombreVialidad || '',
            numero_exterior: satData.numeroExterior || '',
            numero_interior: satData.numeroInterior || '',
            colonia: satData.colonia || ''
        };

        Object.entries(fields).forEach(([fieldId, value]) => {
            const inputElement = document.getElementById(fieldId);
            const displayElement = document.getElementById(`${fieldId}_display`);

            if (isSolicitante) {
                if (displayElement) {
                    displayElement.textContent = value || 'No disponible';
                }
                if (inputElement) {
                    inputElement.value = value || '';
                }
            } else {
                if (inputElement && fieldId !== 'colonia') {
                    inputElement.value = value || '';
                }
            }
        });

        // Buscar código postal para revisor
        if (!isSolicitante && satData.cp) {
            const codigoPostalInput = document.getElementById('codigo_postal');
            if (codigoPostalInput) {
                codigoPostalInput.value = satData.cp;
                const inputEvent = new Event('input', { bubbles: true });
                codigoPostalInput.dispatchEvent(inputEvent);

                // Establecer colonia después de la búsqueda
                setTimeout(() => {
                    const coloniaSelect = document.getElementById('colonia');
                    if (coloniaSelect && satData.colonia) {
                        Array.from(coloniaSelect.options).forEach(option => {
                            if (option.textContent.toLowerCase() === satData.colonia.toLowerCase()) {
                                option.selected = true;
                            }
                        });
                    }
                }, 1000); // Retardo para permitir completar la búsqueda
            }
        }
    }

    // 4. Manejo de subida de PDF
    const fileInput = document.getElementById('constancia_upload');
    const uploadFeedback = document.getElementById('upload-feedback');
    const formGroupConstancia = document.getElementById('formulario__grupo--constancia');

    if (fileInput) {
        fileInput.addEventListener('change', async function() {
            const file = fileInput.files[0];
            if (!file) {
                uploadFeedback.style.display = 'block';
                uploadFeedback.innerHTML = 
                    '<span class="upload-error"><i class="fas fa-exclamation-circle"></i> No se seleccionó ningún archivo.</span>';
                return;
            }

            if (file.type !== 'application/pdf') {
                uploadFeedback.style.display = 'block';
                uploadFeedback.innerHTML = 
                    '<span class="upload-error"><i class="fas fa-exclamation-circle"></i> Debe seleccionar un archivo en formato PDF.</span>';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                uploadFeedback.style.display = 'block';
                uploadFeedback.innerHTML = 
                    '<span class="upload-error"><i class="fas fa-exclamation-circle"></i> El archivo excede el tamaño máximo de 5MB.</span>';
                return;
            }

            // Crear barra de progreso
            let progressBar = formGroupConstancia.querySelector('.pdf-upload-progress');
            if (!progressBar) {
                progressBar = document.createElement('div');
                progressBar.classList.add('pdf-upload-progress');
                progressBar.innerHTML = '<div class="progress-bar"></div>';
                formGroupConstancia.appendChild(progressBar);
            }
            progressBar.style.display = 'block';
            const progressBarInner = progressBar.querySelector('.progress-bar');

            // Simular progreso
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += 10;
                progressBarInner.style.width = `${progress}%`;
                if (progress >= 100) {
                    clearInterval(progressInterval);
                    progressBar.style.display = 'none';
                }
            }, 100);

            try {
                // Extraer datos del PDF
                const pdfData = await window.extractQRCodeFromPDF(file);
                console.log('Datos extraídos del PDF:', pdfData);

                // Obtener datos del SAT
                const satData = await window.scrapeSATData(pdfData.qrUrl);
                console.log('Datos extraídos del SAT:', satData);

                // Autocompletar formularios
                autocompleteFormFields(pdfData, satData);
                populateFormulario2AddressFields(satData);

                // Actualizar UI para subida exitosa
                formGroupConstancia.classList.add('pdf-upload-success');
                uploadFeedback.style.display = 'block';
                uploadFeedback.innerHTML = `
                    <span class="upload-success">
                        <i class="fas fa-check-circle"></i> PDF subido correctamente
                    </span>
                    <a href="#" class="preview-pdf" id="preview-pdf" title="Ver PDF">
                        <i class="fas fa-eye"></i> Ver PDF
                    </a>
                `;

                // Habilitar vista previa del PDF
                const pdfUrl = URL.createObjectURL(file);
                const previewLink = uploadFeedback.querySelector('#preview-pdf');
                previewLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    window.open(pdfUrl, '_blank');
                });
            } catch (error) {
                console.error('Error al procesar el PDF:', error.message);
                uploadFeedback.style.display = 'block';
                uploadFeedback.innerHTML = 
                    '<span class="upload-error"><i class="fas fa-exclamation-circle"></i> Error al procesar el PDF: ' + error.message + '</span>';
                progressBar.style.display = 'none';
            }
        });
    }

    // 5. Código para sectores y actividades
    const sectorSelect = document.getElementById('sectores');
    const actividadSelect = document.getElementById('actividad');
    const actividadesContainer = document.getElementById('actividades-seleccionadas');
    const actividadesSeleccionadas = new Set();
    let actividadesDisponibles = [];
    let actividadesIds = [];

    // Cargar sectores al iniciar
    if (sectorSelect) {
        fetch('/sectores')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    sectorSelect.innerHTML = '<option value="">Seleccione un sector</option>';
                    data.data.forEach(sector => {
                        const option = document.createElement('option');
                        option.value = sector.id;
                        option.textContent = sector.nombre;
                        sectorSelect.appendChild(option);
                    });
                } else {
                    sectorSelect.innerHTML = '<option value="">No hay sectores disponibles</option>';
                }
            })
            .catch(error => {
                console.error('Error al cargar sectores:', error);
                sectorSelect.innerHTML = '<option value="">Error al cargar sectores</option>';
            });

        // Evento para cargar actividades al seleccionar un sector
        sectorSelect.addEventListener('change', function() {
            const sectorId = this.value;
            actividadesSeleccionadas.clear();
            actividadesIds = [];
            actividadesContainer.innerHTML = '';

            if (sectorId) {
                actividadSelect.innerHTML = '<option value="">Seleccione una actividad</option>';
                actividadesDisponibles = [];

                fetch(`/sectores/${sectorId}/actividades`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            actividadesDisponibles = data.data;
                            updateActividadesDropdown();
                        } else {
                            actividadSelect.innerHTML = '<option value="">No hay actividades disponibles</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar actividades:', error);
                        actividadSelect.innerHTML = '<option value="">Error al cargar actividades</option>';
                    });
            } else {
                actividadSelect.innerHTML = '<option value="">Seleccione un sector primero</option>';
                actividadesDisponibles = [];
            }

            validateActividades();
        });
    }

    function updateActividadesDropdown() {
        actividadSelect.innerHTML = '<option value="">Seleccione una actividad</option>';
        actividadesDisponibles.forEach(actividad => {
            if (!actividadesSeleccionadas.has(actividad.id.toString())) {
                const option = document.createElement('option');
                option.value = actividad.id;
                option.textContent = actividad.nombre;
                actividadSelect.appendChild(option);
            }
        });
    }

    if (actividadSelect) {
        actividadSelect.addEventListener('change', function() {
            const selectedValue = this.value;
            const selectedText = this.options[this.selectedIndex].text;

            if (selectedValue && !actividadesSeleccionadas.has(selectedValue)) {
                actividadesSeleccionadas.add(selectedValue);
                actividadesIds.push(selectedValue);

                const actividadItem = document.createElement('div');
                actividadItem.classList.add('actividad-item');
                actividadItem.dataset.value = selectedValue;
                actividadItem.innerHTML = `
                    <span class="actividad-texto">${selectedText}</span>
                    <span class="remove-actividad">×</span>
                `;
                actividadesContainer.appendChild(actividadItem);

                actividadItem.querySelector('.remove-actividad').addEventListener('click', function() {
                    actividadesSeleccionadas.delete(selectedValue);
                    actividadesIds = actividadesIds.filter(id => id !== selectedValue);
                    actividadItem.remove();
                    validateActividades();
                    updateActividadesDropdown();
                });

                actividadSelect.value = '';
                updateActividadesDropdown();
            }

            validateActividades();
        });
    }

    function validateActividades() {
        const errorElement = document.querySelector('#formulario__grupo--actividades .formulario__input-error');
        if (errorElement) {
            if (actividadesSeleccionadas.size === 0) {
                errorElement.style.display = 'block';
            } else {
                errorElement.style.display = 'none';
            }
        }
    }

    // Event listener para tipo_persona
    const tipoPersonaSelect = document.getElementById('tipo_persona');
    if (tipoPersonaSelect) {
        tipoPersonaSelect.addEventListener('change', function() {
            const selectedTipo = this.value;
            updateFormVisibility(selectedTipo);
            // Notificar al script principal para actualizar las secciones y el progreso
            if (window.formNavigation && window.formNavigation.updateSectionsByTipoPersona) {
                window.formNavigation.updateSectionsByTipoPersona(selectedTipo);
            }
        });
    }
});
</script>