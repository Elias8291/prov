// Selección de formularios y inputs
const formulario1 = document.getElementById('formulario1');
const formulario2 = document.getElementById('formulario2');
const formulario3 = document.getElementById('formulario3');
const formulario5 = document.getElementById('formulario5');
const inputs1 = formulario1 ? document.querySelectorAll('#formulario1 input, #formulario1 select, #formulario1 textarea') : [];
const inputs2 = formulario2 ? document.querySelectorAll('#formulario2 input, #formulario2 select') : [];
const inputs3 = formulario3 ? document.querySelectorAll('#formulario3 input, #formulario3 select') : [];
const inputs5 = formulario5 ? document.querySelectorAll('#formulario5 input, #formulario5 select') : [];

// Expresiones regulares para los formularios
const expresiones = {
    rfc: /^[A-Z0-9]{12,13}$/,
    tipo_persona: /^(Física|Moral)$/,
    razon_social: /^[A-Za-z\s&.,0-9]{1,100}$/,
    correo_electronico: /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/,
    contacto_telefono: /^\d{10}$/,
    objeto_social: /^.{1,500}$/,
    sectores: /^\d+$/,
    actividad: /^\d+$/,
    contacto_nombre: /^[A-Za-z\s]{1,40}$/,
    contacto_cargo: /^[A-Za-z\s]{1,50}$/,
    contacto_correo: /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/,
    contacto_telefono_2: /^\d{10}$/,
    contacto_web: /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?$/,
    codigo_postal: /^\d{4,5}$/,
    estado: /^[A-Za-z\s]{1,100}$/,
    municipio: /^[A-Za-z\s]{1,100}$/,
    colonia: /.+/,
    calle: /^[A-Za-z0-9\s]{1,100}$/,
    numero_exterior: /^[A-Za-z0-9\/]{1,10}$/,
    numero_interior: /^[A-Za-z0-9]{0,10}$/,
    entre_calle_1: /^[A-Za-z0-9\s]{1,100}$/,
    entre_calle_2: /^[A-Za-z0-9\s]{1,100}$/,
    numero_escritura: /^\d{1,10}(\/\d{4})?$/,
    nombre_notario: /^[A-Za-z\s]{1,100}$/,
    entidad_federativa: /.+/,
    numero_notario: /^\d{1,10}$/,
    numero_registro: /^(?:[A-Za-z]{0,3}\d{6,14}|\d{9,14})$/,
    fecha_constitucion: /.+/,
    fecha_inscripcion: /.+/,
    
    // Formulario 5
    'nombre-apoderado': /^[A-Za-z\s\.]{1,100}$/,
    'numero-escritura': /^\d{1,10}(\/\d{4})?$/,
    'nombre-notario': /^[A-Za-z\s\.]{1,100}$/,
    'numero-notario': /^\d{1,10}$/,
    'entidad-federativa': /.+/,
    'fecha-escritura': /.+/,
    'numero-registro': /^(?:[A-Za-z]{0,3}\d{6,14}|\d{9,14})$/,
    'fecha-inscripcion': /.+/
};

// Mensajes de error
const mensajesError = {
    rfc: 'El RFC debe tener 12-13 caracteres alfanuméricos (solo mayúsculas y números)',
    tipo_persona: 'Debe seleccionar un tipo de persona',
    razon_social: 'La razón social debe contener solo letras, números, espacios y (&,.)',
    correo_electronico: 'El correo electrónico debe tener un formato válido',
    contacto_telefono: 'El teléfono debe tener 10 dígitos numéricos',
    objeto_social: 'El objeto social es requerido y no debe exceder 500 caracteres',
    sectores: 'Debe seleccionar un sector',
    actividad: 'Debe seleccionar al menos una actividad',
    contacto_nombre: 'El nombre debe contener solo letras y espacios (máx. 40 caracteres)',
    contacto_cargo: 'El cargo debe contener solo letras y espacios (máx. 50 caracteres)',
    contacto_correo: 'El correo de contacto debe tener un formato válido',
    contacto_telefono_2: 'El teléfono de contacto debe tener 10 dígitos numéricos',
    contacto_web: 'La página web debe tener un formato válido (ej. https://www.ejemplo.com)',
    constancia_upload: 'El archivo debe ser PDF y no exceder 5MB',
    codigo_postal: 'El código postal debe contener 4 o 5 dígitos numéricos',
    estado: 'El estado debe contener solo letras y espacios, máximo 100 caracteres',
    municipio: 'El municipio debe contener solo letras y espacios, máximo 100 caracteres',
    colonia: 'Debe seleccionar un asentamiento',
    calle: 'La calle debe contener letras, números o espacios, máximo 100 caracteres',
    numero_exterior: 'El número exterior debe contener letras, números o /, entre 1 y 10 caracteres',
    numero_interior: 'El número interior debe contener letras o números, máximo 10 caracteres',
    entre_calle_1: 'Entre calle 1 debe contener letras, números o espacios, máximo 100 caracteres',
    entre_calle_2: 'Entre calle 2 debe contener letras, números o espacios, máximo 100 caracteres',
    numero_escritura: 'Debe contener de 1 a 10 dígitos, opcionalmente seguido de / y un año de 4 dígitos (ej: 1234, 1234/2024)',
    nombre_notario: 'El nombre del notario debe contener solo letras y espacios (máx. 100 caracteres)',
    entidad_federativa: 'Por favor, seleccione una entidad federativa',
    fecha_constitucion: 'Por favor, seleccione una fecha válida (no futura)',
    numero_notario: 'El número de notario debe contener solo números (máx. 10 dígitos)',
    numero_registro: 'Debe contener de 9 a 14 caracteres: 0 a 3 letras iniciales seguidas de 6 a 14 dígitos (ej: 0123456789, FME123456789)',
    fecha_inscripcion: 'Por favor, seleccione una fecha válida (no anterior a la fecha de constitución)',
    
    // Formulario 5
    'nombre-apoderado': 'El nombre solo puede contener letras, puntos y espacios, máximo 100 caracteres.',
    'numero-escritura': 'Debe contener de 1 a 10 dígitos, opcionalmente seguido de / y un año de 4 dígitos (ej: 1234, 1234/2024).',
    'nombre-notario': 'El nombre del notario solo puede contener letras, puntos y espacios, máximo 100 caracteres.',
    'numero-notario': 'El número del notario debe contener solo números, máximo 10 dígitos.',
    'entidad-federativa': 'Por favor, seleccione una entidad federativa.',
    'fecha-escritura': 'Por favor, seleccione una fecha válida (no futura).',
    'fecha-inscripcion': 'Por favor, seleccione una fecha válida (no anterior a la fecha de escritura).'
};

// Estado de los campos
const campos = {
    rfc: false,
    tipo_persona: false,
    razon_social: false,
    correo_electronico: false,
    contacto_telefono: false,
    objeto_social: false,
    sectores: false,
    actividad: false,
    contacto_nombre: false,
    contacto_cargo: false,
    contacto_correo: false,
    contacto_telefono_2: false,
    contacto_web: true,
    constancia_upload: true,
    codigo_postal: false,
    estado: false,
    municipio: false,
    colonia: false,
    calle: false,
    numero_exterior: false,
    numero_interior: true,
    entre_calle_1: false,
    entre_calle_2: false,
    numero_escritura: false,
    nombre_notario: false,
    entidad_federativa: false,
    fecha_constitucion: false,
    numero_notario: false,
    numero_registro: false,
    fecha_inscripcion: false,
    
    // Formulario 5
    'nombre-apoderado': false,
    'numero-escritura': false,
    'nombre-notario': false,
    'numero-notario': false,
    'entidad-federativa': false,
    'fecha-escritura': false,
    'fecha-inscripcion': false
};

// Validar campo individual
const validarCampo = (expresion, input, campo) => {
    if (expresion.test(input.value) && input.value) { // Ensure non-empty
        // Special case for numero_escritura: validate year range
        if (campo === 'numero_escritura' || campo === 'numero-escritura') {
            const match = input.value.match(/\/(\d{4})$/);
            if (match) {
                const year = parseInt(match[1], 10);
                const currentYear = new Date().getFullYear();
                if (year < 1900 || year > currentYear) {
                    mostrarError(campo, mensajesError[campo]);
                    campos[campo] = false;
                    return;
                }
            }
        }
        ocultarError(campo);
        campos[campo] = true;
    } else {
        mostrarError(campo, mensajesError[campo]);
        campos[campo] = false;
    }
};

// Mostrar error en el campo
const mostrarError = (campo, mensaje) => {
    const formGroup = document.getElementById(`formulario__grupo--${campo}`);
    if (!formGroup) return;
    const errorText = formGroup.querySelector('.formulario__input-error');
    formGroup.classList.add('formulario__grupo-incorrecto');
    formGroup.classList.remove('formulario__grupo-correcto');
    if (errorText) {
        errorText.textContent = mensaje;
        errorText.classList.add('formulario__input-error-activo');
    }
    const input = formGroup.querySelector('input, select, textarea');
    if (input) input.classList.add('input-error');
};

// Ocultar error en el campo
const ocultarError = (campo) => {
    const formGroup = document.getElementById(`formulario__grupo--${campo}`);
    if (!formGroup) return;
    const errorText = formGroup.querySelector('.formulario__input-error');
    formGroup.classList.remove('formulario__grupo-incorrecto');
    formGroup.classList.add('formulario__grupo-correcto');
    if (errorText) errorText.classList.remove('formulario__input-error-activo');
    const input = formGroup.querySelector('input, select, textarea');
    if (input) input.classList.remove('input-error');
};

// Validar estado de campo sin input directo
const validarEstadoCampo = (campo, esValido) => {
    if (esValido) {
        ocultarError(campo);
        campos[campo] = true;
    } else {
        mostrarError(campo, mensajesError[campo]);
        campos[campo] = false;
    }
};

// Validar fechas
const validarFechaConstitucion = (input) => {
    const fecha = new Date(input.value);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0); // Normalizar a medianoche
    if (input.value && !isNaN(fecha) && fecha <= hoy) {
        ocultarError('fecha_constitucion');
        campos.fecha_constitucion = true;
    } else {
        mostrarError('fecha_constitucion', mensajesError.fecha_constitucion);
        campos.fecha_constitucion = false;
    }
};

const validarFechaInscripcion = (input) => {
    const fechaInscripcion = new Date(input.value);
    const fechaConstitucionInput = document.getElementById('fecha_constitucion');
    const fechaConstitucion = fechaConstitucionInput ? new Date(fechaConstitucionInput.value) : null;
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0); // Normalizar a medianoche
    if (
        input.value &&
        !isNaN(fechaInscripcion) &&
        fechaInscripcion <= hoy &&
        (!fechaConstitucion || fechaInscripcion >= fechaConstitucion)
    ) {
        ocultarError('fecha_inscripcion');
        campos.fecha_inscripcion = true;
    } else {
        mostrarError('fecha_inscripcion', mensajesError.fecha_inscripcion);
        campos.fecha_inscripcion = false;
    }
};

// Validar fecha de escritura (formulario 5)
const validarFechaEscritura = (input) => {
    const fecha = new Date(input.value);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0); // Normalizar a medianoche
    if (input.value && !isNaN(fecha) && fecha <= hoy) {
        ocultarError('fecha-escritura');
        campos['fecha-escritura'] = true;
    } else {
        mostrarError('fecha-escritura', mensajesError['fecha-escritura']);
        campos['fecha-escritura'] = false;
    }
};

// Validar fecha de inscripción (formulario 5)
const validarFechaInscripcionForm5 = (input) => {
    const fechaInscripcion = new Date(input.value);
    const fechaEscrituraInput = document.getElementById('fecha-escritura');
    const fechaEscritura = fechaEscrituraInput ? new Date(fechaEscrituraInput.value) : null;
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0); // Normalizar a medianoche
    if (
        input.value &&
        !isNaN(fechaInscripcion) &&
        fechaInscripcion <= hoy &&
        (!fechaEscritura || fechaInscripcion >= fechaEscritura)
    ) {
        ocultarError('fecha-inscripcion');
        campos['fecha-inscripcion'] = true;
    } else {
        mostrarError('fecha-inscripcion', mensajesError['fecha-inscripcion']);
        campos['fecha-inscripcion'] = false;
    }
};

// Validar formulario
const validarFormulario = (e) => {
    const input = e.target;
    const name = input.name;

    switch (name) {
        // Formulario 1
        case 'rfc':
            validarCampo(expresiones.rfc, input, 'rfc');
            break;
        case 'tipo_persona':
            validarCampo(expresiones.tipo_persona, input, 'tipo_persona');
            break;
        case 'razon_social':
            validarCampo(expresiones.razon_social, input, 'razon_social');
            break;
        case 'correo_electronico':
            validarCampo(expresiones.correo_electronico, input, 'correo_electronico');
            break;
        case 'contacto_telefono':
            validarCampo(expresiones.contacto_telefono, input, 'contacto_telefono');
            break;
        case 'objeto_social':
            validarCampo(expresiones.objeto_social, input, 'objeto_social');
            break;
        case 'sectores':
            validarCampo(expresiones.sectores, input, 'sectores');
            const actividadesInput = document.getElementById('actividades_seleccionadas_input');
            if (actividadesInput) {
                try {
                    const actividades = JSON.parse(actividadesInput.value || '[]');
                    campos.actividad = actividades.length > 0;
                    validarEstadoCampo('actividad', campos.actividad);
                } catch {
                    campos.actividad = false;
                    validarEstadoCampo('actividad', false);
                }
            }
            break;
        case 'contacto_nombre':
            validarCampo(expresiones.contacto_nombre, input, 'contacto_nombre');
            break;
        case 'contacto_cargo':
            validarCampo(expresiones.contacto_cargo, input, 'contacto_cargo');
            break;
        case 'contacto_correo':
            validarCampo(expresiones.contacto_correo, input, 'contacto_correo');
            break;
        case 'contacto_telefono_2':
            validarCampo(expresiones.contacto_telefono_2, input, 'contacto_telefono_2');
            break;
        case 'contacto_web':
            if (!input.value) {
                ocultarError('contacto_web');
                campos.contacto_web = true;
            } else {
                validarCampo(expresiones.contacto_web, input, 'contacto_web');
            }
            break;
        case 'constancia_upload':
            if (input.files[0]) {
                const file = input.files[0];
                if (file.type === 'application/pdf' && file.size <= 5 * 1024 * 1024) {
                    ocultarError('constancia_upload');
                    campos.constancia_upload = true;
                    document.getElementById('upload-feedback').style.display = 'flex';
                } else {
                    mostrarError('constancia_upload', mensajesError.constancia_upload);
                    campos.constancia_upload = false;
                    document.getElementById('upload-feedback').style.display = 'none';
                }
            } else {
                ocultarError('constancia_upload');
                campos.constancia_upload = true;
                document.getElementById('upload-feedback').style.display = 'none';
            }
            break;
        // Formulario 2
        case 'codigo_postal':
            validarCampo(expresiones.codigo_postal, input, 'codigo_postal');
            break;
        case 'estado':
            validarCampo(expresiones.estado, input, 'estado');
            break;
        case 'municipio':
            validarCampo(expresiones.municipio, input, 'municipio');
            break;
        case 'colonia':
            validarCampo(expresiones.colonia, input, 'colonia');
            break;
        case 'calle':
            validarCampo(expresiones.calle, input, 'calle');
            break;
        case 'numero_exterior':
            validarCampo(expresiones.numero_exterior, input, 'numero_exterior');
            break;
        case 'numero_interior':
            if (!input.value) {
                ocultarError('numero_interior');
                campos.numero_interior = true;
            } else {
                validarCampo(expresiones.numero_interior, input, 'numero_interior');
            }
            break;
        case 'entre_calle_1':
            validarCampo(expresiones.entre_calle_1, input, 'entre_calle_1');
            break;
        case 'entre_calle_2':
            validarCampo(expresiones.entre_calle_2, input, 'entre_calle_2');
            break;
        // Formulario 3
        case 'numero_escritura':
            validarCampo(expresiones.numero_escritura, input, 'numero_escritura');
            break;
        case 'nombre_notario':
            validarCampo(expresiones.nombre_notario, input, 'nombre_notario');
            break;
        case 'entidad_federativa':
            validarCampo(expresiones.entidad_federativa, input, 'entidad_federativa');
            break;
        case 'fecha_constitucion':
            validarFechaConstitucion(input);
            // Revalidar fecha_inscripcion si existe
            const fechaInscripcionInput = document.getElementById('fecha_inscripcion');
            if (fechaInscripcionInput && fechaInscripcionInput.value) {
                validarFechaInscripcion(fechaInscripcionInput);
            }
            break;
        case 'numero_notario':
            validarCampo(expresiones.numero_notario, input, 'numero_notario');
            break;
        case 'numero_registro':
            validarCampo(expresiones.numero_registro, input, 'numero_registro');
            break;
        case 'fecha_inscripcion':
            validarFechaInscripcion(input);
            break;
            
        // Formulario 5
        case 'nombre-apoderado':
            validarCampo(expresiones['nombre-apoderado'], input, 'nombre-apoderado');
            break;
        case 'numero-escritura':
            validarCampo(expresiones['numero-escritura'], input, 'numero-escritura');
            break;
        case 'nombre-notario':
            validarCampo(expresiones['nombre-notario'], input, 'nombre-notario');
            break;
        case 'numero-notario':
            validarCampo(expresiones['numero-notario'], input, 'numero-notario');
            break;
        case 'entidad-federativa':
            validarCampo(expresiones['entidad-federativa'], input, 'entidad-federativa');
            break;
        case 'fecha-escritura':
            validarFechaEscritura(input);
            // Revalidar fecha-inscripcion si existe
            const fechaInscripcionForm5Input = document.getElementById('fecha-inscripcion');
            if (fechaInscripcionForm5Input && fechaInscripcionForm5Input.value) {
                validarFechaInscripcionForm5(fechaInscripcionForm5Input);
            }
            break;
        case 'numero-registro':
            validarCampo(expresiones['numero-registro'], input, 'numero-registro');
            break;
        case 'fecha-inscripcion':
            validarFechaInscripcionForm5(input);
            break;
    }
};

// Validar actividades seleccionadas
const actualizarValidacionActividades = () => {
    const actividadesInput = document.getElementById('actividades_seleccionadas_input');
    if (actividadesInput) {
        try {
            const actividades = JSON.parse(actividadesInput.value || '[]');
            campos.actividad = actividades.length > 0;
            validarEstadoCampo('actividad', campos.actividad);
        } catch {
            campos.actividad = false;
            validarEstadoCampo('actividad', false);
        }
    }
};

// Asignar eventos a los inputs
inputs1.forEach(input => {
    input.addEventListener('keyup', validarFormulario);
    input.addEventListener('blur', validarFormulario);
    if (input.tagName === 'SELECT' || input.tagName === 'TEXTAREA') {
        input.addEventListener('change', validarFormulario);
    }
    if (input.type === 'file') {
        input.addEventListener('change', validarFormulario);
    }
});

inputs2.forEach(input => {
    input.addEventListener('keyup', validarFormulario);
    input.addEventListener('blur', validarFormulario);
    if (input.tagName === 'SELECT') {
        input.addEventListener('change', validarFormulario);
    }
});

inputs3.forEach(input => {
    input.addEventListener('keyup', validarFormulario);
    input.addEventListener('blur', validarFormulario);
    if (input.tagName === 'SELECT' || input.type === 'date') {
        input.addEventListener('change', validarFormulario);
    }
});

// Asignar eventos a los inputs del formulario 5
inputs5.forEach(input => {
    input.addEventListener('keyup', validarFormulario);
    input.addEventListener('blur', validarFormulario);
    if (input.tagName === 'SELECT' || input.type === 'date') {
        input.addEventListener('change', validarFormulario);
    }
});

// Validar al enviar formularios
const validarSubmit = (formulario, camposRequeridos) => {
    formulario.addEventListener('submit', (e) => {
        e.preventDefault();
        let camposValidos = true;

        camposRequeridos.forEach(campo => {
            const elemento = formulario.querySelector(`[name="${campo}"]`);
            if (elemento && !campos[campo]) {
                camposValidos = false;
                mostrarError(campo, mensajesError[campo]);
                const errorElement = document.querySelector('.formulario__grupo-incorrecto');
                if (errorElement && !errorElement.classList.contains('scrolled-to')) {
                    errorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    errorElement.classList.add('scrolled-to');
                }
            }
        });

        if (camposValidos) {
            formulario.submit();
        } else {
            const errorGeneral = document.createElement('div');
            errorGeneral.classList.add('formulario__mensaje-error');
            errorGeneral.textContent = 'Por favor, complete correctamente todos los campos marcados en rojo';
            errorGeneral.style.color = '#e74c3c';
            errorGeneral.style.padding = '10px';
            errorGeneral.style.marginBottom = '15px';
            errorGeneral.style.textAlign = 'center';
            errorGeneral.style.fontWeight = 'bold';
            const submitBtn = formulario.querySelector('#submit-btn');
            if (submitBtn && !formulario.querySelector('.formulario__mensaje-error')) {
                submitBtn.parentNode.insertBefore(errorGeneral, submitBtn);
                setTimeout(() => {
                    const msg = formulario.querySelector('.formulario__mensaje-error');
                    if (msg) msg.remove();
                }, 5000);
            }
        }
    });
};

// Campos requeridos por formulario
if (formulario1) {
    const camposRequeridos1 = ['rfc', 'tipo_persona', 'contacto_telefono', 'sectores', 'contacto_nombre', 'contacto_cargo', 'contacto_correo', 'contacto_telefono_2'];
    if (document.getElementById('formulario__grupo--razon_social')) camposRequeridos1.push('razon_social', 'correo_electronico');
    if (document.getElementById('tipo_persona')?.value === 'Moral' || document.getElementById('formulario__grupo--razon_social')) {
        if (document.getElementById('formulario__grupo--objeto_social')) camposRequeridos1.push('objeto_social');
    }
    validarSubmit(formulario1, camposRequeridos1);
}

if (formulario2) {
    const camposRequeridos2 = ['codigo_postal', 'estado', 'municipio', 'colonia', 'calle', 'numero_exterior', 'entre_calle_1', 'entre_calle_2'];
    validarSubmit(formulario2, camposRequeridos2);
}

if (formulario3) {
    const camposRequeridos3 = [
        'numero_escritura',
        'nombre_notario',
        'entidad_federativa',
        'fecha_constitucion',
        'numero_notario',
        'numero_registro',
        'fecha_inscripcion'
    ];
    validarSubmit(formulario3, camposRequeridos3);
}

// Campos requeridos para formulario 5
if (formulario5) {
    const camposRequeridos5 = [
        'nombre-apoderado',
        'numero-escritura',
        'nombre-notario',
        'numero-notario',
        'entidad-federativa',
        'fecha-escritura',
        'numero-registro',
        'fecha-inscripcion'
    ];
    validarSubmit(formulario5, camposRequeridos5);
}

// Inicializar validación en carga de página
document.addEventListener('DOMContentLoaded', () => {
    // Prevalidar campos con datos previos
    if (formulario5) {
        inputs5.forEach(input => {
            if (input.value !== '') {
                validarFormulario({ target: input });
            }
        });
    }
});