<style>
.custom-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8));
    backdrop-filter: blur(8px);
    z-index: 1000;
    justify-content: center;
    align-items: center;
    animation: fadeInBackdrop 0.4s ease;
}

.custom-modal-content {
    background: linear-gradient(145deg, #ffffff, #f8f9ff);
    padding: 50px 40px;
    border-radius: 24px;
    max-width: 520px;
    width: 90%;
    text-align: center;
    position: relative;
    box-shadow: 
        0 25px 60px rgba(0, 0, 0, 0.15),
        0 10px 30px rgba(40, 167, 69, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    transform: translateY(-30px) scale(0.9);
    animation: slideUpScale 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    border: 1px solid rgba(255, 255, 255, 0.3);
    overflow: hidden;
}

.custom-modal-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, #28a745, #20c997, #17a2b8, #28a745);
    background-size: 200% 100%;
    animation: shimmer 3s linear infinite;
}

/* Icono de éxito mejorado */
.success-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 50%;
    margin: 0 auto 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 32px;
    font-weight: bold;
    position: relative;
    box-shadow: 
        0 8px 25px rgba(40, 167, 69, 0.3),
        0 4px 12px rgba(40, 167, 69, 0.2);
    animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.3s both;
}

.success-icon::before {
    content: '';
    position: absolute;
    top: -4px;
    left: -4px;
    right: -4px;
    bottom: -4px;
    background: linear-gradient(45deg, rgba(40, 167, 69, 0.3), rgba(32, 201, 151, 0.3));
    border-radius: 50%;
    z-index: -1;
    animation: pulse 2s ease-in-out infinite;
}

.success-icon::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 120%;
    height: 120%;
    background: radial-gradient(circle, rgba(40, 167, 69, 0.1) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    z-index: -2;
    animation: ripple 2s ease-out infinite;
}

.custom-modal-header {
    font-size: 2.2em;
    font-weight: 700;
    background: linear-gradient(135deg, #2c3e50, #34495e);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 25px;
    font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
    letter-spacing: -0.5px;
    animation: textSlideIn 0.6s ease 0.4s both;
}

.custom-modal-body {
    font-size: 1.15em;
    color: #5a6c7d;
    margin-bottom: 35px;
    line-height: 1.7;
    font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
    font-weight: 400;
    animation: textSlideIn 0.6s ease 0.5s both;
}

.custom-modal-body strong {
    background: linear-gradient(135deg, #28a745, #20c997);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 600;
}

.custom-modal-footer {
    margin-top: 35px;
    animation: textSlideIn 0.6s ease 0.6s both;
}

.custom-modal-footer button {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 16px 40px;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    font-size: 1.1em;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
    min-width: 140px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.25);
}

.custom-modal-footer button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s;
}

.custom-modal-footer button:hover {
    background: linear-gradient(135deg, #218838, #1e7e34);
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 12px 35px rgba(40, 167, 69, 0.4);
}

.custom-modal-footer button:hover::before {
    left: 100%;
}

.custom-modal-footer button:active {
    transform: translateY(-1px) scale(1.01);
    transition: all 0.1s ease;
}

.custom-modal-close {
    position: absolute;
    top: 20px;
    right: 25px;
    font-size: 28px;
    color: #adb5bd;
    cursor: pointer;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    background: rgba(248, 249, 250, 0.8);
    backdrop-filter: blur(10px);
}

.custom-modal-close:hover {
    color: #6c757d;
    background: rgba(248, 249, 250, 1);
    transform: rotate(90deg) scale(1.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

@keyframes fadeInBackdrop {
    from { 
        opacity: 0; 
        backdrop-filter: blur(0px);
    }
    to { 
        opacity: 1; 
        backdrop-filter: blur(8px);
    }
}

@keyframes slideUpScale {
    0% {
        transform: translateY(50px) scale(0.8);
        opacity: 0;
    }
    100% {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}

@keyframes bounceIn {
    0% {
        transform: scale(0) rotate(-180deg);
        opacity: 0;
    }
    50% {
        transform: scale(1.2) rotate(-90deg);
    }
    100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
}

@keyframes textSlideIn {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 0.6;
        transform: scale(1);
    }
    50% {
        opacity: 0.3;
        transform: scale(1.1);
    }
}

@keyframes ripple {
    0% {
        opacity: 0.8;
        transform: translate(-50%, -50%) scale(0.8);
    }
    100% {
        opacity: 0;
        transform: translate(-50%, -50%) scale(1.4);
    }
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

/* Responsive mejorado */
@media (max-width: 600px) {
    .custom-modal-content {
        padding: 40px 30px;
        width: 95%;
        margin: 20px;
        border-radius: 20px;
    }
    
    .success-icon {
        width: 70px;
        height: 70px;
        font-size: 28px;
        margin-bottom: 25px;
    }
    
    .custom-modal-header {
        font-size: 1.8em;
        margin-bottom: 20px;
    }
    
    .custom-modal-body {
        font-size: 1.05em;
        margin-bottom: 30px;
    }
    
    .custom-modal-footer button {
        width: 100%;
        padding: 18px;
        font-size: 1em;
    }
    
    .custom-modal-close {
        top: 15px;
        right: 20px;
        width: 35px;
        height: 35px;
        font-size: 24px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .custom-modal-content {
        animation: fadeIn 0.3s ease forwards;
    }
    
    .success-icon {
        animation: fadeIn 0.4s ease 0.2s both;
    }
    
    .success-icon::before,
    .success-icon::after {
        animation: none;
    }
    
    .custom-modal-footer button::before {
        display: none;
    }
}
</style>

<div class="custom-modal" id="{{ $modalId }}" role="dialog" aria-labelledby="modalTitle" aria-hidden="true" @if($showModal) style="display: flex;" @else style="display: none;" @endif>
    <div class="custom-modal-content">
        <span class="custom-modal-close" onclick="closeModal('{{ $modalId }}')" aria-label="Close">×</span>
        <div class="success-icon">✓</div>
        <div class="custom-modal-header" id="modalTitle">{{ $title }}</div>
        <div class="custom-modal-body">
            <p>{!! $message !!}</p>
        </div>
        <div class="custom-modal-footer">
            <button onclick="closeModal('{{ $modalId }}')">Aceptar</button>
        </div>
    </div>
</div>

<script>
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

document.getElementById('{{ $modalId }}').addEventListener('click', function (e) {
    if (e.target === this) {
        closeModal('{{ $modalId }}');
    }
});
</script>