import { createModal } from './utils.js';

export async function scrapeSATData(qrUrl) {
    try {
        // Realiza solicitud HTTP a la URL proporcionada
        const response = await fetch(qrUrl, {
            headers: {
                Accept: 'text/html',
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/91.0.4472.124 Safari/537.36',
            },
            timeout: 15000,
        });

        // Verifica errores HTTP o problemas del servidor
        if (!response.ok) {
            const text = await response.text();
            if (response.status >= 500 || text.includes('Weblogic Bridge Message') || text.includes('No backend server available')) {
                throw new Error('La página del SAT no está cargando. Por favor, intenta de nuevo más tarde.');
            }
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const html = await response.text();
        // Verifica errores de Weblogic en respuesta con estado 200
        if (html.includes('Weblogic Bridge Message') || html.includes('No backend server available')) {
            throw new Error('La página del SAT no está cargando. Por favor, intenta de nuevo más tarde.');
        }

        const doc = new DOMParser().parseFromString(html, 'text/html');

        // Estructura inicial para los datos extraídos
        const data = {
            extractedData: [],
            email: '',
            razonSocial: '',
            nombre: '',
            apellidoPaterno: '',
            apellidoMaterno: '',
            rfc: '',
            cp: '',
            colonia: '',
            nombreVialidad: '',
            numeroExterior: '',
            numeroInterior: '',
            tipoPersona: '',
            curp: null,
        };

        // Extrae RFC y determina tipo de persona
        const rfcMatch = html.match(/RFC:\s*([A-Z0-9]+)/i);
        if (rfcMatch) data.rfc = rfcMatch[1];
        data.tipoPersona = data.rfc.length === 12 ? 'Moral' : data.rfc.length === 13 ? 'Física' : 'Desconocido';

        // Procesa secciones de datos
        doc.querySelectorAll('[data-role="listview"]').forEach((section, index) => {
            const title = section.querySelector('[data-role="list-divider"]')?.textContent.trim() || `Section ${index + 1}`;
            if (!section.querySelector('table')) return;

            const sectionData = { sectionNumber: index + 1, sectionName: title, fields: [] };
            section.querySelectorAll('tr[data-ri]').forEach((row) => {
                const [labelCell, valueCell] = row.querySelectorAll('td[role="gridcell"]');
                if (!labelCell || !valueCell) return;

                const label = labelCell.textContent.replace(/:/g, '').trim();
                const value = valueCell.textContent.trim();
                if (!label || !value || value.includes('$(function') || sectionData.fields.some((f) => f.label === label)) return;

                // Asigna valores a campos específicos
                if (/correo|email/i.test(label)) data.email = value;
                if (/denominación|razón social/i.test(label)) data.razonSocial = value;
                if (label.toLowerCase() === 'nombre') data.nombre = value;
                if (/apellido paterno/i.test(label)) data.apellidoPaterno = value;
                if (/apellido materno/i.test(label)) data.apellidoMaterno = value;
                if (/rfc/i.test(label)) data.rfc = value;
                if (/código postal|cp/i.test(label)) data.cp = value;
                if (/colonia/i.test(label)) data.colonia = value;
                if (/nombre de la vialidad|calle|vialidad/i.test(label)) data.nombreVialidad = value;
                if (/número exterior|numero exterior|no exterior/i.test(label)) data.numeroExterior = value;
                if (/número interior|numero interior|no interior/i.test(label)) data.numeroInterior = value;
                if (data.tipoPersona === 'Física' && /curp/i.test(label)) data.curp = value;

                sectionData.fields.push({ label, value });
            });

            if (sectionData.fields.length) data.extractedData.push(sectionData);
        });

        // Genera nombre completo y nombre final
        data.nombreCompleto = [data.nombre, data.apellidoPaterno, data.apellidoMaterno].filter(Boolean).join(' ');
        data.finalNombre = data.tipoPersona === 'Moral' ? data.razonSocial : data.nombreCompleto;
        if (data.tipoPersona === 'Física') data.razonSocial = data.nombreCompleto;

        return data;
    } catch (error) {
        // Maneja errores de red o específicos del SAT
        if (error.message.includes('timeout') || error.message.includes('Failed to fetch') || 
            error.message.includes('La página del SAT no está cargando') || 
            error.message.includes('NetworkError') || error.name === 'TypeError') {
            throw new Error('La página del SAT no está cargando. Por favor, intenta de nuevo más tarde.');
        }
        throw new Error(`No se pudo obtener los datos del SAT: ${error.message}. Por favor, intenta de nuevo más tarde.`);
    }
}

export function showSATDataModal(satData, qrUrl) {
    if (!satData || !qrUrl) throw new Error('Datos del SAT o URL del QR inválidos');

    // Elimina modal existente
    document.querySelector('.modal-overlay.sat-modal')?.remove();

    // Genera contenido del modal
    const modalBodyContent = satData.extractedData.length === 0
        ? '<p>No se encontraron datos en la página del SAT.</p>'
        : satData.extractedData
              .map((section, index) => {
                  const rows = section.fields.map(field => `<tr><th>${field.label}</th><td>${field.value}</td></tr>`).join('');
                  const rfcRow = index === 0 && satData.rfc ? `<tr><th>RFC</th><td>${satData.rfc}</td></tr>` : '';
                  const curpRow = index === 0 && satData.curp && satData.tipoPersona === 'Física' ? `<tr><th>CURP</th><td>${satData.curp}</td></tr>` : '';
                  return `
                      <div class="sat-section">
                          <h4>${section.sectionName}</h4>
                          <div class="table-responsive">
                              <table>
                                  <tbody>
                                      ${rfcRow}
                                      ${curpRow}
                                      ${rows}
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  `;
              })
              .join('');

    // HTML del modal
    const modalHtml = `
        <div class="modal-container">
            <div class="modal-header">
                <h3>Información del SAT</h3>
                <div class="header-actions">
                    <button class="icon-btn link-btn" onclick="window.open('${qrUrl}', '_blank')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                        </svg>
                    </button>
                    <button class="icon-btn close-modal">×</button>
                </div>
            </div>
            <div class="modal-body">
                ${modalBodyContent}
            </div>
            <div class="modal-footer">
                <button class="small-btn outline" id="closeModalBtn">Cerrar</button>
            </div>
        </div>
    `;

    try {
        // Crea modal y agrega eventos
        const modal = createModal({ className: 'modal-overlay sat-modal', html: modalHtml });
        const closeBtn = modal.querySelector('#closeModalBtn');
        if (closeBtn) closeBtn.addEventListener('click', () => modal.remove());
        modal.addEventListener('click', (event) => { if (event.target === modal) modal.remove(); });
    } catch (error) {
        throw new Error(`No se pudo crear el modal: ${error.message}. Por favor, intenta de nuevo más tarde.`);
    }
}

// Funciones disponibles globalmente
window.scrapeSATData = scrapeSATData;
window.showSATDataModal = showSATDataModal;