<header class="header">
    <div class="header-left">
        <button class="menu-toggle" id="menu-toggle">☰</button>
        <a href="http://127.0.0.1:8000/index">
            <img src="{{ asset('assets/imagenes/logoAdminsitracion.png') }}" alt="Logo Gobierno Digital" class="logo-text">
        </a>
    </div>
    <div class="header-right">
        <div class="notification-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
        </div>
        <div class="user-profile">
            <div class="user-avatar pattern-avatar"> {{ strtoupper(substr(Auth::user()->name, 0, 2)) }} </div>
            <div class="user-name">
                @php
                    $nameParts = explode(' ', Auth::user()->name);
                    $shortName = $nameParts[0];
                    if (count($nameParts) > 1) {
                        $shortName .= ' ' . strtoupper(substr($nameParts[1], 0, 1)) . '.';
                    }
                    echo $shortName;
                @endphp
            </div>
            <div class="profile-dropdown">
                <div class="dropdown-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Mi perfil</span>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="dropdown-item">
                    @csrf
                    <button type="submit" id="logout-button"
                        style="background: none; border: none; padding: 0; cursor: pointer; display: flex; align-items: center; width: 100%;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M10 3H6a2 2 0 0 0-2 2v14c0 1.1.9 2 2 2h4M16 17l5-5-5-5M19.8 12H9"></path>
                        </svg>
                        <span style="margin-left: 8px;">Cerrar sesión</span>
                    </button>
                </form>

            </div>
        </div>
    </div>
</header>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script para asignar colores a los avatares de usuario según la inicial del nombre
        const alphabetGroups = {
            group1: ['A', 'B', 'C', 'D', 'E', 'F'],
            group2: ['G', 'H', 'I', 'J', 'K', 'L'],
            group3: ['M', 'N', 'O', 'P', 'Q', 'R'],
            group4: ['S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']
        };

        const avatars = document.querySelectorAll('.user-avatar');

        avatars.forEach(avatar => {
            const initials = avatar.textContent.trim();
            const firstLetter = initials.charAt(0).toUpperCase();

            avatar.classList.remove('avatar-group1', 'avatar-first-group1', 'avatar-group2',
                'avatar-first-group2', 'avatar-group3', 'avatar-first-group3',
                'avatar-group4', 'avatar-first-group4');

            avatar.classList.add('gradient-effect');

            if (alphabetGroups.group1.includes(firstLetter)) {
                if (firstLetter === 'A') {
                    avatar.classList.add('avatar-first-group1');
                } else {
                    avatar.classList.add('avatar-group1');
                }
            } else if (alphabetGroups.group2.includes(firstLetter)) {
                if (firstLetter === 'G') {
                    avatar.classList.add('avatar-first-group2');
                } else {
                    avatar.classList.add('avatar-group2');
                }
            } else if (alphabetGroups.group3.includes(firstLetter)) {
                if (firstLetter === 'M') {
                    avatar.classList.add('avatar-first-group3');
                } else {
                    avatar.classList.add('avatar-group3');
                }
            } else if (alphabetGroups.group4.includes(firstLetter)) {
                if (firstLetter === 'S') {
                    avatar.classList.add('avatar-first-group4');
                } else {
                    avatar.classList.add('avatar-group4');
                }
            }
        });
    });
</script>
