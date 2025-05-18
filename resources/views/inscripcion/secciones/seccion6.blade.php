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
                            <button type="button" class="action-btn more-btn"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>

                    <div class="folder-contents">
                        @if (empty($documentos['common']))
                            <p>No hay documentos comunes disponibles.</p>
                        @else
                            @foreach ($documentos['common'] as $documento)
                                @php
                                    $fieldName = str_replace(' ', '_', strtolower(preg_replace('/[^A-Za-z0-9\s]/', '', $documento['nombre'])));
                                    $iconClass = match (true) {
                                        stripos($documento['nombre'], 'Identificación') !== false => 'fa-id-card',
                                        stripos($documento['nombre'], 'Domicilio') !== false => 'fa-home',
                                        stripos($documento['nombre'], 'Croquis') !== false => 'fa-map-marked-alt',
                                        stripos($documento['nombre'], 'Carta') !== false => 'fa-file-contract',
                                        stripos($documento['nombre'], 'Acuse') !== false => 'fa-receipt',
                                        stripos($documento['nombre'], 'Situación') !== false => 'fa-file-invoice',
                                        default => 'fa-file-alt',
                                    };
                                @endphp

                                <div class="file-item formulario__grupo" id="grupo__{{ $fieldName }}">
                                    <div class="file-icon">
                                        <i class="fas {{ $iconClass }}"></i>
                                    </div>
                                    <div class="file-info">
                                        <h6>{{ $documento['nombre'] }}</h6>
                                        <span class="file-type">PDF</span>
                                        <span class="file-description">{{ $documento['descripcion'] ?? 'Sin descripción' }}</span>
                                    </div>
                                    <div class="file-upload">
                                        <input type="file" id="{{ $fieldName }}" name="{{ $fieldName }}"
                                               class="file-upload-input" accept=".pdf">
                                        <label for="{{ $fieldName }}" class="file-upload-label">Subir</label>
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
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Documentos específicos basados en el tipo de persona -->
                <div class="document-category">
                    <div class="folder-item {{ $tipoPersona === 'Física' ? 'individual-docs' : 'corporate-docs' }}">
                        <div class="folder-icon">
                            <i class="fas {{ $tipoPersona === 'Física' ? 'fa-user' : 'fa-building' }}"></i>
                        </div>
                        <div class="folder-info">
                            <h5>Documentos Exclusivos para Persona {{ $tipoPersona }}</h5>
                        </div>
                        <div class="folder-actions">
                            <button type="button" class="action-btn more-btn"><i
                                    class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>

                    <div class="folder-contents">
                        @if (empty($documentos['specific']))
                            <p>No hay documentos específicos disponibles para Persona {{ $tipoPersona }}.</p>
                        @else
                            @foreach ($documentos['specific'] as $documento)
                                @php
                                    $fieldName = str_replace(' ', '_', strtolower(preg_replace('/[^A-Za-z0-9\s]/', '', $documento['nombre'])));
                                    $iconClass = match (true) {
                                        stripos($documento['nombre'], 'CURP') !== false => 'fa-id-badge',
                                        stripos($documento['nombre'], 'Nacimiento') !== false => 'fa-certificate',
                                        stripos($documento['nombre'], 'Constitutiva') !== false => 'fa-file-signature',
                                        stripos($documento['nombre'], 'Modificaciones') !== false => 'fa-file-contract',
                                        stripos($documento['nombre'], 'Poder') !== false => 'fa-stamp',
                                        default => 'fa-file-alt',
                                    };
                                @endphp

                                <div class="file-item formulario__grupo" id="grupo__{{ $fieldName }}">
                                    <div class="file-icon">
                                        <i class="fas {{ $iconClass }}"></i>
                                    </div>
                                    <div class="file-info">
                                        <h6>{{ $documento['nombre'] }}</h6>
                                        <span class="file-type">PDF</span>
                                        <span class="file-description">{{ $documento['descripcion'] ?? 'Sin descripción' }}</span>
                                    </div>
                                    <div class="file-upload">
                                        <input type="file" id="{{ $fieldName }}" name="{{ $fieldName }}"
                                               class="file-upload-input" accept=".pdf">
                                        <label for="{{ $fieldName }}" class="file-upload-label">Subir</label>
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
                            @endforeach
                        @endif
                    </div>
                </div>
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