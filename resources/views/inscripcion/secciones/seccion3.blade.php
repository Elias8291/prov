<form id="formulario3">
    <div class="form-section" id="form-step-3">
        <h4><i class="fas fa-building"></i> Datos de Constitución (Persona Moral)</h4>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--numero_escritura">
                <label class="form-label" for="numero_escritura">Número de Escritura</label>
                <input type="text" id="numero_escritura" name="numero_escritura" class="form-control"
                    placeholder="Ej: 12345">
                <p class="formulario__input-error">El número de escritura debe contener solo números (máx. 10 dígitos).</p>
            </div>
            <div class="half-width form-group" id="formulario__grupo--nombre_notario">
                <label class="form-label" for="nombre_notario">Nombre del Notario</label>
                <input type="text" id="nombre_notario" name="nombre_notario" class="form-control"
                    placeholder="Ej: Lic. Juan Pérez González">
                <p class="formulario__input-error">El nombre del notario debe contener solo letras y espacios (máx. 100 caracteres).</p>
            </div>
        </div>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--entidad_federativa">
                <label class="form-label" for="entidad_federativa">Entidad Federativa</label>
                <select id="entidad_federativa" name="entidad_federativa" class="form-control">
                    <option value="">Seleccione un estado</option>
                </select>
                <p class="formulario__input-error">Por favor, seleccione una entidad federativa.</p>
            </div>
            <div class="half-width form-group" id="formulario__grupo--fecha_constitucion">
                <label class="form-label" for="fecha_constitucion">Fecha de Constitución</label>
                <input type="date" id="fecha_constitucion" name="fecha_constitucion" class="form-control">
                <p class="formulario__input-error">Por favor, seleccione una fecha válida.</p>
            </div>
        </div>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--numero_notario">
                <label class="form-label" for="numero_notario">Número de Notario</label>
                <input type="text" id="numero_notario" name="numero_notario" class="form-control"
                    placeholder="Ej: 123">
                <p class="formulario__input-error">El número de notario debe contener solo números (máx. 10 dígitos).</p>
            </div>
            <div class="half-width"></div> <!-- Espacio vacío para mantener el diseño -->
        </div>
        <h4><i class="fas fa-file-contract"></i> Datos de Inscripción en el Registro Público</h4>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--numero_registro">
                <label class="form-label" for="numero_registro">Número de Registro o Folio Mercantil</label>
                <input type="text" id="numero_registro" name="numero_registro" class="form-control"
                    placeholder="Ej: 987654">
                <p class="formulario__input-error">El número de registro debe contener solo números (máx. 10 dígitos).</p>
            </div>
            <div class="half-width form-group" id="formulario__grupo--fecha_inscripcion">
                <label class="form-label" for="fecha_inscripcion">Fecha de Inscripción</label>
                <input type="date" id="fecha_inscripcion" name="fecha_inscripcion" class="form-control" max="">
                <p class="formulario__input-error">Por favor, seleccione una fecha válida.</p>
            </div>            
        </div>
    </div>
</form>
<script>

    document.addEventListener('DOMContentLoaded', function() {
        // Cargar estados cuando se muestre la sección 3
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.target.id === 'seccion3' && mutation.target.style.display ===
                    'block') {
                    cargarEstados();
                }
            });
        });

        observer.observe(document.getElementById('seccion3'), {
            attributes: true,
            attributeFilter: ['style']
        });

        function cargarEstados() {
            if (document.getElementById('entidad_federativa').options.length > 1) {
                return; // Ya están cargados
            }

            fetch('/estados')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('entidad_federativa');
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
                })
                .catch(error => console.error('Error al cargar estados:', error));
        }
    });
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('fecha_inscripcion').setAttribute('max', today);
    document.getElementById('fecha_constitucion').setAttribute('max', today);
</script>
