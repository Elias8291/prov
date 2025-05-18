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
                        <th>ID</th>
                        <th>Razón Social</th>
                        <th>RFC</th>
                        <th>Estado</th>
                        <th>Registro</th>
                        <th>Vencimiento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $proveedor)
                        <tr>
                            <td>{{ $proveedor->pv }}</td>
                            <td class="product-name-cell">
                                <div>
                                    <div class="product-name">{{ Str::limit($proveedor->razon_social, 40) }}</div>
                                    <div class="product-id">{{ $proveedor->rfc }}</div>
                                </div>
                            </td>
                            <td class="rfc">{{ $proveedor->rfc }}</td>
                            <td>
                                <span class="status-badge {{ strtolower($proveedor->estado) == 'activo' ? 'active' : 'inactive' }}">
                                    {{ $proveedor->estado }}
                                </span>
                            </td>
                            <td>
                                @if ($proveedor->fecha_registro)
                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d', $proveedor->fecha_registro)->format('d M Y') }}
                                @else
                                    <span class="text-muted">No asignada</span>
                                @endif
                            </td>
                            <td>
                                @if ($proveedor->fecha_vencimiento)
                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d', $proveedor->fecha_vencimiento)->format('d M Y') }}
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

        <!-- Pagination -->
        <div class="custom-pagination">
            @if ($proveedores->onFirstPage())
                <span class="pagination-arrow disabled">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $proveedores->appends(['days' => $days, 'status' => $status])->previousPageUrl() }}"
                    class="pagination-arrow">
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

            @if ($showStartEllipsis)
                <a href="{{ $proveedores->appends(['days' => $days, 'status' => $status])->url(1) }}"
                    class="pagination-number {{ $currentPage == 1 ? 'active' : '' }}">1</a>
                <span class="pagination-ellipsis">...</span>
            @endif

            @for ($i = $startPage; $i <= $endPage; $i++)
                <a href="{{ $proveedores->appends(['days' => $days, 'status' => $status])->url($i) }}"
                    class="pagination-number {{ $currentPage == $i ? 'active' : '' }}">
                    {{ $i }}
                </a>
            @endfor

            @if ($showEndEllipsis)
                <span class="pagination-ellipsis">...</span>
                <a href="{{ $proveedores->appends(['days' => $days, 'status' => $status])->url($lastPage) }}"
                    class="pagination-number {{ $currentPage == $lastPage ? 'active' : '' }}">
                    {{ $lastPage }}
                </a>
            @endif

            @if ($proveedores->hasMorePages())
                <a href="{{ $proveedores->appends(['days' => $days, 'status' => $status])->nextPageUrl() }}"
                    class="pagination-arrow">
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
                    <div class="form-group">
                        <label>ID Proveedor:</label>
                        <p id="view_pv" class="form-text"></p>
                    </div>
                    <div class="form-group">
                        <label>Razón Social:</label>
                        <p id="view_razon_social" class="form-text"></p>
                    </div>
                    <div class="form-group">
                        <label>RFC:</label>
                        <p id="view_rfc" class="form-text"></p>
                    </div>
                    <div class="form-group">
                        <label>Estado:</label>
                        <p id="view_estado" class="form-text"></p>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Registro:</label>
                        <p id="view_fecha_registro" class="form-text"></p>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Vencimiento:</label>
                        <p id="view_fecha_vencimiento" class="form-text"></p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeViewModal">Cerrar</button>
            </div>
        </div>
    </div>

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
            function attachViewButtonListeners() {
                document.querySelectorAll('.view-btn').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const row = this.closest('tr');
                        if (row) {
                            const pv = row.cells[0].textContent.trim();
                            const razonSocial = row.querySelector('.product-name').textContent.trim();
                            const rfc = row.querySelector('.rfc').textContent.trim();
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
                    let searchUrl = `{{ route('proveedores.search') }}?term=${encodeURIComponent(searchTerm)}`;
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
                    url = `{{ route('proveedores.search') }}?term=${encodeURIComponent(searchTerm)}`;
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
                        formattedFechaRegistro = `${day} ${new Date(year, month - 1).toLocaleString('es', { month: 'short' })} ${year}`;
                    }

                    let formattedFechaVencimiento = '<span class="text-muted">No asignada</span>';
                    if (proveedor.fecha_vencimiento) {
                        const [year, month, day] = proveedor.fecha_vencimiento.split('-');
                        formattedFechaVencimiento = `${day} ${new Date(year, month - 1).toLocaleString('es', { month: 'short' })} ${year}`;
                    }

                    row.innerHTML = `
                        <td>${proveedor.pv}</td>
                        <td class="product-name-cell">
                            <div>
                                <div class="product-name">${proveedor.razon_social ? proveedor.razon_social.substring(0, 40) + (proveedor.razon_social.length > 40 ? '...' : '') : ''}</div>
                                <div class="product-id">${proveedor.rfc || ''}</div>
                            </td>
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

                attachViewButtonListeners();
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
            attachViewButtonListeners();
        });
    </script>
@endsection