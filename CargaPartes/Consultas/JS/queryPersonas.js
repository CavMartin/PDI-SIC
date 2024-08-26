$(document).ready(function(){
    var table = $('#queryTable').DataTable({
        // Configuraciones de DataTables aquí
        language: {
            url: '../../Resources/DataTables/Spanish.json',
        },
        pageLength: 25, // Establece la cantidad de registros por página predeterminada
        searching: false, // Habilita el campo de búsqueda de DataTables
        lengthChange: false, // Desactiva el selector de cantidad de registros por página de DataTables
    });

    $("#consultaBtn").click(function(){
        Swal.fire({
            text: "¿Esta seguro de que desea realizar la consulta?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, realizar consulta',
            cancelButtonText: 'No, Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../PHP/EndPoint_AJAX.php',
                    type: 'POST',
                    data: {
                        action: 'fetchDataQueryPersonas',
                        P_Apellido: $("#P_Apellido").val(),
                        P_Nombre: $("#P_Nombre").val(),
                        P_Alias: $("#P_Alias").val(),
                        P_DNI: $("#P_DNI").val(),
                        P_Rol: $("#P_Rol").val(),
                        P_Genero: $("#P_Genero").val(),
                        P_EstadoCivil: $("#P_EstadoCivil").val()
                    },
                    success: function(response) {

                        // Limpiar datos anteriores
                        table.clear();

                        // Añadir nuevos datos
                        response.data.Personas.forEach(function(item) {
                            table.row.add([
                                '<div class="text-center"><button type="button" class="btn btn-outline-danger" style="min-width: 9rem;" onclick="verIncidencia(\'' + item.FK_Encabezado + '\')"><i class="bi bi-eye"></i> Ir al reporte</button></div>',
                                item.P_Rol || '',
                                item.P_Apellido || '',
                                item.P_Nombre || '',
                                item.P_Alias || '',
                                item.P_DNI || '',
                                item.P_Genero || '',
                                item.P_EstadoCivil || '',
                                '<div class="text-center"><button type="button" class="btn btn-success btn-editar p-2" onclick="buscarEntidad(\'' + item.ID_Persona + '\')"> <i class="bi bi-eye"></i> Previsualizar</button></div>'
                            ]);
                        });
                        
                        // Dibujar la tabla con los nuevos datos
                        table.draw();
                    },
                    error: function() {
                        Swal.fire('Error', 'La solicitud no pudo completarse', 'error');
                    }
                });
            }
        })
    });
});

function buscarEntidad(ID_Persona) {
    $.ajax({
        url: '../PHP/EndPoint_AJAX.php',
        type: 'POST',
        data: {
            action: 'getDataPersona',
            ID_Persona: ID_Persona
        },
        success: function(response) {
            mostrarVentanaModal(response.data); // Aquí se construye la ventana modal con los datos recibidos.
        },
        error: function() {
            alert('Error al recuperar la información de la persona.');
        }
    });
}

function cargarDomicilios(ClavePrimaria) {
    $.ajax({
        url: '../PHP/EndPoint_AJAX.php',
        type: 'GET',
        data: {
            action: 'getDomicilios',
            ID_Persona: ClavePrimaria
        },
        success: function(domicilios) {
            $('#contenedorDomicilios').empty(); // Limpiar contenedor
            if (domicilios.length > 0) { // Verifica si hay domicilios
                let tieneMultiplesDomicilios = domicilios.length > 1;
                domicilios.forEach(function(domicilio, index) {
                    // Define el subtítulo en función de la cantidad de domicilios
                    let subtitulo = tieneMultiplesDomicilios ? `DOMICILIO REGISTRADO #${index + 1}:` : 'DOMICILIO REGISTRADO:';
                    var domicilioHTML = `
                        <div class="fs-5 border border-black rounded m-2 p-2">
                        <h4 class="mb-2"><u>${subtitulo}</u> ${' ' + (domicilio.L_Rol || 'Sin datos')}</h4>
                            <div class="row">
                                <div class="col mb-2"><p><b>CALLE:</b> ${domicilio.L_Calle || 'Sin datos'}</p></div>
                                <div class="col mb-2"><p><b>ALTURA CATASTRAL:</b> ${domicilio.L_AlturaCatastral || 'Sin datos'}</p></div>
                                <div class="col mb-2"><p><b>DETALLE:</b> ${domicilio.L_CalleDetalle || 'Sin datos'}</p></div>
                            </div>
                            <div class="row">
                                <div class="col"><p><b>BARRIO:</b> ${domicilio.L_Barrio || 'Sin datos'}</p></div>
                                <div class="col"><p><b>LOCALIDAD:</b> ${domicilio.L_Localidad || 'Sin datos'}</p></div>
                                <div class="col"><p><b>PROVINCIA:</b> ${domicilio.L_Provincia || 'Sin datos'}</p></div>
                                <div class="col"><p><b>PAÍS:</b> ${domicilio.L_Pais || 'Sin datos'}</p></div>
                            </div>
                        </div>`;
                    $('#contenedorDomicilios').append(domicilioHTML);
                });
            } else {
                $('#contenedorDomicilios').html('<h4 class="fs-4 border border-black rounded mb-2 mx-2 p-2">No hay domicilios registrados para esta persona.</h4>');
            }
        },
        error: function() {
            $('#contenedorDatosComplementarios').html('<h4 class="fs-4 border border-black rounded mb-2 mx-2 p-2">Error al cargar los domicilios de esta persona.</h4>');
        }
    });
}

function cargarDatosComplementarios(ID_Persona) {
    $.ajax({
        url: '../PHP/EndPoint_AJAX.php',
        type: 'GET',
        data: {
            action: 'getDatosComplementarios',
            ClavePrimaria: ID_Persona
        },
        success: function(datosComplementarios) {
            $('#contenedorDatosComplementarios').empty(); // Limpiar contenedor
            if (datosComplementarios.length > 0) { // Verifica si hay datos complementarios
                datosComplementarios.forEach(function(dato, index) {
                    let tieneMultiplesDatos = datosComplementarios.length > 1;
                    let subtitulo = tieneMultiplesDatos ? `DATO COMPLEMENTARIO #${index + 1}:` : 'DATO COMPLEMENTARIO:';
                    let imagenHTML = dato.DC_ImagenAdjunta ? `<div class="text-center"><img src="${dato.DC_ImagenAdjunta}" alt="Imagen Complementaria" class="img-fluid mb-2"></div>` : '';
                    var datoHTML = `
                        <div class="fs-5 border border-black rounded mb-2 mx-2 p-2">
                            <h4 class="mb-2"><u>${subtitulo}</u> ${' ' + (dato.DC_Tipo || 'Sin datos')}</h4>
                            ${imagenHTML}
                            <p><b>DESCRIPCIÓN:</b> ${dato.DC_Comentario || 'Sin datos'}</p>
                        </div>`;
                    $('#contenedorDatosComplementarios').append(datoHTML);
                });
            } else {
                $('#contenedorDatosComplementarios').html('<h4 class="fs-4 border border-black rounded mb-2 mx-2 p-2">No hay datos complementarios registrados para esta persona.</h4>');
            }
        },
        error: function() {
            $('#contenedorDatosComplementarios').html('<h4 class="fs-4 border border-black rounded mb-2 mx-2 p-2">Error al cargar datos complementarios para esta persona.</h4>');
        }
    });
}

function mostrarVentanaModal(data) {
    // Comprobar si hay una imagen en Base64; de lo contrario, usar una imagen por defecto
    const IMAGEN_DEFAULT = '../CSS/Images/PersonaDefault.jpg';
    const SRC_IMAGEN = data.P_FotoPersona || IMAGEN_DEFAULT;

    $('#ModalFotoPersona').attr('src', SRC_IMAGEN);
    $('#ModalRol').html('<b>ROL: </b>' + (data.P_Rol || 'Sin datos'));
    $('#ModalApellido').html('<b>APELLIDO: </b>' + (data.P_Apellido || 'Sin datos'));
    $('#ModalNombre').html('<b>NOMBRE: </b>' + (data.P_Nombre || 'Sin datos'));
    $('#ModalAlias').html('<b>ALIAS: </b>' + (data.P_Alias || 'Sin datos'));
    $('#ModalDNI').html('<b>DNI: </b>' + (data.P_DNI || 'Sin datos'));
    $('#ModalGenero').html('<b>GÉNERO: </b>' + (data.P_Genero || 'Sin datos'));
    $('#ModalEstadoCivil').html('<b>ESTADO CIVIL: </b>' + (data.P_EstadoCivil || 'Sin datos'));

    // Carga de domicilios y datos complementarios
    cargarDomicilios(data.ID_Persona);
    cargarDatosComplementarios(data.ID_Persona);

    // Mostrar la ventana modal
    $('#ventanaModalEntidad').modal('show');
}