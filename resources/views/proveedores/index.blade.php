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
            
            <!-- Controls Bar with Search -->
            <div class="controls-bar">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Buscar proveedores por razón social o RFC...">
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
                                <td>{{ \Carbon\Carbon::parse($proveedor->fecha_registro)->format('d/m/Y') }}</td>
                                <td>
                                    @if($proveedor->fecha_vencimiento)
                                        {{ \Carbon\Carbon::parse($proveedor->fecha_vencimiento)->format('d/m/Y') }}
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
                    <a href="{{ $proveedores->previousPageUrl() }}" class="pagination-arrow">
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
                    <a href="{{ $proveedores->url(1) }}" class="pagination-number {{ $currentPage == 1 ? 'active' : '' }}">1</a>
                    <span class="pagination-ellipsis">...</span>
                @endif
                
                @for($i = $startPage; $i <= $endPage; $i++)
                    <a href="{{ $proveedores->url($i) }}" class="pagination-number {{ $currentPage == $i ? 'active' : '' }}">
                        {{ $i }}
                    </a>
                @endfor
                
                @if($showEndEllipsis)
                    <span class="pagination-ellipsis">...</span>
                    <a href="{{ $proveedores->url($lastPage) }}" class="pagination-number {{ $currentPage == $lastPage ? 'active' : '' }}">
                        {{ $lastPage }}
                    </a>
                @endif

                @if($proveedores->hasMorePages())
                    <a href="{{ $proveedores->nextPageUrl() }}" class="pagination-arrow">
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
    /* Main Table Styles */
    .table-container {
        overflow-x: auto;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #f0f0f0;
    }

    .table th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 500;
        position: sticky;
        top: 0;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Status Badge Styles */
    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
    }

    .status-badge.active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-badge.inactive {
        background-color: #f8d7da;
        color: #721c24;
    }

    /* Header and Controls */
    .page-title {
        font-size: 1.75rem;
        margin-bottom: 5px;
        color: #2C3E50;
    }

    .page-subtitle {
        color: #6c757d;
        margin-bottom: 20px;
    }

    .controls-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .search-container {
        position: relative;
        flex: 1;
        max-width: 400px;
    }

    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }

    .search-input {
        width: 100%;
        padding: 10px 15px 10px 35px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        font-size: 14px;
    }

    /* Action Button */
    .action-buttons {
        display: flex;
        gap: 5px;
    }

    .btn-action {
        border: none;
        background: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .view-btn {
        color: #3498db;
        background-color: rgba(52, 152, 219, 0.1);
    }

    .view-btn:hover {
        background-color: rgba(52, 152, 219, 0.2);
    }

    /* Enhanced Pagination Styles */
    .custom-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 30px;
        margin-bottom: 20px;
        padding: 10px;
        background: #ffffff;
        border-radius: 50px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        max-width: fit-content;
        margin-left: auto;
        margin-right: auto;
    }

    .pagination-number {
        display: flex;
        justify-content: center;
        align-items: center;
        min-width: 40px;
        height: 40px;
        border-radius: 8px;
        color: #495057;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        margin: 0 2px;
        position: relative;
        overflow: hidden;
        z-index: 1;
        font-size: 14px;
        background-color: transparent;
    }

    .pagination-number:before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 0;
        background-color: #3498db;
        transition: height 0.3s ease;
        z-index: -1;
        border-radius: 8px;
        opacity: 0.1;
    }

    .pagination-number:hover:before {
        height: 100%;
    }

    .pagination-number:hover {
        color: #3498db;
        transform: translateY(-3px);
    }

    .pagination-number.active {
        background: linear-gradient(145deg, #3498db, #2980b9);
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(52, 152, 219, 0.35);
    }

    .pagination-number.active:hover {
        transform: translateY(-2px);
    }

    .pagination-arrow {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(145deg, #f8f9fa, #e9ecef);
        color: #3498db;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 14px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.05);
    }

    .pagination-arrow:hover {
        background: linear-gradient(145deg, #3498db, #2980b9);
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(52, 152, 219, 0.4);
    }

    .pagination-arrow.disabled {
        background: linear-gradient(145deg, #f8f9fa, #e9ecef);
        color: #cbd3da;
        cursor: not-allowed;
        pointer-events: none;
        box-shadow: none;
    }

    .pagination-ellipsis {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #6c757d;
        min-width: 30px;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.3s;
    }

    .modal-content {
        background-color: #fff;
        margin: 5% auto;
        width: 600px;
        max-width: 90%;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.3s;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #e0e0e0;
    }

    .modal-title {
        font-size: 1.25rem;
        margin: 0;
        color: #2C3E50;
    }

    .close-modal {
        font-size: 24px;
        cursor: pointer;
        color: #777;
        transition: color 0.2s;
    }

    .close-modal:hover {
        color: #333;
    }

    .modal-body {
        padding: 20px;
        max-height: 500px;
        overflow-y: auto;
    }

    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #e0e0e0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-secondary {
        background-color: #f8f9fa;
        color: #343a40;
        border: 1px solid #ced4da;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-secondary:hover {
        background-color: #e2e6ea;
    }

    /* Alert Styles */
    .alert {
        padding: 12px 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        font-size: 14px;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Provider Details Styles */
    .provider-details {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
    }

    .detail-group {
        margin-bottom: 15px;
        display: flex;
        border-bottom: 1px dashed #e0e0e0;
        padding-bottom: 10px;
    }

    .detail-group:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .detail-label {
        font-weight: 500;
        color: #495057;
        width: 180px;
        flex-shrink: 0;
    }

    .detail-value {
        color: #212529;
    }

    .text-muted {
        color: #6c757d;
        font-style: italic;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
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
            
            if (searchTerm.length >= 2) {
                // Send AJAX request to search endpoint
                fetch(`{{ route('proveedores.search') }}?term=${searchTerm}`)
                    .then(response => response.json())
                    .then(data => {
                        updateTableWithSearchResults(data);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            } else if (searchTerm.length === 0) {
                // If the search field is cleared, reload the page
                window.location.reload();
            }
        });
        
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
            <td class="razon-social">${proveedor.razon_social}</td>
            <td class="rfc">${proveedor.rfc}</td>
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