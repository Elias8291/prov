
@extends('dashboard')

@section('title', 'Iniciar Revisión de Proveedor')
<link rel="stylesheet" href="{{ asset('assets/css/formularios.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/revision.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/tabla.css') }}">

@section('content')
    <div class="dashboard-container">
        <h1 class="page-title">Revisión de Solicitud</h1>

        <!-- New Info Box -->
        <div class="info-box">
            <div class="info-box__header">
                <div class="info-box__title">
                    <i class="fas fa-info-circle"></i>
                    Detalles del Trámite
                </div>
            </div>
            <div class="info-box__details">
                <div class="info-box__item">
                    <span class="info-box__label">RFC Solicitante</span>
                    <span class="info-box__value">{{ $solicitante->rfc }}</span>
                </div>
                <div class="info-box__item">
                    <span class="info-box__label">Tipo de Trámite</span>
                    <span class="info-box__value info-box__value--badge">{{ $tipo_tramite }}</span>
                </div>
                <div class="info-box__item">
                    <span class="info-box__label">Fecha de Finalización</span>
                    <span class="info-box__value">
                        {{ $tramite->fecha_finalizacion ? \Carbon\Carbon::parse($tramite->fecha_finalizacion)->format('d/m/Y H:i') : ($tramite->updated_at ? \Carbon\Carbon::parse($tramite->updated_at)->format('d/m/Y H:i') : 'No disponible') }}
                    </span>
                </div>
                <div class="info-box__item">
                    <span class="info-box__label">Tiempo Restante</span>
                    <span class="info-box__timer" id="countdown-timer">Calculando...</span>
                </div>
            </div>
        </div>

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
                <!-- Formulario 1: Datos Generales (Siempre visible) -->
                <form id="formulario1" method="POST" action="{{ $componentParams['action'] }}"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="seccion" value="1">
                    <input type="hidden" name="action" value="next">
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
                            @if ($constanciaDoc && !empty($constanciaDoc['ruta_archivo']))
                                <a href="javascript:void(0);" class="pdf-icon" title="Ver Constancia de Situación Fiscal"
                                    onclick="togglePdfViewer('{{ $constanciaDoc['ruta_archivo'] }}', '{{ $constanciaDoc['nombre'] ?? 'Constancia de Situación Fiscal' }}')">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            @else
                                <span class="form-hint">Constancia de Situación Fiscal no disponible</span>
                            @endif
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
                            <label for="comment-form1">Comentarios sobre Datos Generales</label>
                            <textarea class="comment-textarea" id="comment-form1" name="comment_form1"
                                placeholder="Escribe tus observaciones aquí..."></textarea>
                            <div class="approval-toggle">
                                <button type="button" class="approval-btn approved" data-status="approved"><i
                                        class="fas fa-check approval-icon"></i> Aprobado</button>
                                <button type="button" class="approval-btn not-approved" data-status="not-approved"><i
                                        class="fas fa-times approval-icon"></i> No Aprobado</button>
                                <input type="hidden" name="approval_form1" id="approval-form1" value="">
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Formulario 2: Domicilio (Siempre visible) -->
                <form id="formulario2" action="{{ $componentParams['action'] }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="next">
                    <input type="hidden" name="seccion" value="2">
                    <div class="form-section" id="form-step-2">
                        <h4><i class="fas fa-map-marker-alt"></i> Domicilio</h4>
                        <!-- Display Duplicate Address Warning -->
                        @if ($duplicate_address_warning)
                            <div class="alert alert-warning" style="margin-bottom: 20px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $duplicate_address_warning }}
                            </div>
                        @endif
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

                        <div class="form-group full-width">
                            <label class="form-label">Croquis del Domicilio</label>
                            <div id="map-container"
                                style="height: 400px; width: 100%; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);">
                            </div>
                            <span class="form-hint">Mapa interactivo que muestra la ubicación del domicilio
                                proporcionado.</span>
                        </div>

                        <div class="commentmaybe
                        <div class="comment-section">
                            <label for="comment-form2">Comentarios sobre Domicilio</label>
                            <textarea class="comment-textarea" id="comment-form2" name="comment_form2"
                                placeholder="Escribe tus observaciones aquí..."></textarea>
                            <div class="approval-toggle">
                                <button type="button" class="approval-btn approved" data-status="approved"><i
                                        class="fas fa-check approval-icon"></i> Aprobado</button>
                                <button type="button" class="approval-btn not-approved" data-status="not-approved"><i
                                        class="fas fa-times approval-icon"></i> No Aprobado</button>
                                <input type="hidden" name="approval_form2" id="approval-form2" value="">
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Formulario 3: Datos de Constitución (Solo Persona Moral) -->
                @if ($componentParams['tipoPersona'] === 'Moral')
                    <form id="formulario3" action="{{ $componentParams['action'] }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="action" value="next">
                        <input type="hidden" name="seccion" value="3">
                        <div class="form-section" id="form-step-3">
                            <h4><i class="fas fa-building"></i> Datos de Constitución (Persona Moral)</h4>
                            <div class="form-group horizontal-group">
                                <div class="half-width form-group">
                                    <label class="form-label data-label" for="numero_escritura">Número de
                                        Escritura</label>
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
                                    <label class="form-label data-label" for="entidad_federativa">Entidad
                                        Federativa</label>
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
                                    <label class="form-label data-label" for="fecha_inscripcion">Fecha de
                                        Inscripción</label>
                                    <span
                                        class="data-field">{{ $componentParams['datosPrevios']['fecha_inscripcion'] ?? 'No disponible' }}</span>
                                </div>
                            </div>

                            <div class="comment-section">
                                <label for="comment-form3">Comentarios sobre Datos de Constitución</label>
                                <textarea class="comment-textarea" id="comment-form3" name="comment_form3"
                                    placeholder="Escribe tus observaciones aquí..."></textarea>
                                <div class="approval-toggle">
                                    <button type="button" class="approval-btn approved" data-status="approved"><i
                                            class="fas fa-check approval-icon"></i> Aprobado</button>
                                    <button type="button" class="approval-btn not-approved"
                                        data-status="not-approved"><i class="fas fa-times approval-icon"></i> No
                                        Aprobado</button>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif

                <!-- Formulario 4: Socios o Accionistas (Solo Persona Moral) -->
                @if ($componentParams['tipoPersona'] === 'Moral')
                    <form id="formulario4" action="{{ $componentParams['action'] }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="action" value="next">
                        <input type="hidden" name="seccion" value="4">
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
                                <label for="comment-form4">Comentarios sobre Socios o Accionistas</label>
                                <textarea class="comment-textarea" id="comment-form4" name="comment_form4"
                                    placeholder="Escribe tus observaciones aquí..."></textarea>
                                <div class="approval-toggle">
                                    <button type="button" class="approval-btn approved" data-status="approved"><i
                                            class="fas fa-check approval-icon"></i> Aprobado</button>
                                    <button type="button" class="approval-btn not-approved"
                                        data-status="not-approved"><i class="fas fa-times approval-icon"></i> No
                                        Aprobado</button>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif

                <!-- Formulario 5: Datos del Apoderado o Representante Legal (Solo Persona Moral) -->
                @if ($componentParams['tipoPersona'] === 'Moral')
                    <form id="formulario5" action="{{ $componentParams['action'] }}" method="POST"
                        enctype="multipart/form-data">
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
                                    <label class="form-label data-label" for="numero-escritura">Número de
                                        Escritura</label>
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
                                    <label class="form-label data-label" for="entidad-federativa">Entidad
                                        Federativa</label>
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
                                    <label class="form-label data-label" for="fecha-inscripcion">Fecha de
                                        Inscripción</label>
                                    <span
                                        class="data-field">{{ $componentParams['datosPrevios']['fecha_inscripcion_apoderado'] ?? 'No disponible' }}</span>
                                </div>
                            </div>

                            <div class="comment-section">
                                <label for="comment-form5">Comentarios sobre Representante Legal</label>
                                <textarea class="comment-textarea" id="comment-form5" name="comment_form5"
                                    placeholder="Escribe tus observaciones aquí..."></textarea>
                                <div class="approval-toggle">
                                    <button type="button" class="approval-btn approved" data-status="approved"><i
                                            class="fas fa-check approval-icon"></i> Aprobado</button>
                                    <button type="button" class="approval-btn not-approved"
                                        data-status="not-approved"><i class="fas fa-times approval-icon"></i> No
                                        Aprobado</button>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif

                <!-- Formulario 6: Documentos del Solicitante (Siempre visible) -->
                <form id="formulario6" action="{{ $componentParams['action'] }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="upload_documents">
                    <input type="hidden" name="seccion" value="6">
                    <div class="form-section" id="form-step-6">
                        <h4><i class="fas fa-file-upload"></i> Documentos del Solicitante</h4>
                        <p class="form-description">A continuación, se muestran los documentos asociados al trámite del
                            solicitante. Haga clic en un documento para revisarlo y agregar observaciones.</p>

                        @if (!empty($componentParams['documentos']))
                            <div class="uploaded-files-grid">
                                @foreach ($componentParams['documentos'] as $index => $documento)
                                    <div class="uploaded-file">
                                        <a href="javascript:void(0);" class="file-link"
                                            onclick="openDocumentModal('{{ $documento['ruta_archivo'] ?? '' }}', '{{ $documento['nombre'] ?? 'Documento sin nombre' }}', '{{ $documento['documento_id'] ?? $index }}', {{ $index }})">
                                            <span
                                                class="file-name">{{ $documento['nombre'] ?? 'Documento sin nombre' }}</span>
                                        </a>
                                        <span class="form-hint">
                                            Tipo: {{ $documento['tipo'] ?? 'No especificado' }}<br>
                                            Estado: {{ $documento['estado'] ?? 'No disponible' }}<br>
                                            Fecha de entrega:
                                            {{ $documento['fecha_entrega'] ? \Carbon\Carbon::parse($documento['fecha_entrega'])->format('d/m/Y H:i') : 'No disponible' }}<br>
                                            Versión: {{ $documento['version_documento'] ?? 'No disponible' }}
                                        </span>
                                        <input type="hidden" name="documentos[{{ $index }}][comment]"
                                            id="comment-doc-{{ $index }}" value="">
                                        <input type="hidden" name="documentos[{{ $index }}][approval]"
                                            id="approval-doc-{{ $index }}" value="">
                                        <input type="hidden" name="documentos[{{ $index }}][documento_id]"
                                            value="{{ $documento['documento_id'] ?? $index }}">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="form-group full-width">
                                <span class="data-field">No hay documentos asociados a este trámite.</span>
                            </div>
                        @endif
                    </div>
                </form>
                <div class="form-actions">
                    <a href="{{ route('revision.index') }}" class="btn btn-secondary">Volver</a>
                    <button type="submit" form="formulario6" class="btn btn-primary">Guardar</button>
                </div>
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

            <!-- Document Modal -->
            <div class="modal" id="document-modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title">
                            <i class="fas fa-file-pdf"></i>
                            <span id="modal-title">Documento</span>
                        </div>
                        <button class="modal-close-btn" onclick="closeDocumentModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-iframe-container">
                            <iframe id="modal-iframe" class="modal-iframe" src=""></iframe>
                        </div>
                        <div class="modal-comment-section">
                            <label for="modal-comment">Observaciones</label>
                            <textarea class="comment-textarea" id="modal-comment"
                                placeholder="Escribe tus observaciones aquí..."></textarea>
                            <div class="approval-toggle">
                                <button type="button" class="approval-btn approved" data-status="approved"
                                    data-selected="false">
                                    <i class="fas fa-check approval-icon"></i> Aprobado
                                </button>
                                <button type="button" class="approval-btn not-approved" data-status="not-approved"
                                    data-selected="false">
                                    <i class="fas fa-times approval-icon"></i> No Aprobado
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Existing JavaScript for PDF viewer and other forms
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
                    pdfViewer.style.height = formHeight + 'px';
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

        // Document Modal Functions
        let currentDocIndex = null;

        function openDocumentModal(pdfUrl, title, docId, index) {
            const modal = document.getElementById('document-modal');
            const iframe = document.getElementById('modal-iframe');
            const titleElement = document.getElementById('modal-title');
            const commentTextarea = document.getElementById('modal-comment');
            const approvedBtn = document.querySelector('#document-modal .approval-btn.approved');
            const notApprovedBtn = document.querySelector('#document-modal .approval-btn.not-approved');

            currentDocIndex = index;
            iframe.src = pdfUrl;
            titleElement.textContent = title;

            // Load existing comment and approval status
            const commentInput = document.getElementById(`comment-doc-${index}`);
            const approvalInput = document.getElementById(`approval-doc-${index}`);
            commentTextarea.value = commentInput ? commentInput.value : '';
            approvedBtn.dataset.selected = approvalInput && approvalInput.value === 'approved' ? 'true' : 'false';
            notApprovedBtn.dataset.selected = approvalInput && approvalInput.value === 'not-approved' ? 'true' : 'false';
            approvedBtn.style.opacity = approvedBtn.dataset.selected === 'true' ? '1' : '0.5';
            notApprovedBtn.style.opacity = notApprovedBtn.dataset.selected === 'true' ? '1' : '0.5';

            modal.style.display = 'flex';
        }

        function closeDocumentModal() {
            const modal = document.getElementById('document-modal');
            const iframe = document.getElementById('modal-iframe');
            const commentTextarea = document.getElementById('modal-comment');
            const approvedBtn = document.querySelector('#document-modal .approval-btn.approved');
            const notApprovedBtn = document.querySelector('#document-modal .approval-btn.not-approved');

            // Save comment and approval status
            if (currentDocIndex !== null) {
                const commentInput = document.getElementById(`comment-doc-${currentDocIndex}`);
                const approvalInput = document.getElementById(`approval-doc-${currentDocIndex}`);
                if (commentInput) commentInput.value = commentTextarea.value;
                if (approvalInput) {
                    approvalInput.value = approvedBtn.dataset.selected === 'true' ? 'approved' : (notApprovedBtn.dataset
                        .selected === 'true' ? 'not-approved' : '');
                }
            }

            modal.style.display = 'none';
            iframe.src = '';
            currentDocIndex = null;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Approval Button Logic for Modal
            const approvedBtn = document.querySelector('#document-modal .approval-btn.approved');
            const notApprovedBtn = document.querySelector('#document-modal .approval-btn.not-approved');

            if (approvedBtn && notApprovedBtn) {
                approvedBtn.addEventListener('click', () => {
                    approvedBtn.dataset.selected = 'true';
                    notApprovedBtn.dataset.selected = 'false';
                    approvedBtn.style.opacity = '1';
                    notApprovedBtn.style.opacity = '0.5';
                });

                notApprovedBtn.addEventListener('click', () => {
                    approvedBtn.dataset.selected = 'false';
                    notApprovedBtn.dataset.selected = 'true';
                    approvedBtn.style.opacity = '0.5';
                    notApprovedBtn.style.opacity = '1';
                });
            }

            // Existing approval toggle logic for other forms
            document.querySelectorAll('.approval-toggle').forEach(toggle => {
                if (toggle.closest('#document-modal')) return; // Skip modal toggles
                const approvedBtn = toggle.querySelector('.approval-btn.approved');
                const notApprovedBtn = toggle.querySelector('.approval-btn.not-approved');
                const hiddenInput = toggle.querySelector('input[type="hidden"]');

                if (approvedBtn && notApprovedBtn && hiddenInput) {
                    approvedBtn.addEventListener('click', () => {
                        approvedBtn.style.opacity = '1';
                        notApprovedBtn.style.opacity = '0.5';
                        hiddenInput.value = 'approved';
                    });

                    notApprovedBtn.addEventListener('click', () => {
                        approvedBtn.style.opacity = '0.5';
                        notApprovedBtn.style.opacity = '1';
                        hiddenInput.value = 'not-approved';
                    });
                }
            });

            // Existing countdown timer logic
            const countdownElement = document.getElementById('countdown-timer');
            const finalizationDate = new Date('{{ $tramite->fecha_finalizacion ?? $tramite->updated_at }}')
                .getTime();
            const tipoTramite = '{{ $tipo_tramite }}';
            const deadlineDate = new Date(finalizationDate + (tipoTramite.toLowerCase() === 'inscripción' ? 3 : 7) *
                24 * 60 * 60 * 1000);

            function updateCountdown() {
                const now = new Date().getTime();
                const timeRemaining = deadlineDate - now;

                if (timeRemaining <= 0) {
                    countdownElement.textContent = '¡Tiempo Expirado!';
                    countdownElement.classList.add('expired');
                    return;
                }

                const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

                countdownElement.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                countdownElement.classList.remove('expired');
            }

            if (finalizationDate) {
                updateCountdown();
                setInterval(updateCountdown, 1000);
            } else {
                countdownElement.textContent = 'Fecha no disponible';
            }
        });
    </script>

    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            border-radius: 8px;
            width: 90%;
            max-width: 1200px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f9fafb;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
        }

        .modal-close-btn:hover {
            color: #000;
        }

        .modal-body {
            display: flex;
            padding: 20px;
            gap: 20px;
        }

        .modal-iframe-container {
            flex: 3;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .modal-iframe {
            width: 100%;
            height: 600px;
            border: none;
        }

        .modal-comment-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .modal-comment-section label {
            font-size: 1rem;
            font-weight: 500;
        }

        .modal-comment-section .comment-textarea {
            width: 100%;
            min-height: 200px;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            resize: vertical;
        }

        .modal-comment-section .approval-toggle {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .modal-comment-section .approval-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .modal-comment-section .approval-btn.approved {
            background-color: #22c55e;
            color: #fff;
        }

        .modal-comment-section .approval-btn.not-approved {
            background-color: #ef4444;
            color: #fff;
        }

        .modal-comment-section .approval-btn:hover {
            opacity: 0.9;
        }
    </style>
@endsection
