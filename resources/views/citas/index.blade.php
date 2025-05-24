@extends('dashboard')

@section('title', '¡Bienvenidos a Proveedores de Oaxaca!')

<link rel="stylesheet" href="{{ asset('assets/css/tabla.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
<div class="dashboard-container">
    <div class="content-wrapper">
        <!-- Display Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header Section with Title -->
        <h1 class="page-title">Administración de Citas</h1>
        <p class="page-subtitle">Gestiona todas las citas y días inhábiles en la plataforma de Proveedores de Oaxaca</p>
        
        <!-- Controls Bar with Search and Buttons -->
        <div class="controls-bar">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Buscar citas por solicitante, trámite o fecha...">
            </div>
            
            <div class="button-group">
                <button class="btn-secondary">
                    <i class="fas fa-filter btn-icon"></i>
                    Filtrar
                </button>
                <button class="btn-primary" id="openViewNonWorkingDaysModalBtn">
                    <i class="fas fa-calendar-xmark btn-icon"></i>
                    Ver Días Inhábiles
                </button>
                <button class="btn-primary" id="openNonWorkingDayModalBtn">
                    <i class="fas fa-plus btn-icon"></i>
                    Agregar Día Inhábil
                </button>
            </div>
        </div>
        
        <!-- Table Container for Citas -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Solicitante</th>
                        <th>Trámite</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($citas as $cita)
                        <tr>
                            <td>{{ $cita->id }}</td>
                            <td class="product-name-cell">
                                <div>
                                    <div class="product-name">{{ $cita->solicitante->nombre ?? 'N/A' }}</div>
                                    <div class="product-id">Creado {{ $cita->created_at->format('d M Y') }}</div>
                                </div>
                            </td>
                            <td>{{ $cita->tramite->nombre ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}</td>
                            <td>{{ $cita->hora_cita }}</td>
                            <td>
                                <div class="status-indicator status-{{ strtolower($cita->estado) }}">
                                    {{ $cita->estado }}
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action view-btn" 
                                            data-id="{{ $cita->id }}"
                                            data-solicitante="{{ $cita->solicitante->nombre ?? 'N/A' }}"
                                            data-tramite="{{ $cita->tramite->nombre ?? 'N/A' }}"
                                            data-fecha="{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}"
                                            data-hora="{{ $cita->hora_cita }}"
                                            data-estado="{{ $cita->estado }}"
                                            data-observaciones="{{ $cita->observaciones }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action edit-btn" 
                                            data-id="{{ $cita->id }}"
                                            data-solicitante-id="{{ $cita->solicitante_id }}"
                                            data-tramite-id="{{ $cita->tramite_id }}"
                                            data-fecha="{{ $cita->fecha_cita instanceof \Carbon\Carbon ? $cita->fecha_cita->format('Y-m-d') : $cita->fecha_cita }}"
                                            data-hora="{{ $cita->hora_cita }}"
                                            data-estado="{{ $cita->estado }}"
                                            data-observaciones="{{ $cita->observaciones }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action delete-btn" 
                                            data-id="{{ $cita->id }}"
                                            data-solicitante="{{ $cita->solicitante->nombre ?? 'N/A' }}"
                                            data-tramite="{{ $cita->tramite->nombre ?? 'N/A' }}"
                                            data-fecha="{{ \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Include Custom Pagination -->
        @include('components.paginacion', ['paginator' => $citas])
    </div>
</div>

<!-- View Non-Working Days Modal -->
<div id="viewNonWorkingDaysModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Días Inhábiles</h2>
            <span class="close-modal">×</span>
        </div>
        <div class="modal-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha(s)</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($diasInhabiles as $dia)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($dia->fecha_inicio)->format('d/m/Y') }}
                                    @if ($dia->fecha_fin)
                                        - {{ \Carbon\Carbon::parse($dia->fecha_fin)->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>{{ $dia->descripcion }}</td>
                                <td>
                                    <button class="btn-action delete-non-working-day-btn"
                                            data-id="{{ $dia->id }}"
                                            data-fecha-inicio="{{ \Carbon\Carbon::parse($dia->fecha_inicio)->format('d/m/Y') }}"
                                            data-fecha-fin="{{ $dia->fecha_fin ? \Carbon\Carbon::parse($dia->fecha_fin)->format('d/m/Y') : '' }}"
                                            data-descripcion="{{ $dia->descripcion }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($diasInhabiles->isEmpty())
                <p class="text-muted text-center">No hay días inhábiles registrados.</p>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" id="closeViewNonWorkingDaysModal">Cerrar</button>
        </div>
    </div>
</div>

<!-- Delete Non-Working Day Modal -->
<div id="deleteNonWorkingDayModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Confirmar Eliminación</h2>
            <span class="close-modal">×</span>
        </div>
        
        <form id="deleteNonWorkingDayForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body">
                <p class="delete-message">¿Estás seguro que deseas eliminar este día inhábil?</p>
                <p class="delete-warning">Esta acción no se puede deshacer.</p>
                
                <div class="non-working-day-info">
                    <p><strong>ID:</strong> <span id="delete_non_working_day_id"></span></p>
                    <p><strong>Fecha(s):</strong> <span id="delete_non_working_day_fecha"></span></p>
                    <p><strong>Descripción:</strong> <span id="delete_non_working_day_descripcion"></span></p>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeDeleteNonWorkingDayModal">Cancelar</button>
                <button type="submit" class="btn-danger">
                    <i class="fas fa-trash"></i> Eliminar Día Inhábil
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Non-Working Day Modal -->
<div id="nonWorkingDayModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Agregar Día Inhábil</h2>
            <span class="close-modal">×</span>
        </div>
        
        <form id="addNonWorkingDayForm" action="{{ route('dias_inhabiles.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" required>
                </div>
                
                <div class="form-group">
                    <label for="fecha_fin">Fecha de Fin (Opcional)</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <input type="text" id="descripcion" name="descripcion" class="form-control" maxlength="255" required>
                    <small class="form-text text-muted">Máximo 255 caracteres</small>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeNonWorkingDayModal">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Guardar Día Inhábil
                </button>
            </div>
        </form>
    </div>
</div>
<!-- View Appointment Modal -->
<div id="viewCitaModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Detalles de la Cita</h2>
            <span class="close-modal">×</span>
        </div>
        <div class="modal-body">
            <div class="cita-info">
                <p><strong>ID:</strong> <span id="view_id"></span></p>
                <p><strong>Solicitante:</strong> <span id="view_solicitante"></span></p>
                <p><strong>Trámite:</strong> <span id="view_tramite"></span></p>
                <p><strong>Fecha:</strong> <span id="view_fecha"></span></p>
                <p><strong>Hora:</strong> <span id="view_hora"></span></p>
                <p><strong>Estado:</strong> <span id="view_estado"></span></p>
                <p><strong>Observaciones:</strong> <span id="view_observaciones"></span></p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" id="closeViewCitaModal">Cerrar</button>
        </div>
    </div>
</div>

<!-- Edit Appointment Modal -->
<div id="editCitaModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Editar Cita</h2>
            <span class="close-modal">×</span>
        </div>
        
        <form id="editCitaForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>ID de Cita</label>
                        <input type="text" id="edit_id" class="form-control" readonly disabled>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_solicitante_id">Solicitante</label>
                    <select id="edit_solicitante_id" name="solicitante_id" class="form-control" required>
                        <option value="" selected disabled>Seleccionar solicitante</option>
                        @foreach ($solicitantes as $solicitante)
                            <option value="{{ $solicitante->id }}">{{ $solicitante->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_tramite_id">Trámite</label>
                    <select id="edit_tramite_id" name="tramite_id" class="form-control" required>
                        <option value="" selected disabled>Seleccionar trámite</option>
                        @foreach ($tramites as $tramite)
                            <option value="{{ $tramite->id }}">{{ $tramite->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_fecha_cita">Fecha</label>
                    <input type="date" id="edit_fecha_cita" name="fecha_cita" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_hora_cita">Hora</label>
                    <input type="time" id="edit_hora_cita" name="hora_cita" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_estado">Estado</label>
                    <select id="edit_estado" name="estado" class="form-control" required>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Confirmada">Confirmada</option>
                        <option value="Cancelada">Cancelada</option>
                        <option value="Completada">Completada</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_observaciones">Observaciones</label>
                    <textarea id="edit_observaciones" name="observaciones" class="form-control"></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeEditCitaModal">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Actualizar Cita
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Appointment Modal -->
<div id="deleteCitaModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Confirmar Eliminación</h2>
            <span class="close-modal">×</span>
        </div>
        
        <form id="deleteCitaForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body">
                <p class="delete-message">¿Estás seguro que deseas eliminar esta cita?</p>
                <p class="delete-warning">Esta acción no se puede deshacer.</p>
                
                <div class="cita-info">
                    <p><strong>ID:</strong> <span id="delete_id"></span></p>
                    <p><strong>Solicitante:</strong> <span id="delete_solicitante"></span></p>
                    <p><strong>Trámite:</strong> <span id="delete_tramite"></span></p>
                    <p><strong>Fecha:</strong> <span id="delete_fecha"></span></p>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeDeleteCitaModal">Cancelar</button>
                <button type="submit" class="btn-danger">
                    <i class="fas fa-trash"></i> Eliminar Cita
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // =============================================
    // VARIABLES FOR ALL MODALS
    // =============================================
    
    // View Non-Working Days Modal Elements
    const viewNonWorkingDaysModal = document.getElementById('viewNonWorkingDaysModal');
    const openViewNonWorkingDaysBtn = document.getElementById('openViewNonWorkingDaysModalBtn');
    const closeViewNonWorkingDaysBtn = viewNonWorkingDaysModal.querySelector('.close-modal');
    const cancelViewNonWorkingDaysBtn = document.getElementById('closeViewNonWorkingDaysModal');
    
    // Delete Non-Working Day Modal Elements
    const deleteNonWorkingDayModal = document.getElementById('deleteNonWorkingDayModal');
    const closeDeleteNonWorkingDayBtn = deleteNonWorkingDayModal.querySelector('.close-modal');
    const cancelDeleteNonWorkingDayBtn = document.getElementById('closeDeleteNonWorkingDayModal');
    const deleteNonWorkingDayForm = document.getElementById('deleteNonWorkingDayForm');
    
    // View Appointment Modal Elements
    const viewCitaModal = document.getElementById('viewCitaModal');
    const closeViewCitaBtn = viewCitaModal.querySelector('.close-modal');
    const cancelViewCitaBtn = document.getElementById('closeViewCitaModal');
    
    // Add Non-Working Day Modal Elements
    const nonWorkingDayModal = document.getElementById('nonWorkingDayModal');
    const openNonWorkingDayBtn = document.getElementById('openNonWorkingDayModalBtn');
    const closeNonWorkingDayBtn = nonWorkingDayModal.querySelector('.close-modal');
    const cancelNonWorkingDayBtn = document.getElementById('closeNonWorkingDayModal');
    
    // Edit Appointment Modal Elements
    const editCitaModal = document.getElementById('editCitaModal');
    const closeEditCitaBtn = editCitaModal.querySelector('.close-modal');
    const cancelEditCitaBtn = document.getElementById('closeEditCitaModal');
    const editCitaForm = document.getElementById('editCitaForm');
    
    // Delete Appointment Modal Elements
    const deleteCitaModal = document.getElementById('deleteCitaModal');
    const closeDeleteCitaBtn = deleteCitaModal.querySelector('.close-modal');
    const cancelDeleteCitaBtn = document.getElementById('closeDeleteCitaModal');
    const deleteCitaForm = document.getElementById('deleteCitaForm');
    
    // =============================================
    // VIEW NON-WORKING DAYS MODAL FUNCTIONS
    // =============================================
    
    // Open View Non-Working Days Modal
    openViewNonWorkingDaysBtn.addEventListener('click', function() {
        viewNonWorkingDaysModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    });
    
    // Close View Non-Working Days Modal Function
    function closeViewNonWorkingDaysModal() {
        viewNonWorkingDaysModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Close View Non-Working Days Modal with X button
    closeViewNonWorkingDaysBtn.addEventListener('click', closeViewNonWorkingDaysModal);
    
    // Close View Non-Working Days Modal with Cancel button
    cancelViewNonWorkingDaysBtn.addEventListener('click', closeViewNonWorkingDaysModal);
    
    // =============================================
    // DELETE NON-WORKING DAY MODAL FUNCTIONS
    // =============================================
    
    // Close Delete Non-Working Day Modal Function
    function closeDeleteNonWorkingDayModal() {
        deleteNonWorkingDayModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Close Delete Non-Working Day Modal with X button
    closeDeleteNonWorkingDayBtn.addEventListener('click', closeDeleteNonWorkingDayModal);
    
    // Close Delete Non-Working Day Modal with Cancel button
    cancelDeleteNonWorkingDayBtn.addEventListener('click', closeDeleteNonWorkingDayModal);
    
    // Open Delete Non-Working Day Modal when delete button is clicked
    document.querySelectorAll('.delete-non-working-day-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const diaId = this.getAttribute('data-id');
            const fechaInicio = this.getAttribute('data-fecha-inicio');
            const fechaFin = this.getAttribute('data-fecha-fin');
            const descripcion = this.getAttribute('data-descripcion');
            
            // Set form action
            deleteNonWorkingDayForm.action = `/dias_inhabiles/${diaId}`;
            
            // Format date range for display
            const fechaDisplay = fechaFin ? `${fechaInicio} - ${fechaFin}` : fechaInicio;
            
            // Fill in confirmation details
            document.getElementById('delete_non_working_day_id').textContent = diaId;
            document.getElementById('delete_non_working_day_fecha').textContent = fechaDisplay;
            document.getElementById('delete_non_working_day_descripcion').textContent = descripcion;
            
            // Display the modal
            deleteNonWorkingDayModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    });
    
    // =============================================
    // VIEW APPOINTMENT MODAL FUNCTIONS
    // =============================================
    
    // Close View Appointment Modal Function
    function closeViewCitaModal() {
        viewCitaModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Close View Appointment Modal with X button
    closeViewCitaBtn.addEventListener('click', closeViewCitaModal);
    
    // Close View Appointment Modal with Cancel button
    cancelViewCitaBtn.addEventListener('click', closeViewCitaModal);
    
    // Open View Appointment Modal when view button is clicked
    document.querySelectorAll('.view-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const citaId = this.getAttribute('data-id');
            const solicitante = this.getAttribute('data-solicitante');
            const tramite = this.getAttribute('data-tramite');
            const fecha = this.getAttribute('data-fecha');
            const hora = this.getAttribute('data-hora');
            const estado = this.getAttribute('data-estado');
            const observaciones = this.getAttribute('data-observaciones') || 'Ninguna';
            
            // Fill in details
            document.getElementById('view_id').textContent = citaId;
            document.getElementById('view_solicitante').textContent = solicitante;
            document.getElementById('view_tramite').textContent = tramite;
            document.getElementById('view_fecha').textContent = fecha;
            document.getElementById('view_hora').textContent = hora;
            document.getElementById('view_estado').textContent = estado;
            document.getElementById('view_observaciones').textContent = observaciones;
            
            // Display the modal
            viewCitaModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    });
    
    // =============================================
    // ADD NON-WORKING DAY MODAL FUNCTIONS
    // =============================================
    
    // Open Non-Working Day Modal
    openNonWorkingDayBtn.addEventListener('click', function() {
        nonWorkingDayModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    });
    
    // Close Non-Working Day Modal Function
    function closeNonWorkingDayModal() {
        nonWorkingDayModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Close Non-Working Day Modal with X button
    closeNonWorkingDayBtn.addEventListener('click', closeNonWorkingDayModal);
    
    // Close Non-Working Day Modal with Cancel button
    cancelNonWorkingDayBtn.addEventListener('click', closeNonWorkingDayModal);
    
    // =============================================
    // EDIT APPOINTMENT MODAL FUNCTIONS
    // =============================================
    
    // Close Edit Appointment Modal Function
    function closeEditCitaModal() {
        editCitaModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Close Edit Appointment Modal with X button
    closeEditCitaBtn.addEventListener('click', closeEditCitaModal);
    
    // Close Edit Appointment Modal with Cancel button
    cancelEditCitaBtn.addEventListener('click', closeEditCitaModal);
    
    // Open Edit Appointment Modal when edit button is clicked
    document.querySelectorAll('.edit-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const citaId = this.getAttribute('data-id');
            const solicitanteId = this.getAttribute('data-solicitante-id');
            const tramiteId = this.getAttribute('data-tramite-id');
            const fecha = this.getAttribute('data-fecha');
            const hora = this.getAttribute('data-hora');
            const estado = this.getAttribute('data-estado');
            const observaciones = this.getAttribute('data-observaciones');
            
            // Set form action
            editCitaForm.action = `/citas/${citaId}`;
            
            // Fill form fields
            document.getElementById('edit_id').value = citaId;
            document.getElementById('edit_solicitante_id').value = solicitanteId;
            document.getElementById('edit_tramite_id').value = tramiteId;
            document.getElementById('edit_fecha_cita').value = fecha;
            document.getElementById('edit_hora_cita').value = hora;
            document.getElementById('edit_estado').value = estado;
            document.getElementById('edit_observaciones').value = observaciones || '';
            
            // Display the modal
            editCitaModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    });
    
    // =============================================
    // DELETE APPOINTMENT MODAL FUNCTIONS
    // =============================================
    
    // Close Delete Appointment Modal Function
    function closeDeleteCitaModal() {
        deleteCitaModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Close Delete Appointment Modal with X button
    closeDeleteCitaBtn.addEventListener('click', closeDeleteCitaModal);
    
    // Close Delete Appointment Modal with Cancel button
    cancelDeleteCitaBtn.addEventListener('click', closeDeleteCitaModal);
    
    // Open Delete Appointment Modal when delete button is clicked
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const citaId = this.getAttribute('data-id');
            const solicitante = this.getAttribute('data-solicitante');
            const tramite = this.getAttribute('data-tramite');
            const fecha = this.getAttribute('data-fecha');
            
            // Set form action
            deleteCitaForm.action = `/citas/${citaId}`;
            
            // Fill in confirmation details
            document.getElementById('delete_id').textContent = citaId;
            document.getElementById('delete_solicitante').textContent = solicitante;
            document.getElementById('delete_tramite').textContent = tramite;
            document.getElementById('delete_fecha').textContent = fecha;
            
            // Display the modal
            deleteCitaModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    });
    
    // =============================================
    // SHARED FUNCTIONALITY
    // =============================================
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === viewNonWorkingDaysModal) {
            closeViewNonWorkingDaysModal();
        }
        if (event.target === deleteNonWorkingDayModal) {
            closeDeleteNonWorkingDayModal();
        }
        if (event.target === viewCitaModal) {
            closeViewCitaModal();
        }
        if (event.target === nonWorkingDayModal) {
            closeNonWorkingDayModal();
        }
        if (event.target === editCitaModal) {
            closeEditCitaModal();
        }
        if (event.target === deleteCitaModal) {
            closeDeleteCitaModal();
        }
    });
    
    // =============================================
    // FORM VALIDATION
    // =============================================
    
    // Validate Add Non-Working Day Form
    document.getElementById('addNonWorkingDayForm').addEventListener('submit', function(e) {
        const descripcion = document.getElementById('descripcion').value;
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        
        // Check description length
        if (descripcion.length > 255) {
            e.preventDefault();
            alert('La descripción no puede exceder los 255 caracteres');
            return;
        }
        
        // Check if fecha_fin is provided and is not before fecha_inicio
        if (fechaFin && fechaFin < fechaInicio) {
            e.preventDefault();
            alert('La fecha de fin no puede ser anterior a la fecha de inicio');
            return;
        }
    });
    
    // Validate Edit Appointment Form
    document.getElementById('editCitaForm').addEventListener('submit', function(e) {
        const fecha = document.getElementById('edit_fecha_cita').value;
        const hora = document.getElementById('edit_hora_cita').value;
        if (!fecha || !hora) {
            e.preventDefault();
            alert('La fecha y hora son obligatorias');
        }
    });
    
    // =============================================
    // TABLE SEARCH FUNCTIONALITY
    // =============================================
    
    const searchInput = document.querySelector('.search-input');
    
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');
        
        tableRows.forEach(function(row) {
            const solicitante = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
            const tramite = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
            const fecha = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
            
            if (solicitante.includes(searchTerm) || tramite.includes(searchTerm) || fecha.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
@endsection