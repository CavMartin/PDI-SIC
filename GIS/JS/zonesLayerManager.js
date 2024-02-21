async function cargarCapaGeoJSON(url, nombreCapa, color = 'grey', fillColor = 'grey') {
    try {
        const response = await fetch(url);
        const data = await response.json();

        var geoJsonLayer = L.geoJSON(data, {
            onEachFeature: (feature, layer) => {
                var popupContent = `<b>Nombre del cuadrante:</b> ${feature.properties.Nombre}<br><b>ID:</b> ${feature.properties.ID}`;
                layer.bindPopup(popupContent);
                layer.setStyle({
                    color: color, // Usa el color especificado o 'grey' si no se proporciona
                    fillColor: fillColor, // Usa el fillColor especificado o 'grey' si no se proporciona
                    fillOpacity: 0.5
                });
            }
        }).addTo(map); // Añade la capa al mapa para que esté seleccionada por defecto

        // Asegurarse de que el grupo "Zonas Priorizadas" esté definido
        if (!groupedOverlays["Zonas Priorizadas"]) {
            groupedOverlays["Zonas Priorizadas"] = {};
        }

        // Añadir la capa GeoJSON al grupo "Zonas Priorizadas" con el nombre especificado
        groupedOverlays["Zonas Priorizadas"][nombreCapa] = geoJsonLayer;
    } catch (error) {
        console.error("Error al cargar el archivo GeoJSON:", error);
    }
}
