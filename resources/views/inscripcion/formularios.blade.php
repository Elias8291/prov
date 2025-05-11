@extends('dashboard')

@section('title', 'Inscripción al Padrón de Proveedores - Proveedores de Oaxaca')
<link rel="stylesheet" href="{{ asset('assets/css/formularios.css') }}">
<script src="{{ asset('assets/js/formulario_validaciones.js') }}"></script>

@section('content')
    <div class="form-background-container">
        <div class="inner-form-container">
            <div class="progress-container">
                <div class="progress-info">
                    <div class="progress-status">
                        <span class="progress-percent" id="progress-percent">0%</span>
                        <span class="progress-text">Completado</span>
                    </div>
                    <span class="progress-text persona-type-text">
                        Formulario para persona 
                        <span class="persona-type-value" id="persona-type-value">
                            @if (Auth::user()->hasRole('revisor'))
                                Pendiente
                            @else
                                {{ Auth::user()->solicitante->tipo_persona ?? 'No definido' }}
                            @endif
                        </span>
                    </span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
            </div>
            <div class="progress-tracker" id="progressTracker">
                <!-- Inicialmente solo mostramos la Sección 1 -->
                <div class="seccion" data-seccion="1">
                    <div class="seccion-numero">01</div>
                    <div class="seccion-titulo">Datos Generales</div>
                </div>
            </div>
            <!-- Render solo la Sección 1 inicialmente -->
            <div id="seccion1" class="form-seccion" style="display: block;">
                <form id="formulario1">
                    @include('inscripcion.secciones.seccion1')
                </form>
            </div>
            <!-- Las demás secciones se renderizarán dinámicamente -->
            <div id="seccion2" class="form-seccion" style="display: none;"></div>
            <div id="seccion3" class="form-seccion" style="display: none;"></div>
            <div id="seccion4" class="form-seccion" style="display: none;"></div>
            <div id="seccion5" class="form-seccion" style="display: none;"></div>
            <div id="seccion6" class="form-seccion" style="display: none;"></div>
            <div id="seccion7" class="form-seccion" style="display: none;"></div>
            <div class="navigation-buttons">
                <button type="button" id="btnAnterior" style="display: none;">Anterior</button>
                <span id="progress-small-text" class="progress-small-text">Sección 1 de 1</span>
                <button type="submit" id="btnSiguiente" form="formulario1" disabled>Siguiente</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const progressTracker = document.getElementById('progressTracker');
            const progressFill = document.getElementById('progress-fill');
            const progressPercent = document.getElementById('progress-percent');
            const personaTypeValue = document.getElementById('persona-type-value');
            const tipoPersonaSelect = document.getElementById('tipo_persona');
            const btnSiguiente = document.getElementById('btnSiguiente');
            const btnAnterior = document.getElementById('btnAnterior');
            const isRevisor = @json(Auth::user()->hasRole('revisor'));
            let seccionActual = 1;
            let secciones = [1]; // Inicialmente solo Sección 1
            let totalSecciones = secciones.length;
            let tipoPersona = isRevisor ? null : @json(Auth::user()->solicitante->tipo_persona ?? null);
            let isNavigating = false;

            const seccionesFisica = [1, 2, 6, 7];
            const seccionesMoral = [1, 2, 3, 4, 5, 6, 7];
            const titulosSecciones = {
                1: 'Datos Generales',
                2: 'Domicilio',
                3: 'Datos de Constitución',
                4: 'Accionistas',
                5: 'Apoderado Legal',
                6: 'Documentos',
                7: 'Final'
            };

            // Excluir la sección 7 de la barra de progreso
            const seccionesSinProgreso = [7];

            function updateProgressTracker() {
                if (!progressTracker) {
                    console.error('Elemento progressTracker no encontrado');
                    return;
                }
                progressTracker.innerHTML = '';
                secciones.forEach((seccion, index) => {
                    if (seccionesSinProgreso.includes(seccion)) {
                        return; // Saltar secciones que no deben aparecer en la barra de progreso
                    }

                    const div = document.createElement('div');
                    div.classList.add('seccion');
                    div.setAttribute('data-seccion', index + 1);
                    div.innerHTML = `
                        <div class="seccion-numero">${String(index + 1).padStart(2, '0')}</div>
                        <div class="seccion-titulo">${titulosSecciones[seccion]}</div>
                    `;
                    if (index + 1 < seccionActual) {
                        div.classList.add('completed');
                    } else if (index + 1 === seccionActual) {
                        div.classList.add('active');
                    }
                    progressTracker.appendChild(div);
                    div.addEventListener('click', function() {
                        const seccionNum = parseInt(this.getAttribute('data-seccion'));
                        if (seccionNum <= seccionActual) {
                            seccionActual = seccionNum;
                            actualizarProgreso();
                            scrollToTop();
                        }
                    });
                });
            }

            function actualizarProgreso() {
                totalSecciones = secciones.length;
                const seccionesVisibles = secciones.filter(seccion => !seccionesSinProgreso.includes(seccion));
                const porcentaje = seccionesVisibles.length <= 1 ? 0 : ((seccionActual - 1) / (seccionesVisibles.length - 1)) * 100;

                if (progressFill) {
                    progressFill.style.width = porcentaje + '%';
                }
                if (progressPercent) {
                    progressPercent.textContent = Math.round(porcentaje) + '%';
                }

                // Actualizar el texto y la mini barra de progreso de progress-small-text
                const progressSmallText = document.getElementById('progress-small-text');
                if (progressSmallText) {
                    progressSmallText.textContent = `Sección ${seccionActual} de ${seccionesVisibles.length}`;
                    progressSmallText.setAttribute('data-section', seccionActual);
                }

                // Mostrar u ocultar secciones
                for (let i = 1; i <= 7; i++) {
                    const seccionElement = document.getElementById(`seccion${i}`);
                    if (seccionElement) {
                        seccionElement.style.display = (secciones[seccionActual - 1] === i) ? 'block' : 'none';
                    }
                }

                if (btnAnterior) {
                    btnAnterior.style.display = seccionActual === 1 ? 'none' : 'block';
                }
                if (btnSiguiente) {
                    btnSiguiente.textContent = seccionActual === totalSecciones ? 'Finalizar' : 'Siguiente';
                    btnSiguiente.setAttribute('form', `formulario${secciones[seccionActual - 1]}`);
                    btnSiguiente.disabled = isRevisor && !tipoPersona;
                }

                updateProgressTracker();
            }

            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            function updateSectionsByTipoPersona(tipo) {
                tipoPersona = tipo;
                secciones = tipo === 'Física' ? seccionesFisica : seccionesMoral;
                totalSecciones = secciones.length;
                if (personaTypeValue) {
                    personaTypeValue.textContent = tipo;
                } else {
                    console.warn('Elemento persona-type-value no encontrado');
                }

                // Cargar dinámicamente las secciones necesarias
                secciones.forEach(seccion => {
                    const seccionElement = document.getElementById(`seccion${seccion}`);
                    if (seccionElement && seccionElement.innerHTML.trim() === '') {
                        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfTokenMeta) {
                            console.error('Metaetiqueta CSRF no encontrada. Asegúrese de incluir @csrf en el layout.');
                            return;
                        }
                        fetch(`/inscripcion/secciones/seccion${seccion}`, {
                            headers: {
                                'X-CSRF-TOKEN': csrfTokenMeta.getAttribute('content')
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            seccionElement.innerHTML = `
                                <form id="formulario${seccion}">
                                    ${html}
                                </form>
                            `;
                        })
                        .catch(error => {
                            console.error(`Error al cargar la sección ${seccion}:`, error);
                        });
                    }
                });

                if (!secciones.includes(secciones[seccionActual - 1])) {
                    seccionActual = 1;
                }
                updateProgressTracker();
                actualizarProgreso();
                if (btnSiguiente) {
                    btnSiguiente.disabled = false;
                }
            }

            if (isRevisor && tipoPersonaSelect) {
                tipoPersonaSelect.addEventListener('change', function() {
                    const selectedTipo = this.value;
                    if (selectedTipo === 'Física' || selectedTipo === 'Moral') {
                        updateSectionsByTipoPersona(selectedTipo);
                        // Actualizar inmediatamente el texto de progreso
                        const seccionesVisibles = selectedTipo === 'Física' ? seccionesFisica.filter(s => !seccionesSinProgreso.includes(s)) : seccionesMoral.filter(s => !seccionesSinProgreso.includes(s));
                        const progressSmallText = document.getElementById('progress-small-text');
                        if (progressSmallText) {
                            progressSmallText.textContent = `Sección ${seccionActual} de ${seccionesVisibles.length}`;
                        }
                    } else {
                        if (personaTypeValue) {
                            personaTypeValue.textContent = 'Pendiente';
                        } else {
                            console.warn('Elemento persona-type-value no encontrado');
                        }
                        secciones = [1];
                        totalSecciones = 1;
                        seccionActual = 1;
                        updateProgressTracker();
                        actualizarProgreso();
                        if (btnSiguiente) {
                            btnSiguiente.disabled = true;
                        }
                        const progressSmallText = document.getElementById('progress-small-text');
                        if (progressSmallText) {
                            progressSmallText.textContent = `Sección ${seccionActual} de 1`;
                        }
                    }
                });
            }

            window.formNavigation = {
                goToNextSection: function() {
                    if (seccionActual < totalSecciones) {
                        seccionActual++;
                        actualizarProgreso();
                        scrollToTop();
                    } else {
                        const form = document.getElementById(`formulario${secciones[seccionActual - 1]}`);
                        if (form) {
                            form.submit();
                        }
                    }
                },
                goToPreviousSection: function() {
                    if (seccionActual > 1) {
                        seccionActual--;
                        actualizarProgreso();
                        scrollToTop();
                    }
                },
                getCurrentSection: function() {
                    return secciones[seccionActual - 1];
                },
                updateSectionsByTipoPersona: updateSectionsByTipoPersona
            };

            if (btnSiguiente) {
                btnSiguiente.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (isNavigating) return;
                    isNavigating = true;

                    const currentForm = document.getElementById(`formulario${secciones[seccionActual - 1]}`);
                    if (!currentForm) {
                        console.error(`Formulario formulario${secciones[seccionActual - 1]} no encontrado`);
                        isNavigating = false;
                        return;
                    }
                    const inputs = currentForm.querySelectorAll('input, select, textarea');
                    let isValid = true;

                    inputs.forEach(input => {
                        if (input.type !== 'hidden' && input.name !== 'actividad') {
                            input.dispatchEvent(new Event('change'));
                            input.dispatchEvent(new Event('blur'));
                            if (input.hasAttribute('required') && !input.value.trim()) {
                                isValid = false;
                                input.classList.add('error');
                            } else {
                                input.classList.remove('error');
                            }
                        }
                    });

                    if (isValid) {
                        window.formNavigation.goToNextSection();
                    } else {
                        console.log('Formulario no válido, revisa los campos requeridos.');
                    }

                    setTimeout(() => {
                        isNavigating = false;
                    }, 500);
                });
            }

            if (btnAnterior) {
                btnAnterior.addEventListener('click', function() {
                    window.formNavigation.goToPreviousSection();
                });
            }

            // Inicializar el progreso con solo la Sección 1
            updateProgressTracker();
            actualizarProgreso();
        });
    </script>
@endsection