@extends('dashboard')

@section('title', 'Opciones de Trámites - Proveedores de Oaxaca')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/registration.css') }}">

<div class="dashboard-container">
    <div class="cards-container">
        <!-- Inscription Card -->
        <div class="registration-section">
            <h1>Inscripción</h1>
            <div class="registration-intro">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                </div>
                <p>Regístrate por primera vez en el padrón de proveedores de Oaxaca.</p>
                <p>Tipo de solicitante: <strong>{{ auth()->user()->solicitante->tipo_persona ?? 'No especificado' }}</strong>.</p>
            </div>
            <div class="form-section">
                <a href="{{ route('inscripcion.terminos_y_condiciones') }}" class="btn-submit">Continuar con Inscripción</a>
            </div>
        </div>

        <!-- Update Card -->
        <div class="registration-section">
            <h1>Actualización</h1>
            <div class="registration-intro">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </div>
                <p>Actualiza los datos de tu cuenta existente.</p>
                <p>Mantén tu información <strong>vigente</strong> para seguir participando.</p>
            </div>
            
        </div>

        <!-- Renewal Card -->
        <div class="registration-section">
            <h1>Renovación</h1>
            <div class="registration-intro">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38"></path>
                    </svg>
                </div>
                <p>Renueva tu registro como proveedor en Oaxaca.</p>
                <p><strong>Mantente activo</strong> con un proceso de renovación sencillo.</p>
            </div>
          
        </div>
    </div>
</div>

<style>
/* Elegant Government Cards - Dashboard Layout */
.cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
    margin-top: 1.5rem;
}

.registration-section {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(157, 36, 73, 0.1);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.registration-section:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(157, 36, 73, 0.15);
}

/* Header section with gradient background */
.registration-section h1 {
    color: #fff;
    font-size: 1.3rem;
    font-weight: 500;
    margin: 0;
    padding: 1.4rem 1.5rem;
    background: linear-gradient(135deg, #9D2449 0%, #B42D54 100%);
    position: relative;
    letter-spacing: 0.5px;
    text-align: center;
    font-family: 'Montserrat', sans-serif;
}

.registration-section h1::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 3px;
    background-color: rgba(255, 255, 255, 0.5);
    border-radius: 3px;
}

.registration-intro {
    padding: 1.8rem 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.registration-intro p {
    margin: 0.6rem 0;
    color: #444;
    line-height: 1.7;
    font-size: 15px;
}

.registration-intro p:first-child {
    font-size: 16px;
    font-weight: 500;
    color: #333;
}

.registration-intro strong {
    color: #9D2449;
    font-weight: 600;
}

.form-section {
    padding: 1.5rem 1.5rem;
    background-color: #f8f8f8;
    border-top: 1px solid #eee;
    text-align: center;
    transition: background-color 0.2s ease;
}

.btn-submit {
    background-color: #9D2449;
    color: white;
    border: none;
    padding: 0.9rem 1.8rem;
    border-radius: 5px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: 0 4px 8px rgba(157, 36, 73, 0.15);
}

.btn-submit:hover {
    background-color: #8c2041;
    box-shadow: 0 6px 12px rgba(157, 36, 73, 0.25);
}

.btn-submit:active {
    transform: translateY(2px);
}

.btn-submit:after {
    content: "";
    display: inline-block;
    width: 18px;
    height: 18px;
    background-color: #fff;
    mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8-8-8z'/%3E%3C/svg%3E");
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8-8-8z'/%3E%3C/svg%3E");
    mask-size: contain;
    -webkit-mask-size: contain;
    mask-repeat: no-repeat;
    -webkit-mask-repeat: no-repeat;
    margin-left: 8px;
}

/* Card icon styles */
.card-icon {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
}

.card-icon svg {
    width: 48px;
    height: 48px;
    color: #9D2449;
}

/* Status indicators */
.status-indicator {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-active {
    background-color: #4CAF50;
    box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
}

.status-inactive {
    background-color: #F44336;
    box-shadow: 0 0 0 2px rgba(244, 67, 54, 0.2);
}

.status-pending {
    background-color: #FFC107;
    box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.2);
}

/* Disabled card state */
.registration-section.disabled {
    opacity: 0.7;
    pointer-events: none;
}

.registration-section.disabled::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.5);
    z-index: 1;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .cards-container {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .registration-section h1 {
        font-size: 1.2rem;
        padding: 1.2rem 1.5rem;
    }
    
    .registration-intro {
        padding: 1.4rem 1.2rem;
    }
    
    .form-section {
        padding: 1.2rem;
    }
    
    .btn-submit {
        width: 100%;
        padding: 0.8rem 1.5rem;
    }
}

/* Print styling */
@media print {
    .cards-container {
        display: block;
    }
    
    .registration-section {
        break-inside: avoid;
        margin-bottom: 1rem;
        box-shadow: none;
        border: 1px solid #ccc;
    }
    
    .btn-submit {
        display: none;
    }
}
</style>
@endsection