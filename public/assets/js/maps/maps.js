function initMap() {
    // Get address fields from the DOM
    const codigoPostal = document.querySelector('#form-step-2 [for="codigo_postal"] + .data-field').textContent.trim();
    const estado = document.querySelector('#form-step-2 [for="estado"] + .data-field').textContent.trim();
    const municipio = document.querySelector('#form-step-2 [for="municipio"] + .data-field').textContent.trim();
    const colonia = document.querySelector('#form-step-2 [for="colonia"] + .data-field').textContent.trim();
    const calle = document.querySelector('#form-step-2 [for="calle"] + .data-field').textContent.trim();
    const numeroExterior = document.querySelector('#form-step-2 [for="numero_exterior"] + .data-field').textContent.trim();
    const numeroInterior = document.querySelector('#form-step-2 [for="numero_interior"] + .data-field').textContent.trim();

    // Construct the full address
    let fullAddress = `${calle} ${numeroExterior}`;
    if (numeroInterior !== 'No disponible') fullAddress += ` ${numeroInterior}`;
    fullAddress += `, ${colonia}, ${municipio}, ${estado}, ${codigoPostal}, México`;

    // Initialize the map
    const mapContainer = document.getElementById('map-container');
    const map = new google.maps.Map(mapContainer, {
        zoom: 15,
        center: { lat: 19.432608, lng: -99.133209 } // Default center (Mexico City)
    });

    // Geocode the address
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
            console.error('Geocode was not successful for the following reason: ' + status);
            mapContainer.innerHTML = '<p style="color: #dc2626; text-align: center; padding: 20px;">No se pudo cargar el mapa. Verifique la dirección proporcionada.</p>';
        }
    });
}