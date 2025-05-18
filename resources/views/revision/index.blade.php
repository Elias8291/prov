
@extends('dashboard')

@section('title', 'Listado de Solicitudes por Revisar')

<link rel="stylesheet" href="{{ asset('assets/css/tabla.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
    <div class="dashboard-container">
        <!-- Header Section with Title -->
        <h1 class="page-title">Solicitudes Pendientes de Revisión</h1>
        <p class="page-subtitle">Listado de solicitantes que requieren revisión (Progreso 1-7)</p>

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
                        <th>RFC</th>
                        <th>Tipo</th>
                        <th>Progreso</th>
                        <th>Avance</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $solicitud)
                        @php
                            // Determine if process is complete based on persona type and progress
                            $isComplete = false;
                            $tipoPersona = $solicitud->solicitante->tipo_persona ?? '';
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
                        @endphp
                        <tr data-process-status="{{ $isComplete ? 'completo' : 'proceso' }}">
                            <td>{{ $solicitud->id }}</td>
                            <td class="product-name-cell">
                                <div>
                                    <div class="product-name">
                                        {{ $solicitud->detalleTramite->razon_social ?? $solicitud->solicitante->usuario->nombre }}
                                    </div>
                                    <div class="product-id">{{ $solicitud->solicitante->rfc }}</div>
                                </div>
                            </td>
                            <td>{{ $solicitud->solicitante->rfc }}</td>
                            <td>{{ $solicitud->solicitante->tipo_persona }}</td>
                            <td>
                                <span class="progress-badge progress-{{ $solicitud->progreso_tramite }}">
                                    Sección {{ $solicitud->progreso_tramite }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td>
                                <span class="estado-badge {{ $estadoClass }}">
                                    {{ $solicitud->estado ?: 'No asignado' }}
                                </span>
                            </td>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($solicitud->created_at)->format('d M Y') }}</div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('revision.show', $solicitud->id) }}" class="btn-action view-btn" title="Ver detalles" data-id="{{ $solicitud->id }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('revision.iniciar', $solicitud->solicitante->rfc) }}" class="btn-action begin-review-btn" title="Comenzar revisión" data-id="{{ $solicitud->id }}">
                                        <i class="fas fa-clipboard-check"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No hay solicitudes pendientes de revisión</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Include Custom Pagination -->
        @include('components.paginacion', ['paginator' => $solicitudes])
    </div>

   

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(function() {
                    alerts.forEach(function(alert) {
                        alert.style.display = 'none';
                    });
                }, 5000);
            }

            // Table Search Functionality
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const tableRows = document.querySelectorAll('tbody tr');

                    tableRows.forEach(row => {
                        const nombre = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
                        const rfc = row.querySelector('.product-id')?.textContent.toLowerCase() || '';
                        if (nombre.includes(searchTerm) || rfc.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // Apply Filters Button
            const applyFiltersBtn = document.getElementById('applyFilters');
            if (applyFiltersBtn) {
                applyFiltersBtn.addEventListener('click', function() {
                    const progressValue = document.getElementById('progressFilter').value;
                    const statusValue = document.getElementById('statusProcessFilter').value;

                    // Client-side filtering for process status
                    const tableRows = document.querySelectorAll('tbody tr');
                    tableRows.forEach(row => {
                        const rowStatus = row.getAttribute('data-process-status');
                        if (statusValue === '' || rowStatus === statusValue) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Server-side filtering for progress level
                    const currentUrl = new URL(window.location.href);
                    if (currentUrl.searchParams.has('progreso')) {
                        currentUrl.searchParams.delete('progreso');
                    }
                    if (progressValue) {
                        currentUrl.searchParams.append('progreso', progressValue);
                    }
                    if (progressValue) {
                        window.location.href = currentUrl.toString();
                    }
                });
            }
        });
    </script>
@endsection