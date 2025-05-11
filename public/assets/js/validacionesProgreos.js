// Asignar el JSON como una cadena para evitar el error del linter
const seccionMapping = JSON.parse('{{ json_encode($secciones) }}');
    
document.addEventListener('DOMContentLoaded', function() {
    const progressTracker = document.getElementById('progressTracker');
    const secciones = progressTracker.getElementsByClassName('seccion');
    const progressFill = document.getElementById('progress-fill');
    const progressPercent = document.getElementById('progress-percent');
    let seccionActual = 1;
    const totalSecciones = secciones.length;
    const btnSiguiente = document.getElementById('btnSiguiente');
    const btnAnterior = document.getElementById('btnAnterior');

    // Resto del código JavaScript sin cambios
    window.formNavigation = {
        goToNextSection: function() {
            if (seccionActual < totalSecciones) {
                seccionActual++;
                actualizarProgreso();
                scrollToTop();
            } else {
                const form = document.getElementById(`formulario${seccionMapping[seccionActual - 1]}`);
                form.submit();
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
            return seccionMapping[seccionActual - 1];
        }
    };

    function actualizarProgreso() {
        for (let i = 0; i < secciones.length; i++) {
            const seccionNum = parseInt(secciones[i].getAttribute('data-seccion'));
            secciones[i].classList.remove('completed', 'active');
            if (seccionNum < seccionActual) {
                secciones[i].classList.add('completed');
            } else if (seccionNum === seccionActual) {
                secciones[i].classList.add('active');
            }
        }
        const porcentaje = ((seccionActual - 1) / (totalSecciones - 1)) * 100;
        progressFill.style.width = porcentaje + '%';
        progressPercent.textContent = Math.round(porcentaje) + '%';
        for (let i = 1; i <= 7; i++) {
            const seccionElement = document.getElementById(`seccion${i}`);
            if (seccionElement) {
                seccionElement.style.display = (seccionMapping[seccionActual - 1] === i) ? 'block' : 'none';
            }
        }
        btnAnterior.style.display = seccionActual === 1 ? 'none' : 'block';
        btnSiguiente.textContent = seccionActual === totalSecciones ? 'Finalizar' : 'Siguiente';
        btnSiguiente.setAttribute('form', `formulario${seccionMapping[seccionActual - 1]}`);
    }

    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    btnSiguiente.addEventListener('click', function(e) {
        e.preventDefault();
        const currentForm = document.getElementById(`formulario${seccionMapping[seccionActual - 1]}`);
        const inputs = currentForm.querySelectorAll('input, select, textarea');
        let isValid = true;

        // Validar todos los campos
        inputs.forEach(input => {
            if (input.type !== 'hidden' && input.name !== 'actividad') {
                input.dispatchEvent(new Event('change'));
                input.dispatchEvent(new Event('blur'));
                if (input.classList.contains('formulario__input-error-activo')) {
                    isValid = false;
                }
            }
        });

        // Validar actividades seleccionadas
        const actividadesSeleccionadas = new Set(
            Array.from(document.querySelectorAll('#actividades-seleccionadas .actividad-item'))
                .map(item => item.dataset.value)
        );
        if (actividadesSeleccionadas.size === 0) {
            const actividadError = document.querySelector('#formulario__grupo--actividades .formulario__input-error');
            actividadError.classList.add('formulario__input-error-activo');
            isValid = false;
        } else {
            const actividadError = document.querySelector('#formulario__grupo--actividades .formulario__input-error');
            actividadError.classList.remove('formulario__input-error-activo');
        }

        if (isValid && seccionActual === 1) {
            const formData = new FormData(currentForm);
            formData.append('actividades', Array.from(actividadesSeleccionadas).join(','));

            // Enviar los datos mediante AJAX
            fetch('{{ route("registro.seccion1") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.formNavigation.goToNextSection();
                } else {
                    alert('Error al guardar los datos: ' + (data.message || 'Inténtalo de nuevo.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al enviar los datos.');
            });
        } else if (isValid) {
            window.formNavigation.goToNextSection();
        }
    });

    btnAnterior.addEventListener('click', function() {
        window.formNavigation.goToPreviousSection();
    });

    for (let i = 0; i < secciones.length; i++) {
        secciones[i].addEventListener('click', function() {
            const seccionNum = parseInt(this.getAttribute('data-seccion'));
            if (seccionNum <= seccionActual) {
                seccionActual = seccionNum;
                actualizarProgreso();
                scrollToTop();
            }
        });
    }

    actualizarProgreso();
});