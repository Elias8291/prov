<div class="form-page password-form" id="passwordForm">
    <button class="back-btn" id="backFromPasswordFormBtn">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Atrás
    </button>

    <img src="{{ asset('assets/imagenes/logoAdminsitracion.png') }}" alt="Logo" class="logo-img">
    <h1>Establecer Contraseña</h1>
    <p>Configura una nueva contraseña para tu cuenta en el <span class="system-name">Padrón de Proveedores de Oaxaca</span></p>

    <form method="POST" action="" id="passwordForm">
        @csrf
        <input type="hidden" name="token" value="{{ request()->query('token') }}">

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="input-group">
            <label for="password">Contraseña*</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="input-group">
            <label for="password-confirm">Confirmar Contraseña*</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn">Establecer Contraseña</button>
    </form>
</div>