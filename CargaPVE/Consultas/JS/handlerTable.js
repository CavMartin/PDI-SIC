function createTable() {
    // Limpiar completamente el contenido del contenedor de la tabla
    var tableContainer = document.getElementById('tableContainer');
    if (tableContainer) {
        tableContainer.innerHTML = ''; // Eliminar todo el contenido previo
        
        // Crear la estructura completa de la tabla
        var tableHTML = `
            <div class="row justify-content-center" style="margin-top: 8rem;">
                <div class="col-12">
                    <div>
                        <table id="queryTable" class="table table-bordered table-hover text-center" style="vertical-align: middle; width: 100%;">
                            <thead>
                                <tr class="table-dark fs-5">
                                    <th>AMPLIAR</th> <!-- Columna para el control de detalles -->
                                    <th style="min-width: 12rem; min-height: 4rem;">FORMULARIO N°</th>
                                    <th style="min-width: 8rem;">FECHA</th>
                                    <th style="min-width: 8rem;">HORA</th>
                                    <th style="min-width: 8rem;">FUENTE</th>
                                    <th style="min-width: 10rem;">REPORTE ASOCIADO</th>
                                    <th style="min-width: 12rem;">TIPOLOGÍA</th>
                                    <th style="min-width: 12rem;">MODALIDAD</th>
                                    <th style="min-width: 12rem;">ESTUPEFACIENTE</th>
                                    <th style="min-width: 8rem;">RELEVANCIA</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
        
        tableContainer.innerHTML = tableHTML; // Insertar la nueva tabla en el contenedor
    } else {
        console.error("No se encontró el contenedor de la tabla.");
    }
}

function formatDetails(rowData) {
    let detailsHTML = `<div class="text-start p-3" style="background-color: #f9f9f9; text-align: justify;">
                           <strong>RELATO DEL HECHO:</strong> ${rowData.Relato || 'Sin datos'}
                       </div>`;

    // Sección de valoración de análisis
    if (rowData.Valoracion !== null && rowData.Valoracion !== '') {
        detailsHTML += `<div class="text-start p-3" style="background-color: #f9f9f9; text-align: justify;">
                            <strong>VALORACIÓN DE ANÁLISIS:</strong> ${rowData.Valoracion}
                        </div>`;
    }

    // Sección de los domicilios relacionados
    if (rowData.Lugares && rowData.Lugares.length > 0) {
        detailsHTML += `<div class="text-start p-3" style="background-color: #f9f9f9;">
                            <strong>DOMICILIOS RELACIONADOS:</strong>`;
        rowData.Lugares.forEach((lugar, index) => {
            // Agregar dirección al HTML
            detailsHTML += `<div class="text-start p-3" style="background-color: #f0f0f0; margin-top: 0.5rem;">
                                <strong>DOMICILIO ${index + 1} - ROL: ${lugar.L_Rol}:</strong> ${lugar.L_Direccion}
                            </div>`;
        });
        detailsHTML += `</div>`; // Cerrar el div de domicilios relacionados
    }

    // Sección de personas relacionadas
    if (rowData.Personas && rowData.Personas.length > 0) {
        detailsHTML += `<div class="text-start p-3" style="background-color: #f9f9f9;">
                            <strong>PERSONAS RELACIONADAS:</strong>`;
        rowData.Personas.forEach((persona, index) => {
            detailsHTML += `<div class="text-start p-3" style="background-color: #f0f0f0; margin-top: 0.5rem;">
                                <strong>PERSONA ${index + 1} - ROL: ${persona.P_Rol}:</strong> APELLIDO: ${persona.P_Apellido}, NOMBRE: ${persona.P_Nombre}, ALIAS: ${persona.P_Alias}
                            </div>`;
        });
        detailsHTML += `</div>`; // Cerrar el div de personas relacionadas
    }

    // Sección de vehículos relacionados
    if (rowData.Vehiculos && rowData.Vehiculos.length > 0) {
        detailsHTML += `<div class="text-start p-3" style="background-color: #f9f9f9;">
                            <strong>VEHÍCULOS RELACIONADOS:</strong>`;
        rowData.Vehiculos.forEach((vehiculo, index) => {
            detailsHTML += `<div class="text-start p-3" style="background-color: #f0f0f0; margin-top: 0.5rem;">
                                <strong>VEHÍCULO ${index + 1} - ROL: ${vehiculo.V_Rol}:</strong> TIPO: ${vehiculo.V_TipoVehiculo}, MARCA: ${vehiculo.V_Marca}, MODELO: ${vehiculo.V_Modelo}, DOMINIO: ${vehiculo.V_Dominio}
                            </div>`;
        });
        detailsHTML += `</div>`; // Cerrar el div de vehículos relacionados
    }

    return detailsHTML;
}

let globalData = [];

function populateTable(data) {
    // Almacenar data globalmente
    globalData = data;

    // Agrupar los datos por Formulario
    const groupedData = groupDataByFormulario(data);

    // Destruye la instancia previa de DataTables si existe y limpia el contenedor
    if ($.fn.DataTable.isDataTable('#queryTable')) {
        $('#queryTable').DataTable().clear().destroy();
    }

    // Generar la tabla nuevamente
    createTable();

    // Inicializar la tabla con DataTables y almacenar la instancia en una variable
    var table = $('#queryTable').DataTable({
        data: groupedData,
        columns: [
            {
                className: 'details-control', // Clase para detectar clics
                orderable: false,
                data: null,
                defaultContent: ''
            },
            {
                data: 'Formulario',
                render: function(data, type, row) {
                    return '<div class="text-center"><button type="button" class="btn btn-outline-success" style="min-width: 9rem;" onclick="verFormularioPVE(\'' + encodeURIComponent(data) + '\')">' + data + '</button></div>';
                }
            },
            { data: 'Fecha' }, // Fecha ya formateada
            { data: 'Hora' },
            { data: 'Fuente' },
            { data: 'ReporteAsociado' },
            { data: 'Tipologia' },
            { data: 'ModalidadComisiva' },
            { data: 'TipoEstupefaciente' },
            {
                data: 'Relevancia',
                render: function(data, type, row) {
                    let color = '';
                    if (data === 'ALTA') {
                        color = 'red';
                    } else if (data === 'MEDIA') {
                        color = 'orange';
                    } else if (data === 'BAJA') {
                        color = 'green';
                    }
                    return `<span style="color:${color}; font-weight:bold;">${data}</span>`;
                }
            },
            {
                data: null,
                visible: false, // Hacer esta columna invisible
                render: function(data, type, row) {
                    // Combinar todos los datos expandibles en una sola cadena
                    let expandableData = '';

                    // Agregar el relato del hecho
                    expandableData += (row.Relato || '') + ' ';

                    // Agregar datos de los domicilios relacionados
                    if (row.Lugares && row.Lugares.length > 0) {
                        row.Lugares.forEach((lugar) => {
                            expandableData += (lugar.L_Direccion || '') + ' ';
                        });
                    }

                    // Agregar datos de las personas relacionadas
                    if (row.Personas && row.Personas.length > 0) {
                        row.Personas.forEach((persona) => {
                            expandableData += (persona.P_Rol || '') + ' ';
                            expandableData += (persona.P_Apellido || '') + ' ';
                            expandableData += (persona.P_Nombre || '') + ' ';
                            expandableData += (persona.P_Alias || '') + ' ';
                        });
                    }

                    // Agregar datos de los vehículos relacionados
                    if (row.Vehiculos && row.Vehiculos.length > 0) {
                        row.Vehiculos.forEach((vehiculo) => {
                            expandableData += (vehiculo.V_TipoVehiculo || '') + ' ';
                            expandableData += (vehiculo.V_Marca || '') + ' ';
                            expandableData += (vehiculo.V_Modelo || '') + ' ';
                            expandableData += (vehiculo.V_Dominio || '') + ' ';
                        });
                    }

                    return expandableData.trim(); // Retornar la cadena concatenada
                }
            }
        ],
        language: {
            url: '../../Resources/DataTables/Spanish.json',
        },
        searching: true,
        pageLength: 15,
        lengthChange: false,
        scrollX: false,
        responsive: false
    });

    // Manejar el evento de clic para mostrar los detalles
    $('#queryTable tbody').on('click', 'td.details-control', function() {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            // Ocultar fila de detalles
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Mostrar fila de detalles
            row.child(formatDetails(row.data())).show();
            tr.addClass('shown');
        }
    });

    // Vincular el campo CustomSearch con el campo de búsqueda de DataTables
    $('#CustomSearch').on('keyup', function() {
        table.search(this.value).draw();
    });
}


// Previsualizar reporte PVE
function verFormularioPVE(formularioPVE) {
    // Crear un formulario temporal
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'Previsualizar_PVE.php';
    form.target = '_blank'; // Abrir en una nueva pestaña

    // Crear un campo de entrada oculto para el formularioPVE
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'formularioPVE';
    input.value = formularioPVE;

    // Agregar el campo al formulario y enviarlo
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
    
    // Eliminar el formulario después de enviar la solicitud
    document.body.removeChild(form);
}

function processData() {
    // Mostrar modal de carga
    Swal.fire({
        title: 'Procesando los datos...',
        text: 'Por favor espere mientras procesamos la información.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    setTimeout(() => {
        // Preprocesar datos
        const processedData = preprocessDataForDownload(globalData);

        // Ocultar el modal de carga
        Swal.close();

        // Mostrar modal para seleccionar el formato de descarga con botones personalizados
        Swal.fire({
            title: 'Selecciona el formato de descarga',
            text: '¿En qué formato deseas descargar los datos?',
            icon: 'question',
            width: '50rem',
            showCancelButton: false,
            showConfirmButton: false,
            html: `
                <div class="container-fluid p-3">
                    <div class="row justify-content-center">
                        <div class="col-md-4 justify-content-center m-2 fs-3">
                            <button type="button" class="btn btn-outline-success btn-lg" style="width: 15rem; background-image: url(../../CSS/Images/CSV.png); background-size: 20%; background-repeat: no-repeat; background-position: left center; display: inline-block;" id="downloadCSVBtn">
                                <b>CSV</b>
                            </button>
                        </div>
                        <div class="col-md-4 justify-content-center m-2 fs-3">
                            <button type="button" class="btn btn-outline-success btn-lg" style="width: 15rem; background-image: url(../../CSS/Images/Excel.png); background-size: 20%; background-repeat: no-repeat; background-position: left center; display: inline-block;" id="downloadExcelBtn">
                                <b>EXCEL</b>
                            </button>
                        </div>
                    </div>
                </div>
            `,
            didOpen: () => {
                // Asignar eventos a los botones dentro del SweetAlert2
                document.getElementById('downloadCSVBtn').addEventListener('click', () => {
                    Swal.close(); // Cerrar el modal antes de realizar la acción
                    downloadCSV(processedData);
                });

                document.getElementById('downloadExcelBtn').addEventListener('click', () => {
                    Swal.close(); // Cerrar el modal antes de realizar la acción
                    downloadExcel(processedData);
                });
            }
        });
    }, 1000); // Simular un pequeño tiempo de procesamiento
}

// Aplanar y procesar el array
function groupDataByFormulario(data) {
    let groupedData = {};

    data.forEach(row => {
        // Convertir la fecha al formato DD/MM/AAAA
        const fechaFormatted = row.Fecha ? row.Fecha.split('-').reverse().join('/') : 'Sin datos';

        // Unificar los campos de dirección
        const textoDireccion = formatDireccion(row);

        // Si el formulario ya existe en groupedData, solo agregar las entidades relacionadas
        if (groupedData[row.Formulario]) {
            // Agregar lugar si no existe en el array de Lugares
            if (row.ID_Lugar) {
                const existingLugar = groupedData[row.Formulario].Lugares.find(lugar => lugar.ID_Lugar === row.ID_Lugar);
                if (!existingLugar) {
                    groupedData[row.Formulario].Lugares.push({
                        ID_Lugar: row.ID_Lugar,
                        L_Rol: row.L_Rol,
                        L_Tipo: row.L_Tipo,
                        L_SubTipo: row.L_SubTipo,
                        L_Calle: row.L_Calle,
                        L_AlturaCatastral: row.L_AlturaCatastral,
                        L_CalleDetalle: row.L_CalleDetalle,
                        L_Interseccion1: row.L_Interseccion1,
                        L_Interseccion2: row.L_Interseccion2,
                        L_Barrio: row.L_Barrio,
                        L_Localidad: row.L_Localidad,
                        L_Direccion: textoDireccion,
                        L_Coordenadas: row.L_Coordenadas
                    });
                }
            }

            // Agregar persona si no existe en el array de Personas
            if (row.ID_Persona) {
                const existingPersona = groupedData[row.Formulario].Personas.find(persona => persona.ID_Persona === row.ID_Persona);
                if (!existingPersona) {
                    groupedData[row.Formulario].Personas.push({
                        ID_Persona: row.ID_Persona,
                        P_Rol: row.P_Rol || 'Sin datos',
                        P_Apellido: row.P_Apellido || 'Sin datos',
                        P_Nombre: row.P_Nombre || 'Sin datos',
                        P_Alias: row.P_Alias || 'Sin datos'
                    });
                }
            }

            // Agregar vehículo si no existe en el array de Vehículos
            if (row.ID_Vehiculo) {
                const existingVehiculo = groupedData[row.Formulario].Vehiculos.find(vehiculo => vehiculo.ID_Vehiculo === row.ID_Vehiculo);
                if (!existingVehiculo) {
                    groupedData[row.Formulario].Vehiculos.push({
                        ID_Vehiculo: row.ID_Vehiculo,
                        V_Rol: row.V_Rol || 'Sin datos',
                        V_TipoVehiculo: row.V_TipoVehiculo || 'Sin datos',
                        V_Marca: row.V_Marca || 'Sin datos',
                        V_Modelo: row.V_Modelo || 'Sin datos',
                        V_Dominio: row.V_Dominio || 'Sin datos'
                    });
                }
            }
        } else {
            // Si el formulario no existe, crear un nuevo objeto con los datos principales y entidades relacionadas
            groupedData[row.Formulario] = {
                Formulario: row.Formulario,
                Fecha: fechaFormatted,
                Hora: row.Hora,
                Fuente: row.Fuente || 'Sin datos',
                ReporteAsociado: row.ReporteAsociado || 'Sin datos',
                Tipologia: row.Tipologia || 'Sin datos',
                ModalidadComisiva: row.ModalidadComisiva || 'Sin datos',
                TipoEstupefaciente: row.TipoEstupefaciente || 'Sin datos',
                Relevancia: row.Relevancia || 'Sin datos',
                PosiblesUsurpaciones: row.PosiblesUsurpaciones === 1 ? 'SÍ' : 'NO',
                ConnivenciaPolicial: row.ConnivenciaPolicial === 1 ? 'SÍ' : 'NO',
                UsoAF: row.UsoAF === 1 ? 'SÍ' : 'NO',
                ParticipacionDeMenores: row.ParticipacionDeMenores === 1 ? 'SÍ' : 'NO',
                ParticipacionOrgCrim: row.ParticipacionOrgCrim === 1 ? 'SÍ' : 'NO',
                Relato: row.Relato || 'Sin datos',
                OrganizacionCriminal: row.OrganizacionCriminal || 'Sin datos',
                Valoracion: row.Valoracion,
                Lugares: row.ID_Lugar ? [{
                    ID_Lugar: row.ID_Lugar,
                    L_Rol: row.L_Rol,
                    L_Tipo: row.L_Tipo,
                    L_SubTipo: row.L_SubTipo,
                    L_Calle: row.L_Calle,
                    L_AlturaCatastral: row.L_AlturaCatastral,
                    L_CalleDetalle: row.L_CalleDetalle,
                    L_Interseccion1: row.L_Interseccion1,
                    L_Interseccion2: row.L_Interseccion2,
                    L_Barrio: row.L_Barrio,
                    L_Localidad: row.L_Localidad,
                    L_Direccion: textoDireccion,
                    L_Coordenadas: row.L_Coordenadas
                }] : [],
                Personas: row.ID_Persona ? [{
                    ID_Persona: row.ID_Persona,
                    P_Rol: row.P_Rol || 'Sin datos',
                    P_Apellido: row.P_Apellido || 'Sin datos',
                    P_Nombre: row.P_Nombre || 'Sin datos',
                    P_Alias: row.P_Alias || 'Sin datos'
                }] : [],
                Vehiculos: row.ID_Vehiculo ? [{
                    ID_Vehiculo: row.ID_Vehiculo,
                    V_Rol: row.V_Rol || 'Sin datos',
                    V_TipoVehiculo: row.V_TipoVehiculo || 'Sin datos',
                    V_Marca: row.V_Marca || 'Sin datos',
                    V_Modelo: row.V_Modelo || 'Sin datos',
                    V_Dominio: row.V_Dominio || 'Sin datos'
                }] : []
            };
        }
    });

    // Convertir el objeto a un array
    return Object.values(groupedData);
}

// Función auxiliar para unificar los campos de dirección
function formatDireccion(lugar) {
    let textoDireccion = lugar.L_Calle || 'Sin datos';
    
    if (lugar.L_AlturaCatastral) textoDireccion += ' N° ' + lugar.L_AlturaCatastral;
    if (lugar.L_CalleDetalle) textoDireccion += ', ' + lugar.L_CalleDetalle;
    if (lugar.L_Interseccion1) {
        textoDireccion += lugar.L_Interseccion2 ? `, entre ${lugar.L_Interseccion1} y ${lugar.L_Interseccion2}` : ' y ' + lugar.L_Interseccion1;
    }
    if (lugar.L_Localidad) textoDireccion += ', ' + lugar.L_Localidad;
    
    return textoDireccion;
}



function preprocessDataForDownload(data) {
    let processedData = [];

    // Suponiendo que data ya está agrupada por el valor de Formulario
    const groupedData = groupDataByFormulario(data);

    groupedData.forEach(row => {
        const encabezado = {
            Formulario: row.Formulario,
            Fecha: row.Fecha,
            Hora: row.Hora,
            Fuente: row.Fuente,
            ReporteAsociado: row.ReporteAsociado,
            Tipologia: row.Tipologia,
            ModalidadComisiva: row.ModalidadComisiva,
            TipoEstupefaciente: row.TipoEstupefaciente,
            Relevancia: row.Relevancia,
            PosiblesUsurpaciones: row.PosiblesUsurpaciones,
            ConnivenciaPolicial: row.ConnivenciaPolicial,
            UsoAF: row.UsoAF,
            ParticipacionDeMenores: row.ParticipacionDeMenores,
            ParticipacionOrgCrim: row.ParticipacionOrgCrim,
            OrganizacionCriminal: row.OrganizacionCriminal,
            Relato: row.Relato,
            Valoracion: row.Valoracion || 'Sin valoración de análisis',
        };

        row.Lugares.forEach(lugar => {
            // Crear una nueva entrada para cada lugar combinado con el encabezado
            let newRow = {
                ...encabezado,
                RolDelLugar: lugar.L_Rol,
                TipoDeLugar: lugar.L_Tipo,
                SubTipo: lugar.L_SubTipo,
                Calle: lugar.L_Calle,
                AlturaCatastral: lugar.L_AlturaCatastral,
                CalleDetalle: lugar.L_CalleDetalle,
                Interseccion1: lugar.L_Interseccion1,
                Interseccion2: lugar.L_Interseccion2,
                Barrio: lugar.L_Barrio,
                Localidad: lugar.L_Localidad,
                Coordenadas: lugar.L_Coordenadas
            };
            processedData.push(newRow);
        });
    });

    return processedData;
}

function downloadCSV(data) {
    const csv = Papa.unparse(data);
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', 'data.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function downloadExcel(data) {
    const worksheet = XLSX.utils.json_to_sheet(data);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Data');

    const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
    const blob = new Blob([excelBuffer], { type: 'application/octet-stream' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', 'data.xlsx');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
