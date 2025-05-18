@extends('dashboard')

@section('title', '¡Bienvenidos a Proveedores de Oaxaca!')

<link rel="stylesheet" href="{{ asset('assets/css/tabla.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
<div class="dashboard-container">
    <div class="content-wrapper">
        <!-- Header Section with Title -->
        <h1 class="page-title">Administración de Usuarios</h1>
        <p class="page-subtitle">Gestiona todos los usuarios registrados en la plataforma de Proveedores de Oaxaca</p>
        
        <!-- Controls Bar with Search and Buttons -->
        <div class="controls-bar">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Buscar usuarios por nombre, RFC o correo...">
            </div>
            
            <div class="button-group">
                <button class="btn-secondary">
                    <i class="fas fa-filter btn-icon"></i>
                    Filtrar
                </button>
                <button class="btn-primary" id="openUserModalBtn">
                    <i class="fas fa-plus btn-icon"></i>
                    Agregar Usuario
                </button>
            </div>
        </div>
        
        <!-- Table Container -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>RFC</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="product-name-cell">
                                <div>
                                    <div class="product-name">{{ $user->nombre }}</div>
                                    <div class="product-id">Desde {{ $user->created_at->format('d M Y') }}</div>
                                </div>
                            </td>
                            <td>
                                <span>{{ $user->rfc }}</span>
                                <div class="product-id">#{{ substr($user->rfc, 0, 8) }}</div>
                            </td>
                            <td>{{ $user->correo }}</td>
                            <td>
                                @if($user->roles->isNotEmpty())
                                    @foreach($user->roles as $role)
                                        <span class="role-badge">{{ $role->name }}</span>
                                        @if(!$loop->last), @endif
                                    @endforeach
                                @else
                                    <span class="role-badge no-role">Sin rol</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusDisplay = [
                                        'activo' => 'Activo',
                                        'inactivo' => 'Inactivo',
                                        'suspendido' => 'Suspendido'
                                    ];
                                    $statusText = $statusDisplay[$user->estado] ?? 'Desconocido';
                                @endphp
                                <div class="status-indicator status-{{ $user->estado }}">
                                    {{ $statusText }}
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action view-btn">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action edit-btn" data-id="{{ $user->id }}" data-nombre="{{ $user->nombre }}" 
                                            data-correo="{{ $user->correo }}" data-rfc="{{ $user->rfc }}" 
                                            data-estado="{{ $user->estado }}" 
                                            data-rol="{{ $user->roles->isNotEmpty() ? $user->roles->first()->name : '' }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action delete-btn" data-id="{{ $user->id }}" data-nombre="{{ $user->nombre }}"
                                            data-rfc="{{ $user->rfc }}" data-correo="{{ $user->correo }}">
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
        @include('components.paginacion', ['paginator' => $users])
    </div>
</div>

<!-- Add User Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Agregar Nuevo Usuario</h2>
            <span class="close-modal">×</span>
        </div>
        
        <form id="addUserForm" action="{{ route('usuarios.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="rfc">RFC</label>
                    <input type="text" id="rfc" name="rfc" class="form-control" maxlength="13" required>
                    <small class="form-text text-muted">Formato: 13 caracteres para personas físicas, 12 para morales</small>
                </div>
                
                <div class="form-group">
                    <label for="correo">Correo Electrónico</label>
                    <input type="email" id="correo" name="correo" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <span class="toggle-password"><i class="fas fa-eye"></i></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        <span class="toggle-password"><i class="fas fa-eye"></i></span>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="rol">Rol</label>
                        <select id="rol" name="rol" class="form-control" required>
                            <option value="" selected disabled>Seleccionar rol</option>
                            <option value="admin">Administrador</option>
                            <option value="proveedor">Proveedor</option>
                            <option value="comprador">Comprador</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado" class="form-control" required>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                            <option value="suspendido">Suspendido</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeModal">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Editar Usuario</h2>
            <span class="close-modal">×</span>
        </div>
        
        <form id="editUserForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>ID de Usuario</label>
                        <input type="text" id="edit_id" class="form-control" readonly disabled>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label>RFC</label>
                        <input type="text" id="edit_rfc" class="form-control" readonly disabled>
                        <small class="form-text text-muted">El RFC no puede ser modificado</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_nombre">Nombre Completo</label>
                    <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_correo">Correo Electrónico</label>
                    <input type="email" id="edit_correo" name="correo" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_password">Contraseña (dejar en blanco para mantener la actual)</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="edit_password" name="password" class="form-control">
                        <span class="toggle-password"><i class="fas fa-eye"></i></span>
                    </div>
                    <small class="form-text text-muted">Dejar en blanco si no desea cambiar la contraseña</small>
                </div>
                
                <div class="form-group">
                    <label for="edit_password_confirmation">Confirmar Contraseña</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="edit_password_confirmation" name="password_confirmation" class="form-control">
                        <span class="toggle-password"><i class="fas fa-eye"></i></span>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="edit_rol">Rol</label>
                        <select id="edit_rol" name="rol" class="form-control" required>
                            <option value="" selected disabled>Seleccionar rol</option>
                            <option value="admin">Administrador</option>
                            <option value="proveedor">Proveedor</option>
                            <option value="comprador">Comprador</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="edit_estado">Estado</label>
                        <select id="edit_estado" name="estado" class="form-control" required>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                            <option value="suspendido">Suspendido</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeEditModal">Cancelar</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete User Modal -->
<div id="deleteUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Confirmar Eliminación</h2>
            <span class="close-modal">×</span>
        </div>
        
        <form id="deleteUserForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-body">
                <p class="delete-message">¿Estás seguro que deseas eliminar este usuario?</p>
                <p class="delete-warning">Esta acción no se puede deshacer. El usuario dejará de tener acceso al sistema.</p>
                
                <div class="user-info">
                    <p><strong>ID:</strong> <span id="delete_id"></span></p>
                    <p><strong>Nombre:</strong> <span id="delete_nombre"></span></p>
                    <p><strong>RFC:</strong> <span id="delete_rfc"></span></p>
                    <p><strong>Correo:</strong> <span id="delete_correo"></span></p>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="closeDeleteModal">Cancelar</button>
                <button type="submit" class="btn-danger">
                    <i class="fas fa-trash"></i> Eliminar Usuario
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
    
    // Add User Modal Elements
    const addModal = document.getElementById('userModal');
    const openAddBtn = document.getElementById('openUserModalBtn');
    const closeAddBtn = addModal.querySelector('.close-modal');
    const cancelAddBtn = document.getElementById('closeModal');
    
    // Edit User Modal Elements
    const editModal = document.getElementById('editUserModal');
    const closeEditBtn = editModal.querySelector('.close-modal');
    const cancelEditBtn = document.getElementById('closeEditModal');
    const editUserForm = document.getElementById('editUserForm');
    
    // Delete User Modal Elements
    const deleteModal = document.getElementById('deleteUserModal');
    const closeDeleteBtn = deleteModal.querySelector('.close-modal');
    const cancelDeleteBtn = document.getElementById('closeDeleteModal');
    const deleteUserForm = document.getElementById('deleteUserForm');
    
    // Toggle Password Elements
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    
    // =============================================
    // ADD USER MODAL FUNCTIONS
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
    // EDIT USER MODAL FUNCTIONS
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
            const userId = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const correo = this.getAttribute('data-correo');
            const rfc = this.getAttribute('data-rfc');
            const estado = this.getAttribute('data-estado');
            const rol = this.getAttribute('data-rol');
            
            // Set form action
            editUserForm.action = `/usuarios/${userId}`;
            
            // Fill form fields
            document.getElementById('edit_id').value = userId;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_correo').value = correo;
            document.getElementById('edit_rfc').value = rfc;
            document.getElementById('edit_estado').value = estado;
            document.getElementById('edit_rol').value = rol;
            
            // Clear password fields
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_password_confirmation').value = '';
            
            // Display the modal
            editModal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        });
    });
    
    // =============================================
    // DELETE USER MODAL FUNCTIONS
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
            const userId = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const rfc = this.getAttribute('data-rfc');
            const correo = this.getAttribute('data-correo');
            
            // Set form action
            deleteUserForm.action = `/usuarios/${userId}`;
            
            // Fill in confirmation details
            document.getElementById('delete_id').textContent = userId;
            document.getElementById('delete_nombre').textContent = nombre;
            document.getElementById('delete_rfc').textContent = rfc;
            document.getElementById('delete_correo').textContent = correo;
            
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
    
    // Toggle password visibility for all password fields
    togglePasswordBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const passwordField = this.parentElement.querySelector('input');
            const type = passwordField.getAttribute('type');
            
            if (type === 'password') {
                passwordField.setAttribute('type', 'text');
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordField.setAttribute('type', 'password');
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
    
    // =============================================
    // FORM VALIDATION
    // =============================================
    
    // Validate Add User Form
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;
        
        if (password !== passwordConfirm) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
        }
        
        // RFC validation
        const rfc = document.getElementById('rfc').value;
        if (rfc.length !== 12 && rfc.length !== 13) {
            e.preventDefault();
            alert('El RFC debe tener 12 caracteres para personas morales o 13 para personas físicas');
        }
    });
    
    // Validate Edit User Form
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        const password = document.getElementById('edit_password').value;
        const passwordConfirm = document.getElementById('edit_password_confirmation').value;
        
        if (password !== '' && password !== passwordConfirm) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
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
            const nombre = row.querySelector('.product-name').textContent.toLowerCase();
            const rfc = row.querySelector('td:nth-child(3) span').textContent.toLowerCase();
            const correo = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            
            if (nombre.includes(searchTerm) || rfc.includes(searchTerm) || correo.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
@endsection