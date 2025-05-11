@extends('dashboard')

@section('title', 'Inscripción al Padrón de Proveedores - Proveedores de Oaxaca')
<link rel="stylesheet" href="{{ asset('assets/css/registration.css') }}">

@section('content')
<div class="dashboard-container">
    <div class="registration-section">
        <h1>Inscripción al Padrón de Proveedores</h1>
        
        <div class="registration-intro">
            <p>Antes de continuar con tu registro en el padrón de proveedores de Oaxaca, por favor revisa y acepta los siguientes términos y condiciones:</p>
            <p>Tipo de solicitante: <strong>{{ $tipoPersona }}</strong>. Recibirás formularios según este tipo.</p>
        </div>
        
        <div class="terms-container">
            <div class="terms-section">
                <div class="terms-header">
                    <h3>Términos y Condiciones</h3>
                </div>
                <div class="terms-content">
                    <p>Al avanzar con tu inscripción, aceptas lo siguiente:</p>
                    <ul class="terms-list">
                        <li>Proporcionarás información completa, veraz y actualizada en los pasos siguientes de este proceso.</li>
                        <li>Los datos que compartas, como el nombre de tu empresa, correo electrónico y teléfono, serán utilizados exclusivamente para gestionar tu registro y participación en el padrón de proveedores.</li>
                        <li>Cumplirás con las normativas locales y estatales de Oaxaca aplicables a los proveedores registrados.</li>
                        <li>Permitirás la verificación de tu información por parte del equipo de Proveedores de Oaxaca para garantizar la integridad del padrón.</li>
                        <li>Nos comprometemos a proteger tus datos conforme a las leyes de privacidad vigentes, usándolos solo para los fines establecidos en este proceso.</li>
                        <li>Podrás actualizar tu información en cualquier momento si hay cambios significativos.</li>
                    </ul>
                </div>
                <div class="terms-footer">
                    <p>Al aceptar estos términos, podrás continuar con el registro y formar parte de nuestra red de proveedores en Oaxaca.</p>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <form id="termsForm" method="POST" action="{{ route('aceptar.terminos') }}">
                @csrf
                <div class="form-group">
                    <label class="custom-checkbox">
                        <input type="checkbox" id="terms" name="terms" required>
                        <span class="checkmark"></span>
                        He leído y acepto los términos y condiciones
                    </label>
                    @error('terms')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">Continuar con el Registro</button>
            </form>
        </div>
    </div>
</div>


@endsection