@extends('dashboard')

@section('title', '¡Bienvenidos a Proveedores de Oaxaca!')
<link rel="stylesheet" href="{{ asset('assets/css/index.css') }}">

@section('content')
<div class="dashboard-container">
    <div class="welcome-card">
        <div class="welcome-left">
            <div class="status-time">
                <div class="time-display" id="currentTime">10:29 am</div>
                <div class="welcome-status">Sesión Activa</div>
            </div>
            <h2 id="greeting">
                Buenos días, {{ auth()->check() ? auth()->user()->name : 'Invitado' }}
            </h2>
            <p class="welcome-subtitle">¿Cómo va tu día? 🌟</p>
            <p class="welcome-description">Bienvenido al Padron de Proveedores del Estado De Oaxaca.</p>   
            
           @if(auth()->check() && auth()->user()->hasRole('solicitante'))
                <?php
                    $tramite = auth()->user()->solicitante
                        ? App\Models\Tramite::where('solicitante_id', auth()->user()->solicitante->id)
                            ->where('estado', 'Pendiente')
                            ->first()
                        : null;
                    $textoBoton = ($tramite && $tramite->progreso_tramite >= 1) ? 'Continuar trámite' : 'Iniciar trámite';
                    $rutaBoton = ($tramite && $tramite->progreso_tramite >= 1) ? route('inscripcion.formulario') : route('inscripcion.terminos_y_condiciones');
                ?>
                <a href="{{ $rutaBoton }}" class="register-button">
                    <span>{{ $textoBoton }}</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            @endif

            <div class="discover-section">
                <h3 class="section-heading">Descubre Proveedores de Oaxaca</h3>
                <div class="cards-container-vertical">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-icon">
                                <i class="fas fa-upload"></i>
                            </div>
                            <h4 class="card-title">Subir Documentos</h4>
                            <p class="card-meta">Carga tus documentos oficiales</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <h4 class="card-title">Estado de Registro</h4>
                            <p class="card-meta">Consulta tu proceso</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-icon">
                                <i class="fas fa-address-book"></i>
                            </div>
                            <h4 class="card-title">Directorio</h4>
                            <p class="card-meta">Encuentra proveedores locales</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <h4 class="card-title">Ayuda</h4>
                            <p class="card-meta">Asistencia con tu registro</p>
                        </div>
                    </div>   
                </div>
            </div>
        </div>
        <div class="welcome-right">
            <img src="{{ asset('assets/imagenes/mujer_bienvenida.png') }}" alt="Asistente" class="welcome-image">
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateTime() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const ampm = hours >= 12 ? 'pm' : 'am';
            const formattedHours = hours % 12 || 12;
            const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;

            document.getElementById('currentTime').textContent = `${formattedHours}:${formattedMinutes} ${ampm}`;

            const greeting = document.getElementById('greeting');
            const userName = '{{ auth()->check() ? auth()->user()->name : 'Invitado' }}';

            if (hours < 12) {
                greeting.textContent = `Buenos días, ${userName}`;
            } else if (hours >= 12 && hours < 19) {
                greeting.textContent = `Buenas tardes, ${userName}`;
            } else {
                greeting.textContent = `Buenas noches, ${userName}`;
            }
        }

        updateTime();
        setInterval(updateTime, 60000);
    });
</script>
@endsection