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
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            width: 100%;
        }
    }
</style>
<form action="{{ route('inscripcion.procesar', ['seccion' => 3]) }}" method="POST" id="formulario3">
    @csrf
    <div class="form-section" id="form-step-3">
        <input type="hidden" name="seccion" value="3">
        <h4><i class="fas fa-building"></i> Datos de Constitución (Persona Moral)</h4>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--numero_escritura">
                <label class="form-label" for="numero_escritura">Número de Escritura</label>
                <input type="text" id="numero_escritura" name="numero_escritura" class="form-control"
                    placeholder="Ej: 12345" value="{{ $datosPrevios['numero_escritura'] ?? '' }}">
                <p class="formulario__input-error">El número de escritura debe contener solo números (máx. 10 dígitos).
                </p>
            </div>
            <div class="half-width form-group" id="formulario__grupo--nombre_notario">
                <label class="form-label" for="nombre_notario">Nombre del Notario</label>
                <input type="text" id="nombre_notario" name="nombre_notario" class="form-control"
                    placeholder="Ej: Lic. Juan Pérez González" value="{{ $datosPrevios['nombre_notario'] ?? '' }}">
                <p class="formulario__input-error">El nombre del notario debe contener solo letras y espacios (máx. 100
                    caracteres).</p>
            </div>
        </div>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--entidad_federativa">
                <label class="form-label" for="entidad_federativa">Entidad Federativa</label>
                <select id="entidad_federativa" name="entidad_federativa" class="form-control">
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
                <input type="date" id="fecha_constitucion" name="fecha_constitucion" class="form-control"
                    value="{{ $datosPrevios['fecha_constitucion'] ?? '' }}">
                <p class="formulario__input-error">Por favor, seleccione una fecha válida.</p>
            </div>
        </div>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--numero_notario">
                <label class="form-label" for="numero_notario">Número de Notario</label>
                <input type="text" id="numero_notario" name="numero_notario" class="form-control"
                    placeholder="Ej: 123" value="{{ $datosPrevios['numero_notario'] ?? '' }}">
                <p class="formulario__input-error">El número de notario debe contener solo números (máx. 10 dígitos).
                </p>
            </div>
            <div class="half-width"></div> <!-- Espacio vacío para mantener el diseño -->
        </div>
        <h4><i class="fas fa-file-contract"></i> Datos de Inscripción en el Registro Público</h4>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--numero_registro">
                <label class="form-label" for="numero_registro">Número de Registro o Folio Mercantil</label>
                <input type="text" id="numero_registro" name="numero_registro" class="form-control"
                    placeholder="Ej: 987654" value="{{ $datosPrevios['numero_registro'] ?? '' }}">
                <p class="formulario__input-error">El número de registro debe contener solo números (máx. 10 dígitos).
                </p>
            </div>
            <div class="half-width form-group" id="formulario__grupo--fecha_inscripcion">
                <label class="form-label" for="fecha_inscripcion">Fecha de Inscripción</label>
                <input type="date" id="fecha_inscripcion" name="fecha_inscripcion" class="form-control"
                    max="" value="{{ $datosPrevios['fecha_inscripcion'] ?? '' }}">
                <p class="formulario__input-error">Por favor, seleccione una fecha válida.</p>
            </div>
        </div>
    </div>
    <div class="form-buttons">
        <button type="button" class="btn btn-secondary" onclick="window.history.back();">Anterior</button>
        <button type="submit" class="btn btn-primary">Siguiente</button>
    </div>
</form>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set max date for date inputs to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('fecha_constitucion').max = today;
        document.getElementById('fecha_inscripcion').max = today;

        // Set fecha_constitucion change listener to update min date for fecha_inscripcion
        document.getElementById('fecha_constitucion').addEventListener('change', function() {
            const fechaConstitucion = this.value;
            if (fechaConstitucion) {
                document.getElementById('fecha_inscripcion').min = fechaConstitucion;
            }
        });

        // Trigger change event on fecha_constitucion to set initial min date for fecha_inscripcion
        const fechaConstitucion = document.getElementById('fecha_constitucion');
        if (fechaConstitucion.value) {
            document.getElementById('fecha_inscripcion').min = fechaConstitucion.value;
        }

        // Form validation is handled by the main form in formularios.blade.php
        console.log('Formulario de datos de constitución cargado correctamente');
    });
</script>