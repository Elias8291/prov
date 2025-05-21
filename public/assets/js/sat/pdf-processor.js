import { createModal, createSpinner, showError } from './utils.js';
import { scrapeSATData, showSATDataModal } from './sat-scraper.js';

// Configure PDF.js to suppress warnings
window.pdfjsLib.GlobalWorkerOptions.workerSrc =
    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

// Disable console warning for PDF.js
const originalConsoleWarn = console.warn;
console.warn = function(msg) {
    // Filter out the specific PDF.js warning
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
                // Removed console log that was showing QR URL

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
        throw new Error(`El Archivo no corresponde a una constancia fiscal`);
    }
}
// Función para enviar y asegurar los datos en el servidor
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
        
        console.log("Enviando datos seguros al servidor:", JSON.stringify(secureData));
        
        const response = await fetch('/secure-registration-data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(secureData)
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
        
        console.log("Token recibido:", result.token);
        return result.token;
    } catch (error) {
        console.error('Error al asegurar datos:', error);
        throw error;
    }
}

function enhanceImage(imageData) {
    const { data, width, height } = imageData;
    const enhancedData = new Uint8ClampedArray(data);

    for (let i = 0; i < data.length; i += 4) {
        const avg = (data[i] + data[i + 1] + data[i + 2]) / 3;
        const contrast = avg > 128 ? 255 : 0;
        enhancedData[i] = enhancedData[i + 1] = enhancedData[i + 2] = contrast;
        enhancedData[i + 3] = 255;
    }

    return new ImageData(enhancedData, width, height);
}

// Actualiza la interfaz y asegura datos en el servidor
function updatePDFDataPreview(pdfData, satData) {
    const isExpired = pdfData.estatus === 'Vencido';
    
    // Actualizar estado del documento
    const documentStatus = document.getElementById('document-status');
    const warningBadge = document.getElementById('warning-badge');
    const pdfDataCard = document.getElementById('pdf-data-card');
    
    if (documentStatus) documentStatus.textContent = `DOCUMENTO ${isExpired ? 'VENCIDO' : 'VÁLIDO'}`;
    if (warningBadge) warningBadge.style.display = isExpired ? 'inline-flex' : 'none';
    if (pdfDataCard) pdfDataCard.classList.toggle('expired', isExpired);

    // Pre-llenar el campo de correo si existe
    const emailInput = document.getElementById('email-input');
    if (emailInput && satData.email) {
        emailInput.value = satData.email.toLowerCase();
    }

    // Configurar botón de datos SAT
    const viewSatDataBtn = document.getElementById('viewSatDataBtn');
    if (viewSatDataBtn) {
        viewSatDataBtn.disabled = false;
        viewSatDataBtn.addEventListener('click', async () => {
            const loading = document.getElementById('sat-data-loading');
            if (loading) loading.style.display = 'block';
            viewSatDataBtn.disabled = true;
            try {
                showSATDataModal(satData, pdfData.qrUrl);
            } catch (error) {
                showError(`Error al mostrar datos SAT: ${error.message}`);
            } finally {
                if (loading) loading.style.display = 'none';
                viewSatDataBtn.disabled = false;
            }
        });
    }

    // Validación de correo electrónico
    if (emailInput) {
        emailInput.addEventListener('blur', () => {
            if (emailInput.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.toLowerCase())) {
                showError('Por favor ingresa una dirección de correo válida.');
            }
        });
    }
    
    // IMPORTANTE: Asegurar datos en el servidor y almacenar token
    secureExtractedData(pdfData, satData)
        .then(token => {
            console.log("Token de seguridad generado correctamente");
            
            // Crear o actualizar campo oculto para el token
            let tokenInput = document.getElementById('secure_data_token');
            if (!tokenInput) {
                tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.id = 'secure_data_token';
                tokenInput.name = 'secure_data_token';
                document.getElementById('registerForm').appendChild(tokenInput);
            }
            tokenInput.value = token;
        })
        .catch(error => showError(`Error al asegurar datos: ${error.message}`));
}

window.extractQRCodeFromPDF = extractQRCodeFromPDF;
window.enhanceImage = enhanceImage;
window.secureExtractedData = secureExtractedData;

// Initialize event listeners
document.addEventListener('DOMContentLoaded', () => {
    const step1 = document.getElementById('registerFormStep1');
    const step2 = document.getElementById('registerFormStep2');
    const nextBtn = document.getElementById('nextToStep2Btn');
    const backBtnStep2 = document.getElementById('backFromRegisterStep2Btn');
    const backBtnStep1 = document.getElementById('backFromRegisterStep1Btn');
    const fileInput = document.getElementById('register-file');
    const viewExampleBtn = document.getElementById('viewExampleBtnStep1');
    
    fileInput?.addEventListener('change', () => {
        const fileLabel = document.querySelector('.custom-file-upload span');
        if (fileLabel) {
            if (fileInput.files.length > 0) {
                fileLabel.textContent = fileInput.files[0].name;
                document.querySelector('.custom-file-upload').classList.add('file-selected');
            } else {
                fileLabel.textContent = 'Subir archivo PDF';
                document.querySelector('.custom-file-upload').classList.remove('file-selected');
            }
        }
    });

    nextBtn?.addEventListener('click', async () => {
        const file = fileInput?.files[0];
        
        if (!file) {
            showError('Debe subir un archivo PDF de la Constancia del SAT para continuar.');
            return;
        }
    
        if (file.type !== 'application/pdf') {
            showError('El archivo debe ser un PDF válido.');
            return;
        }
    
        if (file.size > 5 * 1024 * 1024) {
            showError('El archivo excede el tamaño máximo de 5MB.');
            return;
        }
    
        const loading = createModal({ html: createSpinner() });
        const minimumDelay = 2000; 
        const startTime = Date.now();
    
        try {
            const pdfData = await extractQRCodeFromPDF(file);
            const satData = await scrapeSATData(pdfData.qrUrl);
    
            const elapsedTime = Date.now() - startTime;
            const remainingTime = Math.max(0, minimumDelay - elapsedTime);
    
            setTimeout(() => {
                step1.classList.remove('active');
                step2.classList.add('active');
                updatePDFDataPreview(pdfData, satData);
                document.body.removeChild(loading);
            }, remainingTime);
        } catch (error) {
            const elapsedTime = Date.now() - startTime;
            const remainingTime = Math.max(0, minimumDelay - elapsedTime);
    
            setTimeout(() => {
                showError(error.message);
                document.body.removeChild(loading);
            }, remainingTime);
        }
    });
    
    backBtnStep2?.addEventListener('click', () => {
        step2.classList.remove('active');
        step1.classList.add('active');
    });

    backBtnStep1?.addEventListener('click', () => {
        window.history.back();
    });

    viewExampleBtn?.addEventListener('click', () => {
        window.open('/assets/pdf/ejemplo_sat.pdf', '_blank');
    });
});