<div>
    <form id="formulario3" method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}" enctype="multipart/form-data">
        @if(strtoupper($method) !== 'GET')
            @csrf
        @endif
        <input type="hidden" name="action" value="next">
        <input type="hidden" name="seccion" value="3">
        
        <div class="form-section" id="form-step-3">
            <h4><i class="fas fa-building"></i> Datos de Constitución (Persona Moral)</h4>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--numero_escritura">
                    <label class="form-label" for="numero_escritura">Número de Escritura</label>
                    <input type="text" id="numero_escritura" name="numero_escritura" class="form-control"
                        placeholder="Ej: 1234 o 1234/2024" maxlength="15" value="{{ old('numero_escritura', $datosPrevios['numero_escritura'] ?? '') }}">
                    <p class="formulario__input-error">Debe contener de 1 a 10 dígitos, opcionalmente seguido de / y un año de 4 dígitos (ej: 1234, 1234/2024).</p>
                </div>
                <div class="half-width form-group" id="formulario__grupo--nombre_notario">
                    <label class="form-label" for="nombre_notario">Nombre del Notario</label>
                    <input type="text" id="nombre_notario" name="nombre_notario" class="form-control"
                        placeholder="Ej: Lic. Juan Pérez González" maxlength="100" value="{{ old('nombre_notario', $datosPrevios['nombre_notario'] ?? '') }}">
                    <p class="formulario__input-error">El nombre del notario debe contener solo letras y espacios (máx. 100 caracteres).</p>
                </div>
            </div>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--entidad_federativa">
                    <label class="form-label" for="entidad_federativa">Entidad Federativa</label>
                    <select id="entidad_federativa" name="entidad_federativa" class="form-control" required>
                        <option value="">Seleccione un estado</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado['id'] }}" {{ old('entidad_federativa', $datosPrevios['entidad_federativa'] ?? '') == $estado['id'] ? 'selected' : '' }}>
                                {{ $estado['nombre'] }}
                            </option>
                        @endforeach
                    </select>
                    <p class="formulario__input-error">Por favor, seleccione una entidad federativa.</p>
                </div>
                <div class="half-width form-group" id="formulario__grupo--fecha_constitucion">
                    <label class="form-label" for="fecha_constitucion">Fecha de Constitución</label>
                    <input type="date" id="fecha_constitucion" name="fecha_constitucion" class="form-control" required
                        value="{{ old('fecha_constitucion', $datosPrevios['fecha_constitucion'] ?? '') }}">
                    <p class="formulario__input-error">Por favor, seleccione una fecha válida (no futura).</p>
                </div>
            </div>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--numero_notario">
                    <label class="form-label" for="numero_notario">Número de Notario</label>
                    <input type="text" id="numero_notario" name="numero_notario" class="form-control"
                        placeholder="Ej: 123" maxlength="10" value="{{ old('numero_notario', $datosPrevios['numero_notario'] ?? '') }}">
                    <p class="formulario__input-error">El número de notario debe contener solo números (máx. 10 dígitos).</p>
                </div>
                <div class="half-width"></div> <!-- Espacio vacío para mantener el diseño -->
            </div>
            
            <h4><i class="fas fa-file-contract"></i> Datos de Inscripción en el Registro Público</h4>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--numero_registro">
                    <label class="form-label" for="numero_registro">Número de Registro o Folio Mercantil</label>
                    <input type="text" id="numero_registro" name="numero_registro" class="form-control"
                        placeholder="Ej: 0123456789 o FME123456789" maxlength="14" value="{{ old('numero_registro', $datosPrevios['numero_registro'] ?? '') }}">
                    <p class="formulario__input-error">Debe contener de 9 a 14 caracteres: 0 a 3 letras iniciales seguidas de 6 a 14 dígitos (ej: 0123456789, FME123456789).</p>
                </div>
                <div class="half-width form-group" id="formulario__grupo--fecha_inscripcion">
                    <label class="form-label" for="fecha_inscripcion">Fecha de Inscripción</label>
                    <input type="date" id="fecha_inscripcion" name="fecha_inscripcion" class="form-control" required
                        value="{{ old('fecha_inscripcion', $datosPrevios['fecha_inscripcion'] ?? '') }}">
                    <p class="formulario__input-error">Por favor, seleccione una fecha válida (no anterior a la fecha de constitución).</p>
                </div>
            </div>
            <div id="form-errors" class="alert-danger" style="display: none;"></div>
        </div>
          <div class="form-buttons">
                <button type="button" class="btn btn-secondary" onclick="window.history.back();">Anterior</button>
                <button type="submit" class="btn btn-primary" id="submitForm">Siguiente</button>
            </div>
      
    </form>
</div>