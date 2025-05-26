@extends('dashboard')

@section('title', 'Listado de Solicitudes por Revisar')

<link rel="stylesheet" href="{{ asset('assets/css/tabla.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
    <div class="dashboard-container">
        <!-- Header Section with Title -->
        <h1 class="page-title">Solicitudes Pendientes de Revisión</h1>
        <p class="page-subtitle">Listado de solicitantes que requieren revisión</p>

        <!-- Controls Bar with Search -->
        <div class="controls-bar">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Buscar por nombre o RFC...">
            </div>

            <div class="filters-container">
                <!-- Progress Filter -->
                <div class="filter-item">
                    <label for="progressFilter" class="filter-label">Progreso:</label>
                    <select class="filter-select" id="progressFilter">
                        <option value="" selected>Sección</option>
                        <option value="1">Sección 1</option>
                        <option value="2">Sección 2</option>
                        <option value="3">Sección 3</option>
                        <option value="4">Sección 4</option>
                        <option value="5">Sección 5</option>
                        <option value="6">Sección 6</option>
                        <option value="7">Sección 7</option>
                    </select>
                </div>

                <!-- Estado de Proceso Filter -->
                <div class="filter-item">
                    <label for="statusProcessFilter" class="filter-label">Estado:</label>
                    <select class="filter-select" id="statusProcessFilter">
                        <option value="" selected>Todos los estados</option>
                        <option value="completo">Completos</option>
                        <option value="proceso">En Proceso</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <button id="applyFilters" class="filter-button">
                    <i class="fas fa-filter"></i> Aplicar
                </button>
            </div>
        </div>

        <!-- Alert Messages -->
        <div class="alert alert-danger" style="display: none;" id="errorAlert">
            Ha ocurrido un error al procesar la solicitud.
        </div>

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

        <!-- Table Container -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Trámite</th>
                        <th>Estado</th>
                        <th>Fecha Inicio</th>
                        <th>Tiempo Restante</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $solicitud)
                        @php
                            // Determine if process is complete based on persona type and progress
                            $isComplete = false;
                            $tipoPersona = optional($solicitud->solicitante)->tipo_persona ?? 'Desconocido';
                            if ($tipoPersona === 'Física' && $solicitud->progreso_tramite >= 4) {
                                $isComplete = true;
                            } elseif ($tipoPersona === 'Moral' && $solicitud->progreso_tramite >= 7) {
                                $isComplete = true;
                            }
                            $statusClass = $isComplete ? 'complete-status' : 'pending-status';
                            $statusText = $isComplete ? 'Completo' : 'En Proceso';

                            // Estado class based on the actual status
                            $estadoClass = '';
                            switch ($solicitud->estado) {
                                case 'Aprobado':
                                    $estadoClass = 'estado-aprobado';
                                    break;
                                case 'Rechazado':
                                    $estadoClass = 'estado-rechazado';
                                    break;
                                case 'En Revision':
                                    $estadoClass = 'estado-revision';
                                    break;
                                case 'Pendiente':
                                    $estadoClass = 'estado-pendiente';
                                    break;
                                default:
                                    $estadoClass = 'estado-default';
                            }

                            // Store RFC in a data attribute for searching
                            $rfc = optional($solicitud->solicitante)->rfc ?? 'N/A';

                            // Calculate time remaining
                            $fechaInicio = \Carbon\Carbon::parse($solicitud->fecha_inicio);
                            $currentDate = \Carbon\Carbon::now();

                            // Determine total review hours based on tramite type
                            $tipoTramite = $solicitud->tipo_tramite ?? 'Desconocido';
                            $totalHours = $tipoTramite == 'Renovacion' ? 42 : 72;

                            // Calculate business hours remaining (rough approximation)
                            $hoursElapsed = $fechaInicio->diffInHours($currentDate);
                            $hoursRemaining = $totalHours - $hoursElapsed;

                            // Format display for remaining time
                            if ($hoursRemaining <= 0) {
                                $timeRemainingDisplay = '<span class="time-expired">Vencido</span>';
                                $timeRemainingClass = 'time-expired';
                            } elseif ($hoursRemaining < 24) {
                                $timeRemainingDisplay =
                                    '<span class="time-critical">' . $hoursRemaining . ' horas</span>';
                                $timeRemainingClass = 'time-critical';
                            } else {
                                $days = floor($hoursRemaining / 24);
                                $hours = $hoursRemaining % 24;
                                $timeRemainingDisplay =
                                    '<span class="time-normal">' . $days . 'd ' . $hours . 'h</span>';
                                $timeRemainingClass = 'time-normal';
                            }
                        @endphp
                        <tr data-process-status="{{ $isComplete ? 'completo' : 'proceso' }}"
                            data-progress="{{ $solicitud->progreso_tramite }}" data-rfc="{{ $rfc }}">
                            <td>{{ $solicitud->id }}</td>
                            <td class="product-name-cell">
                                <div>
                                    <div class="product-name">
                                        {{ optional($solicitud->detalleTramite)->razon_social ?? optional($solicitud->solicitante)->usuario->nombre ?? 'Sin Nombre' }}
                                    </div>
                                    <div class="product-id">{{ $rfc }}</div>
                                </div>
                            </td>
                            <td>{{ $tipoPersona }}</td>
                            <td>
                                <span class="tipo-tramite-badge">
                                    {{ $tipoTramite }}
                                </span>
                            </td>
                            <td>
                                <span class="estado-badge {{ $estadoClass }}">
                                    {{ $solicitud->estado ?: 'No asignado' }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $fechaInicio->format('d M Y') }}</div>
                                <div class="time-small">{{ $fechaInicio->format('H:i') }}</div>
                            </td>
                            <td class="{{ $timeRemainingClass }}">
                                {!! $timeRemainingDisplay !!}
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('revision.show', $solicitud->id) }}" class="btn-action view-btn"
                                        title="Ver detalles" data-id="{{ $solicitud->id }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('revision.iniciar', $rfc) }}" class="btn-action begin-review-btn"
                                        title="Comenzar revisión" data-id="{{ $solicitud->id }}">
                                        <i class="fas fa-clipboard-check"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay solicitudes pendientes de revisión</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Include Custom Pagination -->
        @include('components.paginacion', ['paginator' => $solicitudes])
    </div>

    <style>
        /* Add these styles to your CSS file */
        .time-expired {
            color: #ff0000;
            font-weight: bold;
        }

        .time-critical {
            color: #ff6600;
            font-weight: bold;
        }

        .time-normal {
            color: #006633;
        }

        .time-small {
            font-size: 0.8em;
            color: #666;
        }

        .tipo-tramite-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
            background-color: #e3f2fd;
            color: #0d47a1;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ocultar alertas automáticamente
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(function() {
                    alerts.forEach(function(alert) {
                        alert.style.display = 'none';
                    });
                }, 5000);
            }

            // Funcionalidad de búsqueda
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const tableRows = document.querySelectorAll('tbody tr');
                    tableRows.forEach(row => {
                        const nombre = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
                        const rfc = row.getAttribute('data-rfc')?.toLowerCase() || '';
                        if (nombre.includes(searchTerm) || rfc.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // Botón de aplicar filtros
            const applyFiltersBtn = document.getElementById('applyFilters');
            if (applyFiltersBtn) {
                applyFiltersBtn.addEventListener('click', function() {
                    const progressValue = document.getElementById('progressFilter').value;
                    const statusValue = document.getElementById('statusProcessFilter').value;
                    const tableRows = document.querySelectorAll('tbody tr');
                    tableRows.forEach(row => {
                        const rowStatus = row.getAttribute('data-process-status');
                        const rowProgress = row.getAttribute('data-progress');
                        let showRow = true;
                        if (statusValue !== '' && rowStatus !== statusValue) {
                            showRow = false;
                        }
                        if (progressValue !== '' && rowProgress !== progressValue) {
                            showRow = false;
                        }
                        row.style.display = showRow ? '' : 'none';
                    });
                });
            }
        });
    </script>
@endsection