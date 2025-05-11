import { createModal } from './utils.js';

export async function scrapeSATData(qrUrl) {
    console.log('scrapeSATData called with:', qrUrl); // Debug log
    try {
        const response = await fetch(qrUrl, {
            headers: {
                Accept: 'text/html',
                'User-Agent':
                    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/91.0.4472.124 Safari/537.36',
            },
            timeout: 15000, // Set a timeout of 15 seconds to catch slow responses
        });

        // Check for HTTP errors or server issues
        if (!response.ok) {
            const text = await response.text(); // Get the response body
            if (response.status >= 500 || text.includes('Weblogic Bridge Message') || text.includes('No backend server available')) {
                throw new Error('La página del SAT no está cargando. Por favor, intenta de nuevo más tarde.');
            }
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const html = await response.text();
        // Additional check for Weblogic error in case it's returned with a 200 status
        if (html.includes('Weblogic Bridge Message') || html.includes('No backend server available')) {
            throw new Error('La página del SAT no está cargando. Por favor, intenta de nuevo más tarde.');
        }

        const doc = new DOMParser().parseFromString(html, 'text/html');

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

        const rfcMatch = html.match(/RFC:\s*([A-Z0-9]+)/i);
        if (rfcMatch) data.rfc = rfcMatch[1];

        if (data.rfc.length === 12) {
            data.tipoPersona = 'Moral';
        } else if (data.rfc.length === 13) {
            data.tipoPersona = 'Física';
        } else {
            data.tipoPersona = 'Desconocido';
        }

        doc.querySelectorAll('[data-role="listview"]').forEach((section, index) => {
            const title =
                section.querySelector('[data-role="list-divider"]')?.textContent.trim() ||
                `Section ${index + 1}`;
            if (!section.querySelector('table')) return;

            const sectionData = { sectionNumber: index + 1, sectionName: title, fields: [] };
            section.querySelectorAll('tr[data-ri]').forEach((row) => {
                const [labelCell, valueCell] = row.querySelectorAll('td[role="gridcell"]');
                if (!labelCell || !valueCell) return;

                const label = labelCell.textContent.replace(/:/g, '').trim();
                const value = valueCell.textContent.trim();
                if (!label || !value || value.includes('$(function') || sectionData.fields.some((f) => f.label === label)) return;

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

        data.nombreCompleto = [data.nombre, data.apellidoPaterno, data.apellidoMaterno]
            .filter(Boolean)
            .join(' ');

        data.finalNombre = '';
        if (data.tipoPersona === 'Moral') {
            data.finalNombre = data.razonSocial;
        } else if (data.tipoPersona === 'Física') {
            data.finalNombre = data.nombreCompleto;
            data.razonSocial = data.nombreCompleto;
        }

        console.log('scrapeSATData result:', data); // Debug log
        return data;
    } catch (error) {
        console.error('scrapeSATData error:', error);
        // Handle all fetch-related errors, including Failed to fetch, timeout, or specific SAT errors
        if (
            error.message.includes('timeout') ||
            error.message.includes('Failed to fetch') ||
            error.message.includes('La página del SAT no está cargando') ||
            error.message.includes('NetworkError') ||
            error.name === 'TypeError' // Catches CORS or other fetch errors
        ) {
            throw new Error('La página del SAT no está cargando. Por favor, intenta de nuevo más tarde.');
        }
        throw new Error(`No se pudo obtener los datos del SAT: ${error.message}. Por favor, intenta de nuevo más tarde.`);
    }
}

// Make scrapeSATData globally available
window.scrapeSATData = scrapeSATData;

// Display SAT data in a modal
export function showSATDataModal(satData, qrUrl) {
    console.log('showSATDataModal called with:', { satData, qrUrl }); // Debug log
    if (!satData || !qrUrl) {
        console.error('Invalid arguments in showSATDataModal:', { satData, qrUrl });
        throw new Error('Invalid SAT data or QR URL');
    }

    // Break down the template literal for clarity
    const modalBodyContent = satData.extractedData.length === 0
        ? '<p>No se encontraron datos en la página del SAT.</p>'
        : satData.extractedData
              .map((section, index) => {
                  const rows = section.fields.map(field => {
                      return `<tr><th>${field.label}</th><td>${field.value}</td></tr>`;
                  }).join('');

                  const rfcRow = index === 0 && satData.rfc
                      ? `<tr><th>RFC</th><td>${satData.rfc}</td></tr>`
                      : '';
                  const curpRow = index === 0 && satData.curp && satData.tipoPersona === 'Física'
                      ? `<tr><th>CURP</th><td>${satData.curp}</td></tr>`
                      : '';

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
        console.log('Creating modal with HTML:', modalHtml.substring(0, 100) + '...'); // Debug log
        createModal({ className: 'modal-overlay sat-modal', html: modalHtml });
        console.log('Modal created successfully');
    } catch (error) {
        console.error('Error creating modal:', error);
        throw new Error(`Failed to create modal: ${error.message}. Por favor, intenta de nuevo más tarde.`);
    }
}

// Make showSATDataModal globally available
window.showSATDataModal = showSATDataModal;