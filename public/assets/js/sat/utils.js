/**
 * Creates a loading spinner overlay
 * @returns {string} HTML for the spinner
 */
export function createSpinner() {
    return `
        <div class="modal-overlay loading-modal">
            <div class="loading-container">
                <div class="spinner"></div>
                <p class="loading-text">Procesando tu PDF...</p>
            </div>
        </div>
    `;
}

/**
 * Creates and shows a modal dialog
 * @param {Object} options - Modal configuration options
 * @param {string} [options.className='modal-overlay'] - CSS class for the modal
 * @param {string} options.html - HTML content for the modal
 * @param {Function} [options.onSetup] - Callback after modal is created
 * @param {Function} [options.onClose] - Callback before modal is removed
 * @returns {HTMLElement} - The modal element
 */
export function createModal({ className = 'modal-overlay', html, onSetup, onClose }) {
    // Create modal element
    const modal = document.createElement('div');
    modal.className = className;
    modal.innerHTML = html;
    document.body.appendChild(modal);
    
    // Define close function
    const closeModal = () => {
        // Add fadeout animation
        modal.style.opacity = '0';
        
        // Wait for animation to complete before removing
        setTimeout(() => {
            onClose?.();
            document.body.removeChild(modal);
        }, 300);
    };
    
    // Set up event listeners
    modal.querySelector('.close-modal')?.addEventListener('click', closeModal);
    modal.querySelector('#closeModalBtn')?.addEventListener('click', closeModal);
    
    // Close on background click
    modal.addEventListener('click', e => {
        if (e.target === modal) closeModal();
    });
    
    // Run setup callback if provided
    onSetup?.(modal);
    
    return modal;
}

/**
 * Shows an error message in a modal
 * @param {string} message - The error message to display
 * @returns {HTMLElement} - The modal element
 */
export function showError(message) {
    return createModal({
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
        `
    });
}

/**
 * Shows a success message in a modal
 * @param {string} message - The success message to display
 * @returns {HTMLElement} - The modal element
 */
export function showSuccess(message) {
    return createModal({
        className: 'modal-overlay success-modal',
        html: `
            <div class="modal-container">
                <div class="modal-header">
                    <h3>Éxito</h3>
                    <button class="icon-btn close-modal">×</button>
                </div>
                <div class="modal-body">
                    <p>${message}</p>
                </div>
                <div class="modal-footer">
                    <button class="small-btn outline" id="closeModalBtn">Aceptar</button>
                </div>
            </div>
        `
    });
}