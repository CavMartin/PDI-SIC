// Guardar cambios en los formularios
function guardarCambios() {
    var form = document.getElementById('CargarFormulario'); // Asegúrate de tener el ID correcto del formulario

    // Verificar si el formulario es válido
    if (!form.checkValidity()) {
        form.reportValidity();
        return; // No continuar si al formulario le quedan campos requeridos sin completar
    }

    // Recolectar los datos del formulario o los que necesites
    var formData = new FormData(form);

    // Agregar la acción correspondiente
    formData.append('action', 'INSERT_Form');

    $.ajax({
        type: "POST",
        url: "PHP/EndPoint.php",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            try {
                var jsonResponse = response;
                if (jsonResponse.status === 'success') {
                    // Mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Guardado exitoso',
                        text: 'Los cambios se han guardado correctamente.',
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        confirmButtonColor: '#198754',
                        confirmButtonText: 'Volver a la página principal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Obtener el valor del elemento oculto por su ID
                            var Formulario = document.getElementById("formularioPVE").value;

                            // Crear un formulario dinámico
                            var form = document.createElement("form");
                            form.action = "index.php";
                            form.method = "post";

                            // Agregar un campo oculto con el valor obtenido
                            var input = document.createElement("input");
                            input.type = "hidden";
                            input.name = "Formulario";
                            input.value = Formulario;
                            form.appendChild(input);

                            // Adjuntar el formulario al cuerpo del documento y enviarlo
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                } else if (jsonResponse.status === 'error') {
                    // Mensaje de error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al guardar cambios: ' + jsonResponse.message,
                        showConfirmButton: true
                    });
                } else {
                    // Mensaje para cualquier otra respuesta inesperada
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Respuesta inesperada del servidor. Si el error continúa, por favor contacte al administrador del sistema',
                        showConfirmButton: true
                    });
                }
            } catch (e) {
                // Manejo de errores para respuestas no JSON
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Respuesta inesperada del servidor. Si el error continúa, por favor contacte al administrador del sistema',
                    showConfirmButton: true
                });
            }
        },
        error: function(xhr, status, error) {
            // Maneja los errores si es necesario
            console.error("Error al guardar los cambios: " + error); // Registra el error en la consola
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al guardar los cambios: ' + error,
                showConfirmButton: true
            });
        }
    });
}
