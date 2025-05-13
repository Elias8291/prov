@extends('dashboard')

@section('title', 'Inscripción al Padrón de Proveedores - Proveedores de Oaxaca')
<link rel="stylesheet" href="{{ asset('assets/css/formularios.css') }}">

@section('content')
<div class="form-background-container">
    <div class="inner-form-container">
        <div class="progress-container">
            <div class="progress-info">
                <span class="progress-percent">{{ $porcentaje }}%</span>
                <span class="progress-text">Completado</span>
                <span class="progress-text persona-type-text">
                    Formulario para persona: <span class="persona-type-value">{{ $tipoPersona }}</span>
                </span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $porcentaje }}%;"></div>
            </div>
        </div>
        <div class="progress-tracker">
            @foreach ($seccionesInfo as $i => $titulo)
                <div class="seccion {{ $i <= $seccion ? 'active' : '' }}">
                    <div class="seccion-numero">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="seccion-titulo">{{ $titulo }}</div>
                </div>
            @endforeach
        </div>
        <div class="form-seccion">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <form action="{{ route('inscripcion.procesar') }}" method="POST" autocomplete="off">
                @csrf
                {{-- Incluye los campos según la sección --}}
               @include('inscripcion.secciones.' . $seccionPartial, ['datosPrevios' => $datosPrevios])
        <div class="navigation-buttons">
            @if ($seccion > 1)
                <button type="button" onclick="window.location.href='{{ route('inscripcion.formulario') }}?retroceder=1'" class="btn btn-secondary">Anterior</button>
            @else
                <span></span>
            @endif
            <span class="progress-small-text">Sección {{ $seccion }} de {{ $totalSecciones }}</span>
            <button type="submit" class="btn btn-primary">{{ $isConfirmationSection ? 'Finalizar' : 'Siguiente' }}</button>
        </div>
            </form>
        </div>
    </div>
</div>
@endsection