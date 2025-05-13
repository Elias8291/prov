@extends('dashboard')

@section('title', 'Historial de Proveedor')

<link rel="stylesheet" href="{{ asset('assets/css/tabla.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/formularios.css') }}">

@section('content')
    <div class="dashboard-container">
        <div class="content-wrapper">
            <header class="page-header">
                <div class="header-content">
                    <h1 class="page-title">Historial de Proveedor</h1>
   <p class="page-subtitle">Gestión de renovaciones y estados de PVs para <span id="supplier-name">{{ $supplier ? $supplier['name'] : 'Proveedor' }}</span></p>
                </div>
            </header>

            <div id="alert-container">
                @if (isset($message))
                    <div class="alert alert-danger">
                        {{ $message }}
                    </div>
                @endif
            </div>

            <div class="status-tabs">
                <button class="tab-btn active" data-tab="all">
                    <span class="tab-icon"><i class="fas fa-th-list"></i></span>
                    <span class="tab-text">Todos</span>
                    <span class="tab-count" id="count-all">{{ count($pvs) }}</span>
                </button>
                <button class="tab-btn" data-tab="formularios">
                    <span class="tab-icon"><i class="fas fa-file-alt"></i></span>
                    <span class="tab-text">Formularios</span>
                </button>
            </div>

            <!-- Sección de PVs del Proveedor -->
            <div class="supplier-pvs" id="supplier-pvs">
                <!-- Las tarjetas se generarán dinámicamente aquí -->
            </div>

            <!-- Modal para detalles de revisión -->
            <div id="review-modal" class="modal">
                <div class="modal-content">
                    <span class="close-modal-btn">×</span>
                    <div id="modal-content">
                        <!-- Contenido dinámico del modal -->
                    </div>
                </div>
            </div>

            <!-- Modal para registro exitoso -->
            <div id="success-modal" class="modal">
                <div class="modal-content">
                    <span class="close-modal-btn">×</span>
                    <div class="modal-header">
                        <h3 class="modal-title">Registro Exitoso</h3>
                    </div>
                    <div class="modal-body">
                        <p>El registro al padrón de proveedores fue exitoso.</p>
                        <button class="action-btn close-success-modal"><i class="fas fa-check"></i> Aceptar</button>
                    </div>
                </div>
            </div>

            <!-- Modal para registro denegado -->
            <div id="denied-modal" class="modal">
                <div class="modal-content">
                    <span class="close-modal-btn">×</span>
                    <div class="modal-header">
                        <h3 class="modal-title">Registro Denegado</h3>
                    </div>
                    <div class="modal-body">
                        <p>El formulario no cumple con los requisitos necesarios. Por favor, revisa los comentarios y documentos faltantes.</p>
                        <button class="action-btn close-denied-modal"><i class="fas fa-check"></i> Aceptar</button>
                    </div>
                </div>
            </div>

            <!-- Modal para comentarios registrados -->
            <div id="comments-modal" class="modal">
                <div class="modal-content">
                    <span class="close-modal-btn">×</span>
                    <div class="modal-header">
                        <h3 class="modal-title">Comentarios Registrados</h3>
                    </div>
                    <div class="modal-body">
                        <p>Los comentarios sobre el formulario han sido registrados exitosamente.</p>
                        <button class="action-btn close-comments-modal"><i class="fas fa-check"></i> Aceptar</button>
                    </div>
                </div>
            </div>

            <!-- Formulario de revisión -->
            <div id="review-form" class="details-container" style="display: none; position: relative;">
                <a href="#" class="close-form-btn"><i class="fas fa-times"></i></a>
                <div class="form-pdf-container">
                    <div class="form-column">
                        <div class="form-scroll-container">
                            <div class="filled-form-container">
                                <h3 class="filled-form-title">Revisión de Formulario Rellenado</h3>
                                <!-- Datos del formulario -->
                                <div class="form-data-column">
                                    <!-- Datos Generales -->
                                    <div class="form-section filled-section" data-section="datos-generales">
                                        <div class="section-header">
                                            <h4><i class="fas fa-building"></i> Datos Generales</h4>
                                            <button class="pdf-view-btn" data-pdf="{{ asset('assets/pdf/Prueba.pdf') }}" data-section="datos-generales" data-page="1">
                                                <i class="fas fa-eye"></i> Ver PDF
                                            </button>
                                        </div>
                                        <div class="form-group horizontal-group">
                                            <div class="half-width">
                                                <label class="form-label data-label">Tipo de Proveedor</label>
                                                <div class="filled-value">Moral</div>
                                            </div>
                                            <div class="half-width">
                                                <label class="form-label data-label">RFC</label>
                                                <div class="filled-value">{{ $supplier ? $supplier['rfc'] : 'N/A' }}</div>
                                            </div>
                                        </div>
                                        <div class="form-group horizontal-group">
                                            <div class="half-width">
                                                <label class="form-label">Razón Social</label>
                                                <div class="filled-value">{{ $supplier ? $supplier['name'] : 'N/A' }}</div>
                                            </div>
                                            <div class="half-width">
                                                <label class="form-label">Correo Electrónico</label>
                                                <div class="filled-value">{{ $supplier && isset($supplier['contact_email']) ? $supplier['contact_email'] : 'N/A' }}</div>
                                            </div>
                                        </div>
                                        <div class="form-group full-width">
                                            <label class="form-label">Sectores</label>
                                            <div class="filled-value">Tecnología, Servicios</div>
                                        </div>
                                        <div class="form-group full-width">
                                            <label class="form-label">Actividades</label>
                                            <div class="filled-value">Desarrollo de Software</div>
                                        </div>
                                        <div class="form-group full-width">
                                            <label class="form-label">Actividades Seleccionadas</label>
                                            <div class="actividades-container filled-value">
                                                <span class="actividad-chip">Desarrollo de Software</span>
                                                <span class="actividad-chip">Consultoría TI</span>
                                            </div>
                                        </div>
                                        <div class="form-group horizontal-group">
                                            <div class="half-width">
                                                <label class="form-label">Teléfono de Contacto</label>
                                                <div class="filled-value">{{ $supplier ? $supplier['phone'] : 'N/A' }}</div>
                                            </div>
                                            <div class="half-width">
                                                <label class="form-label">Página Web</label>
                                                <div class="filled-value">https://www.tecnologiaavanzada.com</div>
                                            </div>
                                        </div>
                                        <h4><i class="fas fa-address-card"></i> Datos de Contacto</h4>
                                        <span>Persona encargada de recibir solicitudes y requerimientos</span>
                                        <div class="form-group">
                                            <label class="form-label">Nombre Completo</label>
                                            <div class="filled-value">{{ $supplier ? $supplier['contact_name'] : 'N/A' }}</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Cargo o Puesto</label>
                                            <div class="filled-value">Gerente de Ventas</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Correo Electrónico</label>
                                            <div class="filled-value">{{ $supplier && isset($supplier['contact_email']) ? $supplier['contact_email'] : 'N/A' }}</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Teléfono de Contacto 2</label>
                                            <div class="filled-value">{{ $supplier ? $supplier['phone'] : 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <!-- Domicilio -->
                                    <div class="form-section filled-section" data-section="domicilio">
                                        <div class="section-header">
                                            <h4><i class="fas fa-map-marker-alt"></i> Domicilio</h4>
                                            <button class="pdf-view-btn" data-pdf="{{ asset('assets/pdf/Prueba.pdf') }}" data-section="domicilio" data-page="2">
                                                <i class="fas fa-eye"></i> Ver PDF
                                            </button>
                                        </div>
                                        <div class="form-group horizontal-group">
                                            <div class="half-width">
                                                <label class="form-label data-label">Código Postal</label>
                                                <div class="filled-value">06600</div>
                                            </div>
                                            <div class="half-width">
                                                <label class="form-label data-label">Estado</label>
                                                <div class="filled-value">Ciudad de México</div>
                                            </div>
                                        </div>
                                        <div class="form-group horizontal-group">
                                            <div class="half-width">
                                                <label class="form-label data-label">Municipio</label>
                                                <div class="filled-value">Cuauhtémoc</div>
                                            </div>
                                            <div class="half-width">
                                                <label class="form-label">Asentamiento</label>
                                                <div class="filled-value">Colonia Juárez</div>
                                            </div>
                                        </div>
                                        <div class="form-group horizontal-group">
                                            <div class="half-width">
                                                <label class="form-label">Calle</label>
                                                <div class="filled-value">Av. Insurgentes Sur</div>
                                            </div>
                                            <div class="half-width">
                                                <label class="form-label">Número Exterior</label>
                                                <div class="filled-value">123</div>
                                            </div>
                                        </div>
                                        <div class="form-group horizontal-group">
                                            <div class="half-width">
                                                <label class="form-label">Número Interior</label>
                                                <div class="filled-value">4B</div>
                                            </div>
                                            <div class="half-width">
                                                <label class="form-label">Entre Calle 1</label>
                                                <div class="filled-value">Calle Londres</div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Entre Calle 2</label>
                                            <div class="filled-value">Calle Hamburgo</div>
                                        </div>
                                    </div>
                                    <!-- Datos de Constitución -->
                                    <div class="form-section filled-section" data-section="constitucion">
                                        <div class="section-header">
                                            <h4><i class="fas fa-building"></i> Datos de Constitución (Persona Moral)</h4>
                                            <button class="pdf-view-btn" data-pdf="{{ asset('assets/pdf/Prueba.pdf') }}" data-section="constitucion" data-page="3">
                                                <i class="fas fa-eye"></i> Ver PDF
                                            </button>
                                        </div>
                                        <div class="form-group horizontal-group">
                                            <div class="half-width">
                                                <label class="form-label">Número de Escritura</label>
                                                <div class="filled-value">54321</div>
                                            </div>
                                            <div class="half-width">
                                                <label class="form-label">Nombre del Notario</label>
                                                <div class="filled-value">Lic. Juan Pérez González</div>
                                            </div>
                                        </div>
                                        <div class="form-group horizontal-group">
                                            <div class="half-width">
                                                <label class="form-label">Entidad Federativa</label>
                                                <div class="filled-value">Ciudad de México</div>
                                            </div>
                                            <div class="half-width">
                                                <label class="form-label">Fecha de Constitución</label>
                                                <div class="filled-value">15/03/2010</div>
                                            </div>
                                        </div>
                                        <div class="form-group horizontal-group">
                                            <div class="half-width">
                                                <label class="form-label">Número de Notario</label>
                                                <div class="filled-value">456</div>
                                            </div>
                                            <div class="half-width"></div>
                                        </div>
                                        <h4><i class="fas fa-file-contract"></i> Datos de Inscripción en el Registro Público</h4>
                                        <div class="form-group horizontal-group">
                                            <div class="half-width">
                                                <label class="form-label">Número de Registro o Folio Mercantil</label>
                                                <div class="filled-value">987654</div>
                                            </div>
                                            <div class="half-width">
                                                <label class="form-label">Fecha de Inscripción</label>
                                                <div class="filled-value">20/04/2010</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Socios o Accionistas -->
                                    <div class="form-section filled-section" data-section="socios">
                                        <div class="form-container">
                                            <div class="form-column">
                                                <div class="form-header">
                                                    <h4><i class="fas fa-users"></i> Socios o Accionistas (Persona Moral)</h4>
                                                    <p class="subtitle">Lista de socios o accionistas de la empresa</p>
                                                    <div class="percentage-summary">
                                                        <div class="progress-bar-container">
                                                            <div class="progress-bar" style="width: 100%;"></div>
                                                        </div>
                                                        <span>100% asignado</span>
                                                    </div>
                                                </div>
                                                <div class="shareholders-container">
                                                    <div class="shareholder-item">
                                                        <div class="form-group horizontal-group">
                                                            <div class="half-width">
                                                                <label class="form-label">Nombre del Socio/Accionista</label>
                                                                <div class="filled-value">Juan Pérez López</div>
                                                            </div>
                                                            <div class="half-width">
                                                                <label class="form-label">Porcentaje de Participación</label>
                                                                <div class="filled-value">60%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="shareholder-item">
                                                        <div class="form-group horizontal-group">
                                                            <div class="half-width">
                                                                <label class="form-label">Nombre del Socio/Accionista</label>
                                                                <div class="filled-value">María García Sánchez</div>
                                                            </div>
                                                            <div class="half-width">
                                                                <label class="form-label">Porcentaje de Participación</label>
                                                                <div class="filled-value">40%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Representante Legal -->
                                    <div class="form-section filled-section" data-section="representante">
                                        <div class="section-header">
                                            <h4><i class="fas fa-user-tie"></i> Datos del Apoderado o Representante Legal</h4>
                                            <button class="pdf-view-btn" data-pdf="{{ asset('assets/pdf/Prueba.pdf') }}" data-section="representante" data-page="4">
                                                <i class="fas fa-eye"></i> Ver PDF
                                            </button>
                                        </div>
                                        <div class="form-container">
                                            <div class="form-column">
                                                <div class="form-group horizontal-group">
                                                    <div class="half-width">
                                                        <label class="form-label">Nombre</label>
                                                        <div class="filled-value">{{ $supplier ? $supplier['contact_name'] : 'N/A' }}</div>
                                                    </div>
                                                    <div class="half-width">
                                                        <label class="form-label">Número de Escritura</label>
                                                        <div class="filled-value">78910</div>
                                                    </div>
                                                </div>
                                                <div class="form-group horizontal-group">
                                                    <div class="half-width">
                                                        <label class="form-label">Nombre del Notario</label>
                                                        <div class="filled-value">Lic. Ana López Ramírez</div>
                                                    </div>
                                                    <div class="half-width">
                                                        <label class="form-label">Número del Notario</label>
                                                        <div class="filled-value">789</div>
                                                    </div>
                                                </div>
                                                <div class="form-group horizontal-group">
                                                    <div class="half-width">
                                                        <label class="form-label">Entidad Federativa</label>
                                                        <div class="filled-value">Ciudad de México</div>
                                                    </div>
                                                    <div class="half-width">
                                                        <label class="form-label">Fecha de Escritura</label>
                                                        <div class="filled-value">10/05/2020</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Sección de comentarios generales -->
                                    <div class="form-section filled-section" data-section="comentarios">
                                        <div class="section-header">
                                            <h4><i class="fas fa-comment"></i> Comentarios Generales</h4>
                                        </div>
                                        <div class="form-group full-width">
                                            <label class="form-label">Escriba sus comentarios sobre el formulario</label>
                                            <textarea id="general-comments" class="form-input" rows="5" placeholder="Ingrese comentarios generales sobre la revisión del formulario"></textarea>
                                        </div>
                                        <button class="action-btn submit-comments-btn"><i class="fas fa-paper-plane"></i> Enviar Comentarios</button>
                                    </div>
                                    <!-- Botones de decisión -->
                                    <div class="form-section decision-buttons" style="display: none;">
                                        <div class="section-header">
                                            <h4><i class="fas fa-check-circle"></i> Decisión Final</h4>
                                        </div>
                                        <div class="form-group decision-group">
                                            <button class="action-btn accept-btn"><i class="fas fa-check"></i> Aceptar Formulario</button>
                                            <button class="action-btn deny-btn" style="background-color: var(--danger-color);"><i class="fas fa-times"></i> Denegar Formulario</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Carrusel de PDFs -->
                                <div class="pdf-carousel-container">
                                    <h4><i class="fas fa-file-pdf"></i> Documentos Asociados</h4>
                                    <div class="pdf-carousel">
                                        <div class="pdf-card">
                                            <i class="fas fa-file-pdf pdf-icon"></i>
                                            <span class="pdf-name">constancia_situacion_fiscal_2025.pdf</span>
                                            <button class="preview-btn" title="Ver PDF" data-pdf="{{ Storage::url('pdfs/constancia_situacion_fiscal_2025.pdf') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="pdf-card">
                                            <i class="fas fa-file-pdf pdf-icon"></i>
                                            <span class="pdf-name">comprobante_domicilio.pdf</span>
                                            <button class="preview-btn" title="Ver PDF" data-pdf="{{ Storage::url('pdfs/comprobante_domicilio.pdf') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="pdf-card">
                                            <i class="fas fa-file-pdf pdf-icon"></i>
                                            <span class="pdf-name">acta_constitutiva.pdf</span>
                                            <button class="preview-btn" title="Ver PDF" data-pdf="{{ Storage::url('pdfs/acta_constitutiva.pdf') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="pdf-card">
                                            <i class="fas fa-file-pdf pdf-icon"></i>
                                            <span class="pdf-name">poder_notariado.pdf</span>
                                            <button class="preview-btn" title="Ver PDF" data-pdf="{{ Storage::url('pdfs/poder_notariado.pdf') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="pdf-card">
                                            <i class="fas fa-file-pdf pdf-icon"></i>
                                            <span class="pdf-name">identificacion_oficial.pdf</span>
                                            <button class="preview-btn" title="Ver PDF" data-pdf="{{ Storage::url('pdfs/identificacion_oficial.pdf') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="pdf-card">
                                            <i class="fas fa-file-pdf pdf-icon"></i>
                                            <span class="pdf-name">curriculum_actualizado.pdf</span>
                                            <button class="preview-btn" title="Ver PDF" data-pdf="{{ Storage::url('pdfs/curriculum_actualizado.pdf') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="pdf-card">
                                            <i class="fas fa-file-pdf pdf-icon"></i>
                                            <span class="pdf-name">carta_poder.pdf</span>
                                            <button class="preview-btn" title="Ver PDF" data-pdf="{{ Storage::url('pdfs/carta_poder.pdf') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="pdf-card">
                                            <i class="fas fa-file-pdf pdf-icon"></i>
                                            <span class="pdf-name">acuse_recibo.pdf</span>
                                            <button class="preview-btn" title="Ver PDF" data-pdf="{{ Storage::url('pdfs/acuse_recibo.pdf') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="pdf-card">
                                            <i class="fas fa-file-pdf pdf-icon"></i>
                                            <span class="pdf-name">modificaciones_acta.pdf</span>
                                            <button class="preview-btn" title="Ver PDF" data-pdf="{{ Storage::url('pdfs/modificaciones_acta.pdf') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pdf-column" id="pdf-preview" style="display: none;">
                        <div class="pdf-viewer">
                            <div class="pdf-header">
                                <h4><i class="fas fa-file-pdf"></i> Vista Previa del PDF</h4>
                                <button class="close-pdf-btn"><i class="fas fa-times"></i> Cerrar PDF</button>
                            </div>
                            <iframe id="pdf-iframe" src="" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        :root {
            --primary-color: #9D2449;
            --primary-dark: #7a1c38;
            --primary-light: #f8e8ee;
            --expired-color: #6B7280;
            --expired-dark: #4B5563;
            --expired-light: #D1D5DB;
            --success-color: #10b981;
            --success-light: #d1fae5;
            --success-dark: #059669;
            --danger-color: #f32727;
            --danger-light: #fee2e2;
            --danger-dark: #cc1717;
            --text-dark: #374151;
            --text-medium: #6b7280;
            --text-light: #9ca3af;
            --bg-light: #f9fafb;
            --border-light: #e5e7eb;
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --font-primary: 'Inter', 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            --radius-md: 8px;
            --radius-lg: 12px;
            --transition-normal: 0.3s ease;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: var(--font-primary);
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .content-wrapper {
            max-width: 2000px;
            margin: 0 auto;
            padding: 40px 32px;
        }

        .page-header {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-light);
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: var(--text-medium);
            font-size: 14px;
            margin: 0;
        }

        .status-tabs {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 24px;
            padding-bottom: 8px;
            overflow-x: auto;
        }

        .tab-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px 16px;
            background-color: white;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            font-size: 12px;
            font-weight: 500;
            color: var(--text-medium);
            cursor: pointer;
            transition: all var(--transition-normal);
            white-space: nowrap;
            box-shadow: var(--shadow-md);
            min-width: 100px;
        }

        .tab-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-dark);
            box-shadow: 0 4px 8px rgba(107, 114, 128, 0.2);
            font-weight: 600;
        }

        .tab-btn:hover:not(.active) {
            background-color: var(--primary-light);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .supplier-pvs {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .supplier-card {
            background-color: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            transition: all var(--transition-normal);
            border: 1px solid var(--border-light);
            display: flex;
            flex-direction: column;
            position: relative;
            height: 100%;
            max-width: 280px;
            overflow: hidden;
        }

        .supplier-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .supplier-card.activo {
            background-color: white;
        }

        .supplier-card.activo .card-body {
            background-color: white;
        }

        .supplier-card.expirado {
            background-color: white;
            border-color: var(--expired-color);
        }

        .card-header {
            padding: 12px;
            text-align: center;
            position: relative;
        }

        .supplier-card.activo .card-header {
            background: linear-gradient(180deg, var(--primary-light) 0%, white 100%);
        }

        .supplier-card.expirado .card-header {
            background: linear-gradient(180deg, var(--expired-light) 0%, white 100%);
        }

        .supplier-logo {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            color: white;
            box-shadow: var(--shadow-md);
            margin: 0 auto 8px;
        }

        .supplier-card.activo .supplier-logo {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
        }

        .supplier-card.expirado .supplier-logo {
            background: linear-gradient(135deg, var(--expired-dark) 0%, var(--expired-color) 100%);
        }

        .supplier-name {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
            word-break: break-word;
        }

        .supplier-card.activo .supplier-name {
            color: var(--primary-color);
        }

        .supplier-card.expirado .supplier-name {
            color: var(--expired-color);
        }

        .card-body {
            padding: 12px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 6px;
            width: 100%;
            justify-content: center;
        }

        .supplier-card.activo .info-item i {
            color: var(--primary-color);
        }

        .supplier-card.expirado .info-item i {
            color: var(--expired-color);
        }

        .info-label {
            font-size: 10px;
            color: var(--text-light);
            font-weight: 500;
        }

        .info-value {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            box-shadow: var(--shadow-md);
            justify-content: center;
            width: 80px;
            margin: 0 auto;
        }

        .status-pill.activo {
            background-color: var(--success-light);
            color: var(--success-dark);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-pill.activo::before {
            content: "";
            width: 5px;
            height: 5px;
            border-radius: 50%;
            margin-right: 5px;
            background-color: var(--success-color);
        }

        .status-pill.expirado {
            background-color: var(--danger-light);
            color: var(--danger-dark);
            border: 1px solid rgba(243, 39, 39, 0.2);
        }

        .status-pill.expirado::before {
            content: "";
            width: 5px;
            height: 5px;
            border-radius: 50%;
            margin-right: 5px;
            background-color: var(--danger-color);
        }

        .status-pill.pendiente {
            background-color: #fef3c7;
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .status-pill.pendiente::before {
            content: "";
            width: 5px;
            height: 5px;
            border-radius: 50%;
            margin-right: 5px;
            background-color: #f59e0b;
        }

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: var(--radius-md);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-normal);
            border: none;
            box-shadow: var(--shadow-md);
            width: 100%;
            text-align: center;
        }

        .supplier-card.activo .action-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .supplier-card.expirado .action-btn {
            background-color: var(--expired-color);
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .supplier-card.activo .action-btn:hover {
            background-color: var(--primary-dark);
        }

        .supplier-card.expirado .action-btn:hover {
            background-color: var(--expired-dark);
        }

        .modal-body .action-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .modal-body .action-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(2px);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 24px;
            border: none;
            width: 80%;
            max-width: 600px;
            border-radius: var(--radius-lg);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            position: relative;
            animation: slideIn 0.4s ease-out;
        }

        .close-modal-btn {
            position: absolute;
            right: 16px;
            top: 16px;
            color: var(--text-medium);
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            background: var(--bg-light);
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-normal);
        }

        .close-modal-btn:hover {
            color: var(--primary-color);
            background: var(--primary-light);
            transform: rotate(90deg);
        }

        .modal-header {
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-light);
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
            text-align: center;
        }

        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .modal-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            background: var(--bg-light);
            padding: 8px 12px;
            border-radius: var(--radius-md);
            transition: all var(--transition-normal);
        }

        .modal-info:hover {
            background: var(--primary-light);
        }

        .modal-info i {
            color: var(--primary-color);
            font-size: 15px;
        }

        .modal-info span {
            font-weight: 500;
            color: var(--text-dark);
        }

        .details-toggle {
            margin-top: 16px;
            border-top: 1px solid var(--border-light);
        }

        .toggle-details-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border: none;
            background-color: transparent;
            color: var(--text-medium);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-normal);
        }

        .toggle-details-btn:hover {
            color: var(--primary-color);
        }

        .toggle-details-btn i {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .toggle-details-btn.active i {
            transform: rotate(180deg);
        }

        .details-panel {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s cubic-bezier(0, 1, 0, 1);
        }

        .details-panel.active {
            max-height: 1000px;
            transition: max-height 1s ease-in-out;
        }

        .details-content {
            padding: 12px;
            background-color: var(--bg-light);
            border-radius: var(--radius-md);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-item i {
            color: var(--primary-color);
            font-size: 14px;
        }

        .detail-label {
            font-size: 11px;
            color: var(--text-light);
            font-weight: 500;
        }

        .detail-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
        }

        #review-form {
            margin-top: 20px;
            padding: 20px;
            background: white;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            position: relative;
        }

        .close-form-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            font-size: 16px;
            text-decoration: none;
            color: white;
            background-color: var(--primary-color);
            padding: 8px;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-normal);
            box-shadow: var(--shadow-md);
        }

        .close-form-btn:hover {
            background-color: var(--primary-dark);
            transform: scale(1.1);
        }

        .alert {
            padding: 12px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
        }

        .alert-info {
            background-color: #e0f2fe;
            color: #0369a1;
            border-left: 3px solid #0ea5e9;
        }

        .alert-danger {
            background-color: var(--danger-light);
            color: var(--danger-dark);
            border-left: 3px solid var(--danger-color);
        }

        /* Estilos para el formulario y vista previa del PDF */
        .form-pdf-container {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            max-width: 100%;
            min-height: 90vh;
        }

        .form-column {
            flex: 1;
            min-width: 400px;
            max-width: 100%;
            min-height: auto;
            box-sizing: border-box;
            transition: max-width 0.3s ease;
        }

        .form-pdf-container.pdf-visible .form-column {
            max-width: 50%;
        }

        .form-scroll-container {
            max-height: 90vh;
            overflow-y: auto;
            padding-right: 10px;
            box-sizing: border-box;
        }

        .form-scroll-container::-webkit-scrollbar {
            width: 8px;
        }

        .form-scroll-container::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        .form-scroll-container::-webkit-scrollbar-track {
            background: var(--bg-light);
        }

        .pdf-column {
            flex: 1;
            min-width: 400px;
            max-width: 50%;
            min-height: 90vh;
            background: var(--bg-light);
            padding: 20px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-md);
            display: none;
            flex-direction: column;
            overflow-y: auto;
            box-sizing: border-box;
            transition: max-width 0.3s ease;
        }

        .form-pdf-container.pdf-visible .pdf-column {
            display: flex;
        }

        .pdf-column::-webkit-scrollbar {
            width: 8px;
        }

        .pdf-column::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        .pdf-column::-webkit-scrollbar-track {
            background: var(--bg-light);
        }

        .pdf-viewer {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            min-height: 80vh;
        }

        .pdf-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .pdf-viewer h4 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .close-pdf-btn {
            background: var(--danger-color);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all var(--transition-normal);
        }

        .close-pdf-btn:hover {
            background: var(--danger-dark);
            transform: translateY(-2px);
        }

        .pdf-viewer iframe {
            width: 102%;
            height: 106%;
            min-height: 79vh;
            border: none;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            object-fit: contain;
        }

        .filled-form-container {
            background: var(--bg-light);
            padding: 20px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-md);
            width: 100%;
            box-sizing: border-box;
        }

        .filled-form-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 16px;
            text-align: center;
        }

        .filled-section {
            background: white;
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
            transition: background-color 0.3s ease;
        }

        .filled-section.validated {
            background-color: var(--success-light);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .filled-section h4 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pdf-view-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all var(--transition-normal);
        }

        .pdf-view-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .filled-value {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            background: var(--bg-light);
            padding: 8px 12px;
            border-radius: var(--radius-md);
            margin-top: 4px;
        }

        .actividad-chip {
            display: inline-block;
            background: var(--primary-light);
            color: var(--primary-color);
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            margin: 4px;
            font-weight: 500;
        }

        .shareholder-item {
            background: var(--bg-light);
            padding: 8px;
            border-radius: var(--radius-md);
            margin-bottom: 4px;
        }

        .preview-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-normal);
        }

        .preview-btn i {
            font-size: 14px;
        }

        .preview-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Estilos para Socios y Representante Legal */
        .form-section[data-section="socios"] .form-container,
        .form-section[data-section="representante"] .form-container {
            padding: 8px;
            margin-bottom: 0%;
        }

        .form-section[data-section="socios"] .form-column,
        .form-section[data-section="representante"] .form-column {
            padding: 0;
        }

        .shareholders-container {
            margin-top: 8px;
        }

        .form-section[data-section="socios"] .form-header,
        .form-section[data-section="representante"] .section-header {
            margin-bottom: 8px;
        }

        .form-section[data-section="socios"] .subtitle {
            font-size: 12px;
            margin: 4px 0;
        }

        .percentage-summary {
            margin-top: 4px;
        }

        /* Estilos para el carrusel de PDFs */
        .pdf-carousel-container {
            margin-top: 24px;
        }

        .pdf-carousel-container h4 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pdf-carousel {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            padding: 16px 0;
        }

        .form-pdf-container.pdf-visible .pdf-carousel {
            grid-template-columns: repeat(3, 1fr);
        }

        .pdf-card {
            background: var(--success-light);
            border-radius: var(--radius-md);
            padding: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            box-shadow: var(--shadow-md);
            transition: all var(--transition-normal);
            animation: slideUp 0.5s ease-out forwards;
        }

        .pdf-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            background: var(--success-dark);
        }

        .pdf-card:hover .pdf-name,
        .pdf-card:hover .pdf-icon {
            color: white;
        }

        .pdf-card .pdf-icon {
            font-size: 24px;
            color: var(--success-dark);
        }

        .pdf-card .pdf-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--success-dark);
            text-align: center;
            word-break: break-all;
        }

        /* Estilos para los botones de decisión */
        .decision-group {
            display: flex;
            gap: 16px;
            justify-content: center;
            margin-top: 16px;
        }

        .form-input {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            font-size: 14px;
            font-family: var(--font-primary);
            resize: vertical;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px var(--primary-light);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .supplier-pvs {
                grid-template-columns: 1fr;
            }

            .modal-content {
                width: 90%;
            }

            .page-title {
                font-size: 24px;
            }

            .page-subtitle {
                font-size: 12px;
            }

            .tab-btn {
                padding: 8px 12px;
                font-size: 11px;
            }

            .filled-form-title {
                font-size: 20px;
            }

            .filled-section h4,
            .pdf-carousel-container h4 {
                font-size: 16px;
            }

            .filled-value {
                font-size: 13px;
            }

            .pdf-carousel,
            .form-pdf-container.pdf-visible .pdf-carousel {
                grid-template-columns: 1fr;
                padding: 8px 0;
            }

            .pdf-card {
                width: 100%;
                max-width: 300px;
                margin: 0 auto;
            }

            .form-pdf-container {
                flex-direction: column;
                min-height: auto;
            }

            .form-column,
            .pdf-column {
                min-width: 100%;
                max-width: 100%;
                min-height: 70vh;
            }

            .form-pdf-container.pdf-visible .form-column,
            .form-pdf-container.pdf-visible .pdf-column {
                max-width: 100%;
            }

            .pdf-viewer iframe {
                height: 100%;
                max-width: 100%;
            }

            .form-scroll-container {
                max-height: 70vh;
            }

            .decision-group {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener datos pasados desde el controlador
            const supplier = @json($supplier ?? null);
            const pvs = @json($pvs ?? []);

            const supplierName = document.getElementById('supplier-name');
            const supplierPvs = document.getElementById('supplier-pvs');
            const alertContainer = document.getElementById('alert-container');
            const countAll = document.getElementById('count-all');
            const reviewForm = document.getElementById('review-form');
            const reviewModal = document.getElementById('review-modal');
            const successModal = document.getElementById('success-modal');
            const deniedModal = document.getElementById('denied-modal');
            const commentsModal = document.getElementById('comments-modal');
            const modalContent = document.getElementById('modal-content');
            const closeModalBtn = document.querySelector('.close-modal-btn');
            const closeFormBtn = document.querySelector('.close-form-btn');
            const pdfPreview = document.getElementById('pdf-preview');
            const pdfIframe = document.getElementById('pdf-iframe');
            const closePdfBtn = document.querySelector('.close-pdf-btn');
            const formPdfContainer = document.querySelector('.form-pdf-container');
            const decisionButtons = document.querySelector('.decision-buttons');
            const submitCommentsBtn = document.querySelector('.submit-comments-btn');
            const generalComments = document.getElementById('general-comments');

            function renderAlert(message, type = 'info') {
                alertContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
            }

            function closeModal(modal) {
                modal.style.display = 'none';
            }

            function closeForm() {
                reviewForm.style.display = 'none';
                supplierPvs.style.display = 'grid';
                pdfPreview.style.display = 'none';
                formPdfContainer.classList.remove('pdf-visible');
                pdfIframe.src = '';
                decisionButtons.style.display = 'none';
                document.querySelectorAll('.filled-section').forEach(section => {
                    section.classList.remove('validated');
                });
            }

            function closePdf() {
                pdfPreview.style.display = 'none';
                formPdfContainer.classList.remove('pdf-visible');
                pdfIframe.src = '';
                renderAlert('Vista previa del PDF cerrada.', 'info');
                document.querySelectorAll('.filled-section').forEach(section => {
                    section.classList.remove('validated');
                });
            }

            // Close modals
            document.querySelectorAll('.close-modal-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    closeModal(this.closest('.modal'));
                });
            });

            document.querySelector('.close-success-modal').addEventListener('click', function() {
                closeModal(successModal);
                closeForm();
                renderPvs(supplier, pvs);
            });

            document.querySelector('.close-denied-modal').addEventListener('click', function() {
                closeModal(deniedModal);
            });

            document.querySelector('.close-comments-modal').addEventListener('click', function() {
                closeModal(commentsModal);
            });

            window.addEventListener('click', function(event) {
                if (event.target === reviewModal) {
                    closeModal(reviewModal);
                } else if (event.target === successModal) {
                    closeModal(successModal);
                    closeForm();
                    renderPvs(supplier, pvs);
                } else if (event.target === deniedModal) {
                    closeModal(deniedModal);
                } else if (event.target === commentsModal) {
                    closeModal(commentsModal);
                }
            });

            closeFormBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closeForm();
            });

            closePdfBtn.addEventListener('click', closePdf);

            // Event listener para botones de previsualización de PDF en el carrusel
            document.querySelectorAll('.preview-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const pdfUrl = this.dataset.pdf;
                    pdfPreview.style.display = 'block';
                    formPdfContainer.classList.add('pdf-visible');
                    pdfIframe.src = pdfUrl;
                    renderAlert('PDF cargado para revisión.', 'info');
                    checkAllSectionsReviewed();
                });
            });

            // Event listener para botones de vista de PDF en secciones
            document.querySelectorAll('.pdf-view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const pdfUrl = this.dataset.pdf;
                    const section = this.dataset.section;
                    const page = this.dataset.page || 1;

                    pdfPreview.style.display = 'block';
                    formPdfContainer.classList.add('pdf-visible');
                    pdfIframe.src = `${pdfUrl}#page=${page}`;

                    document.querySelectorAll('.filled-section').forEach(section => {
                        section.classList.remove('validated');
                    });
                    const targetSection = document.querySelector(`.filled-section[data-section="${section}"]`);
                    if (targetSection) {
                        targetSection.classList.add('validated');
                        targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }

                    renderAlert(`${section.charAt(0).toUpperCase() + section.slice(1)} validado en página ${page}.`, 'info');
                    checkAllSectionsReviewed();
                });
            });

            // Check if all sections are reviewed to show decision buttons
            function checkAllSectionsReviewed() {
                const sections = document.querySelectorAll('.filled-section:not([data-section="comentarios"]):not([data-section="decision"])');
                const allReviewed = Array.from(sections).every(section => section.classList.contains('validated'));
                if (allReviewed) {
                    decisionButtons.style.display = 'block';
                    decisionButtons.scrollIntoView({ behavior: 'smooth', block: 'end' });
                }
            }

            // Event listener para el botón de aceptar formulario
            document.querySelector('.accept-btn').addEventListener('click', function() {
                closeForm();
                successModal.style.display = 'block';
                renderAlert('Formulario aceptado exitosamente.', 'info');
            });

            // Event listener para el botón de denegar formulario
            document.querySelector('.deny-btn').addEventListener('click', function() {
                deniedModal.style.display = 'block';
                renderAlert('Formulario denegado.', 'danger');
            });

            // Event listener para enviar comentarios
            submitCommentsBtn.addEventListener('click', function() {
                const comments = generalComments.value.trim();
                if (comments) {
                    commentsModal.style.display = 'block';
                    renderAlert('Comentarios registrados exitosamente.', 'info');
                    generalComments.value = ''; // Limpiar el campo
                } else {
                    renderAlert('Por favor, ingrese comentarios antes de enviar.', 'danger');
                }
            });

            function renderPvs(supplier, pvs) {
                supplierPvs.innerHTML = '';
                if (!supplier || pvs.length === 0) {
                    supplierPvs.innerHTML = '<p>No se encontraron trámites asociados al proveedor.</p>';
                    countAll.textContent = '0';
                    return;
                }

                supplierName.textContent = supplier.name;
                countAll.textContent = pvs.length;

                pvs.forEach(pv => {
                    const card = document.createElement('div');
                    card.className = `supplier-card ${pv.status}`;
                    card.innerHTML = `
                        <div class="card-header">
                            <div class="supplier-logo">${supplier.name.substring(0, 2).toUpperCase()}</div>
                            <h3 class="supplier-name">${supplier.name}</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-item status-container">
                                <span class="status-pill ${pv.status}">${pv.status.charAt(0).toUpperCase() + pv.status.slice(1)}</span>
                            </div>
                            <div class="info-item rfc-container">
                                <div class="rfc-content">
                                    <span class="info-label">RFC:</span>
                                    <span class="info-value">${supplier.rfc}</span>
                                </div>
                            </div>
                            <button class="action-btn review-btn" data-pv-id="${pv.pv_id}">
                                <i class="fas fa-clipboard-check"></i> ${pv.status === 'expirado' ? 'Consultar Datos' : 'Iniciar Revisión'}
                            </button>
                        </div>
                    `;
                    supplierPvs.appendChild(card);
                });

                document.querySelectorAll('.review-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const pvId = this.dataset.pvId;
                        const pv = pvs.find(p => p.pv_id === pvId);
                        modalContent.innerHTML = `
                            <div class="modal-header">
                                <h3 class="modal-title">${pv.status === 'expirado' ? 'Consulta de ' : 'Revisión de '}${pv.pv_id}</h3>
                            </div>
                            <div class="modal-body">
                                <div class="modal-info">
                                    <i class="fas fa-id-card"></i>
                                    <span><strong>PV:</strong> ${pv.pv_id}</span>
                                </div>
                                <div class="modal-info">
                                    <i class="fas fa-calendar"></i>
                                    <span><strong>Vigencia:</strong> ${pv.start_date} - ${pv.end_date}</span>
                                </div>
                                <div class="modal-info">
                                    <i class="fas fa-user"></i>
                                    <span><strong>Contacto:</strong> ${supplier.contact_name}</span>
                                </div>
                                <div class="modal-info">
                                    <i class="fas fa-phone"></i>
                                    <span><strong>Teléfono:</strong> ${supplier.phone}</span>
                                </div>
                                <div class="details-toggle">
                                    <button class="toggle-details-btn">
                                        Detalles del PV <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <div class="details-panel">
                                        <div class="details-content">
                                            <div class="detail-item">
                                                <i class="fas fa-calendar-check"></i>
                                                <div>
                                                    <span class="detail-label">Fecha de Registro</span>
                                                    <span class="detail-value">${pv.registration_date}</span>
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-file-alt"></i>
                                                <div>
                                                    <span class="detail-label">Documentos</span>
                                                    <span class="detail-value">${pv.documents_completed}/${pv.documents_total}</span>
                                                </div>
                                            </div>
                                            ${pv.observations ? `
                                                <div class="detail-item">
                                                    <i class="fas fa-comment"></i>
                                                    <div>
                                                        <span class="detail-label">Observaciones</span>
                                                        <span class="detail-value">${pv.observations}</span>
                                                    </div>
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        reviewModal.style.display = 'block';

                        const toggleBtn = modalContent.querySelector('.toggle-details-btn');
                        toggleBtn.addEventListener('click', function() {
                            const panel = this.nextElementSibling;
                            const icon = this.querySelector('i');
                            this.classList.toggle('active');
                            panel.classList.toggle('active');
                        });

                        if (pv.status === 'activo' || pv.status === 'pendiente') {
                            const startReviewBtn = document.createElement('button');
                            startReviewBtn.className = 'action-btn';
                            startReviewBtn.innerHTML = '<i class="fas fa-clipboard-check"></i> Iniciar Revisión';
                            startReviewBtn.addEventListener('click', function() {
                                closeModal(reviewModal);
                                supplierPvs.style.display = 'none';
                                reviewForm.style.display = 'block';
                                pdfPreview.style.display = 'none';
                                formPdfContainer.classList.remove('pdf-visible');
                                pdfIframe.src = '';
                            });
                            modalContent.querySelector('.modal-body').appendChild(startReviewBtn);
                        }
                    });
                });
            }

            document.querySelectorAll('.tab-btn').forEach(tab => {
                tab.addEventListener('click', function() {
                    document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    const tabType = this.dataset.tab;
                    if (tabType === 'formularios') {
                        supplierPvs.style.display = 'none';
                        reviewForm.style.display = 'block';
                        pdfPreview.style.display = 'none';
                        formPdfContainer.classList.remove('pdf-visible');
                        pdfIframe.src = '';
                    } else {
                        supplierPvs.style.display = 'grid';
                        reviewForm.style.display = 'none';
                        pdfPreview.style.display = 'none';
                        formPdfContainer.classList.remove('pdf-visible');
                        pdfIframe.src = '';
                        renderPvs(supplier, pvs);
                    }
                });
            });

            // Renderizar los datos iniciales
            renderPvs(supplier, pvs);
        });
    </script>
@endsection