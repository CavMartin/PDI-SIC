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
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../PHP/EndPoint_AJAX.php',
                    type: 'POST',
                    data: {
                        action: 'fetchDataQueryBandeja',
                        FechaDesde: $("#FechaDesde").val(),
                        FechaHasta: $("#FechaHasta").val(),
                        Clasificacion: $("#Clasificacion").val(),
                        Causa: $("#Causa").val(),
                        Dependencia: $("#Dependencia").val(),
                        Juzgado: $("#Juzgado").val(),
                        Fiscal: $("#Fiscal").val(),
                        Relato: $("#Relato").val()
                    },
                    success: function(response) {
                        // Limpiar datos anteriores
                        table.clear();

                        // Añadir nuevos datos
                        response.data.ID.forEach(function(item) {
                            // Generar botón de estado según si está abierto o cerrado
                            let estadoBtn = item.Estado == 1
                                ? '<div class="text-center"><button type="button" class="btn btn-outline-primary cambiar-estado" data-id="' + item.ID + '" data-estadoactual="1">ABIERTO</button></div>'
                                : '<div class="text-center"><button type="button" class="btn btn-outline-danger cambiar-estado" data-id="' + item.ID + '" data-estadoactual="0">CERRADO</button></div>';
                        
                            // Generar otros botones con `data-*` attributes en lugar de `onclick`
                            let botonVerFormulario = '<button type="button" class="btn btn-outline-danger mx-2 previsualizar-incidencia" style="min-width: 8rem;" data-id="' + item.ID + '" onclick="verIncidencia(\'' + item.ID + '\')">' + item.Formulario + '</button>';
                        
                            // Agregar fila a la DataTable
                            table.row.add([
                                '<div class="text-center">' + botonVerFormulario + '</div>',
                                '<div class="text-center">' + (item.Division || '') + '</div>',
                                estadoBtn,
                                '<div class="text-center">' + (formatearFecha(item.Fecha) || '') + '</div>',
                                item.Clasificacion || '',
                                item.Relato || ''
                            ]);
                        });

                        // Dibujar la tabla con los nuevos datos
                        table.draw();

                        // Delega los eventos desde el contenedor de la DataTable
                        $('#queryTable').off('click', '.cambiar-estado'); // Desasigna los eventos anteriores para evitar duplicados
                        $('#queryTable').on('click', '.cambiar-estado', function() {
                            var ID = $(this).data('id');
                            var estadoActual = $(this).data('estadoactual');
                            cambiarEstado(ID, estadoActual);
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'La solicitud no pudo completarse', 'error');
                    }
                });
            }
        })
    });

    function formatearFecha(Fecha) {
        if (!Fecha) return '';

        const fecha = new Date(Fecha);
        const dia = fecha.getDate().toString().padStart(2, '0');
        const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
        const anio = fecha.getFullYear();

        return `${dia}/${mes}/${anio}`;
    }

    function cambiarEstado(ID, estadoActual) {
        console.log("ID:", ID, "Estado Actual:", estadoActual); // Añade esta línea para depurar
        let nuevoEstado = estadoActual == '1' ? '0' : '1'; // Determinar el nuevo estado
        let mensajeConfirmacion = nuevoEstado == '1' ? `¿Desea reabrir el formulario?` : `¿Desea cerrar el formulario?`; // Mostrar alerta de confirmación con el mensaje personalizado
        Swal.fire({
            title: '¿Está seguro?',
            text: mensajeConfirmacion,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                // Usuario confirmó, proceder con la solicitud AJAX
                $.ajax({
                    url: '../PHP/EndPoint_AJAX.php',
                    type: 'POST',
                    data: {
                        action: 'UPDATE_Estado',
                        ID: ID,
                        NuevoEstado: nuevoEstado
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor:", response); // Añade esta línea para depurar
                        // Encuentra el botón que desencadenó la acción
                        let boton = $('button[data-id="' + ID + '"][class*="cambiar-estado"]');
                        
                        // Actualiza el botón según el nuevo estado
                        if (nuevoEstado == '1') {
                            boton.removeClass('btn-outline-danger').addClass('btn-outline-primary');
                            boton.text('ABIERTO');
                            boton.data('estadoactual', '1'); // Actualiza el estado actual almacenado en el botón
                        } else {
                            boton.removeClass('btn-outline-primary').addClass('btn-outline-danger');
                            boton.text('CERRADO');
                            boton.data('estadoactual', '0'); // Actualiza el estado actual almacenado en el botón
                        }
    
                        Swal.fire('¡Estado Actualizado!', `El estado del formulario ha sido actualizado.`, 'success');
                    },
                    error: function() {
                        Swal.fire('Error', 'La solicitud no pudo completarse', 'error');
                    }
                });
            }
        });
    }
});

function formularioIncidencia(ID) {
    // Crear el formulario
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "../IncidenciaPriorizada/Main.php");
    form.setAttribute("target", "_blank"); // Abrir en una nueva pestaña

    // Crear el campo oculto
    var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", "ID");
    hiddenField.setAttribute("value", ID);

    // Añadir el campo oculto al formulario
    form.appendChild(hiddenField);

    // Añadir el formulario al cuerpo del documento
    document.body.appendChild(form);

    // Enviar el formulario
    form.submit();
}

function formularioReporte(ID) {
    // Crear el formulario
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "../ReportePreliminar/Main.php");
    form.setAttribute("target", "_blank"); // Abrir en una nueva pestaña

    // Crear el campo oculto
    var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", "ID");
    hiddenField.setAttribute("value", ID);

    // Añadir el campo oculto al formulario
    form.appendChild(hiddenField);

    // Añadir el formulario al cuerpo del documento
    document.body.appendChild(form);

    // Enviar el formulario
    form.submit();
}