<div>
    <form id="formulario2" action="{{ route('inscripcion.procesar') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="action" value="next">
        <div class="form-section" id="form-step-2">
            <h4><i class="fas fa-map-marker-alt"></i> Domicilio</h4>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--codigo_postal">
                    <label class="form-label data-label">Código Postal</label>
                    @if (Auth::user()->hasRole('solicitante') && isset($direccion) && $direccion->codigo_postal)
                        <span class="data-field"
                            id="codigo_postal_display">{{ str_pad($direccion->codigo_postal, 5, '0', STR_PAD_LEFT) }}</span>
                        <input type="hidden" id="codigo_postal" name="codigo_postal"
                            value="{{ str_pad($direccion->codigo_postal, 5, '0', STR_PAD_LEFT) }}">
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
                            placeholder="Ej: Jalisco" readonly required
                            value="{{ old('estado', $datosPrevios['estado'] ?? '') }}">
                        <p class="formulario__input-error">El estado debe contener solo letras y espacios, máximo 100
                            caracteres.</p>
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
                            placeholder="Ej: Guadalajara" readonly required
                            value="{{ old('municipio', $datosPrevios['municipio'] ?? '') }}">
                        <p class="formulario__input-error">El municipio debe contener solo letras y espacios, máximo 100
                            caracteres.</p>
                    @endif
                </div>
                <div class="half-width form-group" id="formulario__grupo--colonia">
                    <label class="form-label" for="colonia">Asentamiento</label>
                    <select id="colonia" name="colonia" class="form-control" required>
                        <option value="">Seleccione un Asentamiento</option>
                        @if (isset($datosPrevios['colonia']))
                            <option value="{{ $datosPrevios['colonia'] }}" selected>{{ $datosPrevios['colonia'] }}
                            </option>
                        @endif
                    </select>
                    <p class="formulario__input-error">Debe seleccionar un asentamiento.</p>
                </div>
            </div>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--calle">
                    <label class="form-label" for="calle">Calle</label>
                    <input type="text" id="calle" name="calle" class="form-control"
                        placeholder="Ej: Av. Principal" required maxlength="100"
                        value="{{ old('calle', $datosPrevios['calle'] ?? '') }}">
                    <p class="formulario__input-error">La calle debe contener letras, números o espacios, máximo 100
                        caracteres.</p>
                </div>
                <div class="half-width form-group" id="formulario__grupo--numero_exterior">
                    <label class="form-label" for="numero_exterior">Número Exterior</label>
                    <input type="text" id="numero_exterior" name="numero_exterior" class="form-control"
                        placeholder="Ej: 123 o S/N" required maxlength="10" pattern="[A-Za-z0-9\/]+"
                        value="{{ old('numero_exterior', $datosPrevios['numero_exterior'] ?? '') }}">
                    <p class="formulario__input-error">El número exterior debe contener letras, números o /, entre 1 y
                        10 caracteres.</p>
                </div>
            </div>
            <div class="form-group horizontal-group">
                <div class="half-width form-group" id="formulario__grupo--numero_interior">
                    <label class="form-label" for="numero_interior">Número Interior (Opcional)</label>
                    <input type="text" id="numero_interior" name="numero_interior" class="form-control"
                        placeholder="Ej: 5A" maxlength="10" pattern="[A-Za-z0-9]+"
                        value="{{ old('numero_interior', $datosPrevios['numero_interior'] ?? '') }}">
                    <p class="formulario__input-error">El número interior debe contener letras o números, máximo 10
                        caracteres.</p>
                </div>
                <div class="half-width form-group" id="formulario__grupo--entre_calle_1">
                    <label class="form-label" for="entre_calle_1">Entre Calle 1</label>
                    <input type="text" id="entre_calle_1" name="entre_calle_1" class="form-control"
                        placeholder="Ej: Calle Independencia" required maxlength="100" pattern="[A-Za-z0-9\s]+"
                        value="{{ old('entre_calle_1', $datosPrevios['entre_calle_1'] ?? '') }}">
                    <p class="formulario__input-error">Entre calle 1 debe contener letras, números o espacios, máximo
                        100 caracteres.</p>
                </div>
            </div>
            <div class="form-group" id="formulario__grupo--entre_calle_2">
                <label class="form-label" for="entre_calle_2">Entre Calle 2</label>
                <input type="text" id="entre_calle_2" name="entre_calle_2" class="form-control"
                    placeholder="Ej: Calle Morelos" required maxlength="100" pattern="[A-Za-z0-9\s]+"
                    value="{{ old('entre_calle_2', $datosPrevios['entre_calle_2'] ?? '') }}">
                <p class="formulario__input-error">Entre calle 2 debe contener letras, números o espacios, máximo 100
                    caracteres.</p>
            </div>
        </div>
        <div class="form-buttons">
        <button type="submit" class="btn btn-secondary" name="action" value="previous">Anterior</button>
        <button type="submit" class="btn btn-primary" id="submitForm" name="action" value="next">Siguiente</button>
        </div>
    </form>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Handle postal code AJAX lookup
                $('#codigo_postal').on('input', function() {
                    const codigoPostal = $(this).val();

                    if (codigoPostal.length >= 4 && /^\d{4,5}$/.test(codigoPostal)) {
                        $.ajax({
                            url: '{{ route('inscripcion.obtener-datos-direccion') }}',
                            method: 'POST',
                            data: {
                                codigo_postal: codigoPostal,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Update fields with response data
                                    $('#estado').val(response.estado);
                                    $('#estado_display').text(response.estado);
                                    $('#municipio').val(response.municipio);
                                    $('#municipio_display').text(response.municipio);

                                    // Update colonia dropdown
                                    $('#colonia').empty().append(
                                        '<option value="">Seleccione un Asentamiento</option>');
                                    $.each(response.asentamientos, function(index, asentamiento) {
                                        $('#colonia').append(
                                            $('<option>', {
                                                value: asentamiento.nombre,
                                                text: asentamiento.nombre
                                            })
                                        );
                                    });
                                } else {
                                    // Clear fields if no data found
                                    $('#estado').val('');
                                    $('#estado_display').text('');
                                    $('#municipio').val('');
                                    $('#municipio_display').text('');
                                    $('#colonia').empty().append(
                                        '<option value="">Seleccione un Asentamiento</option>');
                                }
                            },
                            error: function(xhr) {
                                // Clear fields on error
                                $('#estado').val('');
                                $('#estado_display').text('');
                                $('#municipio').val('');
                                $('#municipio_display').text('');
                                $('#colonia').empty().append(
                                    '<option value="">Seleccione un Asentamiento</option>');
                            }
                        });
                    }
                });

                // Initialize with pre-filled postal code if exists
                if ($('#codigo_postal').val()) {
                    const codigoPostal = $('#codigo_postal').val();

                    if (codigoPostal.length >= 4 && /^\d{4,5}$/.test(codigoPostal)) {
                        $.ajax({
                            url: '{{ route('inscripcion.obtener-datos-direccion') }}',
                            method: 'POST',
                            data: {
                                codigo_postal: codigoPostal,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    $('#estado').val(response.estado);
                                    $('#estado_display').text(response.estado);
                                    $('#municipio').val(response.municipio);
                                    $('#municipio_display').text(response.municipio);

                                    $('#colonia').empty().append(
                                        '<option value="">Seleccione un Asentamiento</option>');
                                    $.each(response.asentamientos, function(index, asentamiento) {
                                        $('#colonia').append(
                                            $('<option>', {
                                                value: asentamiento.nombre,
                                                text: asentamiento.nombre
                                            })
                                        );
                                    });

                                    // Pre-select the option if it exists
                                    if ('{{ $datosPrevios['colonia'] ?? '' }}') {
                                        $('#colonia').val('{{ $datosPrevios['colonia'] ?? '' }}');
                                    }
                                }
                            }
                        });
                    }
                }
            });
        </script>
    @endpush
</div>