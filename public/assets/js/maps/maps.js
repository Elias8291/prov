window.initMap = async function() {
    const mapContainer = document.getElementById('map-container');
    if (!mapContainer) {
        console.error('Map container not found');
        return;
    }

    // Select spans using data- attributes
    const codigoPostal = document.querySelector('#form-step-2 [data-codigo-postal]')?.dataset.codigoPostal?.trim() || 'No disponible';
    const estado = document.querySelector('#form-step-2 [data-estado]')?.dataset.estado?.trim() || 'No disponible';
    const municipio = document.querySelector('#form-step-2 [data-municipio]')?.dataset.municipio?.trim() || 'No disponible';
    const colonia = document.querySelector('#form-step-2 [data-colonia]')?.dataset.colonia?.trim() || 'No disponible';
    const calle = document.querySelector('#form-step-2 [data-calle]')?.dataset.calle?.trim() || 'No disponible';
    const numeroExterior = document.querySelector('#form-step-2 [data-numero-exterior]')?.dataset.numeroExterior?.trim() || 'No disponible';
    const numeroInterior = document.querySelector('#form-step-2 [data-numero-interior]')?.dataset.numeroInterior?.trim() || 'No disponible';

    // Check for required fields
    if (!codigoPostal || !estado || !municipio || !colonia || !calle || !numeroExterior || 
        codigoPostal === 'No disponible' || estado === 'No disponible' || municipio === 'No disponible' || 
        colonia === 'No disponible' || calle === 'No disponible' || numeroExterior === 'No disponible') {
        mapContainer.innerHTML = '<p style="color: #dc2626; text-align: center; padding: 20px;">Faltan datos de la dirección.</p>';
        return;
    }

    // Construct full address
    let fullAddress = `${calle} ${numeroExterior}`;
    if (numeroInterior && numeroInterior !== 'No disponible') fullAddress += ` ${numeroInterior}`;
    fullAddress += `, ${colonia}, ${municipio}, ${estado}, ${codigoPostal}, México`;

    try {
        // Load Google Maps libraries
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
        const { Map } = await google.maps.importLibrary("maps");
        const { Geocoder } = await google.maps.importLibrary("geocoding");

        // Initialize map
        const map = new Map(mapContainer, {
            zoom: 15,
            center: { lat: 19.432608, lng: -99.133209 },
            mapId: 'TU_MAP_ID_AQUÍ' // Replace with your actual Map ID
        });

        // Geocode address
        const geocoder = new Geocoder();
        geocoder.geocode({ address: fullAddress }, (results, status) => {
            if (status === 'OK' && results[0]) {
                map.setCenter(results[0].geometry.location);
                new AdvancedMarkerElement({
                    map: map,
                    position: results[0].geometry.location,
                    title: fullAddress
                });
            } else {
                console.error('Geocode failed: ' + status);
                mapContainer.innerHTML = '<p style="color: #dc2626; text-align: center; padding: 20px;">No se pudo cargar el mapa. Verifique la dirección proporcionada.</p>';
            }
        });
    } catch (error) {
        console.error('Error loading Google Maps libraries: ', error);
        mapContainer.innerHTML = '<p style="color: #dc2626; text-align: center; padding: 20px;">Error al cargar el mapa.</p>';
    }
};