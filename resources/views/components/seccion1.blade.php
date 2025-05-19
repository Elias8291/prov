<div>
    <form id="formulario1" method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}" enctype="multipart/form-data">
        @if(strtoupper($method) !== 'GET')
            @csrf
        @endif

        <!-- Sección para subir Constancia de Situación Fiscal, visible solo para admin -->
        @if(auth()->check() && auth()->user()->hasRole('admin'))
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
                    @if(auth()->check() && auth()->user()->hasRole('solicitante'))
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
                    @if(auth()->check() && auth()->user()->hasRole('solicitante'))
                        <span class="data-field">{{ auth()->user()->rfc ?? 'No disponible' }}</span>
                    @else
                        <input type="text" name="rfc" id="rfc" class="form-control" placeholder="Ej. XAXX010101000" maxlength="13" pattern="[A-Z0-9]{12,13}" value="{{ old('rfc') }}">
                        <p class="formulario__input-error"></p>
                    @endif
                </div>
            </div>
            
            <!-- CURP field, shown for solicitante when tipo_persona is Física -->
            @if($mostrarCurp)
                <div class="form-group" id="curp-field">
                    <label class="form-label data-label">CURP</label>
                    <span class="data-field">{{ $datosPrevios['curp'] ?? 'No disponible' }}</span>
                </div>
            @endif
            
            <!-- Campos visibles solo para revisor -->
            @if($isRevisor)
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
            @if($tipoPersona === 'Moral' || $isRevisor)
                <div class="form-group full-width" id="formulario__grupo--objeto_social">
                    <label class="form-label" for="objeto_social">Objeto Social</label>
                    <textarea id="objeto_social" name="objeto_social" class="form-control" maxlength="500">{{ old('objeto_social') }}</textarea>
                    <p class="formulario__input-error"></p>
                </div>
            @endif
            
            <div class="form-group full-width" id="formulario__grupo--sectores">
                <label class="form-label">Sectores</label>
                <select name="sectores" id="sectores" class="form-control">
                    <option value="">Seleccione un sector</option>
                    @foreach($sectores as $sector)
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
                    @if(!empty($actividadesSeleccionadas))
                        @foreach($actividadesSeleccionadas as $actividad)
                            <div class="actividad-seleccionada">
                                <span>{{ $actividad['nombre'] }}</span>
                                <button class="remove-activity" data-id="{{ $actividad['id'] }}" title="Eliminar actividad">×</button>
                            </div>
                        @endforeach
                    @else
                        <span>Sin actividad seleccionada</span>
                    @endif
                </div>
                <input type="hidden" name="actividades_seleccionadas" id="actividades_seleccionadas_input" 
                       value="{{ json_encode(array_column($actividadesSeleccionadas, 'id')) }}">
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
            
            <div class="form-buttons">
        <button type="button" class="btn btn-secondary" onclick="window.history.back();">Anterior</button>
        <button type="submit" class="btn btn-primary" id="submitForm">Siguiente</button>
    </div>
        </div>
    </form>

    @push('scripts')
    <script>
    $(document).ready(function() {
        // Activity selection and objeto_social logic
        const sectorSelect = $('#sectores');
        const actividadSelect = $('#actividad');
        const actividadesSeleccionadas = $('#actividades-seleccionadas');
        const tipoPersonaSelaect = $('#tipo_persona');
        const objetoSocialGroup = $('#formulario__grupo--objeto_social');
        const selectedActivities = @json($actividadesSeleccionadas ?? []);
        let availableActivities = []; // Store all activities for the selected sector

        function updateActividades() {
            actividadSelect.html('<option value="">Seleccione una actividad</option>');
            const remainingActivities = availableActivities.filter(
                actividad => !selectedActivities.some(selected => selected.id == actividad.id)
            );
            remainingActivities.forEach(actividad => {
                const option = $('<option>', {
                    value: actividad.id,
                    text: actividad.nombre
                });
                actividadSelect.append(option);
            });
        }

        function isActivitySelected(id) {
            return selectedActivities.some(activity => activity.id == id);
        }

        function removeActivity(id) {
            const index = selectedActivities.findIndex(activity => activity.id == id);
            if (index !== -1) {
                selectedActivities.splice(index, 1);
                updateActivityDisplay();
                updateActividades(); // Refresh dropdown to include removed activity
            }
        }

        function updateActivityDisplay() {
            actividadesSeleccionadas.html('');
            if (selectedActivities.length === 0) {
                actividadesSeleccionadas.html('<span>Sin actividad seleccionada</span>');
                $('#actividades_seleccionadas_input').val('');
                return;
            }
            selectedActivities.forEach(activity => {
                const activityElement = $('<div>', { class: 'actividad-seleccionada' });
                const activityText = $('<span>', { text: activity.nombre });
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
            $('#actividades_seleccionadas_input').val(JSON.stringify(selectedActivities.map(a => a.id)));
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
                nombre: activityName
            });
            updateActivityDisplay();
            updateActividades(); // Refresh dropdown to remove selected activity
            actividadSelect.val('');
        }

        function toggleObjetoSocial() {
            if (tipoPersonaSelect.length && objetoSocialGroup.length) {
                const isMoral = tipoPersonaSelect.val() === 'Moral';
                const isRevisor = @json($isRevisor);
                objetoSocialGroup.css('display', isMoral || isRevisor ? 'block' : 'none');
            }
        }

        sectorSelect.on('change', function() {
            const sectorId = this.value;
            selectedActivities.length = 0; // Clear selected activities on sector change
            updateActivityDisplay(); // Update display to reflect cleared activities
            if (!sectorId) {
                actividadSelect.html('<option value="">Seleccione una actividad</option>');
                availableActivities = [];
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
                        availableActivities = [];
                        return;
                    }
                    availableActivities = data.actividades;
                    updateActividades();
                },
                error: function(error) {
                    console.error('Error:', error);
                    actividadSelect.html('<option value="">Error al cargar actividades</option>');
                    availableActivities = [];
                }
            });
        });

        actividadSelect.on('change', function() {
            if (this.value) {
                addSelectedActivity();
            }
        });

        // Initialize objeto_social visibility
        toggleObjetoSocial();
        tipoPersonaSelect.on('change', toggleObjetoSocial);

        // Handle remove activity buttons for pre-selected activities
        $(document).on('click', '.remove-activity', function() {
            const activityId = $(this).data('id');
            removeActivity(activityId);
        });

        // If we have a pre-selected sector, load its activities
        @if(old('sectores'))
            sectorSelect.val(@json(old('sectores'))).trigger('change');
        @endif
    });
    </script>
    @endpush
</div>