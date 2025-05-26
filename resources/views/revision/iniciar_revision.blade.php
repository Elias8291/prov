@extends('dashboard')

@section('title', 'Iniciar Revisión de Proveedor')
<link rel="stylesheet" href="{{ asset('assets/css/formularios.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/tabla.css') }}">
<style>
  /* Distinct borders for each form section */
.form-section {
    max-width: 800px;
    margin: 20px auto; /* Increased margin for separation */
    padding: 20px;
    background-color: #f9fafb;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: 2px solid transparent; /* Default border */
}

/* Oaxaca-inspired border colors */
#form-step-1 {
    border-color: #006D77; /* Deep Turquoise for General Data */
}

#form-step-2 {
    border-color: #9A031E; /* Oaxacan Red for Address */
}

#form-step-3 {
    border-color: #E3A008; /* Sunlit Yellow for Constitution Data */
}

#form-step-4 {
    border-color: #468F54; /* Jade Green for Shareholders */
}

#form-step-5 {
    border-color: #5C2A9D; /* Deep Purple for Legal Representative */
}

#form-step-6 {
    border-color: #CB7756; /* Warm Terracotta for Documents */
}

/* Add a subtle divider between forms */
.form-section + .form-section {
    margin-top: 30px; /* Extra spacing between forms */
    position: relative;
}

.form-section + .form-section::before {
    content: '';
    position: absolute;
    top: -15px;
    left: 0;
    width: 100%;
    height: 1px;
    background-color: #e5e7eb; /* Subtle divider line */
}

    /* Form heading */
    .form-section h4 {
        font-size: 1.5rem;
        color: #1f2937;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Form description */
    .form-description {
        font-size: 0.9rem;
        color: #4b5563;
        margin-bottom: 20px;
    }

    /* Form group styling */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group.full-width {
        width: 100%;
    }

    /* Form labels */
    .form-label {
        display: block;
        font-size: 0.95rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    /* File input styling */
    .form-input.file-input {
        display: block;
        width: 100%;
        padding: 10px;
        border: 2px dashed #d1d5db;
        border-radius: 6px;
        background-color: #fff;
        transition: border-color 0.3s ease;
    }

    .form-input.file-input:hover {
        border-color: #3b82f6;
    }

    .form-input.file-input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    /* Form hint */
    .form-hint {
        display: block;
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 5px;
    }

    /* Error message */
    .error-message {
        display: block;
        font-size: 0.85rem;
        color: #dc2626;
        margin-top: 5px;
    }

    /* Uploaded files grid container */
    .uploaded-files-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 10px;
    }

    /* Uploaded file card */
    .uploaded-file {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding: 15px;
        background-color: #ffffff;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .uploaded-file:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* File name */
    .file-name {
        font-size: 0.9rem;
        color: #1f2937;
        font-weight: 500;
        margin-bottom: 10px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 100%;
    }

    /* File link */
    .file-link {
        font-size: 0.85rem;
        color: #2563eb;
        text-decoration: none;
        padding: 6px 12px;
        background-color: #eff6ff;
        border-radius: 4px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .file-link:hover {
        background-color: #2563eb;
        color: #fff;
    }

    /* Form actions */
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    /* Buttons */
    .btn {
        padding: 10px 20px;
        font-size: 0.95rem;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-primary {
        background-color: #2563eb;
        color: #fff;
        border: none;
    }

    .btn-primary:hover {
        background-color: #1d4ed8;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background-color: #6b7280;
        color: #fff;
        border: none;
    }

    .btn-secondary:hover {
        background-color: #4b5563;
        transform: translateY(-2px);
    }

    /* Comment section */
    .comment-section {
        margin-top: 20px;
        padding: 15px;
        background-color: #ffffff;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .comment-section label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        display: block;
    }

    .comment-textarea {
        width: 100%;
        min-height: 100px;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.9rem;
        color: #1f2937;
        resize: vertical;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .comment-textarea:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    /* Updated styles for approval toggle button */
    .approval-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
    }

    .approval-btn {
        padding: 6px 12px;
        font-size: 0.85rem;
        font-weight: 500;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        display: flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .approval-btn.approved {
        background: linear-gradient(135deg, #34d399, #10b981);
        color: #fff;
    }

    .approval-btn.approved:hover {
        background: linear-gradient(135deg, #6ee7b7, #059669);
        transform: scale(1.05);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
    }

    .approval-btn.not-approved {
        background: linear-gradient(135deg, #f87171, #ef4444);
        color: #fff;
    }

    .approval-btn.not-approved:hover {
        background: linear-gradient(135deg, #fca5a5, #dc2626);
        transform: scale(1.05);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
    }

    .approval-icon {
        font-size: 0.9rem;
    }

    /* Split container for PDF viewer */
    .split-container {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .form-container {
        flex: 1;
        min-width: 0;
    }

    .pdf-viewer-container {
        flex: 1;
        position: sticky;
        top: 20px;
        height: 1200px;
        max-height: 1200px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .pdf-viewer-content {
        height: 1140px;
        overflow: auto;
    }

    .pdf-viewer-header {
        padding: 12px 16px;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pdf-viewer-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pdf-viewer-content {
        height: calc(100vh - 120px);
        overflow: auto;
    }

    iframe.pdf-frame {
        width: 100%;
        height: 100%;
        border: none;
    }

    .pdf-icon {
        color: #DC2626;
        font-size: 1.4rem;
        cursor: pointer;
        margin-left: 10px;
        transition: transform 0.2s ease;
    }

    .pdf-icon:hover {
        transform: scale(1.1);
    }

    .pdf-toggle-btn {
        background-color: transparent;
        border: none;
        color: #2563eb;
        cursor: pointer;
        font-size: 1.1rem;
        transition: color 0.3s ease;
    }

    .pdf-toggle-btn:hover {
        color: #1d4ed8;
    }

    /* Media query for smaller devices */
    @media (max-width: 1200px) {
        .split-container {
            flex-direction: column;
        }

        .pdf-viewer-container {
            position: relative;
            max-height: 600px;
            margin-top: 20px;
        }
    }
</style>
@section('content')
    <div class="dashboard-container">
        <h1 class="page-title">Revisión de Solicitud</h1>
        <p class="page-subtitle">Revisión de datos generales del solicitante: {{ $solicitante->rfc }}</p>

        <!-- Mensajes de Alerta -->
        @if (session('success'))
            <div class="alert alert-success" id="successAlert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" id="errorMsgAlert">
                {{ session('error') }}
            </div>
        @endif

        <div class="split-container">
            <div class="form-container">
                <!-- Formulario 1 -->
                <form id="formulario1" method="POST" action="{{ $componentParams['action'] }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-section" id="form-step-1">
                        <h4>
                            <i class="fas fa-building"></i> Datos Generales
                            @php
                                $constanciaDoc = null;
                                $identificacionDoc = null;
                                if (!empty($componentParams['documentos'])) {
                                    foreach ($componentParams['documentos'] as $documento) {
                                        if ($documento['documento_id'] == 1) {
                                            $constanciaDoc = $documento;
                                        }
                                        if ($documento['documento_id'] == 2) {
                                            $identificacionDoc = $documento;
                                        }
                                    }
                                }
                            @endphp
                            <!-- Icono para Constancia de Situación Fiscal (ID 1) -->
                            @if ($constanciaDoc && !empty($constanciaDoc['ruta_archivo']))
                                <a href="javascript:void(0);" class="pdf-icon" title="Ver Constancia de Situación Fiscal"
                                    onclick="togglePdfViewer('{{ $constanciaDoc['ruta_archivo'] }}', '{{ $constanciaDoc['nombre'] ?? 'Constancia de Situación Fiscal' }}')">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            @else
                                <span class="form-hint">Constancia de Situación Fiscal no disponible</span>
                            @endif
                            <!-- Icono para Identificación Oficial (ID 2) solo para Persona Física -->
                            @if ($componentParams['tipoPersona'] === 'Física' && $identificacionDoc && !empty($identificacionDoc['ruta_archivo']))
                                <a href="javascript:void(0);" class="pdf-icon" title="Ver Identificación Oficial"
                                    onclick="togglePdfViewer('{{ $identificacionDoc['ruta_archivo'] }}', '{{ $identificacionDoc['nombre'] ?? 'Identificación Oficial' }}')">
                                    <i class="fas fa-id-card"></i>
                                </a>
                            @elseif ($componentParams['tipoPersona'] === 'Física')
                                <span class="form-hint">Identificación Oficial no disponible</span>
                            @endif
                           
                           
                        </h4>
                        <div class="form-group horizontal-group">
                            <div class="half-width">
                                <label class="form-label data-label">Tipo de Proveedor</label>
                                <span class="data-field">{{ $componentParams['tipoPersona'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width">
                                <label class="form-label data-label">RFC</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['rfc'] ?? 'No disponible' }}</span>
                            </div>
                        </div>

                        <!-- CURP field -->
                        @if ($componentParams['mostrarCurp'])
                            <div class="form-group" id="curp-field">
                                <label class="form-label data-label">CURP</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['curp'] ?? 'No disponible' }}</span>
                            </div>
                        @endif

                        <div class="form-group horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label" for="razon_social">Razón Social</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['razon_social'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width form-group">
                                <label class="form-label" for="correo_electronico">Correo Electrónico</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['email'] ?? 'No disponible' }}</span>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label" for="objeto_social">Objeto Social</label>
                            <span
                                class="data-field">{{ $componentParams['datosPrevios']['objeto_social'] ?? 'No disponible' }}</span>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Sectores</label>
                            <span class="data-field">
                                @if (!empty($componentParams['actividadesSeleccionadas']))
                                    @foreach ($componentParams['actividadesSeleccionadas'] as $actividad)
                                        {{ $actividad['sector_id'] ? $componentParams['sectores']->find($actividad['sector_id'])->nombre ?? 'No disponible' : 'No disponible' }}
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                @else
                                    No disponible
                                @endif
                            </span>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Actividades</label>
                            <span class="data-field">
                                @if (!empty($componentParams['actividadesSeleccionadas']))
                                    @foreach ($componentParams['actividadesSeleccionadas'] as $actividad)
                                        {{ $actividad['nombre'] ?? 'No disponible' }}
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                @else
                                    No disponible
                                @endif
                            </span>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Actividades Seleccionadas</label>
                            <div class="actividades-container">
                                @if (!empty($componentParams['actividadesSeleccionadas']))
                                    @foreach ($componentParams['actividadesSeleccionadas'] as $actividad)
                                        <span>{{ $actividad['nombre'] ?? 'Sin actividad seleccionada' }}</span>
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                @else
                                    <span>Sin actividad seleccionada</span>
                                @endif
                            </div>
                        </div>

                        <div class="horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label" for="contacto_telefono">Teléfono de Contacto</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['telefono'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width form-group">
                                <label class="form-label" for="contacto_web">Página Web (opcional)</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['sitio_web'] ?? 'No disponible' }}</span>
                            </div>
                        </div>

                        <h4><i class="fas fa-address-card"></i> Datos de Contacto</h4>
                        <span>Persona encargada de recibir solicitudes y requerimientos</span>

                        <div class="form-group">
                            <label class="form-label" for="contacto_nombre">Nombre Completo</label>
                            <span
                                class="data-field">{{ $componentParams['datosPrevios']['contacto_nombre'] ?? 'No disponible' }}</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contacto_cargo">Cargo o Puesto</label>
                            <span
                                class="data-field">{{ $componentParams['datosPrevios']['contacto_cargo'] ?? 'No disponible' }}</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contacto_correo">Correo Electrónico</label>
                            <span
                                class="data-field">{{ $componentParams['datosPrevios']['contacto_correo'] ?? 'No disponible' }}</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contacto_telefono_2">Teléfono de Contacto</label>
                            <span
                                class="data-field">{{ $componentParams['datosPrevios']['contacto_telefono_2'] ?? 'No disponible' }}</span>
                        </div>
                         <div class="comment-section">
                            <label for="comment-form2">Comentarios sobre Domicilio</label>
                            <textarea class="comment-textarea" id="comment-form2" name="comment_form2" placeholder="Escribe tus observaciones aquí..."></textarea>
                            <div class="approval-toggle">
                                <button class="approval-btn approved"><i class="fas fa-check approval-icon"></i> Aprobado</button>
                                <button class="approval-btn not-approved"><i class="fas fa-times approval-icon"></i> No Aprobado</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Formulario 2 -->
                <form id="formulario2" action="{{ $componentParams['action'] }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="next">
                    <div class="form-section" id="form-step-2">
                        <h4><i class="fas fa-map-marker-alt"></i> Domicilio</h4>
                        <div class="form-group horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="codigo_postal">Código Postal</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['codigo_postal'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="estado">Estado</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['estado'] ?? 'No disponible' }}</span>
                            </div>
                        </div>
                        <div class="form-group horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="municipio">Municipio</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['municipio'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width form-group">
                                <label class="form-label" for="colonia">Asentamiento</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['colonia'] ?? 'No disponible' }}</span>
                            </div>
                        </div>
                        <div class="form-group horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label" for="calle">Calle</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['calle'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width form-group">
                                <label class="form-label" for="numero_exterior">Número Exterior</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['numero_exterior'] ?? 'No disponible' }}</span>
                            </div>
                        </div>
                        <div class="form-group horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label" for="numero_interior">Número Interior (Opcional)</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['numero_interior'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width form-group">
                                <label class="form-label" for="entre_calle_1">Entre Calle 1</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['entre_calle_1'] ?? 'No disponible' }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="entre_calle_2">Entre Calle 2</label>
                            <span
                                class="data-field">{{ $componentParams['datosPrevios']['entre_calle_2'] ?? 'No disponible' }}</span>
                        </div>

                        <!-- Map Container -->
                        <div class="form-group full-width">
                            <label class="form-label">Croquis del Domicilio</label>
                            <div id="map-container"
                                style="height: 400px; width: 100%; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);">
                            </div>
                            <span class="form-hint">Mapa interactivo que muestra la ubicación del domicilio
                                proporcionado.</span>
                        </div>
                         <div class="comment-section">
                            <label for="comment-form2">Comentarios sobre Domicilio</label>
                            <textarea class="comment-textarea" id="comment-form2" name="comment_form2" placeholder="Escribe tus observaciones aquí..."></textarea>
                            <div class="approval-toggle">
                                <button class="approval-btn approved"><i class="fas fa-check approval-icon"></i> Aprobado</button>
                                <button class="approval-btn not-approved"><i class="fas fa-times approval-icon"></i> No Aprobado</button>
                            </div>
                        </div>
                    </div>
                
                </form>
                <!-- Formulario 3 -->
                <form id="formulario3" action="{{ $componentParams['action'] }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="next">
                    <input type="hidden" name="seccion" value="3">
                    <div class="form-section" id="form-step-3">
                        <h4><i class="fas fa-building"></i> Datos de Constitución (Persona Moral)</h4>
                        <div class="form-group horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="numero_escritura">Número de Escritura</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['numero_escritura'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="nombre_notario">Nombre del Notario</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['nombre_notario'] ?? 'No disponible' }}</span>
                            </div>
                        </div>
                        <div class="form-group horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="entidad_federativa">Entidad Federativa</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['entidad_federativa'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="fecha_constitucion">Fecha de
                                    Constitución</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['fecha_constitucion'] ?? 'No disponible' }}</span>
                            </div>
                        </div>
                        <div class="form-group horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="numero_notario">Número de Notario</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['numero_notario'] ?? 'No disponible' }}</span>
                            </div>
                        </div>
                        <h4><i class="fas fa-file-contract"></i> Datos de Inscripción en el Registro Público</h4>
                        <div class="form-group horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="numero_registro">Número de Registro o Folio
                                    Mercantil</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['numero_registro'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="fecha_inscripcion">Fecha de Inscripción</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['fecha_inscripcion'] ?? 'No disponible' }}</span>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Formulario 4: Socios o Accionistas -->
                <form id="formulario4" action="{{ $componentParams['action'] }}" method="POST">
                    @csrf
                    <div class="form-section" id="form-step-4">
                        <h4><i class="fas fa-user-friends"></i> Socios o Accionistas</h4>
                        @if (!empty($accionistas))
                            @foreach ($accionistas as $index => $accionista)
                                <div class="form-group">
                                    <h5>Socio/Accionista {{ $index + 1 }}</h5>
                                    <div class="horizontal-group">
                                        <div class="half-width form-group">
                                            <label class="form-label data-label"
                                                for="nombre_accionista_{{ $index }}">Nombre Completo</label>
                                            <span class="data-field">
                                                {{ $accionista['nombre'] ?? 'No disponible' }}
                                                {{ $accionista['apellido_paterno'] ?? '' }}
                                                {{ $accionista['apellido_materno'] ?? '' }}
                                            </span>
                                        </div>
                                        <div class="half-width form-group">
                                            <label class="form-label data-label"
                                                for="porcentaje_accionista_{{ $index }}">Porcentaje de
                                                Participación</label>
                                            <span
                                                class="data-field">{{ $accionista['porcentaje_participacion'] ?? 'No disponible' }}%</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="form-group">
                                <span class="data-field">No hay socios o accionistas registrados.</span>
                            </div>
                        @endif
                        <input type="hidden" name="accionistas" id="accionistas-data">

                         <div class="comment-section">
                            <label for="comment-form2">Comentarios sobre Domicilio</label>
                            <textarea class="comment-textarea" id="comment-form2" name="comment_form2" placeholder="Escribe tus observaciones aquí..."></textarea>
                            <div class="approval-toggle">
                                <button class="approval-btn approved"><i class="fas fa-check approval-icon"></i> Aprobado</button>
                                <button class="approval-btn not-approved"><i class="fas fa-times approval-icon"></i> No Aprobado</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Formulario 5: Datos del Apoderado o Representante Legal -->
                <form id="formulario5" action="{{ $componentParams['action'] }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="next">
                    <input type="hidden" name="seccion" value="5">
                    <div class="form-section" id="form-step-5">
                        <h4><i class="fas fa-user-tie"></i> Datos del Apoderado o Representante Legal</h4>
                        <div class="form-group horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="nombre-apoderado">Nombre</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['nombre_apoderado'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="numero-escritura">Número de Escritura</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['numero_escritura_apoderado'] ?? 'No disponible' }}</span>
                            </div>
                        </div>
                        <div class="form-group horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="nombre-notario">Nombre del Notario</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['nombre_notario_apoderado'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="numero-notario">Número del Notario</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['numero_notario_apoderado'] ?? 'No disponible' }}</span>
                            </div>
                        </div>
                        <div class="form-group horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="entidad-federativa">Entidad Federativa</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['entidad_federativa_apoderado'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="fecha-escritura">Fecha de Escritura</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['fecha_escritura_apoderado'] ?? 'No disponible' }}</span>
                            </div>
                        </div>

                        <h4><i class="fas fa-book"></i> Datos de Inscripción en el Registro Público</h4>
                        <div class="form-group horizontal-group">
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="numero-registro">Número de Registro o Folio
                                    Mercantil</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['numero_registro_apoderado'] ?? 'No disponible' }}</span>
                            </div>
                            <div class="half-width form-group">
                                <label class="form-label data-label" for="fecha-inscripcion">Fecha de Inscripción</label>
                                <span
                                    class="data-field">{{ $componentParams['datosPrevios']['fecha_inscripcion_apoderado'] ?? 'No disponible' }}</span>
                            </div>
                        </div>
                         <div class="comment-section">
                            <label for="comment-form2">Comentarios sobre Domicilio</label>
                            <textarea class="comment-textarea" id="comment-form2" name="comment_form2" placeholder="Escribe tus observaciones aquí..."></textarea>
                            <div class="approval-toggle">
                                <button class="approval-btn approved"><i class="fas fa-check approval-icon"></i> Aprobado</button>
                                <button class="approval-btn not-approved"><i class="fas fa-times approval-icon"></i> No Aprobado</button>
                            </div>
                        </div>
                    </div>
                </form>
                <form id="formulario6" action="{{ $componentParams['action'] }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="upload_documents">
                    <input type="hidden" name="seccion" value="6">
                    <!-- Formulario 6: Documentos del Solicitante -->
                    <div class="form-section" id="form-step-6">
                        <h4><i class="fas fa-file-upload"></i> Documentos del Solicitante</h4>
                        <p class="form-description">A continuación, se muestran los documentos asociados al trámite del
                            solicitante.</p>

                        @if (!empty($componentParams['documentos']))
                            <div class="uploaded-files-grid">
                                @foreach ($componentParams['documentos'] as $documento)
                                    <div class="uploaded-file">
                                        <span
                                            class="file-name">{{ $documento['nombre'] ?? 'Documento sin nombre' }}</span>
                                        <span class="form-hint">
                                            Tipo: {{ $documento['tipo'] ?? 'No especificado' }}<br>
                                            Estado: {{ $documento['estado'] ?? 'No disponible' }}<br>
                                            Fecha de entrega:
                                            {{ $documento['fecha_entrega'] ? \Carbon\Carbon::parse($documento['fecha_entrega'])->format('d/m/Y H:i') : 'No disponible' }}<br>
                                            Versión: {{ $documento['version_documento'] ?? 'No disponible' }}
                                        </span>
                                        @if (!empty($documento['ruta_archivo']))
                                            <a href="{{ $documento['ruta_archivo'] }}" target="_blank"
                                                class="file-link">Ver
                                                archivo</a>
                                        @else
                                            <span class="form-hint">No hay archivo disponible</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="form-group full-width">
                                <span class="data-field">No hay documentos asociados a este trámite.</span>
                            </div>
                        @endif

                        <div class="form-actions">
                            <a href="{{ route('revision.index') }}" class="btn btn-secondary">Volver</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- PDF Viewer Container -->
            <div class="pdf-viewer-container" id="pdf-viewer-container" style="display: none;">
                <div class="pdf-viewer-header">
                    <div class="pdf-viewer-title">
                        <i class="fas fa-file-pdf"></i>
                        <span id="pdf-title">Constancia de Situación Fiscal</span>
                    </div>
                    <button class="pdf-toggle-btn" onclick="closePdfViewer()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="pdf-viewer-content">
                    <iframe id="pdf-frame" class="pdf-frame" src=""></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePdfViewer(pdfUrl, pdfTitle) {
            const pdfViewer = document.getElementById('pdf-viewer-container');
            const pdfFrame = document.getElementById('pdf-frame');
            const pdfTitleElement = document.getElementById('pdf-title');

            pdfFrame.src = pdfUrl;
            pdfTitleElement.textContent = pdfTitle;

            if (pdfViewer.style.display === 'none') {
                pdfViewer.style.display = 'flex';
                const formStep1 = document.getElementById('form-step-1');
                if (formStep1) {
                    const formHeight = formStep1.offsetHeight;
                    pdfViewer.style.height = (formHeight) + 'px';
                    const pdfViewerContent = document.querySelector('.pdf-viewer-content');
                    if (pdfViewerContent) {
                        pdfViewerContent.style.height = (formHeight - 60) + 'px';
                    }
                }
            } else {
                pdfViewer.style.display = 'none';
            }
        }

        function closePdfViewer() {
            const pdfViewer = document.getElementById('pdf-viewer-container');
            pdfViewer.style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const pdfIcon = document.querySelector('.pdf-icon');
            if (pdfIcon) {}
        });
    </script>
@endsection
