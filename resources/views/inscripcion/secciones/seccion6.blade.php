<style>
    /* Main Button Styling */
    .form-buttons {
        display: flex;
        justify-content: space-between;
        margin: 20px 0;
        gap: 15px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: 500;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        min-width: 120px;
    }

    .btn-primary {
        background-color: #9d2449;
        color: white;
    }

    .btn-primary:hover {
        background-color: #821f3d;
        box-shadow: 0 4px 8px rgba(157, 36, 73, 0.3);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
        box-shadow: 0 2px 4px rgba(108, 117, 125, 0.2);
    }

    .btn-secondary:hover {
        background-color: #5c636a;
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
        transform: translateY(-2px);
    }

    /* Responsive styles */
    @media (max-width: 576px) {
        .form-buttons {
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            width: 100%;
        }
    }

    /* Error message styling */
    .formulario__input-error {
        color: #dc3545;
        font-size: 14px;
        display: none;
    }

    .formulario__input-error-activo {
        display: block;
    }
</style>

<form id="formulario6" action="{{ route('inscripcion.procesar') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div id="section-6" class="form-section">
        <div class="form-container">
            <div class="form-column">
                <!-- Documentos para ambos (Persona Física y Persona Moral) -->
                <div class="document-category">
                    <div class="folder-item shared-docs">
                        <div class="folder-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="folder-info">
                            <h5>Documentos para Ambos (Persona Física y Persona Moral)</h5>
                        </div>
                        <div class="folder-actions">
                            <button type="button" class="action-btn more-btn"><i
                                    class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>

                    <div class="folder-contents">
                        <!-- Constancia de Situación Fiscal -->
                      

                        <!-- Identificación Oficial -->
                        <div class="file-item formulario__grupo" id="grupo__identificacion_oficial">
                            <div class="file-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="file-info">
                                <h6>Identificación Oficial</h6>
                                <span class="file-type">PDF</span>
                                <span class="file-description">Original vigente (INE, pasaporte o cédula
                                    profesional)</span>
                            </div>
                            <div class="file-upload">
                                <input type="file" id="identificacion_oficial" name="identificacion_oficial"
                                    class="file-upload-input" accept=".pdf" >
                                <label for="identificacion_oficial" class="file-upload-label">Subir</label>
                            </div>
                            <div class="file-status" data-status="pending">
                                <span class="status-icon"><i class="fas fa-clock"></i></span>
                                <span class="status-text">Pendiente</span>
                            </div>
                            <div class="file-preview" style="display: none;">
                                <button type="button" class="preview-btn" title="Ver PDF"><i
                                        class="fas fa-eye"></i></button>
                            </div>
                            <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).</p>
                        </div>

                        <!-- Curriculum Actualizado -->
                        <div class="file-item formulario__grupo" id="grupo__curriculum">
                            <div class="file-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="file-info">
                                <h6>Curriculum Actualizado</h6>
                                <span class="file-type">PDF</span>
                                <span class="file-description">Original, con giro, experiencia, clientes y
                                    recursos</span>
                            </div>
                            <div class="file-upload">
                                <input type="file" id="curriculum" name="curriculum" class="file-upload-input"
                                    accept=".pdf" >
                                <label for="curriculum" class="file-upload-label">Subir</label>
                            </div>
                            <div class="file-status" data-status="pending">
                                <span class="status-icon"><i class="fas fa-clock"></i></span>
                                <span class="status-text">Pendiente</span>
                            </div>
                            <div class="file-preview" style="display: none;">
                                <button type="button" class="preview-btn" title="Ver PDF"><i
                                        class="fas fa-eye"></i></button>
                            </div>
                            <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).</p>
                        </div>

                        <!-- Comprobante de Domicilio -->
                        <div class="file-item formulario__grupo" id="grupo__comprobante_domicilio">
                            <div class="file-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="file-info">
                                <h6>Comprobante de Domicilio</h6>
                                <span class="file-type">PDF</span>
                                <span class="file-description">Copia simple, no mayor a 3 meses</span>
                            </div>
                            <div class="file-upload">
                                <input type="file" id="comprobante_domicilio" name="comprobante_domicilio"
                                    class="file-upload-input" accept=".pdf" >
                                <label for="comprobante_domicilio" class="file-upload-label">Subir</label>
                            </div>
                            <div class="file-status" data-status="pending">
                                <span class="status-icon"><i class="fas fa-clock"></i></span>
                                <span class="status-text">Pendiente</span>
                            </div>
                            <div class="file-preview" style="display: none;">
                                <button type="button" class="preview-btn" title="Ver PDF"><i
                                        class="fas fa-eye"></i></button>
                            </div>
                            <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).</p>
                        </div>

                        <!-- Croquis de Localización y Fotografías -->
                        <div class="file-item formulario__grupo" id="grupo__croquis_fotografias">
                            <div class="file-icon">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div class="file-info">
                                <h6>Croquis de Localización y Fotografías</h6>
                                <span class="file-type">PDF</span>
                                <span class="file-description">Original, del domicilio del proveedor</span>
                            </div>
                            <div class="file-upload">
                                <input type="file" id="croquis_fotografias" name="croquis_fotografias"
                                    class="file-upload-input" accept=".pdf">
                                <label for="croquis_fotografias" class="file-upload-label">Subir</label>
                            </div>
                            <div class="file-status" data-status="pending">
                                <span class="status-icon"><i class="fas fa-clock"></i></span>
                                <span class="status-text">Pendiente</span>
                            </div>
                            <div class="file-preview" style="display: none;">
                                <button type="button" class="preview-btn" title="Ver PDF"><i
                                        class="fas fa-eye"></i></button>
                            </div>
                            <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).</p>
                        </div>

                        <!-- Carta Poder Simple -->
                        <div class="file-item formulario__grupo" id="grupo__carta_poder">
                            <div class="file-icon">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <div class="file-info">
                                <h6>Carta Poder Simple</h6>
                                <span class="file-type">PDF</span>
                                <span class="file-description">Original, con identificación del aceptante, si
                                    aplica</span>
                            </div>
                            <div class="file-upload">
                                <input type="file" id="carta_poder" name="carta_poder" class="file-upload-input"
                                    accept=".pdf">
                                <label for="carta_poder" class="file-upload-label">Subir</label>
                            </div>
                            <div class="file-status" data-status="pending">
                                <span class="status-icon"><i class="fas fa-clock"></i></span>
                                <span class="status-text">Pendiente</span>
                            </div>
                            <div class="file-preview" style="display: none;">
                                <button type="button" class="preview-btn" title="Ver PDF"><i
                                        class="fas fa-eye"></i></button>
                            </div>
                            <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).</p>
                        </div>

                        <!-- Acuse de Recibo -->
                        <div class="file-item formulario__grupo" id="grupo__acuse_recibo">
                            <div class="file-icon">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div class="file-info">
                                <h6>Acuse de Recibo</h6>
                                <span class="file-type">PDF</span>
                                <span class="file-description">Copia simple, última declaración anual y
                                    provisionales</span>
                            </div>
                            <div class="file-upload">
                                <input type="file" id="acuse_recibo" name="acuse_recibo"
                                    class="file-upload-input" accept=".pdf">
                                <label for="acuse_recibo" class="file-upload-label">Subir</label>
                            </div>
                            <div class="file-status" data-status="pending">
                                <span class="status-icon"><i class="fas fa-clock"></i></span>
                                <span class="status-text">Pendiente</span>
                            </div>
                            <div class="file-preview" style="display: none;">
                                <button type="button" class="preview-btn" title="Ver PDF"><i
                                        class="fas fa-eye"></i></button>
                            </div>
                            <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).</p>
                        </div>
                    </div>
                </div>

                @if ($tipoPersona === 'Física')
                    <div class="document-category">
                        <div class="folder-item individual-docs">
                            <div class="folder-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="folder-info">
                                <h5>Documentos Exclusivos para Persona Física</h5>
                            </div>
                            <div class="folder-actions">
                                <button type="button" class="action-btn more-btn"><i
                                        class="fas fa-ellipsis-v"></i></button>
                            </div>
                        </div>

                        <div class="folder-contents">
                            <!-- Acta de Nacimiento -->
                            <div class="file-item formulario__grupo" id="grupo__acta_nacimiento">
                                <div class="file-icon">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <div class="file-info">
                                    <h6>Acta de Nacimiento</h6>
                                    <span class="file-type">PDF</span>
                                    <span class="file-description">Original, no mayor a 3 meses</span>
                                </div>
                                <div class="file-upload">
                                    <input type="file" id="acta_nacimiento" name="acta_nacimiento"
                                        class="file-upload-input" accept=".pdf">
                                    <label for="acta_nacimiento" class="file-upload-label">Subir</label>
                                </div>
                                <div class="file-status" data-status="pending">
                                    <span class="status-icon"><i class="fas fa-clock"></i></span>
                                    <span class="status-text">Pendiente</span>
                                </div>
                                <div class="file-preview" style="display: none;">
                                    <button type="button" class="preview-btn" title="Ver PDF"><i
                                            class="fas fa-eye"></i></button>
                                </div>
                                <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).
                                </p>
                            </div>

                            <!-- CURP -->
                            <div class="file-item formulario__grupo" id="grupo__curp">
                                <div class="file-icon">
                                    <i class="fas fa-id-badge"></i>
                                </div>
                                <div class="file-info">
                                    <h6>CURP</h6>
                                    <span class="file-type">PDF</span>
                                    <span class="file-description">Copia simple, formato actualizado</span>
                                </div>
                                <div class="file-upload">
                                    <input type="file" id="curp" name="curp" class="file-upload-input"
                                        accept=".pdf">
                                    <label for="curp" class="file-upload-label">Subir</label>
                                </div>
                                <div class="file-status" data-status="pending">
                                    <span class="status-icon"><i class="fas fa-clock"></i></span>
                                    <span class="status-text">Pendiente</span>
                                </div>
                                <div class="file-preview" style="display: none;">
                                    <button type="button" class="preview-btn" title="Ver PDF"><i
                                            class="fas fa-eye"></i></button>
                                </div>
                                <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($tipoPersona === 'Moral')
                    <div class="document-category">
                        <div class="folder-item corporate-docs">
                            <div class="folder-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="folder-info">
                                <h5>Documentos Exclusivos para Persona Moral</h5>
                            </div>
                            <div class="folder-actions">
                                <button type="button" class="action-btn more-btn"><i
                                        class="fas fa-ellipsis-v"></i></button>
                            </div>
                        </div>

                        <div class="folder-contents">
                            <!-- Acta Constitutiva -->
                            <div class="file-item formulario__grupo" id="grupo__acta_constitutiva">
                                <div class="file-icon">
                                    <i class="fas fa-file-signature"></i>
                                </div>
                                <div class="file-info">
                                    <h6>Acta Constitutiva</h6>
                                    <span class="file-type">PDF</span>
                                    <span class="file-description">Copia simple, notariada, inscrita en el Registro
                                        Público</span>
                                </div>
                                <div class="file-upload">
                                    <input type="file" id="acta_constitutiva" name="acta_constitutiva"
                                        class="file-upload-input" accept=".pdf">
                                    <label for="acta_constitutiva" class="file-upload-label">Subir</label>
                                </div>
                                <div class="file-status" data-status="pending">
                                    <span class="status-icon"><i class="fas fa-clock"></i></span>
                                    <span class="status-text">Pendiente</span>
                                </div>
                                <div class="file-preview" style="display: none;">
                                    <button type="button" class="preview-btn" title="Ver PDF"><i
                                            class="fas fa-eye"></i></button>
                                </div>
                                <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).
                                </p>
                            </div>

                            <!-- Modificaciones al Acta -->
                            <div class="file-item formulario__grupo" id="grupo__modificaciones_acta">
                                <div class="file-icon">
                                    <i class="fas fa-file-contract"></i>
                                </div>
                                <div class="file-info">
                                    <h6>Modificaciones al Acta</h6>
                                    <span class="file-type">PDF</span>
                                    <span class="file-description">Copia simple, si aplica</span>
                                </div>
                                <div class="file-upload">
                                    <input type="file" id="modificaciones_acta" name="modificaciones_acta"
                                        class="file-upload-input" accept=".pdf">
                                    <label for="modificaciones_acta" class="file-upload-label">Subir</label>
                                </div>
                                <div class="file-status" data-status="pending">
                                    <span class="status-icon"><i class="fas fa-clock"></i></span>
                                    <span class="status-text">Pendiente</span>
                                </div>
                                <div class="file-preview" style="display: none;">
                                    <button type="button" class="preview-btn" title="Ver PDF"><i
                                            class="fas fa-eye"></i></button>
                                </div>
                                <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).
                                </p>
                            </div>

                            <!-- Poder Notariado -->
                            <div class="file-item formulario__grupo" id="grupo__poder_notariado">
                                <div class="file-icon">
                                    <i class="fas fa-stamp"></i>
                                </div>
                                <div class="file-info">
                                    <h6>Poder Notariado</h6>
                                    <span class="file-type">PDF</span>
                                    <span class="file-description">Copia simple, para actos de administración</span>
                                </div>
                                <div class="file-upload">
                                    <input type="file" id="poder_notariado" name="poder_notariado"
                                        class="file-upload-input" accept=".pdf">
                                    <label for="poder_notariado" class="file-upload-label">Subir</label>
                                </div>
                                <div class="file-status" data-status="pending">
                                    <span class="status-icon"><i class="fas fa-clock"></i></span>
                                    <span class="status-text">Pendiente</span>
                                </div>
                                <div class="file-preview" style="display: none;">
                                    <button type="button" class="preview-btn" title="Ver PDF"><i
                                            class="fas fa-eye"></i></button>
                                </div>
                                <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="form-buttons">
        <button type="button" class="btn btn-secondary" onclick="window.history.back();">Anterior</button>
        <button type="submit" class="btn btn-primary" id="submitForm">Siguiente</button>
    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fileInputs = document.querySelectorAll('.file-upload-input');
    const form = document.getElementById('formulario6');
    const submitButton = document.getElementById('submitForm');

    // Handle file input changes
    fileInputs.forEach(input => {
        input.addEventListener('change', (e) => {
            const fileItem = e.target.closest('.file-item');
            const fileStatus = fileItem.querySelector('.file-status');
            const statusIcon = fileStatus.querySelector('.status-icon');
            const statusText = fileStatus.querySelector('.status-text');
            const filePreview = fileItem.querySelector('.file-preview');
            const previewBtn = filePreview.querySelector('.preview-btn');
            const fileUpload = fileItem.querySelector('.file-upload');
            const errorMessage = fileItem.querySelector('.formulario__input-error');

            // Reset error message
            errorMessage.classList.remove('formulario__input-error-activo');

            if (e.target.files.length > 0) {
                const file = e.target.files[0];

                // Client-side validation
                if (file.type !== 'application/pdf') {
                    errorMessage.textContent = 'Por favor suba un archivo PDF válido.';
                    errorMessage.classList.add('formulario__input-error-activo');
                    e.target.value = '';
                    return;
                }

                if (file.size > 10 * 1024 * 1024) {
                    errorMessage.textContent = 'El archivo no debe exceder los 10 MB.';
                    errorMessage.classList.add('formulario__input-error-activo');
                    e.target.value = '';
                    return;
                }

                // Update status to "Pendiente por revisión"
                fileStatus.setAttribute('data-status', 'pending-review');
                statusIcon.innerHTML = '<i class="fas fa-hourglass-half"></i>';
                statusText.textContent = 'Pendiente por revisión';

                // Show preview button
                filePreview.style.display = 'block';

                // Hide upload button and disable input
                fileUpload.style.display = 'none';
                input.disabled = true;

                // Add animation
                fileStatus.classList.add('status-uploaded');
                fileItem.classList.add('file-uploaded-animation');

                // Remove animation after completion
                setTimeout(() => {
                    fileItem.classList.remove('file-uploaded-animation');
                }, 1000);

                // Store file and configure preview
                const fileURL = URL.createObjectURL(file);
                previewBtn.addEventListener('click', () => {
                    window.open(fileURL, '_blank');
                });

                // Clean up URL when file changes
                input.addEventListener('change', () => {
                    URL.revokeObjectURL(fileURL);
                }, { once: true });
            }
        });
    });

    // Form submission handler
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Disable submit button to prevent multiple submissions
        submitButton.disabled = true;
        submitButton.textContent = 'Enviando...';

        // Clear previous error messages
        document.querySelectorAll('.formulario__input-error').forEach(error => {
            error.classList.remove('formulario__input-error-activo');
        });

        // Create a new FormData object and only include file inputs and CSRF token
        const formData = new FormData();
        fileInputs.forEach(input => {
            if (input.files.length > 0) {
                formData.append(input.name, input.files[0]);
            }
        });
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('seccion', '6'); // Explicitly set section

        // Log form data for debugging
        console.log('Contenido de FormData:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value instanceof File ? value.name : value}`);
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                },
            });

            const result = await response.json();

            if (response.ok) {
                window.location.href = '{{ route('inscripcion.formulario') }}';
            } else {
                if (result.errors) {
                    Object.keys(result.errors).forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            const fileItem = input.closest('.file-item');
                            const errorMessage = fileItem.querySelector('.formulario__input-error');
                            errorMessage.textContent = result.errors[field][0];
                            errorMessage.classList.add('formulario__input-error-activo');
                        }
                    });
                } else {
                    alert('Error al enviar el formulario: ' + (result.message || 'Intente de nuevo.'));
                }
            }
        } catch (error) {
            console.error('Error al enviar el formulario:', error);
            alert('Ocurrió un error al enviar el formulario. Por favor, intenta de nuevo.');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Siguiente';
        }
    });
});
</script>