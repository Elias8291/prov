window.initMap = function() {
    const mapContainer = document.getElementById('map-container');
    if (!mapContainer) {
        console.error('Map container not found');
        return;
    }

    const codigoPostal = document.querySelector('#form-step-2 [for="codigo_postal"] + .data-field')?.textContent.trim();
    const estado = document.querySelector('#form-step-2 [for="estado"] + .data-field')?.textContent.trim();
    const municipio = document.querySelector('#form-step-2 [for="municipio"] + .data-field')?.textContent.trim();
    const colonia = document.querySelector('#form-step-2 [for="colonia"] + .data-field')?.textContent.trim();
    const calle = document.querySelector('#form-step-2 [for="calle"] + .data-field')?.textContent.trim();
    const numeroExterior = document.querySelector('#form-step-2 [for="numero_exterior"] + .data-field')?.textContent.trim();
    const numeroInterior = document.querySelector('#form-step-2 [for="numero_interior"] + .data-field')?.textContent.trim();

    if (!codigoPostal || !estado || !municipio || !colonia || !calle || !numeroExterior) {
        mapContainer.innerHTML = '<p style="color: #dc2626; text-align: center; padding: 20px;">Faltan datos de la dirección.</p>';
        return;
    }

    let fullAddress = `${calle} ${numeroExterior}`;
    if (numeroInterior && numeroInterior !== 'No disponible') fullAddress += ` ${numeroInterior}`;
    fullAddress += `, ${colonia}, ${municipio}, ${estado}, ${codigoPostal}, México`;

    const map = new google.maps.Map(mapContainer, {
        zoom: 15,
        center: { lat: 19.432608, lng: -99.133209 }
    });

    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: fullAddress }, (results, status) => {
        if (status === 'OK' && results[0]) {
            map.setCenter(results[0].geometry.location);
            new google.maps.Marker({
                map: map,
                position: results[0].geometry.location,
                title: fullAddress
            });
        } else {
            console.error('Geocode failed: ' + status);
            mapContainer.innerHTML = '<p style="color: #dc2626; text-align: center; padding: 20px;">No se pudo cargar el mapa. Verifique la dirección proporcionada.</p>';
        }
    });
};