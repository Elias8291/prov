@extends('dashboard')

@section('title', 'Listado de Solicitudes por Revisar')

<link rel="stylesheet" href="{{ asset('assets/css/tabla.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
    <div class="dashboard-container">
        <h1 class="page-title">Solicitudes Pendientes de Revisión</h1>
        <p class="page-subtitle">Listado de solicitantes que requieren revisión</p>

        <div class="controls-bar">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Buscar por nombre o RFC...">
            </div>

            <div class="filters-container">
                <div class="filter-buttons">
                    <a href="{{ route('revision.index', ['estado_finalizacion' => 'pendiente']) }}" 
                       class="btn-filter {{ request('estado_finalizacion', 'terminado') === 'pendiente' ? 'active' : '' }}">
                        En Proceso
                    </a>
                    <a href="{{ route('revision.index', ['estado_finalizacion' => 'terminado']) }}" 
                       class="btn-filter {{ request('estado_finalizacion', 'terminado') === 'terminado' ? 'active' : '' }}">
                        Terminados
                    </a>
                </div>
            </div>
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

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Trámite</th>
                        <th>Estado</th>
                        <th>Tiempo para Revisar</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $solicitud)
                        <tr data-rfc="{{ optional($solicitud->solicitante)->rfc ?? 'N/A' }}">
                            <td>{{ $solicitud->id }}</td>
                            <td class="product-name-cell">
                                <div>
                                    <div class="product-name">
                                        {{ optional($solicitud->detalleTramite)->razon_social ?? (optional($solicitud->solicitante)->usuario->nombre ?? 'Sin Nombre') }}
                                    </div>
                                    <div class="product-id">{{ optional($solicitud->solicitante)->rfc ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td>{{ optional($solicitud->solicitante)->tipo_persona ?? 'Desconocido' }}</td>
                            <td>
                                <span class="tipo-tramite-badge">
                                    {{ $solicitud->tipo_tramite ?? 'Desconocido' }}
                                </span>
                            </td>
                            <td>
                                <span class="estado-badge">
                                    {{ $solicitud->estado ?: 'No asignado' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($solicitud->fecha_finalizacion)
                                    @if(isset($solicitud->tiempo_restante))
                                        @if($solicitud->tiempo_restante['vencido'])
                                            <span class="tiempo-chip vencido">
                                                <i class="fas fa-exclamation-circle"></i> Vencido
                                            </span>
                                        @else
                                            @php
                                                $horasRestantes = $solicitud->tiempo_restante['horas'];
                                                $prioridad = '';
                                                if ($horasRestantes <= 12) {
                                                    $prioridad = 'critica';
                                                } elseif ($horasRestantes <= 24) {
                                                    $prioridad = 'alta';
                                                } elseif ($horasRestantes <= 48) {
                                                    $prioridad = 'media';
                                                } else {
                                                    $prioridad = 'normal';
                                                }
                                            @endphp
                                            <span class="tiempo-chip {{ $prioridad }}">
                                                @if($prioridad === 'critica')
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                @elseif($prioridad === 'alta')
                                                    <i class="fas fa-arrow-up"></i>
                                                @elseif($prioridad === 'media')
                                                    <i class="fas fa-arrow-right"></i>
                                                @else
                                                    <i class="fas fa-check"></i>
                                                @endif
                                                {{ $solicitud->tiempo_restante['horas'] }}h {{ $solicitud->tiempo_restante['minutos'] }}m
                                            </span>
                                        @endif
                                    @endif
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="action-buttons">
                                    <a href="{{ route('revision.show', $solicitud->id) }}" class="btn-action view-btn" title="Ver detalles" data-id="{{ $solicitud->id }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('revision.iniciar', $solicitud->id) }}" class="btn-action begin-review-btn" title="Comenzar revisión" data-id="{{ $solicitud->id }}">
                                        <i class="fas fa-clipboard-check"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay solicitudes pendientes de revisión</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.paginacion', ['paginator' => $solicitudes])
    </div>

    <style>
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

        .estado-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
            background-color: #f5f5f5;
            color: #333;
        }

        .controls-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-container {
            position: relative;
            max-width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 8px 8px 8px 32px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-filter {
            padding: 8px 16px;
            background-color: #f0f0f0;
            color: #666;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-filter.active {
            background-color: var(--primary-color, #007bff);
            color: white;
        }

        .btn-filter:hover {
            opacity: 0.9;
            text-decoration: none;
            color: currentColor;
        }

        .text-center {
            text-align: center !important;
        }

        .action-buttons {
            display: inline-flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 4px;
            background-color: var(--primary-color, #007bff);
            color: white;
            text-decoration: none;
        }

        .btn-action:hover {
            opacity: 0.9;
            color: white;
        }

        .tiempo-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.9em;
            font-weight: 500;
            white-space: nowrap;
        }

        .tiempo-chip.vencido {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .tiempo-chip.critica {
            background-color: #dc2626;
            color: white;
        }

        .tiempo-chip.alta {
            background-color: #ea580c;
            color: white;
        }

        .tiempo-chip.media {
            background-color: #eab308;
            color: white;
        }

        .tiempo-chip.normal {
            background-color: #16a34a;
            color: white;
        }
    </style>

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
        });
    </script>
@endsection