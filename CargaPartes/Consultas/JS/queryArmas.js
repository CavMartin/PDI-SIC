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
                        action: 'fetchDataQueryAF',
                        AF_TipoAF: $("#AF_TipoAF").val(),
                        AF_EsDeFabricacionCasera: $("#AF_EsDeFabricacionCasera").val(),
                        AF_Marca: $("#AF_Marca").val(),
                        AF_Modelo: $("#AF_Modelo").val(),
                        AF_Calibre: $("#AF_Calibre").val(),
                        AF_NumeroDeSerie: $("#AF_NumeroDeSerie").val()
                    },
                    success: function(response) {

                        // Limpiar datos anteriores
                        table.clear();

                        // Añadir nuevos datos
                        response.data.Armas.forEach(function(item) {
                            table.row.add([
                                '<div class="text-center"><button type="button" class="btn btn-outline-danger" style="min-width: 9rem;" onclick="verIncidencia(\'' + item.FK_Encabezado + '\')"><i class="bi bi-eye"></i> Ir al reporte</button></div>',
                                item.AF_TipoAF || '',
                                item.AF_Marca || '',
                                item.AF_Modelo || '',
                                item.AF_Calibre || '',
                                item.AF_NumeroDeSerie || '',
                                '<div class="text-center"><button type="button" class="btn btn-success btn-editar p-2" onclick="buscarEntidad(\'' + item.ID_Arma + '\')"> <i class="bi bi-eye"></i> Previsualizar</button></div>'
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

function buscarEntidad(ID_Arma) {
    $.ajax({
        url: '../PHP/EndPoint_AJAX.php',
        type: 'POST',
        data: {
            action: 'getDataArma',
            ID_Arma: ID_Arma
        },
        success: function(response) {
            mostrarVentanaModal(response.data); // Aquí se construye la ventana modal con los datos recibidos.
        },
        error: function() {
            alert('Error al recuperar la información de la persona.');
        }
    });
}

function cargarDatosComplementarios(ID_Arma) {
    $.ajax({
        url: '../PHP/EndPoint_AJAX.php',
        type: 'GET',
        data: {
            action: 'getDatosComplementarios',
            ClavePrimaria: ID_Arma
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
                $('#contenedorDatosComplementarios').html('<h4 class="fs-4 border border-black rounded mb-2 mx-2 p-2">No hay datos complementarios registrados para esta arma de fuego.</h4>');
            }
        },
        error: function() {
            $('#contenedorDatosComplementarios').html('<h4 class="fs-4 border border-black rounded mb-2 mx-2 p-2">Error al cargar datos complementarios para esta arma de fuego.</h4>');
        }
    });
}

function mostrarVentanaModal(data) {
    // Comprobar si hay una imagen en Base64; de lo contrario, usar una imagen por defecto
    $('#ModalClasificacion').html('<b>CLASIFICACIÓN DEL ARMA DE FUEGO: </b>' + (data.AF_TipoAF || 'Sin datos'));
    $('#ModalMarca').html('<b>MARCA: </b>' + (data.AF_Marca || 'Sin datos'));
    $('#ModalModelo').html('<b>MODELO: </b>' + (data.AF_Modelo || 'Sin datos'));
    $('#ModalCalibre').html('<b>CALIBRE: </b>' + (data.AF_Calibre || 'Sin datos'));
    $('#ModalNumero').html('<b>NUMERO DE SERIE: </b>' + (data.AF_NumeroDeSerie || 'Sin datos'));

    // Carga de domicilios y datos complementarios
    cargarDatosComplementarios(data.ID_Arma);

    // Mostrar la ventana modal
    $('#ventanaModalEntidad').modal('show');
}