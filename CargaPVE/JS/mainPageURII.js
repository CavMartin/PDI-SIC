function nuevoFormulario() {
    // Mostrar SweetAlert2 como modal de carga
    Swal.fire({
        title: 'Cargando...',
        text: 'Por favor, espere mientras se obtienen los datos.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();

            // Solicitud AJAX para obtener el número de formulario
            $.ajax({
                url: 'PHP/EndPoint.php',
                type: 'POST',
                data: { action: 'fetchDataNewFormURII' },
                success: function(response) {
                    Swal.close(); // Cerrar el modal de carga

                    // Verificar respuesta
                    if (response.status === 'success') {
                        // Mostrar SweetAlert2 para confirmar la creación del nuevo formulario
                        Swal.fire({
                            title: 'Nuevo Formulario',
                            text: `¿Desea crear un nuevo formulario de carga (${response.data.Formulario})?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#0d6efd',
                            cancelButtonColor: '#dc3545',
                            confirmButtonText: 'Sí, crearlo',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                insertNewFormURII(response.data.Formulario, response.data.Numero, response.data.Año);
                            }
                        });
                    } else {
                        // Manejo de errores
                        Swal.fire('Error', 'No se pudo obtener el número de formulario', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Error en la solicitud AJAX: ' + error, 'error');
                }
            });
        }
    });
}

function insertNewFormURII(Formulario, Numero, Año) {
    Swal.fire({
        title: 'Procesando...',
        text: 'Por favor, espere mientras se crea el formulario.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();

            // Solicitud AJAX para crear el formulario
            $.ajax({
                url: 'PHP/EndPoint.php',
                type: 'POST',
                data: { action: 'insertNewFormURII', Formulario: Formulario, Numero: Numero, Año: Año },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: '¡Creado!',
                            text: 'El formulario ha sido creado exitosamente.',
                            icon: 'success'
                        }).then((result) => {
                            if (result.isConfirmed || result.isDismissed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire('Error', 'No se pudo crear el formulario: ' + response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Error en la solicitud AJAX: ' + error, 'error');
                }
            });
        }
    });
}