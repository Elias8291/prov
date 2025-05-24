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
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #8B0000, #A52A2A);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="3" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
            opacity: 0.3;
        }
        
        .logo-section {
            position: relative;
            z-index: 2;
        }
        
        .government-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .subtitle {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 28px;
            color: #8B0000;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .message {
            font-size: 16px;
            margin-bottom: 25px;
            color: #555;
        }
        
        .verification-section {
            text-align: center;
            margin: 35px 0;
            padding: 25px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 8px;
            border-left: 4px solid #8B0000;
        }
        
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #8B0000, #A52A2A);
            color: white;
            padding: 15px 35px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(139, 0, 0, 0.3);
        }
        
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 0, 0, 0.4);
        }
        
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
            color: #856404;
        }
        
        .warning-icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            background-color: #ffc107;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
        
        .footer-logo {
            margin-bottom: 15px;
        }
        
        .footer-text {
            margin-bottom: 10px;
        }
        
        .divider {
            height: 2px;
            background: linear-gradient(90deg, #8B0000, #A52A2A, #8B0000);
            margin: 20px 0;
        }
        
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .content {
                padding: 25px 20px;
            }
            
            .greeting {
                font-size: 24px;
            }
            
            .verify-button {
                padding: 12px 25px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo-section">
                <div class="government-title">GOBIERNO DEL ESTADO DE OAXACA</div>
                <div class="subtitle">Sistema de Verificación de Identidad</div>
            </div>
        </div>
        
        <div class="content">
            <h1 class="greeting">Hola, {{ $user->nombre }}</h1>
            
            <div class="divider"></div>
            
            <p class="message">
                Gracias por registrarte en nuestro sistema. Para completar tu registro y garantizar la seguridad de tu cuenta, es necesario verificar tu dirección de correo electrónico.
            </p>
            
            <div class="verification-section">
                <p style="margin-bottom: 20px; font-weight: 600; color: #8B0000;">
                    Haz clic en el siguiente botón para verificar tu cuenta:
                </p>
                <a href="{{ $verificationUrl }}" class="verify-button">
                    Verificar Correo Electrónico
                </a>
            </div>
            
            <div class="warning">
                <span class="warning-icon">!</span>
                <strong>Importante:</strong> Este enlace expirará en 5 minutos por motivos de seguridad. Si no verificas tu cuenta dentro de este tiempo, todos los datos asociados serán eliminados automáticamente del sistema.
            </div>
            
            <p class="message">
                Si no te registraste en nuestro sistema, puedes ignorar este correo electrónico de forma segura. Tu información no será procesada ni almacenada.
            </p>
        </div>
        
        <div class="footer">
            <div class="footer-logo">
                <strong>GOBIERNO DEL ESTADO DE OAXACA</strong>
            </div>
            <div class="footer-text">
                Este es un correo electrónico automático, por favor no respondas a este mensaje.
            </div>
            <div class="footer-text">
                Para soporte técnico, contacta al área de sistemas correspondiente.
            </div>
        </div>
    </div>
</body>
</html>