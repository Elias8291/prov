@extends('dashboard')

@section('title', 'Inscripción al Padrón de Proveedores - Proveedores de Oaxaca')
<link rel="stylesheet" href="{{ asset('assets/css/formularios.css') }}">
@section('content')
    <div class="form-background-container">
        <div class="inner-form-container">
            <div class="progress-container">
                <div class="progress-info">
                    <span class="progress-percent">{{ $porcentaje }}%</span>
                    <span class="progress-text">Completado</span>
                    <span class="progress-text persona-type-text">
                        Formulario para persona: <span class="persona-type-value">{{ $tipoPersona }}</span>
                    </span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: {{ $porcentaje }}%;"
                        data-target="{{ $porcentaje }}"></div>
                </div>
            </div>
            <div class="progress-tracker">
                @foreach ($seccionesInfo as $i => $titulo)
                    <div class="progress-bar-advance" id="progressBarAdvance"></div>
                    <div class="seccion {{ $i <= $seccion ? 'active' : '' }}">
                        <div class="seccion-numero">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="seccion-titulo">{{ $titulo }}</div>
                    </div>
                @endforeach
            </div>
            <div class="form-seccion">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any()))
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="formulario1" method="POST" action="{{ route('inscripcion.procesar') }}"
                    enctype="multipart/form-data">
                    @csrf

                    @if ($seccionPartial === 'seccion1')
                        <x-seccion1 :tipoPersona="$tipoPersona" :datosPrevios="$datosPrevios" :sectores="$sectores" :isRevisor="auth()->check() && auth()->user()->hasRole('revisor')"
                            :mostrarCurp="$mostrarCurp" :seccion="$seccion" :totalSecciones="$totalSecciones" :isConfirmationSection="$isConfirmationSection ?? false" :actividadesSeleccionadas="$actividadesSeleccionadas ?? []" />
                    @elseif($seccionPartial === 'seccion2')
                        <x-seccion2 :datosPrevios="$datosPrevios" :direccion="$direccion ?? null" :estados="$estados ?? []" :isRevisor="auth()->check() && auth()->user()->hasRole('revisor')"
                            :seccion="$seccion" :totalSecciones="$totalSecciones" :isConfirmationSection="$isConfirmationSection ?? false" />
                    @elseif($seccionPartial === 'seccion3')
                        <x-seccion3 :datosPrevios="$datosPrevios" :estados="$estados ?? []" :isRevisor="auth()->check() && auth()->user()->hasRole('revisor')" :seccion="$seccion"
                            :totalSecciones="$totalSecciones" :isConfirmationSection="$isConfirmationSection ?? false" :constitutionData="$constitutionData ?? null" />
                    @elseif($seccionPartial === 'seccion4')
                        <x-seccion4 :datosPrevios="$datosPrevios" :isRevisor="auth()->check() && auth()->user()->hasRole('revisor')" :seccion="$seccion" :totalSecciones="$totalSecciones"
                            :isConfirmationSection="$isConfirmationSection ?? false" :accionistas="$datosPrevios['accionistas'] ?? []" />
                    @elseif($seccionPartial == 'seccion5')
                        <x-seccion5 :action="route('inscripcion.procesar_seccion')" :method="'POST'" :datos-previos="$datosPrevios ?? []" :is-revisor="false"
                            :seccion="5" :total-secciones="6" :is-confirmation-section="false" :estados="$estados ?? []" />
                    @else
                        @include('inscripcion.secciones.' . $seccionPartial, [
                            'datosPrevios' => $datosPrevios,
                        ])
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>

    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ANIMAR BARRA DE PORCENTAJE
            const progressFill = document.getElementById('progressFill');
            if (progressFill) {
                // Forzamos recalcular el width (trigger transition)
                setTimeout(() => {
                    progressFill.style.width = progressFill.dataset.target + '%';
                }, 80);
            }

            // ANIMAR TRACKER (rayita)
            const tracker = document.querySelector('.progress-tracker');
            const advanceBar = document.getElementById('progressBarAdvance');
            if (tracker && advanceBar) {
                const secciones = tracker.querySelectorAll('.seccion');
                const active = tracker.querySelectorAll('.seccion.active').length;
                // Calcula avance: 0% si 1 sección, 100% si todas, proporcional si no
                let percent = 0;
                if (secciones.length > 1) {
                    percent = ((active - 1) / (secciones.length - 1)) * 100;
                }
                advanceBar.style.width = percent + '%';
            }

            // Lógica para la selección de sectores y actividades
            const sectorSelect = $('#sectores');
            const actividadSelect = $('#actividad');
            const actividadesSeleccionadas = $('#actividades-seleccionadas');
            const tipoPersonaSelect = $('#tipo_persona');
            const objetoSocialGroup = $('#formulario__grupo--objeto_social');
            const selectedActivities = @json($actividadesSeleccionadas ?? []);
            let availableActivities = []; // Store all activities for the selected sector

            function updateActividades() {
                actividadSelect.html('<option value="">Seleccione una actividad</option>');
                const remainingActivities = availableActivities.filter(
                    actividad => !selectedActivities.some(selected => selected.id == actividad.id)
                );
                remainingActivities.forEach(actividad => {
                    const option = $('<option>', {
                        value: actividad.id,
                        text: actividad.nombre
                    });
                    actividadSelect.append(option);
                });
            }

            function isActivitySelected(id) {
                return selectedActivities.some(activity => activity.id == id);
            }

            function removeActivity(id) {
                const index = selectedActivities.findIndex(activity => activity.id == id);
                if (index !== -1) {
                    selectedActivities.splice(index, 1);
                    updateActivityDisplay();
                    updateActividades(); // Refresh dropdown to include removed activity
                }
            }

            function updateActivityDisplay() {
                actividadesSeleccionadas.html('');
                if (selectedActivities.length === 0) {
                    actividadesSeleccionadas.html('<span>Sin actividad seleccionada</span>');
                    $('#actividades_seleccionadas_input').val('');
                    return;
                }
                selectedActivities.forEach(activity => {
                    const activityElement = $('<div>', {
                        class: 'actividad-seleccionada'
                    });
                    const activityText = $('<span>', {
                        text: activity.nombre
                    });
                    const removeButton = $('<button>', {
                        class: 'remove-activity',
                        html: '×',
                        'data-id': activity.id,
                        title: 'Eliminar actividad'
                    }).on('click', function() {
                        removeActivity(activity.id);
                    });
                    activityElement.append(activityText).append(removeButton);
                    actividadesSeleccionadas.append(activityElement);
                });
                $('#actividades_seleccionadas_input').val(JSON.stringify(selectedActivities.map(a => a.id)));
            }

            function addSelectedActivity() {
                const selectedOption = actividadSelect[0].options[actividadSelect[0].selectedIndex];
                if (!selectedOption || selectedOption.value === '') {
                    return;
                }
                const activityId = selectedOption.value;
                const activityName = selectedOption.textContent;
                if (isActivitySelected(activityId)) {
                    return;
                }
                selectedActivities.push({
                    id: activityId,
                    nombre: activityName
                });
                updateActivityDisplay();
                updateActividades(); // Refresh dropdown to remove selected activity
                actividadSelect.val('');
            }

            function toggleObjetoSocial() {
                if (tipoPersonaSelect.length && objetoSocialGroup.length) {
                    const isMoral = tipoPersonaSelect.val() === 'Moral';
                    const isRevisor = @json(auth()->check() && auth()->user()->hasRole('revisor'));
                    objetoSocialGroup.css('display', isMoral || isRevisor ? 'block' : 'none');
                }
            }

            sectorSelect.on('change', function() {
                const sectorId = this.value;
                selectedActivities.length = 0; // Clear selected activities on sector change
                updateActivityDisplay(); // Update display to reflect cleared activities
                if (!sectorId) {
                    actividadSelect.html('<option value="">Seleccione una actividad</option>');
                    availableActivities = [];
                    return;
                }
                $.ajax({
                    url: `/inscripcion/actividades?sector_id=${sectorId}`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        if (data.error) {
                            console.error(data.error);
                            actividadSelect.html(
                                '<option value="">Error al cargar actividades</option>');
                            availableActivities = [];
                            return;
                        }
                        availableActivities = data.actividades;
                        updateActividades();
                    },
                    error: function(error) {
                        console.error('Error:', error);
                        actividadSelect.html(
                            '<option value="">Error al cargar actividades</option>');
                        availableActivities = [];
                    }
                });
            });

            actividadSelect.on('change', function() {
                if (this.value) {
                    addSelectedActivity();
                }
            });

            // Initialize objeto_social visibility
            toggleObjetoSocial();
            tipoPersonaSelect.on('change', toggleObjetoSocial);

            // Handle remove activity buttons for pre-selected activities
            $(document).on('click', '.remove-activity', function() {
                const activityId = $(this).data('id');
                removeActivity(activityId);
            });

            // If we have a pre-selected sector, load its activities
            @if (old('sectores'))
                sectorSelect.val(@json(old('sectores'))).trigger('change');
            @endif
        });
    </script>
@endpush
