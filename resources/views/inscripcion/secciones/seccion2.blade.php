
<style>/* Main Button Styling */
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
  background-color: #9d2449;;
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
} </style>
<form id="formulario2" action="{{ route('inscripcion.procesar_seccion') }}" method="POST">
    <div class="form-section" id="form-step-2">
        <h4><i class="fas fa-map-marker-alt"></i> Domicilio</h4>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--codigo_postal">
                <label class="form-label data-label">Código Postal</label>
                @if (Auth::user()->hasRole('solicitante') && isset($direccion) && $direccion->codigo_postal)
                    <span class="data-field" id="codigo_postal_display">{{ str_pad($direccion->codigo_postal, 5, '0', STR_PAD_LEFT) }}</span>
                    <input type="hidden" id="codigo_postal" name="codigo_postal" value="{{ str_pad($direccion->codigo_postal, 5, '0', STR_PAD_LEFT) }}">
                @else
                    <input type="text" id="codigo_postal" name="codigo_postal" class="form-control" 
                           placeholder="Ej: 12345" required pattern="[0-9]{4,5}" maxlength="5" 
                           value="{{ old('codigo_postal', str_pad($datosPrevios['codigo_postal'] ?? '', 5, '0', STR_PAD_LEFT)) }}">
                    <p class="formulario__input-error">El código postal debe contener 4 o 5 dígitos numéricos.</p>
                @endif
            </div>
            <div class="half-width form-group" id="formulario__grupo--estado">
                <label class="form-label data-label">Estado</label>
                @if (Auth::user()->hasRole('solicitante') && isset($datosPrevios['estado']))
                    <span class="data-field" id="estado_display">{{ $datosPrevios['estado'] }}</span>
                    <input type="hidden" id="estado" name="estado" value="{{ $datosPrevios['estado'] }}">
                @else
                    <input type="text" id="estado" name="estado" class="form-control" 
                           placeholder="Ej: Jalisco" readonly 
                           value="{{ old('estado', $datosPrevios['estado'] ?? '') }}">
                    <p class="formulario__input-error">El estado debe contener solo letras y espacios, máximo 100 caracteres.</p>
                @endif
            </div>
        </div>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--municipio">
                <label class="form-label data-label">Municipio</label>
                @if (Auth::user()->hasRole('solicitante') && isset($datosPrevios['municipio']))
                    <span class="data-field" id="municipio_display">{{ $datosPrevios['municipio'] }}</span>
                    <input type="hidden" id="municipio" name="municipio" value="{{ $datosPrevios['municipio'] }}">
                @else
                    <input type="text" id="municipio" name="municipio" class="form-control" 
                           placeholder="Ej: Guadalajara" readonly 
                           value="{{ old('municipio', $datosPrevios['municipio'] ?? '') }}">
                    <p class="formulario__input-error">El municipio debe contener solo letras y espacios, máximo 100 caracteres.</p>
                @endif
            </div>
            <div class="half-width form-group" id="formulario__grupo--colonia">
                <label class="form-label" for="colonia">Asentamiento</label>
                <select id="colonia" name="colonia" class="form-control" required>
                    <option value="">Seleccione un Asentamiento</option>
                    @if (isset($datosPrevios['colonia']))
                        <option value="{{ $datosPrevios['colonia'] }}" selected>{{ $datosPrevios['colonia'] }}</option>
                    @endif
                </select>
                <p class="formulario__input-error">Debe seleccionar un asentamiento.</p>
            </div>
        </div>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--calle">
                <label class="form-label" for="calle">Calle</label>
                <input type="text" id="calle" name="calle" class="form-control" 
                       placeholder="Ej: Av. Principal" required maxlength="100" pattern="[A-Za-z0-9\s]+" 
                       value="{{ old('calle', $datosPrevios['calle'] ?? '') }}">
                <p class="formulario__input-error">La calle debe contener letras, números o espacios, máximo 100 caracteres.</p>
            </div>
            <div class="half-width form-group" id="formulario__grupo--numero_exterior">
                <label class="form-label" for="numero_exterior">Número Exterior</label>
                <input type="text" id="numero_exterior" name="numero_exterior" class="form-control" 
                       placeholder="Ej: 123" required maxlength="10" pattern="[A-Za-z0-9]+" 
                       value="{{ old('numero_exterior', $datosPrevios['numero_exterior'] ?? '') }}">
                <p class="formulario__input-error">El número exterior debe contener letras o números, máximo 10 caracteres.</p>
            </div>
        </div>
        <div class="form-group horizontal-group">
            <div class="half-width form-group" id="formulario__grupo--numero_interior">
                <label class="form-label" for="numero_interior">Número Interior</label>
                <input type="text" id="numero_interior" name="numero_interior" class="form-control" 
                       placeholder="Ej: 5A" maxlength="10" pattern="[A-Za-z0-9]+" 
                       value="{{ old('numero_interior', $datosPrevios['numero_interior'] ?? '') }}">
                <p class="formulario__input-error">El número interior debe contener letras o números, máximo 10 caracteres, o dejar en blanco.</p>
            </div>
            <div class="half-width form-group" id="formulario__grupo--entre_calle_1">
                <label class="form-label" for="entre_calle_1">Entre Calle 1</label>
                <input type="text" id="entre_calle_1" name="entre_calle_1" class="form-control" 
                       placeholder="Ej: Calle Independencia" maxlength="100" pattern="[A-Za-z0-9\s]+" 
                       value="{{ old('entre_calle_1', $datosPrevios['entre_calle_1'] ?? '') }}">
                <p class="formulario__input-error">Entre calle 1 debe contener letras, números o espacios, máximo 100 caracteres, o dejar en blanco.</p>
            </div>
        </div>
        <div class="form-group" id="formulario__grupo--entre_calle_2">
            <label class="form-label" for="entre_calle_2">Entre Calle 2</label>
            <input type="text" id="entre_calle_2" name="entre_calle_2" class="form-control" 
                   placeholder="Ej: Calle Morelos" maxlength="100" pattern="[A-Za-z0-9\s]+" 
                   value="{{ old('entre_calle_2', $datosPrevios['entre_calle_2'] ?? '') }}">
            <p class="formulario__input-error">Entre calle 2 debe contener letras, números o espacios, máximo 100 caracteres, o dejar en blanco.</p>
        </div>
    </div>
    <div class="form-buttons">
        <button type="button" class="btn btn-secondary" onclick="window.history.back();">Anterior</button>
        <button type="submit" class="btn btn-primary">Siguiente</button>
    </div>
</form>

<!-- Include jQuery (if not already included) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Listen for changes to the codigo_postal input
    $('#codigo_postal').on('input', function() {
        var codigoPostal = $(this).val();
        console.log('Código postal changed:', codigoPostal);

        // Only proceed if the postal code is 4 or 5 digits
        if (codigoPostal.length >= 4 && codigoPostal.match(/^\d{4,5}$/)) {
            console.log('Fetching address data for postal code:', codigoPostal);
            $.ajax({
                url: '{{ route("inscripcion.obtener_datos_direccion") }}',
                method: 'POST',
                data: {
                    codigo_postal: codigoPostal,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Address data received:', response);
                    
                    if (response.success) {
                        // Update fields with response data
                        $('#estado').val(response.estado);
                        $('#estado_display').text(response.estado);
                        
                        $('#municipio').val(response.municipio);
                        $('#municipio_display').text(response.municipio);
                        
                        // Clear and populate asentamiento dropdown
                        $('#colonia').empty();
                        $('#colonia').append('<option value="">Seleccione un Asentamiento</option>');
                        
                        $.each(response.asentamientos, function(index, asentamiento) {
                            $('#colonia').append(
                                $('<option>', {
                                    value: asentamiento.nombre,
                                    text: asentamiento.nombre
                                })
                            );
                        });
                        
                        // Show success indicator
                        $('#formulario__grupo--codigo_postal').removeClass('formulario__grupo-incorrecto').addClass('formulario__grupo-correcto');
                        $('#formulario__grupo--codigo_postal .formulario__input-error').hide();
                    } else {
                        // Show error message
                        $('#formulario__grupo--codigo_postal').removeClass('formulario__grupo-correcto').addClass('formulario__grupo-incorrecto');
                        $('#formulario__grupo--codigo_postal .formulario__input-error').text(response.message).show();
                        
                        // Clear fields
                        $('#estado').val('');
                        $('#estado_display').text('');
                        $('#municipio').val('');
                        $('#municipio_display').text('');
                        $('#colonia').empty().append('<option value="">Seleccione un Asentamiento</option>');
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching address data:', xhr);
                    
                    // Show error message
                    $('#formulario__grupo--codigo_postal').removeClass('formulario__grupo-correcto').addClass('formulario__grupo-incorrecto');
                    $('#formulario__grupo--codigo_postal .formulario__input-error').text('Error al obtener datos. Intente de nuevo.').show();
                    
                    // Clear fields
                    $('#estado').val('');
                    $('#estado_display').text('');
                    $('#municipio').val('');
                    $('#municipio_display').text('');
                    $('#colonia').empty().append('<option value="">Seleccione un Asentamiento</option>');
                }
            });
        }
    });

    // Make sure to trigger the input event if the field already has a value when the page loads
    if ($('#codigo_postal').val()) {
        $('#codigo_postal').trigger('input');
    }
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