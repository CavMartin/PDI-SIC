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
                        action: 'fetchDataQueryLugares',
                        L_Rol: $("#L_Rol").val(),
                        L_TipoLugar: $("#L_TipoLugar").val(),
                        L_Calle: $("#L_Calle").val(),
                        L_AlturaDesde: $("#L_AlturaDesde").val(),
                        L_AlturaHasta: $("#L_AlturaHasta").val(),
                        L_Barrio: $("#L_Barrio").val(),
                        L_Localidad: $("#L_Localidad").val()
                    },
                    success: function(response) {
                        // Limpiar datos anteriores
                        table.clear();

                        // Añadir nuevos datos
                        response.data.Lugares.forEach(function(item) {
                            // Construcción del texto de la dirección
                            let textoDireccion = item.L_Calle || '';

                            if (item.L_AlturaCatastral) {
                                textoDireccion += ' N° ' + item.L_AlturaCatastral;
                            }
                            if (item.L_CalleDetalle) {
                                textoDireccion += ', ' + item.L_CalleDetalle;
                            }
                            if (item.L_Interseccion1) {
                                if (item.L_Interseccion2) {
                                    textoDireccion += ', entre ' + item.L_Interseccion1 + ' y ' + item.L_Interseccion2;
                                } else {
                                    textoDireccion += ' y ' + item.L_Interseccion1;
                                }
                            }


                            table.row.add([
                                `<div class="text-center"><button type="button" class="btn btn-outline-danger" style="min-width: 9rem;" onclick="verIncidencia('${item.FK_Encabezado}')"> <i class="bi bi-eye"></i> Ir al reporte</button></div>`,
                                item.L_Rol || '',
                                textoDireccion || '',
                                item.L_Barrio || '',
                                item.L_Localidad || '',
                                `<div class="text-center"><button type="button" class="btn btn-success btn-editar p-2" onclick="buscarLugarHecho('${item.ID_Lugar}')"> <i class="bi bi-house-add"></i> Previsualizar</button></div>`,
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

function buscarLugarHecho(ID_Lugar) {
    $.ajax({
        url: '../PHP/EndPoint_AJAX.php',
        type: 'POST',
        data: {
            action: 'getDataLugar',
            ID_Lugar: ID_Lugar
        },
        success: function(response) {
            mostrarVentanaModalLugar(response.data); // Aquí se construye la ventana modal con los datos recibidos.
        },
        error: function() {
            alert('Error al recuperar la información de la persona.');
        }
    });
}

function cargarDatosComplementarios(ID_Lugar) {
    $.ajax({
        url: '../PHP/EndPoint_AJAX.php',
        type: 'GET',
        data: {
            action: 'getDatosComplementarios',
            ClavePrimaria: ID_Lugar
        },
        success: function(datosComplementarios) {
            $('#contenedorSecundario').empty(); // Limpiar contenedor
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
                    $('#contenedorSecundario').append(datoHTML);
                });
            } else {
                $('#contenedorSecundario').html('<h4 class="fs-4 border border-black rounded mb-2 mx-2 p-2">No hay datos complementarios registrados para este lugar.</h4>');
            }
        },
        error: function() {
            $('#contenedorSecundario').html('<h4 class="fs-4 border border-black rounded mb-2 mx-2 p-2">Error al cargar datos complementarios para este lugar.</h4>');
        }
    });
}

function mostrarVentanaModalLugar(data) {
    // Construcción del texto de la dirección
    let textoDireccion = data.L_Calle || '';

    if (data.L_AlturaCatastral) {
        textoDireccion += ' N° ' + data.L_AlturaCatastral;
    }
    if (data.L_CalleDetalle) {
        textoDireccion += ', ' + data.L_CalleDetalle;
    }
    if (data.L_Interseccion1) {
        if (data.L_Interseccion2) {
            textoDireccion += ', entre ' + data.L_Interseccion1 + ' y ' + data.L_Interseccion2;
        } else {
            textoDireccion += ' y ' + data.L_Interseccion1;
        }
    }

    // Ahora establece el texto combinado en el elemento correspondiente de la ventana modal
    $('#ModalRol').html('<b>ROL DEL LUGAR: </b>' + (data.L_Rol || 'Sin datos'));
    $('#ModalTipo').html('<b>CLASIFICACIÓN: </b>' + (data.L_TipoLugar || 'Sin datos'));
    $('#ModalDomicilio').html('<b>LUGAR DEL HECHO: </b>' + textoDireccion);
    $('#ModalBarrio').html('<b>BARRIO: </b>' + (data.L_Barrio || 'Sin datos'));
    $('#ModalLocalidad').html('<b>LOCALIDAD: </b>' + (data.L_Localidad || 'Sin datos'));
    $('#ModalProvincia').html('<b>PROVINCIA: </b>' + (data.L_Provincia || 'Sin datos'));

    // Verifica si el botón existe antes de actualizar el atributo onclick
    if ($('#btnClonarEntidad').length > 0) {
        $('#btnClonarEntidad').attr('onclick', "mostrarIncidenciasParaClonacion('" + data.ID_Lugar + "')");
    }

    // Carga de domicilios y datos complementarios
    cargarDatosComplementarios(data.ID_Lugar);

    // Mostrar la ventana modal
    $('#ventanaModalEntidad').modal('show');
}
