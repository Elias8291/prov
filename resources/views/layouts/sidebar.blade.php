<aside class="sidebar" id="sidebar">
    <nav class="sidebar-menu">
        <!-- Dashboard - visible para todos -->
        <div class="menu-item {{ request()->routeIs('index') ? 'active' : '' }}">
            <a href="{{ route('index') }}"
                style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px">
                <div class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                </div>
                <div class="menu-text">Dashboard</div>
            </a>
        </div>

        <!-- Inscripción - visible para todos -->
        <div class="menu-item">
            <a href="{{ route('inscripcion.terminos_y_condiciones') }}"
                style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px">
                <div class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="7" r="4" />
                        <path d="M5.5 21h13a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2z" />
                    </svg>
                </div>
                <div class="menu-text">Inscripción</div>
            </a>
        </div>

        <!-- Revisión - visible para todos -->
        <div class="menu-item {{ request()->routeIs('revision.index') ? 'active' : '' }}">
            <a href="{{ route('revision.index') }}"
                style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px">
                <div class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M9 18l6-6-6-6" />
                        <path d="M20 6v12" />
                    </svg>
                </div>
                <div class="menu-text">Revisión</div>
            </a>
        </div>

        <!-- Usuarios - solo visible para roles distintos a Solicitante -->
        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('OtroRol'))
        <div class="menu-item {{ request()->routeIs('usuarios.index') ? 'active' : '' }}">
            <a href="{{ route('usuarios.index') }}"
                style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px">
                <div class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <div class="menu-text">Usuarios</div>
            </a>
        </div>
        @endif

        <!-- Roles - solo visible para roles distintos a Solicitante -->
        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('OtroRol'))
        <div class="menu-item {{ request()->routeIs('roles.index') ? 'active' : '' }}">
            <a href="{{ route('roles.index') }}"
                style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px">
                <div class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="8.5" cy="7" r="4" />
                        <path d="M20 8v6" />
                        <path d="M23 11h-6" />
                    </svg>
                </div>
                <div class="menu-text">Roles</div>
            </a>
        </div>
        @endif

        <!-- Proveedores - solo visible para roles distintos a Solicitante -->
        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('OtroRol'))
        <div class="menu-item {{ request()->routeIs('proveedores.index') ? 'active' : '' }}">
            <a href="{{ route('proveedores.index') }}"
                style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px">
                <div class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="14" rx="2" />
                        <path d="M3 8h18" />
                        <path d="M8 12h8" />
                    </svg>
                </div>
                <div class="menu-text">Proveedores</div>
            </a>
        </div>
        @endif

        <!-- Documentos - solo visible para roles distintos a Solicitante -->
        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('OtroRol'))
        <div class="menu-item {{ request()->routeIs('documentos.index') ? 'active' : '' }}">
            <a href="{{ route('documentos.index') }}"
                style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px">
                <div class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                        <polyline points="10 9 9 9 8 9" />
                    </svg>
                </div>
                <div class="menu-text">Documentos</div>
            </a>
        </div>
        @endif
    </nav>
</aside>

