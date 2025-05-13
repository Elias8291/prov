<form id="formulario1" method="POST" action="{{ route('inscripcion.procesar') }}" enctype="multipart/form-data">
    @csrf
    <!-- Sección para subir Constancia de Situación Fiscal, visible solo para admin -->
    @if (auth()->check() && auth()->user()->hasRole('revisor'))
        <div class="form-section" id="constancia-upload-section">
            <h4><i class="fas fa-file-pdf"></i> Subir Constancia de Situación Fiscal</h4>
            <div class="form-group full-width" id="formulario__grupo--constancia">
                <label class="form-label" for="constancia_upload">
                    <span>Seleccionar Constancia de Situación Fiscal</span>
                    <span class="file-desc">Formato PDF, máximo 5MB</span>
                </label>
                <input type="file" id="constancia_upload" name="constancia_upload" class="form-control" accept="application/pdf">
                @error('constancia_upload')
                    <p class="formulario__input-error">{{ $message }}</p>
                @enderror
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
                    <span class="data-field">{{ $tipoPersona ?? 'No disponible' }}</span>
                @else
                    <select name="tipo_persona" id="tipo_persona" class="form-control">
                        <option value="">Seleccione un tipo</option>
                        <option value="Física">Física</option>
                        <option value="Moral">Moral</option>
                    </select>
                    @error('tipo_persona')
                        <p class="formulario__input-error">{{ $message }}</p>
                    @enderror
                @endif
            </div>
            <div class="half-width">
                <label class="form-label data-label">RFC</label>
                @if (Auth::user()->hasRole('solicitante'))
                    <span class="data-field">{{ Auth::user()->rfc ?? 'No disponible' }}</span>
                @else
                    <input type="text" name="rfc" id="rfc" class="form-control" placeholder="Ej. XAXX010101000" maxlength="13" pattern="[A-Z0-9]{12,13}">
                    @error('rfc')
                        <p class="formulario__input-error">{{ $message }}</p>
                    @enderror
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
                    <input type="text" id="razon_social" name="razon_social" class="form-control" maxlength="100" pattern="[A-Za-z\s&.,0-9]+">
                    @error('razon_social')
                        <p class="formulario__input-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="half-width form-group" id="formulario__grupo--correo_electronico">
                    <label class="form-label" for="correo_electronico">Correo Electrónico</label>
                    <input type="email" id="correo_electronico" name="correo_electronico" class="form-control">
                    @error('correo_electronico')
                        <p class="formulario__input-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif
        <div class="form-group full-width" id="formulario__grupo--sectores">
            <label class="form-label">Sectores</label>
            <select name="sectores" id="sectores" class="form-control">
                <option value="">Seleccione un sector</option>
                @foreach ($sectores as $sector)
                    <option value="{{ $sector['id'] }}">{{ $sector['nombre'] }}</option>
                @endforeach
            </select>
            @error('sectores')
                <p class="formulario__input-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group full-width" id="formulario__grupo--actividades">
            <label class="form-label">Actividades</label>
            <select name="actividad" id="actividad" class="form-control">
                <option value="">Seleccione una actividad</option>
            </select>
            @error('actividad')
                <p class="formulario__input-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group full-width" id="actividades-seleccionadas-container">
            <label class="form-label">Actividades Seleccionadas</label>
            <div id="actividades-seleccionadas" class="actividades-container">
                <!-- Actividades seleccionadas se añadirán aquí dinámicamente -->
            </div>
        </div>
        <div class="horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--contacto_telefono">
                <label class="form-label" for="contacto_telefono">Teléfono de Contacto</label>
                <input type="tel" id="contacto_telefono" name="contacto_telefono" class="form-control" pattern="[0-9]{10}">
                @error('contacto_telefono')
                    <p class="formulario__input-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="half-width form-group" id="formulario__grupo--contacto_web">
                <label class="form-label" for="contacto_web">Página Web (opcional)</label>
                <input type="url" id="contacto_web" name="contacto_web" class="form-control" placeholder="https://www.ejemplo.com">
                @error('contacto_web')
                    <p class="formulario__input-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <h4><i class="fas fa-address-card"></i> Datos de Contacto</h4>
        <span>Persona encargada de recibir solicitudes y requerimientos</span>
        <div class="form-group" id="formulario__grupo--contacto_nombre">
            <label class="form-label" for="contacto_nombre">Nombre Completo</label>
            <input type="text" id="contacto_nombre" name="contacto_nombre" class="form-control" maxlength="40" pattern="[A-Za-z\s]+">
            @error('contacto_nombre')
                <p class="formulario__input-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group" id="formulario__grupo--contacto_cargo">
            <label class="form-label" for="contacto_cargo">Cargo o Puesto</label>
            <input type="text" id="contacto_cargo" name="contacto_cargo" class="form-control" maxlength="50" pattern="[A-Za-z\s]+">
            @error('contacto_cargo')
                <p class="formulario__input-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group" id="formulario__grupo--contacto_correo">
            <label class="form-label" for="contacto_correo">Correo Electrónico</label>
            <input type="email" id="contacto_correo" name="contacto_correo" class="form-control">
            @error('contacto_correo')
                <p class="formulario__input-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group" id="formulario__grupo--contacto_telefono_2">
            <label class="form-label" for="contacto_telefono_2">Teléfono de Contacto</label>
            <input type="tel" id="contacto_telefono_2" name="contacto_telefono_2" class="form-control" pattern="[0-9]{10}">
            @error('contacto_telefono_2')
                <p class="formulario__input-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group form-actions">
            <div class="button-group">
                @if ($seccion > 1)
                    <button type="submit" name="retroceder" value="1" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Anterior
                    </button>
                @endif

                <button type="submit" id="btnSiguiente" class="navigation-button">
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sectorSelect = document.getElementById('sectores');
        const actividadSelect = document.getElementById('actividad');
        const actividadesSeleccionadas = document.getElementById('actividades-seleccionadas');

        // Array to store selected activities
        const selectedActivities = [];

        // Function to update the actividades dropdown
        function updateActividades(actividades) {
            // Clear current options
            actividadSelect.innerHTML = '<option value="">Seleccione una actividad</option>';

            // Add new options
            actividades.forEach(actividad => {
                const option = document.createElement('option');
                option.value = actividad.id;
                option.textContent = actividad.nombre;
                actividadSelect.appendChild(option);
            });
        }

        // Function to check if an activity is already selected
        function isActivitySelected(id) {
            return selectedActivities.some(activity => activity.id === id);
        }

        // Function to remove an activity
        function removeActivity(id) {
            const index = selectedActivities.findIndex(activity => activity.id === id);
            if (index !== -1) {
                selectedActivities.splice(index, 1);
                updateActivityDisplay();
            }
        }

        // Function to update the display of selected activities
        function updateActivityDisplay() {
            actividadesSeleccionadas.innerHTML = '';

            if (selectedActivities.length === 0) {
                actividadesSeleccionadas.innerHTML = '<span>Sin actividad seleccionada</span>';
                return;
            }

            selectedActivities.forEach(activity => {
                const activityElement = document.createElement('div');
                activityElement.className = 'actividad-seleccionada';

                const activityText = document.createElement('span');
                activityText.textContent = activity.name;

                const removeButton = document.createElement('button');
                removeButton.className = 'remove-activity';
                removeButton.innerHTML = '&times;';
                removeButton.setAttribute('data-id', activity.id);
                removeButton.onclick = function() {
                    removeActivity(activity.id);
                };

                activityElement.appendChild(activityText);
                activityElement.appendChild(removeButton);
                actividadesSeleccionadas.appendChild(activityElement);
            });
        }

        // Function to add a selected activity
        function addSelectedActivity() {
            const selectedOption = actividadSelect.options[actividadSelect.selectedIndex];
            if (!selectedOption || selectedOption.value === '') {
                return;
            }

            const activityId = selectedOption.value;
            const activityName = selectedOption.textContent;

            // Check if already selected
            if (isActivitySelected(activityId)) {
                return; // Already selected, don't add again
            }

            // Add to selected activities
            selectedActivities.push({
                id: activityId,
                name: activityName
            });

            // Update the display
            updateActivityDisplay();

            // Reset the select to the default option
            actividadSelect.value = '';
        }

        // Event listener for sector change
        sectorSelect.addEventListener('change', function() {
            const sectorId = this.value;

            if (!sectorId) {
                actividadSelect.innerHTML = '<option value="">Seleccione una actividad</option>';
                selectedActivities.length = 0; // Clear selected activities
                updateActivityDisplay();
                return;
            }

            // Make AJAX request to fetch activities
            fetch(`/inscripcion/actividades?sector_id=${sectorId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        actividadSelect.innerHTML =
                            '<option value="">Error al cargar actividades</option>';
                        selectedActivities.length = 0; // Clear selected activities
                        updateActivityDisplay();
                        return;
                    }
                    updateActividades(data.actividades);
                })
                .catch(error => {
                    console.error('Error:', error);
                    actividadSelect.innerHTML =
                        '<option value="">Error al cargar actividades</option>';
                    selectedActivities.length = 0; // Clear selected activities
                    updateActivityDisplay();
                });
        });

        // Event listener for activity selection change
        actividadSelect.addEventListener('change', function() {
            if (this.value) {
                addSelectedActivity();
            }
        });
    });
</script>
