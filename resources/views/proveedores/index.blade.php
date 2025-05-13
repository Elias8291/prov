@extends('dashboard')

@section('title', 'Listado de Proveedores - Proveedores de Oaxaca')

<link rel="stylesheet" href="{{ asset('assets/css/tabla.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
    <div class="dashboard-container">
        <!-- Header Section with Title -->
        <h1 class="page-title">Listado de Proveedores</h1>
        <p class="page-subtitle">Consulta de proveedores registrados en la plataforma de Proveedores de Oaxaca</p>
        
        <!-- Controls Bar with Search, Days Filter and Status Filter -->
        <div class="controls-bar">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Buscar por razón social o RFC...">
            </div>
            
            <div class="filters-container">
                <!-- Status Filter -->
                <div class="filter-item">
                    <label for="statusFilter" class="filter-label">Estado:</label>
                    <select class="filter-select" id="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="Activo" {{ $status == 'Activo' ? 'selected' : '' }}>Activos</option>
                        <option value="Inactivo" {{ $status == 'Inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                
                <!-- Days Filter -->
                <div class="filter-item">
                    <label for="daysFilter" class="filter-label">Vencimiento:</label>
                    <select class="filter-select" id="daysFilter">
                        <option value="">Todos los vencimientos</option>
                        <option value="10" {{ $days == '10' ? 'selected' : '' }}>Próximos 10 días</option>
                        <option value="15" {{ $days == '15' ? 'selected' : '' }}>Próximos 15 días</option>
                        <option value="20" {{ $days == '20' ? 'selected' : '' }}>Próximos 20 días</option>
                        <option value="30" {{ $days == '30' ? 'selected' : '' }}>Próximos 30 días</option>
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
        
        <!-- Table Container -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th data-label="ID Proveedor">ID</th>
                        <th data-label="Razón Social">Razón Social</th>
                        <th data-label="RFC">RFC</th>
                        <th data-label="Estado">Estado</th>
                        <th data-label="Fecha Registro">Registro</th>
                        <th data-label="Fecha Vencimiento">Vencimiento</th>
                        <th data-label="Acciones">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $proveedor)
                        <tr>
                            <td data-label="ID Proveedor">{{ $proveedor->pv }}</td>
                            <td data-label="Razón Social" class="razon-social">{{ Str::limit($proveedor->razon_social, 40) }}</td>
                            <td data-label="RFC" class="rfc">{{ $proveedor->rfc }}</td>
                            <td data-label="Estado">
                                <span class="status-badge {{ strtolower($proveedor->estado) == 'activo' ? 'active' : 'inactive' }}">
                                    {{ $proveedor->estado }}
                                </span>
                            </td>
                            <td data-label="Fecha Registro">
                                @if($proveedor->fecha_registro)
                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d', $proveedor->fecha_registro)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">No asignada</span>
                                @endif
                            </td>
                            <td data-label="Fecha Vencimiento">
                                @if($proveedor->fecha_vencimiento)
                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d', $proveedor->fecha_vencimiento)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">No asignada</span>
                                @endif
                            </td>
                            <td data-label="Acciones">
                                <div class="action-buttons">
                                    <button class="btn-action view-btn" data-id="{{ $proveedor->pv }}" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron proveedores</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Stylish Pagination -->
        <div class="custom-pagination">
            @if($proveedores->onFirstPage())
                <span class="pagination-arrow disabled">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $proveedores->appends(['days' => $days, 'status' => $status])->previousPageUrl() }}" class="pagination-arrow">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            @php
                $currentPage = $proveedores->currentPage();
                $lastPage = $proveedores->lastPage();
                $startPage = max(1, min($currentPage - 2, $lastPage - 4));
                $endPage = min($lastPage, max(5, $currentPage + 2));
                $showStartEllipsis = $startPage > 1;
                $showEndEllipsis = $endPage < $lastPage;
            @endphp
            
            @if($showStartEllipsis)
                <a href="{{ $proveedores->appends(['days' => $days, 'status' => $status])->url(1) }}" class="pagination-number {{ $currentPage == 1 ? 'active' : '' }}">1</a>
                <span class="pagination-ellipsis">...</span>
            @endif
            
            @for($i = $startPage; $i <= $endPage; $i++)
                <a href="{{ $proveedores->appends(['days' => $days, 'status' => $status])->url($i) }}" class="pagination-number {{ $currentPage == $i ? 'active' : '' }}">
                    {{ $i }}
                </a>
            @endfor
            
            @if($showEndEllipsis)
                <span class="pagination-ellipsis">...</span>
                <a href="{{ $proveedores->appends(['days' => $days, 'status' => $status])->url($lastPage) }}" class="pagination-number {{ $currentPage == $lastPage ? 'active' : '' }}">
                    {{ $lastPage }}
                </a>
            @endif

            @if($proveedores->hasMorePages())
                <a href="{{ $proveedores->appends(['days' => $days, 'status' => $status])->nextPageUrl() }}" class="pagination-arrow">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="pagination-arrow disabled">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>

    <!-- View Provider Details Modal -->
    <div id="viewProveedorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Detalles del Proveedor</h2>
                <span class="close-modal">×</span>
            </div>
            
            <div class="modal-body">
                <div class="provider-details">
                    <div class="detail-group">
                        <span class="detail-label">ID Proveedor:</span>
                        <span class="detail-value" id="view_pv"></span>
                    </div>
                    
                    <div class="detail-group">
                        <span class="detail-label">Razón Social:</span>
                        <span class="detail-value" id="view_razon_social"></span>
                    </div>
                    
                    <div class="detail-group">
                        <span class="detail-label">RFC:</span>
                        <span class="detail-value" id="view_rfc"></span>
                    </div>
                    
                    <div class="detail-group">
                        <span class="detail-label">Estado:</span>
                        <span class="detail-value" id="view_estado"></span>
                    </div>
                    
                    <div class="detail-group">
                        <span class="detail-label">Fecha de Registro:</span>
                        <span class="detail-value" id="view_fecha_registro"></span>
                    </div>
                    
                    <div class="detail-group">
                        <span class="detail-label">Fecha de Vencimiento:</span>
                        <span class="detail-value" id="view_fecha_vencimiento"></span>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeViewModal">Cerrar</button>
            </div>
        </div>
    </div>

    <style>
        /* Guinda Color Scheme */
        :root {
            --guinda: #800000;
            --guinda-light: #a52a2a;
            --guinda-dark: #660000;
        }

        /* Compact Dashboard Container */
        .dashboard-container {
            padding: 15px;
            max-width: 1220px; /* Compact container */
            margin: 0 auto;
            box-sizing: border-box;
        }

        /* Table Container */
        .table-container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 0 10px;
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

        /* Adjusted Column Widths */
        .table th:nth-child(1),
        .table td:nth-child(1) { width: 7%; } /* ID Proveedor */
        .table th:nth-child(2),
        .table td:nth-child(2) { width: 35%; } /* Razón Social (increased) */
        .table th:nth-child(3),
        .table td:nth-child(3) { width: 15%; } /* RFC */
        .table th:nth-child(4),
        .table td:nth-child(4) { width: 10%; } /* Estado */
        .table th:nth-child(5),
        .table td:nth-child(5) { width: 15%; } /* Fecha Registro */
        .table th:nth-child(6),
        .table td:nth-child(6) { width: 15%; } /* Fecha Vencimiento */
        .table th:nth-child(7),
        .table td:nth-child(7) { 
            width: 5%; /* Acciones (reduced) */
            text-align: center; /* Center icon */
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        /* Status Badge */
        .status-badge {
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 500;
            display: inline-block;
        }

        .status-badge.active {
            background: #28a745;
            color:  #fff;
        }

        .status-badge.inactive {
            background: #6c757d; /* Gray for inactive */
            color: #fff;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: center; /* Center icon */
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

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
        }

        .modal-content {
            background: #fff;
            max-width: 500px;
            margin: 5% auto;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--guinda);
        }

        .close-modal {
            cursor: pointer;
            font-size: 20px;
            color: #666;
        }

        .close-modal:hover {
            color: var(--guinda);
        }

        .modal-body {
            padding: 15px 0;
        }

        .detail-group {
            display: flex;
            margin-bottom: 12px;
        }

        .detail-label {
            font-weight: 600;
            color: var(--guinda);
            width: 130px;
            font-size: 13px;
        }

        .detail-value {
            color: #333;
            flex: 1;
            font-size: 13px;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
            text-align: right;
        }

        .btn-secondary {
            background: var(--guinda);
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
        }

        .btn-secondary:hover {
            background: var(--guinda-light);
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
            max-width: 300px; /* Larger search input */
        }

        .search-input {
            width: 100%;
            padding: 8px 12px 8px 36px;
            border: 2px solid var(--guinda);
            border-radius: 25px; /* Rounded design */
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

        /* Pagination Styles */
        .custom-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
        }

        .pagination-arrow {
            padding: 6px 10px;
            border-radius: 5px;
            background: #f8f9fa;
            color: var(--guinda);
            text-decoration: none;
            font-size: 13px;
        }

        .pagination-arrow:hover {
            background: #e9ecef;
            color: var(--guinda-light);
        }

        .pagination-arrow.disabled {
            color: #ccc;
            pointer-events: none;
        }

        .pagination-number {
            padding: 6px 10px;
            border-radius: 5px;
            background: #f8f9fa;
            color: var(--guinda);
            text-decoration: none;
            font-size: 13px;
        }

        .pagination-number.active {
            background: var(--guinda);
            color: #fff;
        }

        .pagination-number:hover {
            background: #e9ecef;
            color: var(--guinda-light);
        }

        .pagination-ellipsis {
            padding: 6px 10px;
            color: #666;
            font-size: 13px;
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

            .table tbody, .table tr, .table td {
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

            .table td:nth-child(7) {
                text-align: center; /* Keep Acciones centered */
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
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // View Modal Elements
            const viewModal = document.getElementById('viewProveedorModal');
            const closeViewBtn = viewModal.querySelector('.close-modal');
            const cancelViewBtn = document.getElementById('closeViewModal');
            const errorAlert = document.getElementById('errorAlert');
            
            // Close View Modal Function
            function closeViewModal() {
                viewModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
            
            // Close View Modal with X button
            closeViewBtn.addEventListener('click', closeViewModal);
            
            // Close View Modal with Close button
            cancelViewBtn.addEventListener('click', closeViewModal);
            
            // Open View Modal when view button is clicked
            document.querySelectorAll('.view-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    if (row) {
                        const pv = row.cells[0].textContent.trim();
                        const razonSocial = row.cells[1].textContent.trim();
                        const rfc = row.cells[2].textContent.trim();
                        const estado = row.cells[3].textContent.trim();
                        const fechaRegistro = row.cells[4].textContent.trim();
                        const fechaVencimiento = row.cells[5].textContent.trim();
                        
                        document.getElementById('view_pv').textContent = pv;
                        document.getElementById('view_razon_social').textContent = razonSocial;
                        document.getElementById('view_rfc').textContent = rfc;
                        document.getElementById('view_estado').textContent = estado;
                        document.getElementById('view_fecha_registro').textContent = fechaRegistro;
                        document.getElementById('view_fecha_vencimiento').textContent = fechaVencimiento;
                        
                        viewModal.style.display = 'block';
                        document.body.style.overflow = 'hidden';
                    }
                });
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === viewModal) {
                    closeViewModal();
                }
            });
            
            // Table Search Functionality
            const searchInput = document.querySelector('.search-input');
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const status = document.getElementById('statusFilter').value;
                const days = document.getElementById('daysFilter').value;
                
                if (searchTerm.length >= 2) {
                    let searchUrl = `{{ route('proveedores.search') }}?term=${searchTerm}`;
                    if (status) searchUrl += `&status=${status}`;
                    if (days) searchUrl += `&days=${days}`;
                    
                    fetch(searchUrl)
                        .then(response => response.json())
                        .then(data => {
                            updateTableWithSearchResults(data);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showAlert(errorAlert, 'Error al buscar proveedores.');
                        });
                } else if (searchTerm.length === 0) {
                    if (!status && !days) {
                        window.location.reload();
                    } else {
                        applyFilters();
                    }
                }
            });
            
            // Apply Filters Button
            const applyFiltersBtn = document.getElementById('applyFilters');
            applyFiltersBtn.addEventListener('click', function() {
                applyFilters();
            });
            
            // Function to apply filters
            function applyFilters() {
                const status = document.getElementById('statusFilter').value;
                const days = document.getElementById('daysFilter').value;
                const searchTerm = document.querySelector('.search-input').value;
                
                let url = `{{ route('proveedores.index') }}?`;
                if (days) url += `days=${days}&`;
                if (status) url += `status=${status}&`;
                
                if (searchTerm && searchTerm.length >= 2) {
                    url = `{{ route('proveedores.search') }}?term=${searchTerm}`;
                    if (days) url += `&days=${days}`;
                    if (status) url += `&status=${status}`;
                    
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            updateTableWithSearchResults(data);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showAlert(errorAlert, 'Error al buscar proveedores.');
                        });
                } else {
                    window.location.href = url;
                }
            }
            
            // Function to update table with search results
            function updateTableWithSearchResults(proveedores) {
                const tableBody = document.querySelector('tbody');
                tableBody.innerHTML = '';
                
                if (proveedores.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = '<td colspan="7" class="text-center">No se encontraron resultados</td>';
                    tableBody.appendChild(row);
                    return;
                }
                
                proveedores.forEach(proveedor => {
                    const row = document.createElement('tr');
                    let formattedFechaRegistro = '<span class="text-muted">No asignada</span>';
                    if (proveedor.fecha_registro) {
                        const [year, month, day] = proveedor.fecha_registro.split('-');
                        formattedFechaRegistro = `${day}/${month}/${year}`;
                    }
                    
                    let formattedFechaVencimiento = '<span class="text-muted">No asignada</span>';
                    if (proveedor.fecha_vencimiento) {
                        const [year, month, day] = proveedor.fecha_vencimiento.split('-');
                        formattedFechaVencimiento = `${day}/${month}/${year}`;
                    }
                    
                    row.innerHTML = `
                        <td data-label="ID Proveedor">${proveedor.pv}</td>
                        <td data-label="Razón Social" class="razon-social">${proveedor.razon_social ? proveedor.razon_social.substring(0, 40) + (proveedor.razon_social.length > 40 ? '...' : '') : ''}</td>
                        <td data-label="RFC" class="rfc">${proveedor.rfc || ''}</td>
                        <td data-label="Estado">
                            <span class="status-badge ${proveedor.estado.toLowerCase() === 'activo' ? 'active' : 'inactive'}">
                                ${proveedor.estado}
                            </span>
                        </td>
                        <td data-label="Fecha Registro">${formattedFechaRegistro}</td>
                        <td data-label="Fecha Vencimiento">${formattedFechaVencimiento}</td>
                        <td data-label="Acciones">
                            <div class="action-buttons">
                                <button class="btn-action view-btn" data-id="${proveedor.pv}" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    
                    tableBody.appendChild(row);
                });
                
                attachEventListenersToButtons();
            }
            
            // Function to attach event listeners to dynamically created buttons
            function attachEventListenersToButtons() {
                document.querySelectorAll('.view-btn').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const row = this.closest('tr');
                        if (row) {
                            const pv = row.cells[0].textContent.trim();
                            const razonSocial = row.cells[1].textContent.trim();
                            const rfc = row.cells[2].textContent.trim();
                            const estado = row.cells[3].textContent.trim();
                            const fechaRegistro = row.cells[4].textContent.trim();
                            const fechaVencimiento = row.cells[5].textContent.trim();
                            
                            document.getElementById('view_pv').textContent = pv;
                            document.getElementById('view_razon_social').textContent = razonSocial;
                            document.getElementById('view_rfc').textContent = rfc;
                            document.getElementById('view_estado').textContent = estado;
                            document.getElementById('view_fecha_registro').textContent = fechaRegistro;
                            document.getElementById('view_fecha_vencimiento').textContent = fechaVencimiento;
                            
                            viewModal.style.display = 'block';
                            document.body.style.overflow = 'hidden';
                        }
                    });
                });
            }
            
            // Show alert message
            function showAlert(alertElement, message) {
                alertElement.textContent = message;
                alertElement.style.display = 'block';
                setTimeout(function() {
                    alertElement.style.display = 'none';
                }, 5000);
            }
            
            // Initialize event listeners
            attachEventListenersToButtons();
        });
    </script>
@endsection