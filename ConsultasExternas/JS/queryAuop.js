$(document).ready(function(){
    var table = $('#queryTable').DataTable({
        // Configuraciones de DataTables aquí
        language: {
            url: '../Resources/DataTables/Spanish.json',
        },
        pageLength: 25, // Establece la cantidad de registros por página predeterminada
        searching: false, // Desactiva el campo de búsqueda de DataTables
        lengthChange: false, // Desactiva el selector de cantidad de registros por página de DataTables
    });

    $("#consultaBtn").click(function(){
        Swal.fire({
            text: "¿Está seguro de que desea realizar la consulta?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, realizar consulta',
            cancelButtonText: 'No, Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar modal de loading de SweetAlert2
                Swal.fire({
                    title: 'Cargando...',
                    text: 'Por favor, espere mientras se procesa la consulta.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: 'PHP/EndPoint.php',
                    type: 'POST',
                    data: {
                        action: 'fetchDataQueryAUOP',
                        FechaDesde: $("#FechaDesde").val(),
                        FechaHasta: $("#FechaHasta").val(),
                        Hora: $("#Hora").val(),
                        OtraDependencia: $("#OtraDependencia").val(),
                        DelitoAUOP: $("#DelitoAUOP").val(),
                        LugardelHecho: $("#LugardelHecho").val(),
                        Barrio: $("#Barrio").val(),
                        Victima: $("#Victima").val(),
                        Imputado: $("#Imputado").val(),
                        RelatoDelHecho: $("#RelatoDelHecho").val()
                    },
                    success: function(response) {
                        try {
                            // Limpiar datos anteriores
                            table.clear();

                            // Verificar que response.data.auop sea un array antes de iterar
                            if (Array.isArray(response.data.auop)) {
                                // Añadir nuevos datos
                                response.data.auop.forEach(function(item) {
                                    table.row.add([
                                        item.Fecha || '',
                                        item.Hora || '',
                                        item.OtraDependencia || '',
                                        item.DelitoAUOP || '',
                                        item.LugardelHecho || '',
                                        item.Barrio || '',
                                        item.Victima || '',
                                        item.Imputado || '',
                                        item.RelatoDelHecho || ''
                                    ]);
                                });
                            } else {
                                console.error("response.data.auop no es un array:", response.data);
                                Swal.fire('Error', 'La respuesta de la consulta no es válida', 'error');
                            }
                            
                            // Dibujar la tabla con los nuevos datos
                            table.draw();
                        } catch (error) {
                            console.error("Error procesando la respuesta:", error);
                            Swal.fire('Error', 'Hubo un problema procesando la respuesta de la consulta', 'error');
                        } finally {
                            // Ocultar modal de loading de SweetAlert2
                            Swal.close();
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'La solicitud no pudo completarse', 'error');
                        // Ocultar modal de loading en caso de error
                        Swal.close();
                    }
                });
            }
        });
    });
});
