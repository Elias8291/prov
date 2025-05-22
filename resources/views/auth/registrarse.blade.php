<meta name="csrf-token" content="{{ csrf_token() }}">
<form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
    @csrf
    <div class="form-page register-form inactive" id="registerFormStep1">
        <img src="{{ asset('assets/imagenes/logoAdminsitracion.png') }}" alt="Logo" class="logo-img">
        <h1>Regístrate</h1>
        <p>Registro en el <span class="system-name">Padrón de Proveedores de Oaxaca</span></p>
        <div class="input-group">
            <div class="file-input-header">
                <label for="register-file">Constancia del SAT (PDF)*</label>
                <button type="button" class="small-btn outline" id="viewExampleBtnStep1">
                    <svg>...</svg>
                    Ver ejemplo
                </button>
            </div>
            <label class="custom-file-upload" for="register-file">
                <svg>...</svg>
                <span>{{ session('temp_sat_file_name', 'Subir archivo PDF') }}</span>
                <small>Tamaño máximo: 5MB</small>
            </label>
            <input type="file" id="register-file" name="sat_file" accept="application/pdf" required>
        </div>
        <div class="pdf-data-container" style="display: {{ session('temp_sat_file_path') ? 'block' : 'none' }};">
            <div class="success-card" id="pdf-data-card">
                <button type="button" class="small-btn outline" id="viewSatDataBtn" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Ver Datos del SAT
                </button>
                <div class="email-section">
                    <p class="name-display"><strong>Correo Electrónico:</strong></p>
                    <input type="email" name="email" id="email-input" class="email-input"
                        placeholder="INGRESE CORREO" value="{{ old('email') }}" required>
                    @error('email')
                        <label class="error-message"
                            style="color: #F44336; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</label>
                    @enderror
                </div>
                <div class="password-section">
                    <p class="name-display"><strong>Contraseña:</strong></p>
                    <div class="password-input-container">
                        <input type="password" name="password" id="password-input" class="email-input"
                            placeholder="INGRESE CONTRASEÑA" required>
                        <button type="button" class="password-toggle">...</button>
                    </div>
                    @error('password')
                        <label class="error-message"
                            style="color: #F44336; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</label>
                    @enderror
                </div>
                <div class="password-confirm-section">
                    <p class="name-display"><strong>Confirmar Contraseña:</strong></p>
                    <div class="password-input-container">
                        <input type="password" name="password_confirmation" id="password-confirm-input"
                            class="email-input" placeholder="CONFIRME CONTRASEÑA" required>
                        <button type="button" class="password-toggle">...</button>
                    </div>
                    @error('password_confirmation')
                        <label class="error-message"
                            style="color: #F44336; font-size: 0.9rem; margin-top: 5px; display: block;">{{ $message }}</label>
                    @enderror
                </div>
                <input type="hidden" name="secure_data_token" id="secure_data_token"
                    value="{{ old('secure_data_token') }}">
            </div>
            <button type="submit" class="btn" id="registerBtn">Registrarse</button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('register-file');
        const pdfDataContainer = document.querySelector('.pdf-data-container');
        const fileLabel = document.querySelector('.custom-file-upload span');
        const fileUploadContainer = document.querySelector('.custom-file-upload');

        // Function to process PDF file
        async function processPDF(file, fileName) {
            if (!file) return;

            if (file.type !== 'application/pdf') {
                alert('El archivo debe ser un PDF válido.');
                fileInput.value = '';
                fileLabel.textContent = 'Subir archivo PDF';
                fileUploadContainer.classList.remove('file-selected');
                pdfDataContainer.style.display = 'none';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('El archivo excede el tamaño máximo de 5MB.');
                fileInput.value = '';
                fileLabel.textContent = 'Subir archivo PDF';
                fileUploadContainer.classList.remove('file-selected');
                pdfDataContainer.style.display = 'none';
                return;
            }

            fileLabel.textContent = fileName;
            const loading = createModal({
                html: createSpinner()
            });
            const minimumDelay = 2000;
            const startTime = Date.now();

            try {
                const pdfData = await extractQRCodeFromPDF(file);
                const satData = await scrapeSATData(pdfData.qrUrl);

                const token = await secureExtractedData(pdfData, satData);
                let tokenInput = document.getElementById('secure_data_token');
                if (!tokenInput) {
                    tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.id = 'secure_data_token';
                    tokenInput.name = 'secure_data_token';
                    document.getElementById('registerForm').appendChild(tokenInput);
                }
                tokenInput.value = token;

                const elapsedTime = Date.now() - startTime;
                const remainingTime = Math.max(0, minimumDelay - elapsedTime);

                setTimeout(() => {
                    fileUploadContainer.classList.add('file-selected');
                    const emailInput = document.getElementById('email-input');
                    if (emailInput && satData.email) {
                        emailInput.value = satData.email.toLowerCase();
                    }
                    pdfDataContainer.style.display = 'block';
                    document.body.removeChild(loading);
                }, remainingTime);
            } catch (error) {
                const elapsedTime = Date.now() - startTime;
                const remainingTime = Math.max(0, minimumDelay - elapsedTime);

                setTimeout(() => {
                    alert(`Error: ${error.message}`);
                    fileInput.value = '';
                    fileLabel.textContent = 'Subir archivo PDF';
                    fileUploadContainer.classList.remove('file-selected');
                    pdfDataContainer.style.display = 'none';
                    document.body.removeChild(loading);
                }, remainingTime);
            }
        }

        // File input change handler
        fileInput?.addEventListener('change', async () => {
            if (fileInput.files.length > 0) {
                await processPDF(fileInput.files[0], fileInput.files[0].name);
            } else {
                fileLabel.textContent = 'Subir archivo PDF';
                fileUploadContainer.classList.remove('file-selected');
                pdfDataContainer.style.display = 'none';
            }
        });

        // Check for temporary file on page load
        @if (session('temp_sat_file_path'))
            fetch('{{ asset('storage/' . session('temp_sat_file_path')) }}')
                .then(response => response.blob())
                .then(blob => {
                    const fileName = '{{ session('temp_sat_file_name', 'Subir archivo PDF') }}';
                    const file = new File([blob], fileName, {
                        type: 'application/pdf'
                    });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                    processPDF(file, fileName);
                })
                .catch(error => {
                    console.error('Error al cargar el archivo temporal:', error);
                    fileLabel.textContent = 'Subir archivo PDF';
                    fileUploadContainer.classList.remove('file-selected');
                    pdfDataContainer.style.display = 'none';
                });
        @endif
    });
</script>
