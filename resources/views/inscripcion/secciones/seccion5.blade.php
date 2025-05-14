
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

<form id="formulario5" action="{{ route('inscripcion.procesar') }}" method="POST">
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
        <button type="submit" class="btn btn-primary">Siguiente</button>
    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar estados al cargar la página
    cargarEstados();

    function cargarEstados() {
        const select = document.getElementById('entidad-federativa'); // Cambiado a entidad-federativa
        if (!select) {
            console.error('No se encontró el elemento con ID entidad-federativa');
            return;
        }
        if (select.options.length > 1) {
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
                console.log('Estados cargados exitosamente');
            })
            .catch(error => console.error('Error al cargar estados:', error));
    }
});
</script>