function cambiarEstado(ID, FormularioID) {
    const CERRADO = 0
    let mensajeConfirmacion = `¿Desea cerrar el formulario #${FormularioID}?`; // Mostrar alerta de confirmación con el mensaje personalizado
    Swal.fire({
        title: mensajeConfirmacion,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'PHP/EndPoint_AJAX.php',
                type: 'POST',
                data: {
                    action: 'UPDATE_Estado',
                    ID: ID,
                    NuevoEstado: CERRADO
                },
                success: function(response) {
                    if (response.status === "success") {
                        location.reload();
                    } else {
                        Swal.fire('Error', 'La solicitud no pudo completarse correctamente', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'La solicitud no pudo completarse', 'error');
                }
            });
        }        
    });
}

function formNuevaIncidencia() {
    // Mostrar SweetAlert2 como modal de carga
    Swal.fire({
        title: 'Cargando...',
        text: 'Por favor, espere mientras se obtienen los datos.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();

            // Solicitud AJAX para obtener el número de formulario
            $.ajax({
                url: 'PHP/EndPoint_AJAX.php',
                type: 'POST',
                data: { action: 'fetchDataNewForm' },
                success: function(response) {
                    Swal.close(); // Cerrar el modal de carga

                    // Verificar respuesta
                    if (response.status === 'success') {
                        // Mostrar SweetAlert2 para confirmar la creación del nuevo formulario
                        Swal.fire({
                            title: 'Nuevo Formulario',
                            text: `¿Desea crear un nuevo formulario de carga (${response.data.Formulario}) para el grupo ${response.data.Grupo} ?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#0d6efd',
                            cancelButtonColor: '#dc3545',
                            confirmButtonText: 'Sí, crearlo',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                insertNewForm(response.data.Formulario, response.data.Numero, response.data.Año);
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

function insertNewForm(Formulario, Numero, Año) {
    Swal.fire({
        title: 'Procesando...',
        text: 'Por favor, espere mientras se crea el formulario.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();

            // Solicitud AJAX para crear el formulario
            $.ajax({
                url: 'PHP/EndPoint_AJAX.php',
                type: 'POST',
                data: { action: 'insertNewForm', Formulario: Formulario, Numero: Numero, Año: Año },
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

function formLogOut() {
  // Crear un formulario dinámicamente
  var form = document.createElement('form');
  form.action = "Logout.php";
  form.method = "post";

  // Agregar un campo oculto con el valor POST
  var hiddenInput = document.createElement('input');
  hiddenInput.type = 'hidden';
  hiddenInput.name = 'Logout';
  hiddenInput.value = 'Logout';
  form.appendChild(hiddenInput);

  // Enviar el formulario
  document.body.appendChild(form);
  form.submit();
}