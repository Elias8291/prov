@extends('dashboard')

@section('title', 'Iniciar Revisión de Proveedor')
<link rel="stylesheet" href="{{ asset('assets/css/formularios.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/tabla.css') }}">
@section('content')
    <div class="dashboard-container">
        <a href="javascript:void(0)" onclick="goBack()" class="btn btn-secondary back-button">
            <i class="fas fa-arrow-left"></i> Regresar
        </a>
        <h1 class="page-title">Revisión de Solicitud</h1>
        <p class="page-subtitle">Revisión de datos generales del solicitante: {{ $solicitante->rfc }}</p>

        <!-- Mensajes de Alerta -->
        @if (session('success'))
            <div class="alert alert-success" id="successAlert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" id="errorMsgAlert">
                {{ session('error') }}
            </div>
        @endif

        <!-- Renderizar Componente Seccion1 -->
        <x-seccion1 :action="$componentParams['action']" :method="$componentParams['method']" :tipoPersona="$componentParams['tipoPersona']" :datosPrevios="$componentParams['datosPrevios']" :sectores="$componentParams['sectores']"
            :isRevisor="$componentParams['isRevisor']" :mostrarCurp="$componentParams['mostrarCurp']" :seccion="$componentParams['seccion']" :totalSecciones="$componentParams['totalSecciones']" :isConfirmationSection="$componentParams['isConfirmationSection']" :actividadesSeleccionadas="$componentParams['actividadesSeleccionadas']"
            revisor-status="inactive" />

        <!-- Opcional: Sección para comentarios del revisor -->
        @if ($componentParams['isRevisor'])
            <form action="{{ $componentParams['action'] }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="form-control">
                        <option value="En Revision">En Revisión</option>
                        <option value="Aprobado">Aprobado</option>
                        <option value="Rechazado">Rechazado</option>
                        <option value="Por Cotejar">Por Cotejar</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Revisión</button>
            </form>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(function() {
                    alerts.forEach(function(alert) {
                        alert.style.display = 'none';
                    });
                }, 5000);
            }

            function goBack() {
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    window.location.href =
                    '{{ url('/dashboard') }}'; 
                }
            }
        });
    </script>
@endsection
