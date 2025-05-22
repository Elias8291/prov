// utils.js
export function createModal({ className = 'modal-overlay', html, onSetup, onClose }) {
    const modal = document.createElement('div');
    modal.className = className;
    modal.innerHTML = html;
    document.body.appendChild(modal);

    setTimeout(() => {
        modal.classList.add('visible');
    }, 10);

    const closeModal = () => {
        modal.style.opacity = '0';
        modal.style.visibility = 'hidden';
        setTimeout(() => {
            onClose?.();
            modal.remove();
        }, 400);
    };

    modal.querySelector('.close-modal')?.addEventListener('click', closeModal);
    modal.querySelector('#closeModalBtn')?.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    onSetup?.(modal);
    return modal;
}

export function createSpinner() {
    return '<div class="spinner"></div>';
}

export function showError(message) {
    const modal = createModal({
        className: 'modal-overlay error-modal',
        html: `
            <div class="modal-container">
                <div class="modal-header">
                    <h3>Error</h3>
                    <button class="icon-btn close-modal">×</button>
                </div>
                <div class="modal-body">
                    <p>${message}</p>
                </div>
                <div class="modal-footer">
                    <button class="small-btn outline" id="closeModalBtn">Cerrar</button>
                </div>
            </div>
        `,
    });
    return modal;
}

// Expose functions globally
window.createModal = createModal;
window.createSpinner = createSpinner;
window.showError = showError;