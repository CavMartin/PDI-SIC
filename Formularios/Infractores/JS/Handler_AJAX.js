// Guardar cambios en los formularios
function guardarCambios(formId, action) {
    // Obtén una referencia al formulario por su ID
    var form = document.getElementById(formId);

    // Verificar si el formulario es válido
    if (!form.checkValidity()) {
        form.reportValidity();
        return; // No continuar si al formulario le quedan campos requeridos sin completar
    }

    // Recolectar los datos del formulario o los que necesites
    var formData = new FormData(form);

    // Agregar la acción correspondiente
    formData.append('action', action);

    $.ajax({
        type: "POST",
        url: "../PHP/EndPoint_AJAX.php",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status === 'success') {
                // Mensaje de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Guardado exitoso',
                    text: 'Los cambios se han guardado correctamente.' ,
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'Volver a la página principal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Obtener el valor del elemento oculto por su ID
                        var ID = document.getElementById("ID").value;

                        // Crear un formulario dinámico
                        var form = document.createElement("form");
                        form.action = "Main.php";
                        form.method = "post";

                        // Agregar un campo oculto con el valor obtenido
                        var input = document.createElement("input");
                        input.type = "hidden";
                        input.name = "ID";
                        input.value = ID;
                        form.appendChild(input);

                        // Adjuntar el formulario al cuerpo del documento y enviarlo
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            } else if (response.status === 'error') {
                // Mensaje de error
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar cambios: ' + response.message,
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

// Llamadas a la función genérica desde las funciones específicas
function guardarCambiosReporte() {
    guardarCambios('CargarReporte', 'insertarDatosReporte');
}

function guardarCambiosEncabezado() {
    guardarCambios('CargarEncabezado', 'insertarDatosEncabezado');
}

function guardarCambiosPersona() {
    guardarCambios('CargarPersonas', 'insertarDatosPersona');
}

function guardarCambiosLugar() {
    guardarCambios('CargarLugares', 'insertarDatosLugar');
}

function guardarCambiosVehiculo() {
    guardarCambios('CargarVehiculos', 'insertarDatosVehiculo');
}

function guardarCambiosAF() {
    guardarCambios('CargarArmas', 'insertarDatosAF');
}

function guardarCambiosMensaje() {
    guardarCambios('CargarMensajes', 'insertarDatosMensaje');
}

function eliminarEntidad(ClavePrimaria, action) {
    // Primero, muestra un diálogo de confirmación
    Swal.fire({
        title: '¿Está seguro de que desea eliminar esta entidad?',
        text: "¡Esta acción no podrá revertirse!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        // Si el usuario confirma, entonces procede con la operación AJAX
        if (result.isConfirmed) {
            // Obtén el valor del campo oculto por su ID
            var clavePrimariaValue = document.getElementById(ClavePrimaria).value;

            // Crear un objeto FormData y agregar los datos que necesitas
            var formData = new FormData();
            formData.append('action', action);
            formData.append('ClavePrimaria', clavePrimariaValue);

            $.ajax({
                type: "POST",
                url: "../PHP/EndPoint_AJAX.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        // Mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminación exitosa',
                            text: 'El registro se ha eliminado correctamente.' ,
                            showConfirmButton: true,
                            allowOutsideClick: false,
                            confirmButtonColor: '#198754',
                            confirmButtonText: 'Volver a la página principal',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Obtener el valor del elemento oculto por su ID
                                var ID = document.getElementById("ID").value;
                                var DispositivoSIACIP = document.getElementById("DispositivoSIACIP").value;
        
                                // Crear un formulario dinámico
                                var form = document.createElement("form");
                                form.action = "Main.php";
                                form.method = "post";
        
                                // Agregar un campo oculto con el valor obtenido
                                var input = document.createElement("input");
                                input.type = "hidden";
                                input.name = "ID";
                                input.value = ID;
                                form.appendChild(input);
        
                                var input = document.createElement("input");
                                input.type = "hidden";
                                input.name = "DispositivoSIACIP";
                                input.value = DispositivoSIACIP;
                                form.appendChild(input);
        
                                // Adjuntar el formulario al cuerpo del documento y enviarlo
                                document.body.appendChild(form);
                                form.submit();
                            }
                        });
                    } else if (response.status === 'error') {
                        // Mensaje de error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al intentar eliminar el registro: ' + response.message,
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
                },
                error: function(xhr, status, error) {
                    // Manejo de errores
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
    });
}

// Llamadas a la función genérica desde las funciones específicas
function eliminarPersona() {
    eliminarEntidad('ClavePrimaria', 'eliminarPersona');
}

function eliminarLugar() {
    eliminarEntidad('ClavePrimaria', 'eliminarLugar');
}

function eliminarVehiculo() {
    eliminarEntidad('ClavePrimaria', 'eliminarVehiculo');
}

function eliminarAF() {
    eliminarEntidad('ClavePrimaria', 'eliminarAF');
}

function eliminarMensaje() {
    eliminarEntidad('ClavePrimaria', 'eliminarMensaje');
}
