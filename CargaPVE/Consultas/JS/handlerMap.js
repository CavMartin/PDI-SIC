class MapHandler {
    constructor(mapContainerId, defaultLat = -31.31, defaultLng = -61.08, defaultZoom = 7) {
        this.mapContainerId = mapContainerId; // ID del contenedor HTML donde se renderizará el mapa
        this.defaultLat = defaultLat; // Latitud por defecto para centrar el mapa
        this.defaultLng = defaultLng; // Longitud por defecto para centrar el mapa
        this.defaultZoom = defaultZoom; // Nivel de zoom por defecto del mapa
        this.map = null; // Objeto Leaflet para el mapa, inicializado más tarde
        this.data = []; // Almacena los datos obtenidos de la consulta
        this.isPopulated = false; // Bandera para verificar si el mapa ya ha sido populado con datos
        this.layerControl = null; // Control de capas de Leaflet para alternar entre diferentes capas en el mapa
        this.coordinatesControl = null; // Control para copiar coordenadas
        this.processedMarkers = []; // Almacena los datos procesados para GIS
        this.markerClusterGroup = null; // Grupo de clústeres para agrupar los marcadores del mapa
        this.searchControl = null; // Control de búsqueda
        this.drawnItems = new L.FeatureGroup(); // Almacena los polígonos dibujados
        this.selectedLayer = null; // Almacena el polígono o círculo actualmente seleccionado
        this.heatmapPoints = []; // Puntos para el mapa de calor
        this.heatmapLayer = null; // Capa de mapa de calor, inicializada más tarde
        this.heatmapAddedToControl = false; // Bandera para verificar si el heatmap se ha agregado al control de capas
        this.contextMenuItems = []; // Configurar elementos del menú contextual
    }

    initializeMap() {
        if (!this.map) {
            this.map = L.map(this.mapContainerId, {
                center: [this.defaultLat, this.defaultLng], // Centrar el mapa en la latitud y longitud predeterminadas
                zoom: this.defaultZoom, // Nivel de zoom predeterminado
                zoomControl: false, // Desactiva el control de zoom por defecto
                contextmenu: true,  // Habilitar el menú contextual
                contextmenuItems: [
                    {
                        text: 'Centrar Mapa Aquí',
                        callback: this.centerMapHere.bind(this), // Opción de centrar el mapa en la posición del clic
                    },
                    {
                        text: 'Zoom +',
                        callback: this.zoomInHere.bind(this), // Opción de hacer zoom in
                    },
                    {
                        text: 'Zoom -',
                        callback: this.zoomOutHere.bind(this), // Opción de hacer zoom out
                    }
                ]
            });

            // Añadir capa de mapa de fondo
            L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, &copy; <a href="https://carto.com/attributions">CARTO</a>'
            }).addTo(this.map);

            // Añadir los controles de zoom en la esquina inferior derecha
            L.control.zoom({
                position: 'bottomright' // Posición en la esquina inferior derecha
            }).addTo(this.map);

            // Inicializar el dropdown en la esquina superior derecha del mapa
            this.addDropdownControl();

            // Inicializar el grupo de clústeres
            this.markerClusterGroup = L.markerClusterGroup(); 
            this.map.addLayer(this.markerClusterGroup);

            // Añadir el control de coordenadas
            this.coordinatesControl = new L.Control.Coordinates();
            this.coordinatesControl.addTo(this.map);
            this.map.on('click', (e) => {
                this.coordinatesControl.setCoordinates(e); // Actualizar coordenadas al hacer clic en el mapa
            });

            // Configurar la capa de mapa de calor con un gradiente personalizado
            const cfg = {
                radius: 0.004, // Radio de los puntos en el mapa de calor
                maxOpacity: 1,
                scaleRadius: true, // Escalar el radio según el zoom
                useLocalExtrema: false,
                latField: 'lat',
                lngField: 'lng',
                valueField: 'count',
                gradient: {
                    0.1: 'yellow',
                    0.5: 'orange',
                    0.9: 'red'
                }
            };
            this.heatmapLayer = new HeatmapOverlay(cfg);

            // Añadir el control de capas
            this.layerControl = L.control.groupedLayers(null, {
                "Menú de selección de capas": {
                    "Posibles PVE": this.markerClusterGroup // Añadir los clústeres al control de capas
                }
            }, {
                collapsed: false // Evitar que el menú de capas se colapse
            }).addTo(this.map);

            // Añadir el control de búsqueda de leaflet-search
            this.searchControl = new L.Control.Search({
                layer: this.markerClusterGroup,
                position: 'topleft',
                propertyName: 'title',
                moveToLocation: function(latlng, title, map) {
                    map.setView(latlng, 17); // Ajustar el zoom cuando se encuentra un marcador
                }
            }).addTo(this.map);

            // Añadir el botón de pantalla completa
            this.map.addControl(new L.Control.Fullscreen({
                position: 'topleft',
                title: 'Pantalla completa',
                titleCancel: 'Salir de pantalla completa'
            }));

            // Añadir el grupo de capas dibujadas
            this.map.addLayer(this.drawnItems);

            // Configurar las opciones de dibujo
            const drawControl = new L.Control.Draw({
                edit: {
                    featureGroup: this.drawnItems,
                },
                draw: {
                    polygon: {
                        allowIntersection: false,
                        shapeOptions: {
                            color: '#000000', // Color por defecto para los polígonos
                            weight: 4, // Grosor de la línea del polígono
                        },
                        contextmenu: true, // Habilitar menú contextual para polígonos
                        contextmenuItems: this.contextMenuItems, // Elementos del menú contextual para polígonos
                    },
                    circle: {
                        shapeOptions: {
                            color: '#000000', // Color por defecto para los círculos
                            weight: 4, // Grosor de la línea del círculo
                        },
                        contextmenu: true, // Habilitar menú contextual para círculos
                        contextmenuItems: this.contextMenuItems, // Elementos del menú contextual para círculos
                    },
                    marker: false,
                    circlemarker: false,
                    polyline: false,
                    rectangle: false,
                }
            });
            this.map.addControl(drawControl);

            // Manejar el evento de creación de polígonos/círculos
            this.map.on(L.Draw.Event.CREATED, (event) => {
                const layer = event.layer;

                // Añadir el menú contextual al polígono/círculo
                layer.bindContextMenu({
                    contextmenu: true,
                    contextmenuItems: [
                        '-',
                        {
                            text: 'Descargar datos',
                            callback: () => this.selectPointsWithinPolygon(layer)
                        }
                    ]
                });

                // Manejar el evento de clic para cambiar el color
                layer.on('click', () => {
                    if (this.selectedLayer === layer) {
                        this.deselectLayer(); // Si se hace clic sobre el mismo polígono/círculo, deseleccionarlo
                    } else {
                        this.highlightLayer(layer); // De lo contrario, seleccionarlo
                    }
                });

                this.drawnItems.addLayer(layer);
            });

            // Configurar el menú contextual global del mapa
            this.map.options.contextmenuItems = [
                {
                    text: 'Centrar Mapa Aquí',
                    callback: (e) => this.centerMapHere(e)
                },
                {
                    text: 'Zoom +',
                    callback: (e) => this.zoomInHere(e)
                },
                {
                    text: 'Zoom -',
                    callback: (e) => this.zoomOutHere(e)
                }
            ];

            // Añadir el botón de impresión
            L.easyPrint({
                title: 'Descargar mapa',
                position: 'topleft',
                filename: 'Captura_Mapa_PVE',
                exportOnly: true, // No abre el diálogo de impresión
                sizeModes: ['A4Portrait', 'A4Landscape'] // Opciones de tamaño
            }).addTo(this.map);
        }
    }

    // Método para agregar el dropdown en el mapa
    addDropdownControl() {
        const userRole = this.getUserRoleFromSession(); // Función para obtener el rol de la sesión

        const menuItems = [
            {
                html: '<i class="bi bi-fire"></i> <b class="text-danger" style="cursor: pointer;">Generar mapa de calor</b>', 
                title: 'Generar mapa de calor',
                afterClick: () => {
                    this.configureHeatMap();
                },
            }
        ];

        if (userRole <= 2) { // Si el rol es 2 o menos
            menuItems.push({
                html: '<i class="bi bi-download"></i> <b class="text-success" style="cursor: pointer;">Descargar mapa web</b>', 
                title: 'Descargar mapa web',
                afterClick: () => { 
                    this.downloadMap();
                },
            });
        }

        const dropdownControl = new L.Control.BootstrapDropdowns({
            position: "topleft", // Posición en la parte superior izquierda
            className: "btn-lg", // Clase CSS personalizada
            menuItems: menuItems,
        });

        dropdownControl.addTo(this.map); 

        // Aplicar desplazamiento para evitar que se superponga con otros controles
        const dropdownContainer = dropdownControl.getContainer();
        dropdownContainer.classList.add('dropend'); // Hace que el menú se despliegue a la derecha
    }

    getUserRoleFromSession() {
        return parseInt(document.getElementById('userRole').value, 10);
    }

    // Manejar el menú contextual en polígonos/círculos
    onPolygonContextMenu(e) {
        const layer = e.relatedTarget || e.target;
        this.selectPointsWithinPolygon(layer);
    }

    // Método para resaltar un polígono o círculo y restaurar el color de los demás
    highlightLayer(layer) {
        // Restaurar el color del polígono/círculo previamente seleccionado
        if (this.selectedLayer) {
            this.selectedLayer.setStyle({ color: '#000000' }); // Cambiar este valor al color original
        }

        // Establecer el nuevo polígono/círculo como seleccionado y cambiar su color
        this.selectedLayer = layer;
        layer.setStyle({ color: 'red' }); // Cambiar este valor al color de resaltado deseado
    }

    // Método para deseleccionar el polígono o círculo actual
    deselectLayer() {
        if (this.selectedLayer) {
            this.selectedLayer.setStyle({ color: '#000000' }); // Restaurar el color original
            this.selectedLayer = null; // Limpiar la selección
        }
    }

    // Centrar el mapa en la posición del menú contextual
    centerMapHere(e) {
        this.map.panTo(e.latlng);
    }

    // Hacer zoom in en la posición del menú contextual
    zoomInHere(e) {
        this.map.zoomIn();
    }

    // Hacer zoom out en la posición del menú contextual
    zoomOutHere(e) {
        this.map.zoomOut();
    }

    // Método para procesar los datos
    processData(data) {
        this.processedMarkers = [];
        this.heatmapPoints = [];

        const processedLugares = new Set();

        data.forEach(item => {
            if (item.Lugares && Array.isArray(item.Lugares)) {
                item.Lugares.forEach(lugar => {
                    if (!processedLugares.has(lugar.ID_Lugar)) {
                        const coords = this.processLugar(item, lugar);
                        if (coords) {
                            this.heatmapPoints.push(coords);
                            processedLugares.add(lugar.ID_Lugar);
                        }
                    }
                });
            } else if (item.L_Coordenadas && !processedLugares.has(item.ID_Lugar)) {
                const coords = this.processLugar(item, item);
                if (coords) {
                    this.heatmapPoints.push(coords);
                    processedLugares.add(item.ID_Lugar);
                }
            }
        });
    }

    // Método auxiliar para procesar un lugar y agregarlo a los puntos y marcadores
    processLugar(item, lugar) {
        if (lugar.L_Coordenadas) {
            const coords = lugar.L_Coordenadas.split(',').map(coord => parseFloat(coord.trim()));
            if (coords.length === 2 && !isNaN(coords[0]) && !isNaN(coords[1])) {
                const color = item.Fuente === '0800' ? 'green' : item.Fuente === '911' ? 'blue' : 'gray';

                const icon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background-color: ${color}; width: 12px; height: 12px; border-radius: 50%;"></div>`,
                    iconSize: [12, 12]
                });

                let textoDireccion = lugar.L_Calle || 'Sin datos';
                if (lugar.L_AlturaCatastral) textoDireccion += ' N° ' + lugar.L_AlturaCatastral;
                if (lugar.L_CalleDetalle) textoDireccion += ', ' + lugar.L_CalleDetalle;
                if (lugar.L_Interseccion1) {
                    textoDireccion += lugar.L_Interseccion2 ? `, entre ${lugar.L_Interseccion1} y ${lugar.L_Interseccion2}` : ' y ' + lugar.L_Interseccion1;
                }

                // Sanitizar textos en el popup
                textoDireccion = this.sanitizeText(textoDireccion);
                const relatoSanitizado = this.sanitizeText(item.Relato);

                const marker = L.marker([coords[0], coords[1]], { icon, title: textoDireccion }).bindPopup(`
                    <b>FORMULARIO N°:</b> ${item.Formulario}<br>
                    <b>FECHA:</b> ${item.Fecha.split('-').reverse().join('/')}<br>
                    <b>FUENTE:</b> ${item.Fuente}<br>
                    <b>REPORTE ASOCIADO:</b> ${item.ReporteAsociado}<br>
                    <b>TIPOLOGÍA:</b> ${item.Tipologia}<br>
                    <b>MODALIDAD COMISIVA:</b> ${item.ModalidadComisiva}<br>
                    <b>TIPO DE ESTUPEFACIENTES:</b> ${item.TipoEstupefaciente}<br>
                    <b>DOMICILIO:</b> ${textoDireccion}<br>
                    <b>LOCALIDAD:</b> ${lugar.L_Localidad}<br>
                    <b>RELATO DEL HECHO:</b> <div class="justified-text">${relatoSanitizado}</div><br>
                `);

                this.processedMarkers.push(marker);
                return { lat: coords[0], lng: coords[1], count: 0.1 };
            }
        }
        return null;
    }

    // Método para cargar los marcadores al mapa
    populateMap(data) {
        this.clearMap();
        this.processData(data);

        this.processedMarkers.forEach(marker => {
            this.markerClusterGroup.addLayer(marker);
        });

        this.isPopulated = true;
    }

    // Método para mostrar el SweetAlert y obtener los parámetros del mapa de calor
    configureHeatMap() {
        Swal.fire({
            title: 'Configurar Mapa de Calor',
            width: '40rem',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Generar mapa de calor',
            html: `
                <div class="container-fluid p-3">
                    <div class="col">
                        <div class="input-group mb-3">
                            <span class="input-group-text fw-bold col-8">OPCACIDAD MÁXIMA:</span>
                            <input type="number" id="maxOpacity" class="form-control text-center col-4" value="1" min="0" max="1" step="0.1">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold col-8" for="gradientValue1">COLOR 1:</label>
                                <input type="number" id="gradientValue1" class="form-control text-center" placeholder="0.1" value="0.1" min="0" max="1" step="0.1">
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <input type="color" class="form-control form-control-color" id="gradientColor1" value="#ffff00" title="Choose your color">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold col-8" for="gradientValue2">COLOR 2:</label>
                                <input type="number" id="gradientValue2" class="form-control text-center" placeholder="0.5" value="0.5" min="0" max="1" step="0.1">
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <input type="color" class="form-control form-control-color" id="gradientColor2" value="#ffa500" title="Choose your color">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold col-8" for="gradientValue3">COLOR 3:</label>
                                <input type="number" id="gradientValue3" class="form-control text-center" placeholder="0.9" value="0.9" min="0" max="1" step="0.1">
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <input type="color" class="form-control form-control-color" id="gradientColor3" value="#ff0000" title="Choose your color">
                            </div>
                        </div>
                    </div>
                </div>
            `,
            focusConfirm: false,
            didOpen: () => {
                // Lógica para validar y ajustar los valores de los gradientes
                document.getElementById('gradientValue1').addEventListener('input', function() {
                    const value1 = parseFloat(this.value);
                    const value2 = parseFloat(document.getElementById('gradientValue2').value);
                
                    if (value1 >= value2) {
                        this.value = value2 - 0.1; // Ajusta el valor para que sea menor
                    }
                });

                document.getElementById('gradientValue2').addEventListener('input', function() {
                    const value2 = parseFloat(this.value);
                    const value1 = parseFloat(document.getElementById('gradientValue1').value);
                    const value3 = parseFloat(document.getElementById('gradientValue3').value);
                
                    if (value2 <= value1) {
                        this.value = value1 + 0.1; // Ajusta el valor para que sea mayor que el valor 1
                    }
                
                    if (value2 >= value3) {
                        this.value = value3 - 0.1; // Ajusta el valor para que sea menor que el valor 3
                    }
                });

                document.getElementById('gradientValue3').addEventListener('input', function() {
                    const value3 = parseFloat(this.value);
                    const value2 = parseFloat(document.getElementById('gradientValue2').value);
                
                    if (value3 <= value2) {
                        this.value = value2 + 0.1; // Ajusta el valor para que sea mayor
                    }
                });
            },
            preConfirm: () => {
                const maxOpacity = document.getElementById('maxOpacity').value;
                const gradientValue1 = parseFloat(document.getElementById('gradientValue1').value);
                const gradientValue2 = parseFloat(document.getElementById('gradientValue2').value);
                const gradientValue3 = parseFloat(document.getElementById('gradientValue3').value);

                const color1 = document.getElementById('gradientColor1').value;
                const color2 = document.getElementById('gradientColor2').value;
                const color3 = document.getElementById('gradientColor3').value;

                const gradientColors = {
                    [gradientValue1]: color1,
                    [gradientValue2]: color2,
                    [gradientValue3]: color3,
                };

                return { maxOpacity: parseFloat(maxOpacity), gradient: gradientColors };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this.generateHeatMap(result.value);
            }
        });        
    }

    // Método para generar el mapa de calor usando los parámetros personalizados
    generateHeatMap(config = {}) {
        const heatMapData = {
            data: this.heatmapPoints
        };

        // Verificar si ya existe una capa de mapa de calor y eliminarla del mapa
        if (this.heatmapLayer && this.map.hasLayer(this.heatmapLayer)) {
            this.map.removeLayer(this.heatmapLayer);
        }

        // Configurar el mapa de calor con los parámetros proporcionados
        const heatmapConfig = {
            radius: 0.004,
            maxOpacity: config.maxOpacity || 1,
            scaleRadius: true,
            useLocalExtrema: false,
            latField: 'lat',
            lngField: 'lng',
            valueField: 'count',
            gradient: config.gradient || { 0.1: 'yellow', 0.5: 'orange', 0.9: 'red' }
        };

        // Crear una nueva capa de mapa de calor con la configuración actualizada
        this.heatmapLayer = new HeatmapOverlay(heatmapConfig);

        // Añadir la nueva capa de mapa de calor al mapa
        this.heatmapLayer.addTo(this.map);
        this.heatmapLayer.setData(heatMapData);

        // Asegurarse de que solo el mapa de calor o el grupo de clústeres esté visible
        if (this.map.hasLayer(this.markerClusterGroup)) {
            this.map.removeLayer(this.markerClusterGroup);
        }

        // Eliminar y recrear el control de capas para evitar inconsistencias
        if (this.layerControl) {
            this.map.removeControl(this.layerControl);
        }

        this.layerControl = L.control.groupedLayers(null, {
            "Menú de selección de capas": {
                "Posibles PVE": this.markerClusterGroup, // Otras capas que necesites incluir
                "Mapa de Calor": this.heatmapLayer,
            }
        }, {
            collapsed: false // Evitar que el menú de capas se colapse
        }).addTo(this.map);

        this.cleanEmptyLabels();
    }

    // Método para manejar la seleccion de marcadores dentro de los poligonos y extraer sus datos
    selectPointsWithinPolygon(polygonLayer) {
        const selectedPoints = [];

        this.markerClusterGroup.eachLayer((marker) => {
            if (polygonLayer.getBounds().contains(marker.getLatLng())) {
                const latlng = marker.getLatLng();
                const popupContent = marker.getPopup().getContent();

                const data = this.parsePopupContent(popupContent); // Extraer cada campo del popup con el método parsePopupContent

                data.LATITUD = latlng.lat;  // Agregar campo para latitud
                data.LONGITUD = latlng.lng; // Agregar campo para longitud

                selectedPoints.push(data);
            }
        });
        Swal.fire({ // Mostrar ventana modal con SweetAlert
            title: 'Seleccione el formato de sus datos',
            icon: 'question',
            width: '50rem',
            showCancelButton: false,
            showConfirmButton: false,
            html: `
                <div class="container-fluid p-3">
                    <div class="row justify-content-center">
                        <div class="col-md-3 justify-content-center m-2">
                            <button type="button" class="btn btn-outline-primary btn-lg fs-3" style="width: 11rem; background-image : url(../../CSS/Images/Word.png); background-size: 20%; background-repeat: no-repeat; background-position: left center; display: inline-block;" id="downloadPointsToWordBtn">
                                <b>WORD</b>
                            </button>
                        </div>
                        <div class="col-md-3 justify-content-center m-2">
                            <button type="button" class="btn btn-outline-secondary btn-lg fs-3" style="width: 11rem; background-image : url(../../CSS/Images/CSV.png); background-size: 20%; background-repeat: no-repeat; background-position: left center; display: inline-block;" id="downloadPointsToCSVBtn">
                                <b>CSV</b>
                            </button>
                        </div>
                        <div class="col-md-3 justify-content-center m-2">
                            <button type="button" class="btn btn-outline-success btn-lg fs-3" style="width: 11rem; background-image : url(../../CSS/Images/Excel.png); background-size: 20%; background-repeat: no-repeat; background-position: left center; display: inline-block;" id="downloadPointsToExcelBtn">
                                <b>EXCEL</b>
                            </button>
                        </div>
                    </div>
                </div>
            `,
            didOpen: () => { // Asignar eventos a los botones
                document.getElementById('downloadPointsToExcelBtn').addEventListener('click', () => {
                    Swal.close(); // Cerrar el modal actual antes de realizar la acción
                    this.exportSelectedPointsToExcel(selectedPoints);
                });
                document.getElementById('downloadPointsToWordBtn').addEventListener('click', () => {
                    Swal.close(); // Cerrar el modal actual antes de realizar la acción
                    this.exportSelectedPointsToWord(selectedPoints);
                });
                document.getElementById('downloadPointsToCSVBtn').addEventListener('click', () => {
                    Swal.close(); // Cerrar el modal actual antes de realizar la acción
                    this.exportSelectedPointsToCSV(selectedPoints);
                });
            }
        });         
    }

    // Método para obtener los datos de los PopUp de cada marcador seleccionados
    parsePopupContent(popupContent) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(popupContent, 'text/html');
    
        return {
            FORMULARIO: doc.querySelector('b:nth-of-type(1)').nextSibling.nodeValue.trim(),
            FECHA: doc.querySelector('b:nth-of-type(2)').nextSibling.nodeValue.trim(),
            FUENTE: doc.querySelector('b:nth-of-type(3)').nextSibling.nodeValue.trim(),
            REPORTE_ASOCIADO: doc.querySelector('b:nth-of-type(4)').nextSibling.nodeValue.trim(),
            TIPOLOGÍA: doc.querySelector('b:nth-of-type(5)').nextSibling.nodeValue.trim(),
            MODALIDAD_COMISIVA: doc.querySelector('b:nth-of-type(6)').nextSibling.nodeValue.trim(),
            TIPO_DE_ESTUPEFACIENTE: doc.querySelector('b:nth-of-type(7)').nextSibling.nodeValue.trim(),
            DOMICILIO: doc.querySelector('b:nth-of-type(8)').nextSibling.nodeValue.trim(),
            LOCALIDAD: doc.querySelector('b:nth-of-type(9)').nextSibling.nodeValue.trim(),
            RELATO: doc.querySelector('div.justified-text').textContent.trim()
        };
    }

    // Método para convertir los datos de los marcadores seleccionados en un archivo CSV
    exportSelectedPointsToCSV(selectedPoints) {
        // Convertir el array de objetos a CSV
        const csvContent = selectedPoints.map(point => {
            return Object.values(point).map(value => `"${value}"`).join(',');
        }).join('\n');

        // Agregar encabezados de columnas
        const headers = Object.keys(selectedPoints[0]).map(key => `"${key}"`).join(',');
        const csvData = `${headers}\n${csvContent}`;

        // Crear un Blob y disparar la descarga
        const blob = new Blob([csvData], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = "HORUS_PVE.csv";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url); // Limpiar la URL después de la descarga
    }

    // Método para convertir los datos de los marcadores seleccionados en un archivo Xlsx
    exportSelectedPointsToExcel(selectedPoints) {
        selectedPoints.forEach(point => { // Reemplazar comas por puntos en las coordenadas
            if (point.LATITUD) {
                point.LATITUD = point.LATITUD.toString().replace(',', '.');
            }
            if (point.LONGITUD) {
                point.LONGITUD = point.LONGITUD.toString().replace(',', '.');
            }
        });

        const worksheet = XLSX.utils.json_to_sheet(selectedPoints); // Convertir los objetos a una hoja de calculo

        const workbook = XLSX.utils.book_new(); // Crear un libro excel
        XLSX.utils.book_append_sheet(workbook, worksheet, "HORUS");

        XLSX.writeFile(workbook, "HORUS_PVE.xlsx"); // Guardar el archivo Excel
    }

    // Método para convertir los datos de los marcadores seleccionados en un archivo Doc
    exportSelectedPointsToWord(selectedPoints) {
        // Crear el documento
        const doc = new docx.Document({
            sections: [
                {
                    properties: {},
                    children: selectedPoints.flatMap(point => {
                        return [
                            new docx.Paragraph({
                                children: [
                                    new docx.TextRun({
                                        text: `FORMULARIO N°: `,
                                        bold: true,
                                    }),
                                    new docx.TextRun(`${point.FORMULARIO}`),
                                ],
                            }),
                            new docx.Paragraph({
                                children: [
                                    new docx.TextRun({
                                        text: `FECHA: `,
                                        bold: true,
                                    }),
                                    new docx.TextRun(`${point.FECHA}`),
                                ],
                            }),
                            new docx.Paragraph({
                                children: [
                                    new docx.TextRun({
                                        text: `FUENTE: `,
                                        bold: true,
                                    }),
                                    new docx.TextRun(`${point.FUENTE}`),
                                ],
                            }),
                            new docx.Paragraph({
                                children: [
                                    new docx.TextRun({
                                        text: `REPORTE ASOCIADO: `,
                                        bold: true,
                                    }),
                                    new docx.TextRun(`${point.REPORTE_ASOCIADO}`),
                                ],
                            }),
                            new docx.Paragraph({
                                children: [
                                    new docx.TextRun({
                                        text: `TIPOLOGÍA: `,
                                        bold: true,
                                    }),
                                    new docx.TextRun(`${point.TIPOLOGÍA}`),
                                ],
                            }),
                            new docx.Paragraph({
                                children: [
                                    new docx.TextRun({
                                        text: `MODALIDAD COMISIVA: `,
                                        bold: true,
                                    }),
                                    new docx.TextRun(`${point.MODALIDAD_COMISIVA}`),
                                ],
                            }),
                            new docx.Paragraph({
                                children: [
                                    new docx.TextRun({
                                        text: `TIPO DE ESTUPEFACIENTE: `,
                                        bold: true,
                                    }),
                                    new docx.TextRun(`${point.TIPO_DE_ESTUPEFACIENTE}`),
                                ],
                            }),
                            new docx.Paragraph({
                                children: [
                                    new docx.TextRun({
                                        text: `DOMICILIO: `,
                                        bold: true,
                                    }),
                                    new docx.TextRun(`${point.DOMICILIO}`),
                                ],
                            }),
                            new docx.Paragraph({
                                children: [
                                    new docx.TextRun({
                                        text: `LOCALIDAD: `,
                                        bold: true,
                                    }),
                                    new docx.TextRun(`${point.LOCALIDAD}`),
                                ],
                            }),
                            new docx.Paragraph({
                                children: [
                                    new docx.TextRun({
                                        text: `RELATO DEL HECHO:`,
                                        bold: true,
                                    }),
                                    new docx.TextRun(` ${point.RELATO}`),
                                ],
                            }),
                            new docx.Paragraph(" "), // Salto de línea adicional entre registros
                        ];
                    })
                }
            ]
        });
    
        // Exportar el archivo a .docx
        docx.Packer.toBlob(doc).then(blob => {
            // Crear un enlace temporal para descargar el archivo
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = "HORUS_PVE.docx";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url); // Limpiar la URL después de la descarga
        }).catch(error => {
            console.error("Error al generar el documento Word: ", error);
        });
    }
    
    // Método para forzar el redimensionamiento del mapa
    forceResizeMap() {
        if (this.map) {
            setTimeout(() => {
                this.map.invalidateSize(); // Forzar el redimensionamiento del mapa
            }, 200); // Un pequeño retraso para asegurarse de que el contenedor esté correctamente dimensionado
        }
    }
    
    // Método para limpiar las capas del mapa
    clearMap() {
        if (this.map) {
            if (this.markerClusterGroup) {
                this.markerClusterGroup.clearLayers(); // Limpiar todos los marcadores del grupo de clústeres
            }

            if (this.heatmapLayer) {
                this.heatmapLayer.setData({ data: [] }); // Limpiar todos los puntos del mapa de calor
            }

            // Resetear el flag cuando se limpia el mapa
            this.isPopulated = false;
        }
    }

    // Método para limpiar los labels vacíos del control de capas
    cleanEmptyLabels() {
        const labels = document.querySelectorAll('.leaflet-control-layers-overlays label');
        labels.forEach(label => {
            if (label.textContent.trim() === '') {
                label.remove(); // Elimina el label vacío
            }
        });
    }

    // Método para escapar caracteres problemáticos en el texto
    sanitizeText(text) {
        if (typeof text !== 'string') {
            return text;
        }

        // Reemplazar caracteres problemáticos
        let sanitizedText = text
            .replace(/`/g, '')         // Reemplazar acento grave por un espacio
            .replace(/\n/g, ' ')       // Reemplazar saltos de línea por un espacio
            .replace(/\r/g, ' ')       // Reemplazar retorno de carro por un espacio
            .replace(/\t/g, ' ')       // Reemplazar tabulaciones por un espacio
            .replace(/\b/g, ' ');      // Reemplazar backspace por un espacio

        // Eliminar espacios antes de las comas
        sanitizedText = sanitizedText.replace(/\s+,/g, ',');

        // Reemplazar múltiples espacios por un único espacio
        sanitizedText = sanitizedText.replace(/\s\s+/g, ' ');

        return sanitizedText.trim(); // Recortar espacios al inicio y al final
    }

    // Método para descargar el mapa como HTML
    downloadMap() {
        const templateStart = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Web PVE</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />
</head>
    <body>
        <div id="map" style="height: 100vh; width: 100%;"></div>
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
        <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
        <script>
            var map = L.map('map').setView([${this.defaultLat}, ${this.defaultLng}], ${this.defaultZoom});
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 20,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            var markerClusterGroup = L.markerClusterGroup();
            map.addLayer(markerClusterGroup);

            // Añadir pantalla completa
            map.addControl(new L.Control.Fullscreen({
                position: 'topleft',
                title: 'Pantalla completa',
                titleCancel: 'Salir de pantalla completa'
            }));
        </script>
        `;

        const markersScript = this.processedMarkers.map(marker => {
            const latlng = marker.getLatLng();
            const popupContent = marker.getPopup().getContent(); // Ya sanitizado
            const title = marker.options.title;
            const iconHtml = marker.options.icon.options.html;

        return `
            var icon = L.divIcon({
                className: 'custom-div-icon',
                html: '${iconHtml}',
                iconSize: [12, 12]
            });
            var marker = L.marker([${latlng.lat}, ${latlng.lng}], { icon: icon, title: '${title}' }).bindPopup(\`${popupContent}\`);
            markerClusterGroup.addLayer(marker);
            `;
        }).join('');

        const templateEnd = `
        <script>
                ${markersScript}
        </script>
    </body>
</html>
        `;

        const template = templateStart + templateEnd;

        const blob = new Blob([template], { type: 'text/html' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'MapaPVE.html';
        link.click();
    }

}
