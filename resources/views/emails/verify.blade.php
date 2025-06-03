<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo electrónico - Gobierno de Oaxaca</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #1e293b;
            line-height: 1.6;
            padding: 40px 20px;
            min-height: 100vh;
        }
        
        .email-container {
            max-width: 580px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        

        
        .content {
            padding: 48px 40px;
            text-align: center;
        }
        
        .illustration {
            width: 200px;
            height: 160px;
            margin: 0 auto 40px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .illustration::before {
            content: '📧';
            font-size: 64px;
            position: relative;
            z-index: 2;
        }
        
        .illustration::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(139, 0, 0, 0.1) 0%, transparent 70%);
        }
        
        .main-title {
            font-size: 32px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
            letter-spacing: -0.03em;
        }
        
        .description {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 32px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        
        .cta-section {
            margin: 40px 0;
        }
        
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: -0.01em;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px rgba(139, 0, 0, 0.25);
            border: none;
            cursor: pointer;
        }
        
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(139, 0, 0, 0.35);
        }
        
        .verify-button:active {
            transform: translateY(0);
        }
        
        .security-notice {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 16px;
            padding: 24px;
            margin: 32px 0;
            position: relative;
            overflow: hidden;
        }
        
        .security-notice::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        }
        
        .notice-content {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .notice-icon {
            width: 24px;
            height: 24px;
            background: #f59e0b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        
        .notice-text {
            color: #92400e;
            font-size: 14px;
            line-height: 1.5;
            text-align: left;
        }
        
        .additional-info {
            font-size: 14px;
            color: #64748b;
            margin-top: 32px;
            line-height: 1.5;
        }
        
        .footer {
            background: #f8fafc;
            padding: 32px 40px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        
        .footer-logo {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 16px;
            font-size: 16px;
        }
        
        .footer-text {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 8px;
            line-height: 1.4;
        }
        
        .footer-links {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer-link {
            color: #8B0000;
            text-decoration: none;
            font-size: 12px;
            margin: 0 8px;
        }
        
        .footer-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 600px) {
            body {
                padding: 20px 10px;
            }
            
            .content {
                padding: 32px 24px;
            }
            
            .main-title {
                font-size: 26px;
            }
            
            .verify-button {
                padding: 14px 24px;
                font-size: 15px;
            }
            
            .illustration {
                width: 160px;
                height: 130px;
            }
            
            .illustration::before {
                font-size: 48px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">

        
        <div class="content">
            <div class="illustration"></div>
            
            <h1 class="main-title">Confirma tu dirección de correo</h1>
            
            <p class="description">
                Gracias por iniciar su registro en el sistema del Padrón de Proveedores de la Secretaría de Administración de Oaxaca.
            </p>
            
            <p class="description">
                Para completar su registro y garantizar la seguridad de su cuenta, necesitamos confirmar su dirección de correo electrónico.
            </p>
            
            <div class="cta-section">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    Sí, verificar mi correo
                </a>
            </div>
            
            <div class="security-notice">
                <div class="notice-content">
                    <div class="notice-icon">!</div>
                    <div class="notice-text">
                        <strong>Importante para tu seguridad:</strong> Este enlace expirará en 72 horas por motivos de seguridad. Si no verificas tu cuenta dentro de este tiempo, todos los datos asociados serán eliminados automáticamente del sistema para proteger tu información.
                    </div>
                </div>
            </div>
            
            <p class="additional-info">
                Si no te registraste en nuestro sistema, puedes ignorar este correo electrónico de forma segura. Tu información no será procesada ni almacenada.
            </p>
        </div>
        
        <div class="footer">
            <div class="footer-logo">SECRETARÍA DE ADMINISTRACIÓN DE OAXACA</div>
            <div class="footer-text">
                Padrón de Proveedores - Sistema de Registro
            </div>
            <div class="footer-text">
                Página web: www.administracion.oaxaca.gob.mx
            </div>
            <div class="footer-text">
                Este es un correo electrónico automático, por favor no respondas a este mensaje.
            </div>
        </div>
    </div>
</body>
</html>