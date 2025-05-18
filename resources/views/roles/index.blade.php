@extends('dashboard')

@section('title', 'Administración de Roles - Proveedores de Oaxaca')

<link rel="stylesheet" href="{{ asset('assets/css/tabla.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
<div class="dashboard-container">
    <div class="content-wrapper">
        <!-- Header Section with Title -->
        <h1 class="page-title">Administración de Roles</h1>
        <p class="page-subtitle">Gestiona todos los roles y permisos en la plataforma de Proveedores de Oaxaca</p>
        
        <!-- Controls Bar with Search and Buttons -->
        <div class="controls-bar">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Buscar roles por nombre...">
            </div>
            
            <div class="button-group">
                <button class="btn-primary" id="openRoleModalBtn">
                    <i class="fas fa-plus btn-icon"></i>
                    Agregar Rol
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
                        <th>Usuarios Asignados</th>
                        <th>Permisos</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td class="product-name-cell">
                                <div>
                                    <div class="product-name">{{ $role->name }}</div>
                                    <div class="product-id">{{ $role->guard_name }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="user-count-badge">{{ $role->users->count() }}</span>
                                <span>usuarios</span>
                            </td>
                            <td>
                                <div class="permissions-list">
                                    @if($role->permissions->isNotEmpty())
                                        @foreach($role->permissions->take(3) as $permission)
                                            <span class="permission-badge">{{ $permission->name }}</span>
                                        @endforeach
                                        @if($role->permissions->count() > 3)
                                            <span class="more-badge">+{{ $role->permissions->count() - 3 }} más</span>
                                        @endif
                                    @else
                                        <span class="no-permissions">Sin permisos asignados</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>{{ $role->created_at->format('d M Y') }}</div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action view-btn" data-id="{{ $role->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action edit-btn" data-id="{{ $role->id }}" data-name="{{ $role->name }}" 
                                        data-permissions="{{ $role->permissions->pluck('name') }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action delete-btn" data-id="{{ $role->id }}" data-name="{{ $role->name }}" 
                                        data-users-count="{{ $role->users->count() }}">
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
        @include('components.paginacion', ['paginator' => $roles])
    </div>
</div>

<!-- Add Role Modal -->
<div id="roleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Agregar Nuevo Rol</h2>
            <span class="close-modal">×</span>
        </div>
        
        <form id="addRoleForm" action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Nombre del Rol</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                    <small class="form-text text-muted">El nombre del rol debe ser único y descriptivo</small>
                </div>
                
                <div class="form-group">
                    <label for="permissions">Permisos</label>
                    <div class="permissions-container">
                        @foreach($permissions as $permission)
                            <div class="permission-item">
                                <input type="checkbox" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}">
                                <label for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeModal">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Guardar Rol
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Role Modal -->
<div id="editRoleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Editar Rol</h2>
            <span class="close-modal">×</span>
        </div>
        
        <form id="editRoleForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_name">Nombre del Rol</label>
                    <input type="text" id="edit_name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_permissions">Permisos</label>
                    <div class="permissions-container" id="edit_permissions_container">
                        @foreach($permissions as $permission)
                            <div class="permission-item">
                                <input type="checkbox" id="edit_permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}">
                                <label for="edit_permission_{{ $permission->id }}">{{ $permission->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeEditModal">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Actualizar Rol
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Role Modal -->
<div id="deleteRoleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Confirmar Eliminación</h2>
            <span class="close-modal">×</span>
        </div>
        
        <form id="deleteRoleForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body">
                <p class="delete-message">¿Estás seguro que deseas eliminar este rol?</p>
                <p class="delete-warning">Esta acción no se puede deshacer.</p>
                
                <div class="user-info">
                    <p><strong>ID:</strong> <span id="delete_id"></span></p>
                    <p><strong>Nombre:</strong> <span id="delete_name"></span></p>
                    <p><strong>Usuarios asignados:</strong> <span id="delete_users_count"></span></p>
                </div>

                <div id="users_warning" class="alert alert-warning" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Este rol tiene usuarios asignados. La eliminación no será posible mientras existan usuarios con este rol.
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeDeleteModal">Cancelar</button>
                <button type="submit" id="confirmDeleteBtn" class="btn-danger">
                    <i class="fas fa-trash"></i> Eliminar Rol
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for All Modals -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // =============================================
    // VARIABLES FOR ALL MODALS
    // =============================================
    
    // Add Role Modal Elements
    const addModal = document.getElementById('roleModal');
    const openAddBtn = document.getElementById('openRoleModalBtn');
    const closeAddBtn = addModal.querySelector('.close-modal');
    const cancelAddBtn = document.getElementById('closeModal');
    
    // Edit Role Modal Elements
    const editModal = document.getElementById('editRoleModal');
    const closeEditBtn = editModal.querySelector('.close-modal');
    const cancelEditBtn = document.getElementById('closeEditModal');
    const editRoleForm = document.getElementById('editRoleForm');
    
    // Delete Role Modal Elements
    const deleteModal = document.getElementById('deleteRoleModal');
    const closeDeleteBtn = deleteModal.querySelector('.close-modal');
    const cancelDeleteBtn = document.getElementById('closeDeleteModal');
    const deleteRoleForm = document.getElementById('deleteRoleForm');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
    // =============================================
    // ADD ROLE MODAL FUNCTIONS
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
    // EDIT ROLE MODAL FUNCTIONS
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
            const roleId = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const permissions = this.getAttribute('data-permissions');
            
            // Parse the permissions JSON
            const permissionsList = permissions ? JSON.parse(permissions.replace(/"/g, '"')) : [];
            
            // Set form action
            editRoleForm.action = `/roles/${roleId}`;
            
            // Fill form fields
            document.getElementById('edit_name').value = name;
            
            // Reset all permissions checkboxes
            document.querySelectorAll('#edit_permissions_container input[type="checkbox"]').forEach(function(checkbox) {
                checkbox.checked = false;
            });
            
            // Check permissions that the role has
            permissionsList.forEach(function(permissionName) {
                const checkbox = document.querySelector(`#edit_permissions_container input[value="${permissionName}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
            
            // Display the modal
            editModal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        });
    });
    
    // =============================================
    // DELETE ROLE MODAL FUNCTIONS
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
            const roleId = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const usersCount = parseInt(this.getAttribute('data-users-count'));
            
            // Set form action
            deleteRoleForm.action = `/roles/${roleId}`;
            
            // Fill in confirmation details
            document.getElementById('delete_id').textContent = roleId;
            document.getElementById('delete_name').textContent = name;
            document.getElementById('delete_users_count').textContent = usersCount;
            
            // Show warning and disable delete button if role has users
            if (usersCount > 0) {
                document.getElementById('users_warning').style.display = 'block';
                confirmDeleteBtn.disabled = true;
            } else {
                document.getElementById('users_warning').style.display = 'none';
                confirmDeleteBtn.disabled = false;
            }
            
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
            const name = row.querySelector('.product-name').textContent.toLowerCase();
            
            if (name.includes(searchTerm)) {
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