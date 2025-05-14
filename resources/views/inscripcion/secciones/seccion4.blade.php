
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
            flex-direction: column | column;
            gap: 10px;
        }

        .btn {
            width: 100%;
        }
    }

    /* Estilos adicionales para notificaciones */
    .notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 16px;
        border-radius: 5px;
        background-color: white;
        color: #333;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        opacity: 1;
        transition: opacity 0.3s;
        max-width: 300px;
    }

    .notification.info {
        border-left: 4px solid #007bff;
    }

    .notification.success {
        border-left: 4px solid #28a745;
    }

    .notification.warning {
        border-left: 4px solid #ffc107;
    }

    .notification.error {
        border-left: 4px solid #dc3545;
    }
</style>

<form id="formulario4" action="{{ route('inscripcion.procesar') }}" method="POST">
    @csrf
    <div class="form-section" id="form-step-4">
        <div class="form-container">
            <div class="form-column">
                <div class="form-header">
                    <h4><i class="fas fa-users"></i> Socios o Accionistas (Persona Moral)</h4>
                    <p class="subtitle">Agrega los socios o accionistas de la empresa</p>
                    <div class="percentage-summary">
                        <div class="progress-bar-container">
                            <div class="progress-bar" id="percentage-bar"></div>
                        </div>
                        <span id="percentage-text">0% asignado</span>
                    </div>
                </div>

                <div class="shareholders-container" id="shareholders-container">
                    <!-- Tarjetas de accionistas se agregan dinámicamente -->
                </div>

                <button type="button" id="add-shareholder" class="btn-add-shareholder">
                    <i class="fas fa-plus-circle"></i> Agregar Socio/Accionista
                </button>
            </div>
        </div>
    </div>
    <!-- Campo oculto para almacenar los datos de accionistas como JSON -->
    <input type="hidden" name="accionistas" id="accionistas-data">
    <div class="form-buttons">
        <button type="button" class="btn btn-secondary" onclick="window.history.back();">Anterior</button>
        <button type="submit" class="btn btn-primary">Siguiente</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('shareholders-container');
    const addBtn = document.getElementById('add-shareholder');
    const percentageBar = document.getElementById('percentage-bar');
    const percentageText = document.getElementById('percentage-text');
    const form = document.getElementById('formulario4');
    const accionistasDataInput = document.getElementById('accionistas-data');

    let shareholderCount = 0;
    let activeCard = null;
    let shareholdersArray = []; // Arreglo para almacenar los datos de los accionistas

    // Función para actualizar el campo oculto con los datos de accionistas
    function updateHiddenField() {
        accionistasDataInput.value = JSON.stringify(shareholdersArray);
        console.log('Campo oculto actualizado con datos:', shareholdersArray);
    }

    // Función para agregar un nuevo accionista
    function addShareholder() {
        shareholderCount++;
        const card = document.createElement('div');
        card.className = 'shareholder-card expanded';
        card.dataset.id = shareholderCount;
        
        card.innerHTML = `
        <div class="shareholder-header">
            <div class="shareholder-number">${shareholderCount}</div>
            <div class="shareholder-title">Socio ${shareholderCount}</div>
            <button type="button" class="btn-delete-shareholder" title="Eliminar accionista">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="shareholder-fields">
            <div class="form-group-shareholder">
                <label for="name-${shareholderCount}">Nombre(s)*</label>
                <input type="text" id="name-${shareholderCount}" class="form-control-shareholder" placeholder="Ej: Juan Carlos" required>
            </div>
            <div class="form-group-shareholder">
                <label for="lastname1-${shareholderCount}">Apellido Paterno*</label>
                <input type="text" id="lastname1-${shareholderCount}" class="form-control-shareholder" placeholder="Ej: González" required>
            </div>
            <div class="form-group-shareholder">
                <label for="lastname2-${shareholderCount}">Apellido Materno</label>
                <input type="text" id="lastname2-${shareholderCount}" class="form-control-shareholder" placeholder="Ej: López">
            </div>
            <div class="form-group-shareholder">
                <label for="percentage-${shareholderCount}">Porcentaje de Acciones*</label>
                <div class="percentage-input-container">
                    <input type="number" id="percentage-${shareholderCount}" 
                           class="form-control-shareholder percentage-input" 
                           placeholder="Ej: 50" min="0" max="100" step="0.01" required>
                    <span class="percentage-suffix">%</span>
                </div>
            </div>
        </div>
        `;

        container.appendChild(card);
        
        activeCard = card;

        // Agregar el indicador de porcentaje para vista compacta
        const headerDiv = card.querySelector('.shareholder-header');
        const percentageSpan = document.createElement('span');
        percentageSpan.className = 'shareholder-percentage';
        percentageSpan.textContent = '0%';
        headerDiv.appendChild(percentageSpan);

        // Enfocar el primer campo del nuevo accionista
        const firstInput = card.querySelector('input');
        if (firstInput) firstInput.focus();

        // Agregar nuevo accionista al arreglo
        shareholdersArray.push({
            id: shareholderCount,
            nombre: '',
            apellido_paterno: '',
            apellido_materno: '',
            porcentaje: 0
        });

        console.log('Accionistas actualizados:', shareholdersArray);
        
        // Actualizar el campo oculto inmediatamente
        updateHiddenField();
        updatePercentageSummary();
    }

    // Función para eliminar un accionista
    function deleteShareholder(card) {
        if (container.children.length > 1) {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.9)';
            
            setTimeout(() => {
                // Remover del arreglo
                const id = parseInt(card.dataset.id);
                shareholdersArray = shareholdersArray.filter(shareholder => shareholder.id !== id);
                
                card.remove();
                updateShareholderNumbers();
                updatePercentageSummary();
                
                // Actualizar el campo oculto después de eliminar
                updateHiddenField();
                console.log('Accionistas actualizados tras eliminar:', shareholdersArray);
            }, 300);
        } else {
            showNotification('Debe haber al menos un socio/accionista registrado.', 'warning');
        }
    }

    // Función para mostrar notificaciones
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Función para actualizar los números de los accionistas
    function updateShareholderNumbers() {
        const cards = container.querySelectorAll('.shareholder-card');
        cards.forEach((card, index) => {
            const number = index + 1;
            card.querySelector('.shareholder-number').textContent = number;
            
            const titleElement = card.querySelector('.shareholder-title');
            const nameInput = card.querySelector('input[id^="name-"]');
            const lastname1Input = card.querySelector('input[id^="lastname1-"]');
            const lastname2Input = card.querySelector('input[id^="lastname2-"]');
            
            if (nameInput || lastname1Input || lastname2Input) {
                updateCardTitle(card);
            } else {
                titleElement.textContent = `Socio ${number}`;
            }

            // Actualizar el ID en el arreglo
            const id = parseInt(card.dataset.id);
            const shareholder = shareholdersArray.find(sh => sh.id === id);
            if (shareholder) {
                shareholder.id = number;
                card.dataset.id = number;
            }
        });
        shareholderCount = cards.length;
        
        // Actualizar el campo oculto después de reordenar
        updateHiddenField();
    }

    // Función para actualizar el título de la tarjeta con el nombre completo
    function updateCardTitle(card) {
        const id = parseInt(card.dataset.id);
        const nameInput = card.querySelector('input[id^="name-"]');
        const lastname1Input = card.querySelector('input[id^="lastname1-"]');
        const lastname2Input = card.querySelector('input[id^="lastname2-"]');
        const percentageInput = card.querySelector('.percentage-input');
        
        const name = nameInput ? nameInput.value.trim() : '';
        const lastname1 = lastname1Input ? lastname1Input.value.trim() : '';
        const lastname2 = lastname2Input ? lastname2Input.value.trim() : '';
        const percentage = percentageInput ? parseFloat(percentageInput.value || 0).toFixed(2) : '0.00';
        
        const titleElement = card.querySelector('.shareholder-title');
        const percentageElement = card.querySelector('.shareholder-percentage');
        
        let fullname = '';
        if (name) fullname += name;
        if (lastname1) {
            if (fullname) fullname += ' ';
            fullname += lastname1;
        }
        if (lastname2) {
            if (fullname) fullname += ' ';
            fullname += lastname2;
        }
        
        if (fullname) {
            titleElement.textContent = fullname;
        } else {
            const number = card.querySelector('.shareholder-number').textContent;
            titleElement.textContent = `Socio ${number}`;
        }
        
        if (percentageElement) {
            percentageElement.textContent = `${percentage}%`;
            
            if (percentage > 50) {
                percentageElement.style.backgroundColor = 'var(--primary-light)';
                percentageElement.style.color = 'var(--primary-color)';
            } else if (percentage > 20) {
                percentageElement.style.backgroundColor = 'var(--accent-light)';
                percentageElement.style.color = 'var(--accent-color)';
            } else {
                percentageElement.style.backgroundColor = 'var(--secondary-light)';
                percentageElement.style.color = 'var(--secondary-color)';
            }
        }

        // Actualizar el arreglo
        const shareholder = shareholdersArray.find(sh => sh.id === id);
        if (shareholder) {
            shareholder.nombre = name;
            shareholder.apellido_paterno = lastname1;
            shareholder.apellido_materno = lastname2;
            shareholder.porcentaje = parseFloat(percentage) || 0;
            
            // Actualizar el campo oculto después de modificar datos
            updateHiddenField();
        }
    }

    // Función para contraer todas las tarjetas excepto la activa
    function collapseOtherCards(activeCardId) {
        const cards = container.querySelectorAll('.shareholder-card');
        cards.forEach(card => {
            if (card.dataset.id !== activeCardId) {
                card.classList.remove('expanded');
            }
        });
    }

    // Función para calcular y mostrar el resumen de porcentajes
    function updatePercentageSummary() {
        const inputs = container.querySelectorAll('.percentage-input');
        let total = 0;

        inputs.forEach(input => {
            const value = parseFloat(input.value) || 0;
            total += value;
        });

        const percentage = Math.min(total, 100);
        percentageBar.style.width = `${percentage}%`;
        
        percentageBar.classList.remove('complete', 'warning', 'danger');
        
        if (total > 100) {
            percentageText.textContent = `⚠️ ${total.toFixed(2)}% (Excede el 100%)`;
            percentageText.style.color = 'var(--danger-color)';
            percentageBar.classList.add('danger');
        } else if (total === 100) {
            percentageText.textContent = `✓ ${total.toFixed(2)}% (Completo)`;
            percentageText.style.color = 'var(--success-color)';
            percentageBar.classList.add('complete');
        } else {
            percentageText.textContent = `${total.toFixed(2)}% asignado`;
            percentageText.style.color = 'var(--primary-color)';
        }

        return total;
    }

    // Event listeners
    addBtn.addEventListener('click', () => {
        collapseOtherCards(null);
        addShareholder();
    });

    container.addEventListener('click', (e) => {
        const card = e.target.closest('.shareholder-card');
        if (!card) return;

        if (e.target.closest('.btn-delete-shareholder')) {
            deleteShareholder(card);
        } else if (!card.classList.contains('expanded')) {
            collapseOtherCards(card.dataset.id);
            card.classList.add('expanded');
            activeCard = card;
        }
    });

    container.addEventListener('input', (e) => {
        if (!e.target.matches('input')) return;
        
        const card = e.target.closest('.shareholder-card');
        if (!card) return;
        
        if (e.target.id.startsWith('name-') || 
            e.target.id.startsWith('lastname1-') || 
            e.target.id.startsWith('lastname2-')) {
            updateCardTitle(card);
        }
        
        if (e.target.classList.contains('percentage-input')) {
            if (parseFloat(e.target.value) > 100) {
                e.target.value = 100;
            }
            updatePercentageSummary();
            updateCardTitle(card);
        }
    });

    container.addEventListener('focusout', (e) => {
        if (!e.target.matches('input')) return;
        
        const card = e.target.closest('.shareholder-card');
        if (!card) return;
        
        setTimeout(() => {
            const activeElement = document.activeElement;
            if (!card.contains(activeElement) && !e.target.closest('.btn-delete-shareholder')) {
                card.classList.remove('expanded');
            }
        }, 100);
    });

    // Validar y enviar el formulario
    form.addEventListener('submit', (e) => {
        e.preventDefault(); // Prevenir el envío predeterminado para validar

        const totalPercentage = updatePercentageSummary();

        // Validar que el porcentaje total sea 100%
        if (Math.abs(totalPercentage - 100) > 0.1) {
            showNotification('El porcentaje total debe ser exactamente 100%.', 'error');
            return;
        }

        // Validar que todos los campos requeridos estén completos
        let hasErrors = false;
        shareholdersArray.forEach((shareholder, index) => {
            if (!shareholder.nombre || !shareholder.apellido_paterno || shareholder.porcentaje <= 0) {
                showNotification(`El accionista ${index + 1} debe tener nombre, apellido paterno y un porcentaje mayor a 0.`, 'error');
                hasErrors = true;
            }
        });

        if (hasErrors) return;

        // Actualizar una última vez el campo oculto antes de enviar
        updateHiddenField();

        console.log('Enviando datos al servidor:', shareholdersArray);
        console.log('Valor del campo oculto:', accionistasDataInput.value);

        // Enviar el formulario
        form.submit();
    });

    // Agregar primer accionista por defecto
    addShareholder();
});
</script>