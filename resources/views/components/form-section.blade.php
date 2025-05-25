@props([
    'seccion' => 1,
    'tipoPersona' => 'Física',
    'datosPrevios' => [],
    'isRevisor' => false,
    'action' => '',
    'method' => 'POST',
    'estados' => [],
    'sectores' => [],
    'actividadesSeleccionadas' => [],
    'direccion' => null,
    'isConfirmationSection' => false,
    'mostrarCurp' => false,
])

@php
    $formId = "formulario{$seccion}";
    $partial = "inscripcion.seccion{$seccion}";
@endphp

<form id="{{ $formId }}" action="{{ $action }}" method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}" enctype="multipart/form-data">
    @if(strtoupper($method) !== 'GET')
        @csrf
    @endif
    <input type="hidden" name="action" value="next">
    <input type="hidden" name="seccion" value="{{ $seccion }}">

    @include($partial, [
        'datosPrevios' => $datosPrevios,
        'tipoPersona' => $tipoPersona,
        'isRevisor' => $isRevisor,
        'estados' => $estados,
        'sectores' => $sectores,
        'actividadesSeleccionadas' => $actividadesSeleccionadas,
        'direccion' => $direccion,
        'isConfirmationSection' => $isConfirmationSection,
        'mostrarCurp' => $mostrarCurp,
    ])

    <div class="form-buttons">
        @if($seccion > 1)
            <button type="button" class="btn btn-secondary" onclick="goToPreviousSection('{{ $formId }}')">Anterior</button>
        @else
            <button type="button" class="btn btn-secondary" onclick="window.history.back();">Volver</button>
        @endif
        <button type="submit" class="btn btn-primary" id="submitForm">
            {{ $isConfirmationSection ? 'Confirmar' : 'Siguiente' }}
        </button>
    </div>
</form>

@push('scripts')
    <script>
        function goToPreviousSection(formId) {
            $(`#${formId} input[name="action"]`).val('previous');
            $(`#${formId}`).submit();
        }
    </script>
@endpush