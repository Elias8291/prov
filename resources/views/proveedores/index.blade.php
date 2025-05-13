@extends('dashboard')

@section('title', 'Listado de Proveedores - Proveedores de Oaxaca')

<link rel="stylesheet" href="{{ asset('assets/css/tabla.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
    <div class="dashboard-container">
        <div class="content-wrapper">
            <!-- Header Section with Title -->
            <h1 class="page-title">Listado de Proveedores</h1>
            <p class="page-subtitle">Consulta de proveedores registrados en la plataforma de Proveedores de Oaxaca</p>
            
            <!-- Controls Bar with Search, Days Filter and Status Filter -->
            <div class="controls-bar">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Buscar proveedores por razón social o RFC...">
                </div>
                
                <div class="filters-container">
                    <!-- Status Filter -->
                    <div class="filter-item">
                        <label for="statusFilter" class="filter-label">Estado:</label>
                        <select class="filter-select" id="statusFilter">
                            <option value="">Todos los estados</option>
                            <option value="Activo" {{ $status == 'Activo' ? 'selected' : '' }}>Activos</option>
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
                        <i class="fas fa-filter"></i> Aplicar Filtros
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
                            <th>ID Proveedor</th>
                            <th>Razón Social</th>
                            <th>RFC</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Fecha Vencimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proveedores as $proveedor)
                            <tr>
                                <td>{{ $proveedor->pv }}</td>
                                <td class="razon-social">{{ $proveedor->razon_social }}</td>
                                <td class="rfc">{{ $proveedor->rfc }}</td>
                                <td>
                                    <span class="status-badge {{ strtolower($proveedor->estado) == 'activo' ? 'active' : 'inactive' }}">
                                        {{ $proveedor->estado }}
                                    </span>
                                </td>
                                <td>
                                    @if($proveedor->fecha_registro)
                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $proveedor->fecha_registro)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">No asignada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($proveedor->fecha_vencimiento)
                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $proveedor->fecha_vencimiento)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">No asignada</span>
                                    @endif
                                </td>
                                <td>
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
            
            <!-- Stylish Pagination with enhanced styling -->
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
                    
                    // Logic to show limited page numbers with ellipsis
                    $startPage = max(1, min($currentPage - 2, $lastPage - 4));
                    $endPage = min($lastPage, max(5, $currentPage + 2));
                    
                    if ($startPage > 1) {
                        $showStartEllipsis = true;
                    } else {
                        $showStartEllipsis = false;
                    }
                    
                    if ($endPage < $lastPage) {
                        $showEndEllipsis = true;
                    } else {
                        $showEndEllipsis = false;
                    }
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
    </div>

    <!-- View Provider Details Modal (unchanged) -->
    <div id="viewProveedorModal" class="modal">
        <!-- Modal content is unchanged -->
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
    /* Main Table Styles (unchanged) */
    /* ... existing styles ... */
    
    /* New Filter Controls Styles */
    .filters-container {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .filter-item {
        display: flex;
        align-items: center;
    }
    
    .filter-label {
        margin-right: 8px;
        font-weight: 500;
        color: #495057;
    }
    
    .filter-select {
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        font-size: 14px;
        color: #495057;
        background-color: #fff;
        min-width: 160px;
    }
    
    .filter-button {
        background: linear-gradient(145deg, #3498db, #2980b9);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 5px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .filter-button:hover {
        background: linear-gradient(145deg, #2980b9, #3498db);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    /* Updated Controls Bar Layout */
    .controls-bar {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    @media (min-width: 992px) {
        .controls-bar {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // =============================================
        // VIEW PROVEEDOR MODAL FUNCTIONS
        // =============================================
        
        // View Modal Elements
        const viewModal = document.getElementById('viewProveedorModal');
        const closeViewBtn = viewModal.querySelector('.close-modal');
        const cancelViewBtn = document.getElementById('closeViewModal');
        
        // Error alert element
        const errorAlert = document.getElementById('errorAlert');
        
        // Close View Modal Function
        function closeViewModal() {
            viewModal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Re-enable scrolling
        }
        
        // Close View Modal with X button
        closeViewBtn.addEventListener('click', closeViewModal);
        
        // Close View Modal with Close button
        cancelViewBtn.addEventListener('click', closeViewModal);
        
        // Open View Modal when view button is clicked
        document.querySelectorAll('.view-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const proveedorId = this.getAttribute('data-id');
                
                // Find the row data directly from the table
                const row = this.closest('tr');
                if (row) {
                    const pv = row.cells[0].textContent.trim();
                    const razonSocial = row.cells[1].textContent.trim();
                    const rfc = row.cells[2].textContent.trim();
                    const estado = row.cells[3].textContent.trim();
                    const fechaRegistro = row.cells[4].textContent.trim();
                    const fechaVencimiento = row.cells[5].textContent.trim();
                    
                    // Fill the modal with the data
                    document.getElementById('view_pv').textContent = pv;
                    document.getElementById('view_razon_social').textContent = razonSocial;
                    document.getElementById('view_rfc').textContent = rfc;
                    document.getElementById('view_estado').textContent = estado;
                    document.getElementById('view_fecha_registro').textContent = fechaRegistro;
                    document.getElementById('view_fecha_vencimiento').textContent = fechaVencimiento;
                    
                    viewModal.style.display = 'block';
                    document.body.style.overflow = 'hidden'; // Prevent scrolling
                }
            });
        });
        
        // =============================================
        // SHARED FUNCTIONALITY
        // =============================================
        
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
                // Send AJAX request to search endpoint
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
                    });
            } else if (searchTerm.length === 0) {
                // If the search field is cleared and no filters, reload the page
                if (!status && !days) {
                    window.location.reload();
                } else {
                    // Otherwise, apply just the filters
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
            
            // Build the URL with filters
            let url = `{{ route('proveedores.index') }}?`;
            if (days) url += `days=${days}&`;
            if (status) url += `status=${status}&`;
            
            // If there's a search term, use search endpoint instead
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
                // Otherwise navigate to the index with filters
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
                
                // Format dates
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
                    <td>${proveedor.pv}</td>
                    <td class="razon-social">${proveedor.razon_social || ''}</td>
                    <td class="rfc">${proveedor.rfc || ''}</td>
                    <td>
                        <span class="status-badge ${proveedor.estado.toLowerCase() === 'activo' ? 'active' : 'inactive'}">
                            ${proveedor.estado}
                        </span>
                    </td>
                    <td>${formattedFechaRegistro}</td>
                    <td>${formattedFechaVencimiento}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action view-btn" data-id="${proveedor.pv}" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
            
            // Reattach event listeners to the new buttons
            attachEventListenersToButtons();
        }
        
        // Function to attach event listeners to dynamically created buttons
        function attachEventListenersToButtons() {
            // View button functionality
            document.querySelectorAll('.view-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const proveedorId = this.getAttribute('data-id');
                    
                    // Find the row data directly from the table
                    const row = this.closest('tr');
                    if (row) {
                        const pv = row.cells[0].textContent.trim();
                        const razonSocial = row.cells[1].textContent.trim();
                        const rfc = row.cells[2].textContent.trim();
                        const estado = row.cells[3].textContent.trim();
                        const fechaRegistro = row.cells[4].textContent.trim();
                        const fechaVencimiento = row.cells[5].textContent.trim();
                        
                        // Fill the modal with the data
                        document.getElementById('view_pv').textContent = pv;
                        document.getElementById('view_razon_social').textContent = razonSocial;
                        document.getElementById('view_rfc').textContent = rfc;
                        document.getElementById('view_estado').textContent = estado;
                        document.getElementById('view_fecha_registro').textContent = fechaRegistro;
                        document.getElementById('view_fecha_vencimiento').textContent = fechaVencimiento;
                        
                        viewModal.style.display = 'block';
                        document.body.style.overflow = 'hidden'; // Prevent scrolling
                    }
                });
            });
        }
        
        // Show alert message
        function showAlert(alertElement, message) {
            alertElement.textContent = message;
            alertElement.style.display = 'block';
            
            // Auto hide after 5 seconds
            setTimeout(function() {
                alertElement.style.display = 'none';
            }, 5000);
        }
        
        // Initialize event listeners
        attachEventListenersToButtons();
    });
    </script>
@endsection