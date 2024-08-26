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
                        action: 'fetchDataQuerySecuestros',
                        DC_Tipo: $("#DC_Tipo").val(),
                        DC_Comentario: $("#DC_Comentario").val()
                    },
                    success: function(response) {

                        // Limpiar datos anteriores
                        table.clear();

                        // Añadir nuevos datos
                        response.data.Secuestros.forEach(function(item) {
                            table.row.add([
                                '<div class="text-center"><button type="button" class="btn btn-outline-danger" style="min-width: 9rem;" onclick="verIncidencia(\'' + item.FK_Encabezado + '\')"><i class="bi bi-eye"></i> Ir al reporte</button></div>',
                                item.DC_Tipo || '',
                                item.DC_Comentario || '',
                                '<div class="text-center"><button type="button" class="btn btn-success btn-editar p-2" onclick="buscarEntidad(\'' + item.ID_DatoComplementario + '\')"> <i class="bi bi-eye"></i> Previsualizar</button></div>'
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

function buscarEntidad(ID_DatoComplementario) {
    $.ajax({
        url: '../PHP/EndPoint_AJAX.php',
        type: 'POST',
        data: {
            action: 'getDataSecuestro',
            ID_DatoComplementario: ID_DatoComplementario
        },
        success: function(response) {
            mostrarVentanaModal(response.data); // Aquí se construye la ventana modal con los datos recibidos.
        },
        error: function() {
            alert('Error al recuperar la información de la persona.');
        }
    });
}

function mostrarVentanaModal(data) {
    // Comprobar si hay una imagen en Base64; de lo contrario, usar una imagen por defecto
    const IMAGEN_DEFAULT = '../../CSS/Images/ImagenDefault.png';
    const SRC_IMAGEN = data.DC_ImagenAdjunta || IMAGEN_DEFAULT;

    $('#ModalMedio').html('<b>TIPO DE SECUESTRO: </b>' + (data.DC_Tipo || 'Sin datos'));
    $('#ModalContenido').html('<b>COMENTARIO: </b>' + (data.DC_Comentario || 'Sin datos'));
    $('#ModalImagen').attr('src', SRC_IMAGEN);

    // Mostrar la ventana modal
    $('#ventanaModalEntidad').modal('show');
}