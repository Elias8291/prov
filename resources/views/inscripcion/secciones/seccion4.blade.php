<form id="formulario4">
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
</form>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('shareholders-container');
    const addBtn = document.getElementById('add-shareholder');
    const percentageBar = document.getElementById('percentage-bar');
    const percentageText = document.getElementById('percentage-text');

    let shareholderCount = 0;
    let activeCard = null;

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
                <label for="name-${shareholderCount}">Nombre(s)</label>
                <input type="text" id="name-${shareholderCount}" class="form-control-shareholder" placeholder="Ej: Juan Carlos">
            </div>
            <div class="form-group-shareholder">
                <label for="lastname1-${shareholderCount}">Apellido Paterno</label>
                <input type="text" id="lastname1-${shareholderCount}" class="form-control-shareholder" placeholder="Ej: González">
            </div>
            <div class="form-group-shareholder">
                <label for="lastname2-${shareholderCount}">Apellido Materno</label>
                <input type="text" id="lastname2-${shareholderCount}" class="form-control-shareholder" placeholder="Ej: López">
            </div>
            <div class="form-group-shareholder">
                <label for="percentage-${shareholderCount}">Porcentaje de Acciones</label>
                <div class="percentage-input-container">
                    <input type="number" id="percentage-${shareholderCount}" 
                           class="form-control-shareholder percentage-input" 
                           placeholder="Ej: 50" min="0" max="100" step="0.01">
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

        updatePercentageSummary();
    }

    // Función para eliminar un accionista
    function deleteShareholder(card) {
        if (container.children.length > 1) {
            // Efecto de desvanecimiento al eliminar
            card.style.opacity = '0';
            card.style.transform = 'scale(0.9)';
            
            setTimeout(() => {
                card.remove();
                updateShareholderNumbers();
                updatePercentageSummary();
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
        });
        shareholderCount = cards.length;
    }

    // Función para actualizar el título de la tarjeta con el nombre completo
    function updateCardTitle(card) {
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
        
        // Construir nombre completo en orden: Nombre + ApellidoPaterno + ApellidoMaterno
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
        
        // Actualizar título
        if (fullname) {
            titleElement.textContent = fullname;
        } else {
            const number = card.querySelector('.shareholder-number').textContent;
            titleElement.textContent = `Socio ${number}`;
        }
        
        // Actualizar indicador de porcentaje
        if (percentageElement) {
            percentageElement.textContent = `${percentage}%`;
            
            // Aplicar colores según el porcentaje
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

        // Actualizar barra y texto
        const percentage = Math.min(total, 100);
        percentageBar.style.width = `${percentage}%`;
        
        // Resetear clases
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
    }

    // Event listeners
    addBtn.addEventListener('click', () => {
        // Contraer tarjetas existentes antes de añadir una nueva
        collapseOtherCards(null);
        addShareholder();
    });

    container.addEventListener('click', (e) => {
        const card = e.target.closest('.shareholder-card');
        if (!card) return;

        if (e.target.closest('.btn-delete-shareholder')) {
            deleteShareholder(card);
        } else if (!card.classList.contains('expanded')) {
            // Expandir tarjeta al hacer clic y contraer las demás
            collapseOtherCards(card.dataset.id);
            card.classList.add('expanded');
            activeCard = card;
        }
    });

    // Actualizar información cuando se cambia algún campo
    container.addEventListener('input', (e) => {
        if (!e.target.matches('input')) return;
        
        const card = e.target.closest('.shareholder-card');
        if (!card) return;
        
        // Actualizar título de la tarjeta inmediatamente cuando se escriba en cualquier campo
        if (e.target.id.startsWith('name-') || 
            e.target.id.startsWith('lastname1-') || 
            e.target.id.startsWith('lastname2-')) {
            updateCardTitle(card);
        }
        
        if (e.target.classList.contains('percentage-input')) {
            // Validar que no se ingresen valores mayores a 100
            if (parseFloat(e.target.value) > 100) {
                e.target.value = 100;
            }
            updatePercentageSummary();
            updateCardTitle(card); // Actualizar también el indicador de porcentaje
        }
    });

    // Contraer tarjeta al perder el foco en todos sus inputs
    container.addEventListener('focusout', (e) => {
        if (!e.target.matches('input')) return;
        
        const card = e.target.closest('.shareholder-card');
        if (!card) return;
        
        // Verificar si el nuevo elemento enfocado está dentro de la misma tarjeta
        setTimeout(() => {
            const activeElement = document.activeElement;
            if (!card.contains(activeElement) && !e.target.closest('.btn-delete-shareholder')) {
                card.classList.remove('expanded');
            }
        }, 100);
    });

    // Agregar estilos de notificación dinámicamente
    const styleElement = document.createElement('style');
    styleElement.textContent = `
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 16px;
            border-radius: var(--border-radius);
            background-color: white;
            color: var(--text-dark);
            box-shadow: var(--shadow-subtle);
            z-index: 1000;
            opacity: 1;
            transition: opacity 0.3s;
            max-width: 300px;
        }
        
        .notification.info {
            border-left: 4px solid var(--primary-color);
        }
        
        .notification.success {
            border-left: 4px solid var(--success-color);
        }
        
        .notification.warning {
            border-left: 4px solid var(--warning-color);
        }
        
        .notification.error {
            border-left: 4px solid var(--danger-color);
        }
    `;
    document.head.appendChild(styleElement);

    // Agregar primer accionista por defecto
    addShareholder();
});
</Script>
