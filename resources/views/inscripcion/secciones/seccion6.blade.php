<form id="formulario6">
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
                          <button class="action-btn more-btn"><i class="fas fa-ellipsis-v"></i></button>
                      </div>
                  </div>

                  <div class="folder-contents">
                      <!-- Constancia de Situación Fiscal -->
                      <div class="file-item formulario__grupo" id="grupo__constancia_situacion_fiscal">
                          <div class="file-icon">
                              <i class="fas fa-receipt"></i>
                          </div>
                          <div class="file-info">
                              <h6>Constancia de Situación Fiscal</h6>
                              <span class="file-type">PDF</span>
                              <span class="file-description">Original, vigente, emitido por el SAT, no mayor a 3 meses</span>
                          </div>
                          <div class="file-upload">
                              <input type="file" id="constancia_situacion_fiscal" name="constancia_situacion_fiscal" class="file-upload-input" accept=".pdf" required>
                              <label for="constancia_situacion_fiscal" class="file-upload-label">Subir</label>
                          </div>
                          <div class="file-status" data-status="pending">
                              <span class="status-icon"><i class="fas fa-clock"></i></span>
                              <span class="status-text">Pendiente</span>
                          </div>
                          <div class="file-preview" style="display: none;">
                              <button class="preview-btn" title="Ver PDF"><i class="fas fa-eye"></i></button>
                          </div>
                          <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).</p>
                      </div>

                      <!-- Identificación Oficial -->
                      <div class="file-item formulario__grupo" id="grupo__identificacion_oficial">
                          <div class="file-icon">
                              <i class="fas fa-id-card"></i>
                          </div>
                          <div class="file-info">
                              <h6>Identificación Oficial</h6>
                              <span class="file-type">PDF</span>
                              <span class="file-description">Original vigente (INE, pasaporte o cédula profesional)</span>
                          </div>
                          <div class="file-upload">
                              <input type="file" id="identificacion_oficial" name="identificacion_oficial" class="file-upload-input" accept=".pdf" required>
                              <label for="identificacion_oficial" class="file-upload-label">Subir</label>
                          </div>
                          <div class="file-status" data-status="pending">
                              <span class="status-icon"><i class="fas fa-clock"></i></span>
                              <span class="status-text">Pendiente</span>
                          </div>
                          <div class="file-preview" style="display: none;">
                              <button class="preview-btn" title="Ver PDF"><i class="fas fa-eye"></i></button>
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
                              <span class="file-description">Original, con giro, experiencia, clientes y recursos</span>
                          </div>
                          <div class="file-upload">
                              <input type="file" id="curriculum" name="curriculum" class="file-upload-input" accept=".pdf" required>
                              <label for="curriculum" class="file-upload-label">Subir</label>
                          </div>
                          <div class="file-status" data-status="pending">
                              <span class="status-icon"><i class="fas fa-clock"></i></span>
                              <span class="status-text">Pendiente</span>
                          </div>
                          <div class="file-preview" style="display: none;">
                              <button class="preview-btn" title="Ver PDF"><i class="fas fa-eye"></i></button>
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
                              <input type="file" id="comprobante_domicilio" name="comprobante_domicilio" class="file-upload-input" accept=".pdf" required>
                              <label for="comprobante_domicilio" class="file-upload-label">Subir</label>
                          </div>
                          <div class="file-status" data-status="pending">
                              <span class="status-icon"><i class="fas fa-clock"></i></span>
                              <span class="status-text">Pendiente</span>
                          </div>
                          <div class="file-preview" style="display: none;">
                              <button class="preview-btn" title="Ver PDF"><i class="fas fa-eye"></i></button>
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
                              <input type="file" id="croquis_fotografias" name="croquis_fotografias" class="file-upload-input" accept=".pdf" required>
                              <label for="croquis_fotografias" class="file-upload-label">Subir</label>
                          </div>
                          <div class="file-status" data-status="pending">
                              <span class="status-icon"><i class="fas fa-clock"></i></span>
                              <span class="status-text">Pendiente</span>
                          </div>
                          <div class="file-preview" style="display: none;">
                              <button class="preview-btn" title="Ver PDF"><i class="fas fa-eye"></i></button>
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
                              <span class="file-description">Original, con identificación del aceptante, si aplica</span>
                          </div>
                          <div class="file-upload">
                              <input type="file" id="carta_poder" name="carta_poder" class="file-upload-input" accept=".pdf">
                              <label for="carta_poder" class="file-upload-label">Subir</label>
                          </div>
                          <div class="file-status" data-status="pending">
                              <span class="status-icon"><i class="fas fa-clock"></i></span>
                              <span class="status-text">Pendiente</span>
                          </div>
                          <div class="file-preview" style="display: none;">
                              <button class="preview-btn" title="Ver PDF"><i class="fas fa-eye"></i></button>
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
                              <span class="file-description">Copia simple, última declaración anual y provisionales</span>
                          </div>
                          <div class="file-upload">
                              <input type="file" id="acuse_recibo" name="acuse_recibo" class="file-upload-input" accept=".pdf" required>
                              <label for="acuse_recibo" class="file-upload-label">Subir</label>
                          </div>
                          <div class="file-status" data-status="pending">
                              <span class="status-icon"><i class="fas fa-clock"></i></span>
                              <span class="status-text">Pendiente</span>
                          </div>
                          <div class="file-preview" style="display: none;">
                              <button class="preview-btn" title="Ver PDF"><i class="fas fa-eye"></i></button>
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
                              <button class="action-btn more-btn"><i class="fas fa-ellipsis-v"></i></button>
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
                                  <input type="file" id="acta_nacimiento" name="acta_nacimiento" class="file-upload-input" accept=".pdf">
                                  <label for="acta_nacimiento" class="file-upload-label">Subir</label>
                              </div>
                              <div class="file-status" data-status="pending">
                                  <span class="status-icon"><i class="fas fa-clock"></i></span>
                                  <span class="status-text">Pendiente</span>
                              </div>
                              <div class="file-preview" style="display: none;">
                                  <button class="preview-btn" title="Ver PDF"><i class="fas fa-eye"></i></button>
                              </div>
                              <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).</p>
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
                                  <input type="file" id="curp" name="curp" class="file-upload-input" accept=".pdf">
                                  <label for="curp" class="file-upload-label">Subir</label>
                              </div>
                              <div class="file-status" data-status="pending">
                                  <span class="status-icon"><i class="fas fa-clock"></i></span>
                                  <span class="status-text">Pendiente</span>
                              </div>
                              <div class="file-preview" style="display: none;">
                                  <button class="preview-btn" title="Ver PDF"><i class="fas fa-eye"></i></button>
                              </div>
                              <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).</p>
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
                              <button class="action-btn more-btn"><i class="fas fa-ellipsis-v"></i></button>
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
                                  <span class="file-description">Copia simple, notariada, inscrita en el Registro Público</span>
                              </div>
                              <div class="file-upload">
                                  <input type="file" id="acta_constitutiva" name="acta_constitutiva" class="file-upload-input" accept=".pdf">
                                  <label for="acta_constitutiva" class="file-upload-label">Subir</label>
                              </div>
                              <div class="file-status" data-status="pending">
                                  <span class="status-icon"><i class="fas fa-clock"></i></span>
                                  <span class="status-text">Pendiente</span>
                              </div>
                              <div class="file-preview" style="display: none;">
                                  <button class="preview-btn" title="Ver PDF"><i class="fas fa-eye"></i></button>
                              </div>
                              <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).</p>
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
                                  <input type="file" id="modificaciones_acta" name="modificaciones_acta" class="file-upload-input" accept=".pdf">
                                  <label for="modificaciones_acta" class="file-upload-label">Subir</label>
                              </div>
                              <div class="file-status" data-status="pending">
                                  <span class="status-icon"><i class="fas fa-clock"></i></span>
                                  <span class="status-text">Pendiente</span>
                              </div>
                              <div class="file-preview" style="display: none;">
                                  <button class="preview-btn" title="Ver PDF"><i class="fas fa-eye"></i></button>
                              </div>
                              <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).</p>
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
                                  <input type="file" id="poder_notariado" name="poder_notariado" class="file-upload-input" accept=".pdf">
                                  <label for="poder_notariado" class="file-upload-label">Subir</label>
                              </div>
                              <div class="file-status" data-status="pending">
                                  <span class="status-icon"><i class="fas fa-clock"></i></span>
                                  <span class="status-text">Pendiente</span>
                              </div>
                              <div class="file-preview" style="display: none;">
                                  <button class="preview-btn" title="Ver PDF"><i class="fas fa-eye"></i></button>
                              </div>
                              <p class="formulario__input-error">Por favor suba un archivo PDF válido (máximo 10 MB).</p>
                          </div>
                      </div>
                  </div>
              @endif
          </div>
      </div>
  </div>
</form>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Selecciona todos los inputs de tipo file
        const fileInputs = document.querySelectorAll('.file-upload-input');

        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                const fileItem = e.target.closest('.file-item');
                const fileStatus = fileItem.querySelector('.file-status');
                const statusIcon = fileStatus.querySelector('.status-icon');
                const statusText = fileStatus.querySelector('.status-text');
                const filePreview = fileItem.querySelector('.file-preview');
                const previewBtn = filePreview.querySelector('.preview-btn');
                const fileUpload = fileItem.querySelector('.file-upload');
                const fileUploadLabel = fileUpload.querySelector('.file-upload-label');

                if (e.target.files.length > 0) {
                    // Cambiar el estado a "Pendiente por revisión"
                    fileStatus.setAttribute('data-status', 'pending-review');
                    statusIcon.innerHTML = '<i class="fas fa-hourglass-half"></i>';
                    statusText.textContent = 'Pendiente por revisión';

                    // Mostrar el botón de vista previa
                    filePreview.style.display = 'block';

                    // Ocultar el botón "Subir" y deshabilitar el input
                    fileUpload.style.display = 'none';
                    input.disabled = true;

                    // Agregar animación
                    fileStatus.classList.add('status-uploaded');
                    fileItem.classList.add('file-uploaded-animation');

                    // Remover la animación después de que termine
                    setTimeout(() => {
                        fileItem.classList.remove('file-uploaded-animation');
                    }, 1000);

                    // Almacenar el archivo y configurar el botón de vista previa
                    const file = e.target.files[0];
                    const fileURL = URL.createObjectURL(file);

                    previewBtn.addEventListener('click', () => {
                        window.open(fileURL, '_blank');
                    });

                    // Limpiar la URL cuando el archivo cambie (aunque no debería ocurrir por el disabled)
                    input.addEventListener('change', () => {
                        URL.revokeObjectURL(fileURL);
                    }, {
                        once: true
                    });
                }
            });
        });
    });
</script>
