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
        background-color: #821f3d; /* Darker shade for better hover feedback */
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
                @error('constancia_upload')
                    <p class="formulario__input-error">{{ $message }}</p>
                @enderror
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
                    <input type="text" name="rfc" id="rfc" class="form-control" placeholder="Ej. XAXX010101000" maxlength="13" pattern="[A-Z0-9]{12,13}" value="{{ old('rfc') }}">
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
                    <input type="text" id="razon_social" name="razon_social" class="form-control" maxlength="100" pattern="[A-Za-z\s&.,0-9]+" value="{{ old('razon_social') }}">
                    @error('razon_social')
                        <p class="formulario__input-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="half-width form-group" id="formulario__grupo--correo_electronico">
                    <label class="form-label" for="correo_electronico">Correo Electrónico</label>
                    <input type="email" id="correo_electronico" name="correo_electronico" class="form-control" value="{{ old('correo_electronico') }}">
                    @error('correo_electronico')
                        <p class="formulario__input-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif
        <!-- Objeto Social field, shown only for Moral providers -->
        @if (($tipoPersona ?? '') === 'Moral' || Auth::user()->hasRole('revisor'))
            <div class="form-group full-width" id="formulario__grupo--objeto_social" style="{{ ($tipoPersona ?? '') !== 'Moral' && !Auth::user()->hasRole('revisor') ? 'display: none;' : '' }}">
                <label class="form-label" for="objeto_social">Objeto Social</label>
                <textarea id="objeto_social" name="objeto_social" class="form-control" rows="4" maxlength="500" placeholder="Describa el objeto social de la empresa">{{ old('objeto_social', $datosPrevios['objeto_social'] ?? '') }}</textarea>
                @error('objeto_social')
                    <p class="formulario__input-error">{{ $message }}</p>
                @enderror
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
            <input type="hidden" name="actividades_seleccionadas" id="actividades_seleccionadas_input">
        </div>
        <div class="horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--contacto_telefono">
                <label class="form-label" for="contacto_telefono">Teléfono de Contacto</label>
                <input type="tel" id="contacto_telefono" name="contacto_telefono" class="form-control" pattern="[0-9]{10}" value="{{ old('contacto_telefono') }}">
                @error('contacto_telefono')
                    <p class="formulario__input-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="half-width form-group" id="formulario__grupo--contacto_web">
                <label class="form-label" for="contacto_web">Página Web (opcional)</label>
                <input type="url" id="contacto_web" name="contacto_web" class="form-control" placeholder="https://www.ejemplo.com" value="{{ old('contacto_web') }}">
                @error('contacto_web')
                    <p class="formulario__input-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <h4><i class="fas fa-address-card"></i> Datos de Contacto</h4>
        <span>Persona encargada de recibir solicitudes y requerimientos</span>
        <div class="form-group" id="formulario__grupo--contacto_nombre">
            <label class="form-label" for="contacto_nombre">Nombre Completo</label>
            <input type="text" id="contacto_nombre" name="contacto_nombre" class="form-control" maxlength="40" pattern="[A-Za-z\s]+" value="{{ old('contacto_nombre') }}">
            @error('contacto_nombre')
                <p class="formulario__input-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group" id="formulario__grupo--contacto_cargo">
            <label class="form-label" for="contacto_cargo">Cargo o Puesto</label>
            <input type="text" id="contacto_cargo" name="contacto_cargo" class="form-control" maxlength="50" pattern="[A-Za-z\s]+" value="{{ old('contacto_cargo') }}">
            @error('contacto_cargo')
                <p class="formulario__input-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group" id="formulario__grupo--contacto_correo">
            <label class="form-label" for="contacto_correo">Correo Electrónico</label>
            <input type="email" id="contacto_correo" name="contacto_correo" class="form-control" value="{{ old('contacto_correo') }}">
            @error('contacto_correo')
                <p class="formulario__input-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group" id="formulario__grupo--contacto_telefono_2">
            <label class="form-label" for="contacto_telefono_2">Teléfono de Contacto</label>
            <input type="tel" id="contacto_telefono_2" name="contacto_telefono_2" class="form-control" pattern="[0-9]{10}" value="{{ old('contacto_telefono_2') }}">
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
                <button type="submit" id="btnSiguiente" class="navigation-button {{ $isConfirmationSection ?? false ? 'is-confirmation' : '' }}">
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
        const actividadesSeleccionadasInput = document.getElementById('actividades_seleccionadas_input');
        const tipoPersonaSelect = document.getElementById('tipo_persona');
        const objetoSocialGroup = document.getElementById('formulario__grupo--objeto_social');
        const form = document.getElementById('formulario1');

        // Array to store selected activities
        const selectedActivities = [];

        // Function to update the actividades dropdown
        function updateActividades(actividades) {
            actividadSelect.innerHTML = '<option value="">Seleccione una actividad</option>';
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
                actividadesSeleccionadasInput.value = '';
                return;
            }
            selectedActivities.forEach(activity => {
                const activityElement = document.createElement('div');
                activityElement.className = 'actividad-seleccionada';
                const activityText = document.createElement('span');
                activityText.textContent = activity.name;
                const removeButton = document.createElement('button');
                removeButton.className = 'remove-activity';
                removeButton.innerHTML = '×';
                removeButton.setAttribute('data-id', activity.id);
                removeButton.onclick = function() {
                    removeActivity(activity.id);
                };
                activityElement.appendChild(activityText);
                activityElement.appendChild(removeButton);
                actividadesSeleccionadas.appendChild(activityElement);
            });
            actividadesSeleccionadasInput.value = JSON.stringify(selectedActivities.map(a => a.id));
        }

        // Function to add a selected activity
        function addSelectedActivity() {
            const selectedOption = actividadSelect.options[actividadSelect.selectedIndex];
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
            actividadSelect.value = '';
        }

        // Function to toggle objeto_social visibility
        function toggleObjetoSocial() {
            if (tipoPersonaSelect && objetoSocialGroup) {
                const isMoral = tipoPersonaSelect.value === 'Moral';
                objetoSocialGroup.style.display = isMoral ? 'block' : 'none';
            }
        }

        // Event listener for sector change
        sectorSelect.addEventListener('change', function() {
            const sectorId = this.value;
            if (!sectorId) {
                actividadSelect.innerHTML = '<option value="">Seleccione una actividad</option>';
                selectedActivities.length = 0;
                updateActivityDisplay();
                return;
            }
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
                    actividadSelect.innerHTML = '<option value="">Error al cargar actividades</option>';
                    selectedActivities.length = 0;
                    updateActivityDisplay();
                    return;
                }
                updateActividades(data.actividades);
            })
            .catch(error => {
                console.error('Error:', error);
                actividadSelect.innerHTML = '<option value="">Error al cargar actividades</option>';
                selectedActivities.length = 0;
                updateActivityDisplay();
            });
        });

        // Initial check for objeto_social visibility
        toggleObjetoSocial();

        // Listen for changes in tipo_persona
        if (tipoPersonaSelect) {
            tipoPersonaSelect.addEventListener('change', toggleObjetoSocial);
        }

        // Event listener for activity selection change
        actividadSelect.addEventListener('change', function() {
            if (this.value) {
                addSelectedActivity();
            }
        });

        // Client-side validation for objeto_social
        form.addEventListener('submit', function(e) {
            const tipoPersona = tipoPersonaSelect?.value;
            const objetoSocial = document.getElementById('objeto_social')?.value;
            if (tipoPersona === 'Moral' && (!objetoSocial || objetoSocial.trim() === '')) {
                e.preventDefault();
                alert('El Objeto Social es obligatorio para proveedores Morales.');
                document.getElementById('objeto_social')?.focus();
            }
        });
    });
</script>

<style>
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
</style>