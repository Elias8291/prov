import { createModal, createSpinner, showError } from './utils.js';
import { scrapeSATData, showSATDataModal } from './sat-scraper.js';

// Configure PDF.js
window.pdfjsLib.GlobalWorkerOptions.workerSrc =
    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

// Suppress PDF.js warnings
const originalConsoleWarn = console.warn;
console.warn = function (msg) {
    if (msg && typeof msg === 'string' && msg.includes('Indexing all PDF objects')) {
        return;
    }
    originalConsoleWarn.apply(console, arguments);
};

async function extractQRCodeFromPDF(file) {
    try {
        const pdf = await pdfjsLib.getDocument({ data: new Uint8Array(await file.arrayBuffer()) }).promise;
        const data = { name: '', rfc: '', date: '', regimen: '', qrUrl: '', tipo: '', estatus: '' };

        for (let i = 1; i <= Math.min(3, pdf.numPages); i++) {
            const page = await pdf.getPage(i);
            const text = (await page.getTextContent()).items.map((item) => item.str).join(' ');

            const rfcMatch = text.match(/([A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3})/);
            if (rfcMatch) {
                data.rfc = rfcMatch[0];
                data.tipo = data.rfc.length === 12 ? 'Moral' : 'Física';
            }

            const { width, height } = page.getViewport({ scale: 1 });
            const canvas = Object.assign(document.createElement('canvas'), { width, height });
            await page.render({ canvasContext: canvas.getContext('2d'), viewport: page.getViewport({ scale: 1 }) }).promise;
            const qrCode = jsQR(canvas.getContext('2d').getImageData(0, 0, width, height).data, width, height);
            if (qrCode) {
                data.qrUrl = qrCode.data;

                const satUrlPattern = /^https:\/\/siat\.sat\.gob\.mx\/app\/qr\/faces\/pages\/mobile\/validadorqr\.jsf\?.*D1=\d+.*&D2=\d+.*&D3=[^&]+/;
                if (!satUrlPattern.test(data.qrUrl)) {
                    throw new Error('El código QR no pertenece al SAT o tiene un formato inválido.');
                }

                const dateMatch = text.match(/(\d{2}\/\d{2}\/\d{4})/g);
                if (dateMatch) {
                    data.date = dateMatch[dateMatch.length - 1];
                    const [day, month, year] = data.date.split('/');
                    data.estatus =
                        new Date(`${year}-${month}-${day}`) < new Date().setHours(0, 0, 0, 0) ? 'Vencido' : 'Vigente';
                }

                data.name = text.match(/NOMBRE(?:\sDEL\sCONTRIBUYENTE)?:\s*([A-ZÀ-ÚÑ&\s]+)/i)?.[1]?.trim() || '';
                data.regimen = text.match(/RÉGIMEN(?:\sFISCAL)?:\s*([A-ZÀ-ÚÑ\s]+)/i)?.[1]?.trim() || '';
                break;
            }
        }

        if (!data.qrUrl) throw new Error('No se pudo encontrar el código QR en el PDF.');
        if (!data.rfc) console.warn('Advertencia: No se pudo encontrar el RFC en el texto extraído.');

        return data;
    } catch (error) {
        throw new Error(`No se pudo procesar el archivo PDF: ${error.message}`);
    }
}

async function secureExtractedData(pdfData, satData) {
    try {
        const secureData = {
            nombre: pdfData.tipo === 'Moral' ? satData.razonSocial || pdfData.name : satData.nombreCompleto || pdfData.name,
            tipo_persona: pdfData.tipo,
            rfc: pdfData.rfc,
            curp: satData.curp || null,
            cp: satData.cp || '',
            direccion: [
                satData.nombreVialidad?.toUpperCase(),
                satData.numeroExterior,
                satData.numeroInterior ? `INT. ${satData.numeroInterior}` : '',
                satData.colonia ? `COL. ${satData.colonia.toUpperCase()}` : '',
            ]
                .filter(Boolean)
                .join(' ') || '',
        };

        const response = await fetch('/secure-registration-data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(secureData),
        });

        if (!response.ok) {
            if (response.status === 422) {
                const errorData = await response.json();
                throw new Error(`Error de validación: ${Object.values(errorData.errors || {}).flat().join(', ')}`);
            } else {
                throw new Error(`Error del servidor (${response.status}): ${await response.text()}`);
            }
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message || 'Error desconocido al asegurar datos');
        }

        return result.token;
    } catch (error) {
        throw new Error(`Error al asegurar datos: ${error.message}`);
    }
}

function updatePDFDataPreview(pdfData, satData) {
    const isExpired = pdfData.estatus === 'Vencido';

    const documentStatus = document.getElementById('document-status');
    const warningBadge = document.getElementById('warning-badge');
    const pdfDataCard = document.getElementById('pdf-data-card');

    if (documentStatus) documentStatus.textContent = `DOCUMENTO ${isExpired ? 'VENCIDO' : 'VÁLIDO'}`;
    if (warningBadge) warningBadge.style.display = isExpired ? 'inline-flex' : 'none';
    if (pdfDataCard) pdfDataCard.classList.toggle('expired', isExpired);

    const emailInput = document.getElementById('email-input');
    if (emailInput && satData.email) {
        emailInput.value = satData.email.toLowerCase();
    }

    const viewSatDataBtn = document.getElementById('viewSatDataBtn');
    if (viewSatDataBtn) {
        viewSatDataBtn.disabled = !satData || satData.extractedData.length === 0;
        viewSatDataBtn.addEventListener(
            'click',
            async () => {
                const loading = document.getElementById('sat-data-loading');
                if (loading) loading.style.display = 'block';
                viewSatDataBtn.disabled = true;
                try {
                    console.log('Intentando mostrar modal con satData:', satData);
                    showSATDataModal(satData, pdfData.qrUrl);
                } catch (error) {
                    console.error('Error al mostrar modal:', error);
                    showError(`Error al mostrar datos SAT: ${error.message}`);
                } finally {
                    if (loading) loading.style.display = 'none';
                    viewSatDataBtn.disabled = !satData || satData.extractedData.length === 0;
                }
            },
            { once: true }
        );
    }

    if (emailInput) {
        emailInput.addEventListener('blur', () => {
            if (emailInput.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.toLowerCase())) {
                showError('Por favor ingresa una dirección de correo válida.');
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('register-file');
    const pdfDataContainer = document.querySelector('.pdf-data-container');
    const viewExampleBtn = document.getElementById('viewExampleBtnStep1');
    const fileLabel = document.querySelector('.custom-file-upload span');
    const fileUploadContainer = document.querySelector('.custom-file-upload');

    fileInput?.addEventListener('change', async () => {
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            if (file.type !== 'application/pdf') {
                showError('El archivo debe ser un PDF válido.');
                fileInput.value = '';
                fileLabel.textContent = 'Subir archivo PDF';
                fileUploadContainer.classList.remove('file-selected');
                pdfDataContainer.style.display = 'none';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showError('El archivo excede el tamaño máximo de 5MB.');
                fileInput.value = '';
                fileLabel.textContent = 'Subir archivo PDF';
                fileUploadContainer.classList.remove('file-selected');
                pdfDataContainer.style.display = 'none';
                return;
            }

            fileLabel.textContent = file.name;
            const loading = createModal({ html: createSpinner() });
            const minimumDelay = 2000;
            const startTime = Date.now();

            try {
                const pdfData = await extractQRCodeFromPDF(file);
                const satData = await scrapeSATData(pdfData.qrUrl);

                const token = await secureExtractedData(pdfData, satData);
                let tokenInput = document.getElementById('secure_data_token');
                if (!tokenInput) {
                    tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.id = 'secure_data_token';
                    tokenInput.name = 'secure_data_token';
                    document.getElementById('registerForm').appendChild(tokenInput);
                }
                tokenInput.value = token;

                const elapsedTime = Date.now() - startTime;
                const remainingTime = Math.max(0, minimumDelay - elapsedTime);

                setTimeout(() => {
                    fileUploadContainer.classList.add('file-selected');
                    updatePDFDataPreview(pdfData, satData);
                    pdfDataContainer.style.display = 'block';
                    document.body.removeChild(loading);
                }, remainingTime);
            } catch (error) {
                const elapsedTime = Date.now() - startTime;
                const remainingTime = Math.max(0, minimumDelay - elapsedTime);

                setTimeout(() => {
                    showError(error.message);
                    fileInput.value = '';
                    fileLabel.textContent = 'Subir archivo PDF';
                    fileUploadContainer.classList.remove('file-selected');
                    pdfDataContainer.style.display = 'none';
                    document.body.removeChild(loading);
                }, remainingTime);
            }
        } else {
            fileLabel.textContent = 'Subir archivo PDF';
            fileUploadContainer.classList.remove('file-selected');
            pdfDataContainer.style.display = 'none';
        }
    });

    viewExampleBtn?.addEventListener('click', () => {
        window.open('/assets/pdf/ejemplo_sat.pdf', '_blank');
    });

    // Password toggle functionality
    document.querySelectorAll('.password-toggle').forEach((toggle) => {
        toggle.addEventListener('click', function () {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('.password-toggle-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                    <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                    <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                `;
            }
        });
    });
});

window.extractQRCodeFromPDF = extractQRCodeFromPDF;
window.secureExtractedData = secureExtractedData;