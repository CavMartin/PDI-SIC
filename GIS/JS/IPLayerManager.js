class IPLayerManager {
    constructor(map) {
        this.map = map;
        // Definir colores directamente en la configuración de layerGroups
        this.layerGroups = {
            'Aprehendidos': L.layerGroup().addTo(map),
            'Balaceras': L.layerGroup().addTo(map),
            'Heridos': L.layerGroup().addTo(map),
            'Óbitos': L.layerGroup().addTo(map),
            'Otros': L.layerGroup().addTo(map),
        };
        // Asignar colores a cada grupo para usarlos al crear los marcadores
        this.colors = {
            'Aprehendidos': 'blue',
            'Balaceras': 'green',
            'Heridos': 'red',
            'Óbitos': 'black',
            'Otros': 'gray'
        };
    }

    formatearFecha(fecha) {
        if (!fecha) return '';
        const partes = fecha.split('-');
        return partes.length === 3 ? `${partes[2]}/${partes[1]}/${partes[0]}` : fecha;
    }

    construirDireccion(item) {
        let texto = item.L_Calle;
        texto += item.L_AlturaCatastral ? ` N° ${item.L_AlturaCatastral}` : '';
        texto += item.L_CalleDetalle ? `, ${item.L_CalleDetalle}` : '';
        texto += item.L_Interseccion1 ? `, entre ${item.L_Interseccion1}` : '';
        texto += item.L_Interseccion2 ? ` y ${item.L_Interseccion2}` : '';
        texto += `, ${item.L_Localidad}`;
        return texto;
    }

    procesarCoordenadas(coordenadas) {
        if (!coordenadas) return null;
        const partes = coordenadas.split(',').map(Number);
        return partes.length === 2 && !isNaN(partes[0]) && !isNaN(partes[1]) ? partes : null;
    }

    convertirDatosAGeoJSON(data) {
        return data.map(item => {
            const coordenadas = this.procesarCoordenadas(item.L_Coordenadas);
            if (!coordenadas) return null;

            return {
                type: 'Feature',
                geometry: {
                    type: 'Point',
                    coordinates: coordenadas.reverse()
                },
                properties: {
                    ...item,
                    IP_Fecha: this.formatearFecha(item.IP_Fecha),
                    direccionCompleta: this.construirDireccion(item)
                }
            };
        }).filter(feature => feature !== null);
    }

    addMarkerLayer(data) {
        const features = this.convertirDatosAGeoJSON(data);

        features.forEach(feature => {
            const grupoHecho = feature.properties.IP_GrupoHecho || 'Otros';
            const marker = L.marker(feature.geometry.coordinates.reverse(), {
                icon: L.divIcon({
                    className: 'custom-div-icon',
                    iconSize: [20, 20],
                    html: `<div style="background-color: ${this.colors[grupoHecho]};" class="custom-marker"></div>`
                })
            }).bindPopup(this.generarPopupContent(feature));

            this.layerGroups[grupoHecho].addLayer(marker);
        });
    }

    generarPopupContent(feature) {
        const fechaFormateada = this.formatearFecha(feature.properties.Fecha);
        return `
            <br><b>ID:</b> ${feature.properties.ID}
            <br><b>Fecha:</b> ${fechaFormateada}
            <br><b>Tipo:</b> ${feature.properties.Tipo}
            <br><b>Juzgado:</b> ${feature.properties.Juzgado}
            <br><b>Dependencia:</b> ${feature.properties.Dependencia}
            <br><b>Causa:</b> ${feature.properties.Causa}
            <br><b>Lugar del hecho:</b> ${feature.properties.direccionCompleta}
            <br><b>Relato:</b> ${feature.properties.Relato}
        `;
    }

    cargarDatos(url) {
        fetch(url)
            .then(response => response.json())
            .then(data => this.addMarkerLayer(data))
            .catch(error => console.error('Error al cargar los datos:', error));
    }
    
}