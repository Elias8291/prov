@extends('dashboard')

@section('title', 'Administración de Documentos - Proveedores de Oaxaca')

<link rel="stylesheet" href="{{ asset('assets/css/tabla.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
<div class="dashboard-container">
    <div class="content-wrapper">
        <!-- Header Section with Title -->
        <h1 class="page-title">Administración de Documentos</h1>
        <p class="page-subtitle">Gestiona todos los documentos en la plataforma de Proveedores de Oaxaca</p>
        
        <!-- Controls Bar with Search and Buttons -->
        <div class="controls-bar">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Buscar documentos por nombre...">
            </div>
            
            <div class="button-group">
                <button class="btn-primary" id="openDocumentoModalBtn">
                    <i class="fas fa-plus btn-icon"></i>
                    Agregar Documento
                </button>
            </div>
        </div>
        
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">
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
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Visible</th>
                        <th>Tipo Persona</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documentos as $documento)
                        <tr>
                            <td>{{ $documento->id }}</td>
                            <td class="product-name-cell">
                                <div>
                                    <div class="product-name">{{ $documento->nombre }}</div>
                                    <div class="product-id">ID: {{ $documento->id }}</div>
                                </div>
                            </td>
                            <td>{{ $documento->tipo }}</td>
                            <td>{{ Str::limit($documento->descripcion, 30) }}</td>
                            <td>
                                <span class="status-badge {{ $documento->es_visible ? 'badge-success' : 'badge-danger' }}">
                                    {{ $documento->es_visible ? 'Visible' : 'No visible' }}
                                </span>
                            </td>
                            <td>{{ $documento->tipo_persona }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action view-btn" data-id="{{ $documento->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action edit-btn" 
                                        data-id="{{ $documento->id }}"
                                        data-nombre="{{ $documento->nombre }}"
                                        data-tipo="{{ $documento->tipo }}"
                                        data-descripcion="{{ $documento->descripcion }}"
                                        data-fecha="{{ $documento->fecha_expiracion }}"
                                        data-visible="{{ $documento->es_visible }}"
                                        data-tipo-persona="{{ $documento->tipo_persona }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action delete-btn" 
                                        data-id="{{ $documento->id }}"
                                        data-nombre="{{ $documento->nombre }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    
                    @if(count($documentos) == 0)
                        <tr>
                            <td colspan="8" class="text-center py-4">No hay documentos registrados</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
         @include('components.paginacion', ['paginator' => $documentos])
    </div>
</div>

<!-- Add Documento Modal -->
<div id="documentoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Agregar Nuevo Documento</h2>
            <span class="close-modal">&times;</span>
        </div>
        
        <form id="addDocumentoForm" action="{{ route('documentos.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="nombre">Nombre del Documento</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                    <small class="form-text text-muted">Nombre descriptivo del documento</small>
                </div>
                
                <div class="form-group">
                    <label for="tipo">Tipo</label>
                    <input type="text" id="tipo" name="tipo" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="fecha_expiracion">Fecha de Expiración</label>
                    <input type="date" id="fecha_expiracion" name="fecha_expiracion" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Es Visible</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="es_visible" id="visible_si" value="1" checked>
                        <label class="form-check-label" for="visible_si">Sí</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="es_visible" id="visible_no" value="0">
                        <label class="form-check-label" for="visible_no">No</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="tipo_persona">Tipo de Persona</label>
                    <select id="tipo_persona" name="tipo_persona" class="form-control" required>
                        <option value="" disabled selected>Seleccione el tipo de persona</option>
                        <option value="Física">Física</option>
                        <option value="Moral">Moral</option>
                        <option value="Ambas">Ambas</option>
                    </select>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeModal">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Guardar Documento
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Documento Modal -->
<div id="editDocumentoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Editar Documento</h2>
            <span class="close-modal">&times;</span>
        </div>
        
        <form id="editDocumentoForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_nombre">Nombre del Documento</label>
                    <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_tipo">Tipo</label>
                    <input type="text" id="edit_tipo" name="tipo" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_descripcion">Descripción</label>
                    <textarea id="edit_descripcion" name="descripcion" class="form-control" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_fecha_expiracion">Fecha de Expiración</label>
                    <input type="date" id="edit_fecha_expiracion" name="fecha_expiracion" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Es Visible</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="es_visible" id="edit_visible_si" value="1">
                        <label class="form-check-label" for="edit_visible_si">Sí</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="es_visible" id="edit_visible_no" value="0">
                        <label class="form-check-label" for="edit_visible_no">No</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_tipo_persona">Tipo de Persona</label>
                    <select id="edit_tipo_persona" name="tipo_persona" class="form-control" required>
                        <option value="Física">Física</option>
                        <option value="Moral">Moral</option>
                        <option value="Ambas">Ambas</option>
                    </select>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeEditModal">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Actualizar Documento
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Documento Modal -->
<div id="deleteDocumentoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Confirmar Eliminación</h2>
            <span class="close-modal">&times;</span>
        </div>
        
        <form id="deleteDocumentoForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body">
                <p class="delete-message">¿Está seguro que desea eliminar este documento?</p>
                <p class="delete-warning">Esta acción no se puede deshacer.</p>
                
                <div class="user-info">
                    <p><strong>ID:</strong> <span id="delete_id"></span></p>
                    <p><strong>Nombre:</strong> <span id="delete_nombre"></span></p>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeDeleteModal">Cancelar</button>
                <button type="submit" class="btn-danger">
                    <i class="fas fa-trash"></i> Eliminar Documento
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
    
    // Add Documento Modal Elements
    const addModal = document.getElementById('documentoModal');
    const openAddBtn = document.getElementById('openDocumentoModalBtn');
    const closeAddBtn = addModal.querySelector('.close-modal');
    const cancelAddBtn = document.getElementById('closeModal');
    
    // Edit Documento Modal Elements
    const editModal = document.getElementById('editDocumentoModal');
    const closeEditBtn = editModal.querySelector('.close-modal');
    const cancelEditBtn = document.getElementById('closeEditModal');
    const editDocumentoForm = document.getElementById('editDocumentoForm');
    
    // Delete Documento Modal Elements
    const deleteModal = document.getElementById('deleteDocumentoModal');
    const closeDeleteBtn = deleteModal.querySelector('.close-modal');
    const cancelDeleteBtn = document.getElementById('closeDeleteModal');
    const deleteDocumentoForm = document.getElementById('deleteDocumentoForm');
    
    // =============================================
    // ADD DOCUMENTO MODAL FUNCTIONS
    // =============================================
    
    // Open Add Modal
    openAddBtn.addEventListener('click', function() {
        addModal.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    });
    
    // Close Add Modal Function
    function closeAddModal() {
        addModal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Re-enable scrolling
    }
    
    // Close Add Modal with X button
    closeAddBtn.addEventListener('click', closeAddModal);
    
    // Close Add Modal with Cancel button
    cancelAddBtn.addEventListener('click', closeAddModal);
    
    // =============================================
    // EDIT DOCUMENTO MODAL FUNCTIONS
    // =============================================
    
    // Close Edit Modal Function
    function closeEditModal() {
        editModal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Re-enable scrolling
    }
    
    // Close Edit Modal with X button
    closeEditBtn.addEventListener('click', closeEditModal);
    
    // Close Edit Modal with Cancel button
    cancelEditBtn.addEventListener('click', closeEditModal);
    
    // Open Edit Modal when edit button is clicked
    document.querySelectorAll('.edit-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const documentoId = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const tipo = this.getAttribute('data-tipo');
            const descripcion = this.getAttribute('data-descripcion');
            const fecha = this.getAttribute('data-fecha');
            const visible = this.getAttribute('data-visible');
            const tipoPersona = this.getAttribute('data-tipo-persona');
            
            // Set form action
            editDocumentoForm.action = `/documentos/${documentoId}`;
            
            // Fill form fields
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_tipo').value = tipo;
            document.getElementById('edit_descripcion').value = descripcion;
            document.getElementById('edit_fecha_expiracion').value = fecha;
            
            // Set select value for tipo_persona
            const tipoPersonaSelect = document.getElementById('edit_tipo_persona');
            for (let i = 0; i < tipoPersonaSelect.options.length; i++) {
                if (tipoPersonaSelect.options[i].value === tipoPersona) {
                    tipoPersonaSelect.selectedIndex = i;
                    break;
                }
            }
            
            // Set radio buttons
            if (visible === '1') {
                document.getElementById('edit_visible_si').checked = true;
            } else {
                document.getElementById('edit_visible_no').checked = true;
            }
            
            // Display the modal
            editModal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        });
    });
    
    // =============================================
    // DELETE DOCUMENTO MODAL FUNCTIONS
    // =============================================
    
    // Close Delete Modal Function
    function closeDeleteModal() {
        deleteModal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Re-enable scrolling
    }
    
    // Close Delete Modal with X button
    closeDeleteBtn.addEventListener('click', closeDeleteModal);
    
    // Close Delete Modal with Cancel button
    cancelDeleteBtn.addEventListener('click', closeDeleteModal);
    
    // Open Delete Modal when delete button is clicked
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const documentoId = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            
            // Set form action
            deleteDocumentoForm.action = `/documentos/${documentoId}`;
            
            // Fill in confirmation details
            document.getElementById('delete_id').textContent = documentoId;
            document.getElementById('delete_nombre').textContent = nombre;
            
            // Display the modal
            deleteModal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        });
    });
    
    // =============================================
    // SHARED FUNCTIONALITY
    // =============================================
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === addModal) {
            closeAddModal();
        }
        if (event.target === editModal) {
            closeEditModal();
        }
        if (event.target === deleteModal) {
            closeDeleteModal();
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
            const name = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
            const tipo = row.cells[2]?.textContent.toLowerCase() || '';
            const descripcion = row.cells[3]?.textContent.toLowerCase() || '';
            
            if (name.includes(searchTerm) || tipo.includes(searchTerm) || descripcion.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Auto hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        setTimeout(function() {
            alerts.forEach(function(alert) {
                alert.style.display = 'none';
            });
        }, 5000);
    }
});
</script>
@endsection