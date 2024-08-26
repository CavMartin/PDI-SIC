$(document).ready(function(){
    var table = $('#queryTable').DataTable({
        // Configuraciones de DataTables aquí
        language: {
            url: '../Resources/DataTables/Spanish.json',
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
                    url: 'PHP/EndPoint.php',
                    type: 'POST',
                    data: {
                        action: 'fetchDataQuery911',
                        FechaDesde: $("#FechaDesde").val(),
                        FechaHasta: $("#FechaHasta").val(),
                        Nota: $("#Nota").val(),
                        Region: $("#Region").val(),
                        Localidad: $("#Localidad").val(),
                        Direccion: $("#Direccion").val(),
                        Tipificacion: $("#Tipificacion").val(),
                        Denunciados: $("#Denunciados").val(),
                        Relato: $("#Relato").val()
                    },
                    success: function(response) {
                        // Limpiar datos anteriores
                        table.clear();

                        // Añadir nuevos datos
                        response.data.forEach(function(item) {
                            table.row.add([
                                item.Fecha || '',
                                item.Nota || '',
                                item.Tipificacion || '',
                                item.Direccion || '',
                                item.Localidad || '',
                                item['0800/911'] || '',
                                item.Denunciados || '',
                                item.Relato || ''
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