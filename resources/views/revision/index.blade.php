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
        <!-- Table Container -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th data-label="ID Trámite">ID</th>
                        <th data-label="Solicitante">Nombre del Solicitante</th>
                        <th data-label="RFC">RFC</th>
                        <th data-label="Tipo Persona">Tipo</th>
                        <th data-label="Sección Progreso">Progreso</th>
                        <th data-label="Avance">Avance</th>
                        <th data-label="Estado">Estado</th>
                        <th data-label="Fecha Solicitud">Fecha</th>
                        <th data-label="Acciones">Acciones</th>
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
                            <td data-label="ID Trámite">{{ $solicitud->id }}</td>
                            <td data-label="Solicitante" class="solicitante-nombre">
                                {{ $solicitud->detalleTramite->razon_social ?? $solicitud->solicitante->usuario->nombre }}
                            </td>
                            <td data-label="RFC" class="rfc">{{ $solicitud->solicitante->rfc }}</td>
                            <td data-label="Tipo Persona">{{ $solicitud->solicitante->tipo_persona }}</td>
                            <td data-label="Sección Progreso">
                                <span class="progress-badge progress-{{ $solicitud->progreso_tramite }}">
                                    Sección {{ $solicitud->progreso_tramite }}
                                </span>
                            </td>
                            <td data-label="Avance">
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td data-label="Estado">
                                <span class="estado-badge {{ $estadoClass }}">
                                    {{ $solicitud->estado ?: 'No asignado' }}
                                </span>
                            </td>
                            <td data-label="Fecha Solicitud">
                                {{ \Carbon\Carbon::parse($solicitud->created_at)->format('d/m/Y') }}
                            </td>
                            <td data-label="Acciones">
                                <div class="action-buttons">
                                    <a href="{{ route('revision.show', $solicitud->id) }}" class="btn-action view-btn"
                                        title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('revision.iniciar', $solicitud->solicitante->rfc) }}"
                                        class="btn-action begin-review-btn" title="Comenzar revisión">
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

        <!-- Stylish Pagination -->
        <div class="custom-pagination">
            {{ $solicitudes->links() }}
        </div>
    </div>

    <style>
        /* Guinda Color Scheme */
        :root {
            --guinda: #800000;
            --guinda-light: #a52a2a;
            --guinda-dark: #660000;
            --complete-color: #28a745;
            --pending-color: #ffc107;
        }

        /* Compact Dashboard Container */
        .dashboard-container {
            padding: 15px;
            max-width: 1220px;
            margin: 40px auto;
            box-sizing: border-box;
        }

        /* Alert Styles */
        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        /* Table Styles */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            table-layout: auto;
        }

        .table th,
        .table td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
            font-size: 13px;
            white-space: normal;
            word-break: break-word;
        }

        .table th {
            background: var(--guinda);
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
        }

        .table td {
            color: #333;
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        /* Progress Badge */
        .progress-badge {
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 500;
            display: inline-block;
            color: #fff;
        }

        .progress-1 {
            background: #007bff;
            /* Blue */
        }

        .progress-2 {
            background: #6f42c1;
            /* Purple */
        }

        .progress-3 {
            background: #fd7e14;
            /* Orange */
        }

        .progress-4 {
            background: #28a745;
            /* Green */
        }

        .progress-5 {
            background: #17a2b8;
            /* Cyan */
        }

        .progress-6 {
            background: #6c757d;
            /* Gray */
        }

        .progress-7 {
            background: #dc3545;
            /* Red */
        }

        /* Status Badge */
        .status-badge {
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 500;
            display: inline-block;
        }

        .complete-status {
            background-color: var(--complete-color);
            color: white;
        }


        .estado-badge {
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 500;
            display: inline-block;
            color: white;
        }

        .estado-aprobado {
            background-color: #4CAF50;
            /* Green */
        }

        .estado-rechazado {
            background-color: #F44336;
            /* Red */
        }

        .estado-revision {
            background-color: #2196F3;
            /* Blue */
        }

        .estado-pendiente {
            background-color: #FF9800;
            /* Orange */
        }

        .estado-default {
            background-color: #9E9E9E;
            /* Gray */
        }

        .pending-status {
            background-color: var(--pending-color);
            color: #333;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 6px;
        }

        .btn-action {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            font-size: 13px;
            color: var(--guinda);
        }

        .btn-action:hover {
            color: var(--guinda-light);
        }

        /* Enhanced Search and Filter Controls */
        .controls-bar {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 15px;
        }

        .search-container {
            position: relative;
            flex: 1;
            max-width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 8px 12px 8px 36px;
            border: 2px solid var(--guinda);
            border-radius: 25px;
            font-size: 14px;
            color: #333;
            background: #fff;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--guinda-light);
            box-shadow: 0 0 5px rgba(165, 42, 42, 0.3);
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--guinda);
            font-size: 14px;
        }

        .filters-container {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-item {
            display: flex;
            align-items: center;
        }

        .filter-label {
            margin-right: 6px;
            font-weight: 600;
            color: var(--guinda);
            font-size: 13px;
        }

        .filter-select {
            padding: 6px 10px;
            border: 2px solid var(--guinda);
            border-radius: 25px;
            font-size: 13px;
            color: #333;
            background: #fff;
            min-width: 140px;
            transition: border-color 0.3s;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--guinda-light);
        }

        .filter-button {
            background: var(--guinda);
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 25px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }

        .filter-button:hover {
            background: var(--guinda-light);
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        @media (min-width: 768px) {
            .controls-bar {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        /* Responsive Table for Small Screens */
        @media (max-width: 600px) {
            .dashboard-container {
                padding: 10px;
            }

            .table th,
            .table td {
                padding: 6px 8px;
                font-size: 12px;
            }

            .table {
                display: block;
                overflow-x: hidden;
            }

            .table thead {
                display: none;
            }

            .table tbody,
            .table tr,
            .table td {
                display: block;
                width: 100%;
            }

            .table tr {
                margin-bottom: 8px;
                border-bottom: 1px solid #dee2e6;
            }

            .table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            .table td:before {
                content: attr(data-label);
                position: absolute;
                left: 8px;
                width: 45%;
                font-weight: 600;
                text-align: left;
                font-size: 12px;
                color: var(--guinda);
            }

            .table td:nth-child(8) {
                text-align: center;
            }

            .search-container {
                max-width: 100%;
            }

            .search-input {
                padding: 6px 10px 6px 30px;
                font-size: 13px;
            }

            .filter-select {
                min-width: 120px;
            }
        }

        /* Summary Cards */
        .summary-cards {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .summary-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
            flex: 1;
            min-width: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .summary-title {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: var(--guinda);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide success and error messages after 5 seconds
            const successAlert = document.getElementById('successAlert');
            const errorMsgAlert = document.getElementById('errorMsgAlert');
            const errorAlert = document.getElementById('errorAlert');

            if (successAlert) {
                setTimeout(function() {
                    successAlert.style.display = 'none';
                }, 5000);
            }

            if (errorMsgAlert) {
                setTimeout(function() {
                    errorMsgAlert.style.display = 'none';
                }, 5000);
            }

            // Table Search Functionality
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const tableRows = document.querySelectorAll('tbody tr');

                    tableRows.forEach(row => {
                        const nombre = row.querySelector('.solicitante-nombre')?.textContent
                            .toLowerCase() || '';
                        const rfc = row.querySelector('.rfc')?.textContent.toLowerCase() || '';

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

                    // Clear existing parameters
                    if (currentUrl.searchParams.has('progreso')) {
                        currentUrl.searchParams.delete('progreso');
                    }

                    // Add new parameters if progress filter is selected
                    if (progressValue) {
                        currentUrl.searchParams.append('progreso', progressValue);
                    }

                    // Only redirect if progress filter is applied
                    if (progressValue) {
                        window.location.href = currentUrl.toString();
                    }
                });
            }

            // Show alert message function
            function showAlert(alertElement, message) {
                alertElement.textContent = message;
                alertElement.style.display = 'block';
                setTimeout(function() {
                    alertElement.style.display = 'none';
                }, 5000);
            }
        });
    </script>
@endsection
