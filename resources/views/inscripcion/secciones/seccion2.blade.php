<form id="formulario2">
    <div class="form-section" id="form-step-2">
        <h4><i class="fas fa-map-marker-alt"></i> Domicilio</h4>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--codigo_postal">
                <label class="form-label data-label">Código Postal</label>
                @if (Auth::user()->hasRole('solicitante'))
                    <span class="data-field" id="codigo_postal_display">{{ Auth::user()->solicitante->direccion->codigo_postal ?? 'No disponible' }}</span>
                    <input type="hidden" id="codigo_postal" name="codigo_postal" value="{{ Auth::user()->solicitante->direccion->codigo_postal ?? '' }}">
                @else
                    <input type="text" id="codigo_postal" name="codigo_postal" class="form-control" placeholder="Ej: 12345" required pattern="[0-9]{5}" maxlength="5" value="">
                    <p class="formulario__input-error">El código postal debe contener exactamente 5 dígitos numéricos.</p>
                @endif
            </div>
            <div class="half-width form-group" id="formulario__grupo--estado">
                <label class="form-label data-label">Estado</label>
                @if (Auth::user()->hasRole('solicitante'))
                    <span class="data-field" id="estado_display">{{ Auth::user()->solicitante->direccion->estado ?? 'No disponible' }}</span>
                    <input type="hidden" id="estado" name="estado" value="{{ Auth::user()->solicitante->direccion->estado ?? '' }}">
                @else
                    <input type="text" id="estado" name="estado" class="form-control" placeholder="Ej: Jalisco" readonly value="">
                    <p class="formulario__input-error">El estado debe contener solo letras y espacios, máximo 100 caracteres.</p>
                @endif
            </div>
        </div>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--municipio">
                <label class="form-label data-label">Municipio</label>
                @if (Auth::user()->hasRole('solicitante'))
                    <span class="data-field" id="municipio_display">{{ Auth::user()->solicitante->direccion->municipio ?? 'No disponible' }}</span>
                    <input type="hidden" id="municipio" name="municipio" value="{{ Auth::user()->solicitante->direccion->municipio ?? '' }}">
                @else
                    <input type="text" id="municipio" name="municipio" class="form-control" placeholder="Ej: Guadalajara" readonly value="">
                    <p class="formulario__input-error">El municipio debe contener solo letras y espacios, máximo 100 caracteres.</p>
                @endif
            </div>
            <div class="half-width form-group" id="formulario__grupo--colonia">
                <label class="form-label" for="colonia">Asentamiento</label>
                <select id="colonia" name="colonia" class="form-control" required>
                    <option value="">Seleccione un Asentamiento</option>
                </select>
                <p class="formulario__input-error">Debe seleccionar un asentamiento.</p>
            </div>
        </div>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--calle">
                <label class="form-label" for="calle">Calle</label>
                <input type="text" id="calle" name="calle" class="form-control" placeholder="Ej: Av. Principal" required maxlength="100" pattern="[A-Za-z0-9\s]+" value="{{ Auth::user()->solicitante->calle ?? '' }}">
                <p class="formulario__input-error">La calle debe contener letras, números o espacios, máximo 100 caracteres.</p>
            </div>
            <div class="half-width form-group" id="formulario__grupo--numero_exterior">
                <label class="form-label" for="numero_exterior">Número Exterior</label>
                <input type="text" id="numero_exterior" name="numero_exterior" class="form-control" placeholder="Ej: 123" required maxlength="10" pattern="[A-Za-z0-9]+" value="{{ Auth::user()->solicitante->numero_exterior ?? '' }}">
                <p class="formulario__input-error">El número exterior debe contener letras o números, máximo 10 caracteres.</p>
            </div>
        </div>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--numero_interior">
                <label class="form-label" for="numero_interior">Número Interior</label>
                <input type="text" id="numero_interior" name="numero_interior" class="form-control" placeholder="Ej: 5A" maxlength="10" pattern="[A-Za-z0-9]+" value="{{ Auth::user()->solicitante->numero_interior ?? '' }}">
                <p class="formulario__input-error">El número interior debe contener letras o números, máximo 10 caracteres, o dejar en blanco.</p>
            </div>
            <div class="half-width form-group" id="formulario__grupo--entre_calle_1">
                <label class="form-label" for="entre_calle_1">Entre Calle 1</label>
                <input type="text" id="entre_calle_1" name="entre_calle_1" class="form-control" placeholder="Ej: Calle Independencia" maxlength="100" pattern="[A-Za-z0-9\s]+" value="{{ Auth::user()->solicitante->entre_calle_1 ?? '' }}">
                <p class="formulario__input-error">Entre calle 1 debe contener letras, números o espacios, máximo 100 caracteres, o dejar en blanco.</p>
            </div>
        </div>
        <div class="form-group" id="formulario__grupo--entre_calle_2">
            <label class="form-label" for="entre_calle_2">Entre Calle 2</label>
            <input type="text" id="entre_calle_2" name="entre_calle_2" class="form-control" placeholder="Ej: Calle Morelos" maxlength="100" pattern="[A-Za-z0-9\s]+" value="{{ Auth::user()->solicitante->entre_calle_2 ?? '' }}">
            <p class="formulario__input-error">Entre calle 2 debe contener letras, números o espacios, máximo 100 caracteres, o dejar en blanco.</p>
        </div>
    </div>
</form>
<script>
 document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const isSolicitante = @json(Auth::user()->hasRole('solicitante'));

    // Function to populate fields with data
    function populateAddressFields(data) {
        // Populate estado
        document.getElementById('estado').value = data.estado || '';
        if (isSolicitante) {
            document.getElementById('estado_display').textContent = data.estado || 'No disponible';
        }

        // Populate municipio
        document.getElementById('municipio').value = data.municipio || '';
        if (isSolicitante) {
            document.getElementById('municipio_display').textContent = data.municipio || 'No disponible';
        }

        // Populate asentamientos dropdown
        const coloniaSelect = document.getElementById('colonia');
        coloniaSelect.innerHTML = '<option value="">Seleccione un Asentamiento</option>';
        
        if (data.asentamientos && data.asentamientos.length > 0) {
            data.asentamientos.forEach(asentamiento => {
                const option = document.createElement('option');
                option.value = asentamiento.id;
                option.textContent = `${asentamiento.nombre} (${asentamiento.tipo_asentamiento})`;
                coloniaSelect.appendChild(option);
            });
        }
    }

    // Function to reset fields on error or invalid input
    function resetAddressFields() {
        document.getElementById('estado').value = '';
        document.getElementById('municipio').value = '';
        if (isSolicitante) {
            document.getElementById('estado_display').textContent = 'No disponible';
            document.getElementById('municipio_display').textContent = 'No disponible';
        }
        const coloniaSelect = document.getElementById('colonia');
        coloniaSelect.innerHTML = '<option value="">Seleccione un Asentamiento</option>';
    }

    if (isSolicitante) {
        // Existing logic for solicitante
        fetch('/solicitante/direccion-data', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error('Error from server:', data.error);
                resetAddressFields();
                return;
            }

            // Populate postal code
            document.getElementById('codigo_postal_display').textContent = data.codigo_postal || 'No disponible';
            document.getElementById('codigo_postal').value = data.codigo_postal || '';

            populateAddressFields(data);
        })
        .catch(error => {
            console.error('Error al cargar datos de dirección:', error);
            resetAddressFields();
        });
    } else {
        // Logic for revisor_1
        const codigoPostalInput = document.getElementById('codigo_postal');
        let debounceTimer;
        
        codigoPostalInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const codigoPostal = this.value.trim();

            // Only trigger search if exactly 5 digits
            if (codigoPostal.length === 5 && /^\d{5}$/.test(codigoPostal)) {
                debounceTimer = setTimeout(() => {
                    fetch(`/api/codigo-postal/${codigoPostal}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            console.error('Error from server:', data.error);
                            resetAddressFields();
                            return;
                        }

                        populateAddressFields(data);
                    })
                    .catch(error => {
                        console.error('Error al buscar datos de dirección:', error);
                        resetAddressFields();
                    });
                }, 500); // Debounce de 500ms
            } else {
                // Reset fields if input is not 5 digits
                resetAddressFields();
            }
        });
    }
});
    </script>