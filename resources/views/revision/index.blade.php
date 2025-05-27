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
                        <th>Fecha Inicio</th>
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
                            <td>
                                <div>{{ \Carbon\Carbon::parse($solicitud->fecha_inicio)->format('d M Y') }}</div>
                                <div class="time-small">{{ \Carbon\Carbon::parse($solicitud->fecha_inicio)->format('H:i') }}</div>
                            </td>
                            <td>
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

        .search-container {
            position: relative;
            max-width: 300px;
            margin-bottom: 20px;
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