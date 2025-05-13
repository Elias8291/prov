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
                            <option value="Activo">Activos</option>
                        </select>
                    </div>
                    
                    <!-- Days Filter -->
                    <div class="filter-item">
                        <label for="daysFilter" class="filter-label">Vencimiento:</label>
                        <select class="filter-select" id="daysFilter">
                            <option value="">Todos los vencimientos</option>
                            <option value="10">Próximos 10 días</option>
                            <option value="15">Próximos 15 días</option>
                            <option value="20">Próximos 20 días</option>
                            <option value="30">Próximos 30 días</option>
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
                        <!-- Placeholder for table rows -->
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron proveedores</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Stylish Pagination -->
            <div class="custom-pagination">
                <span class="pagination-arrow disabled">
                    <i class="fas fa-chevron-left"></i>
                </span>
                <a href="#" class="pagination-number active">1</a>
                <span class="pagination-arrow disabled">
                    <i class="fas fa-chevron-right"></i>
                </span>
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
        /* General Layout */
        .dashboard-container {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .content-wrapper {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 10px;
        }

        .page-subtitle {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 20px;
        }

        /* Controls Bar */
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

        .search-container {
            position: relative;
            max-width: 300px;
            width: 100%;
        }

        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .search-input {
            width: 100%;
            padding: 8px 12px 8px 35px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 14px;
        }

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

        /* Table Styles */
        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #343a40;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
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

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            color: #6c757d;
            transition: color 0.3s;
        }

        .btn-action:hover {
            color: #343a40;
        }

        /* Pagination */
        .custom-pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 5px;
            background: #f8f9fa;
            color: #6c757d;
            text-decoration: none;
            transition: background 0.3s;
        }

        .pagination-arrow:hover {
            background: #e9ecef;
        }

        .pagination-arrow.disabled {
            background: #f8f9fa;
            color: #ced4da;
            pointer-events: none;
        }

        .pagination-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 5px;
            background: #f8f9fa;
            color: #343a40;
            text-decoration: none;
            transition: background 0.3s;
        }

        .pagination-number:hover {
            background: #e9ecef;
        }

        .pagination-number.active {
            background: #3498db;
            color: white;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            overflow: auto;
        }

        .modal-content {
            background: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #343a40;
        }

        .close-modal {
            font-size: 24px;
            cursor: pointer;
            color: #6c757d;
        }

        .close-modal:hover {
            color: #343a40;
        }

        .modal-body {
            padding: 20px 0;
        }

        .provider-details {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .detail-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .detail-label {
            font-weight: 500;
            color: #495057;
        }

        .detail-value {
            color: #343a40;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            text-align: right;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        /* Alert */
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
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
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === viewModal) {
                closeViewModal();
            }
        });

        // Show alert message
        function showAlert(alertElement, message) {
            alertElement.textContent = message;
            alertElement.style.display = 'block';
            
            // Auto hide after 5 seconds
            setTimeout(function() {
                alertElement.style.display = 'none';
            }, 5000);
        }
    });
    </script>
@endsection