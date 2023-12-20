function Logear(event) {
    event.preventDefault(); // Previene el envío normal del formulario
    var formData = {
        username: $("#username").val(),
        password: $("#password").val()
    };

    $.ajax({
        type: "POST",
        url: "Back_Login.php",
        data: formData,
        dataType: "json",
        encode: true
    }).done(function(data) {
        if (data.success) {
            window.location.href = 'Main.php';
        } else if (data.bloqueado) {
            // Manejar la situación de bloqueo
            var tiempoRestante = data.tiempo_restante;
            var intervalo = setInterval(function() {
                if (tiempoRestante <= 0) {
                    clearInterval(intervalo);
                    window.location.reload(); // Recargar la página cuando el tiempo se agote
                } else {
                    tiempoRestante--;
                    // Actualizar el mensaje de bloqueo con el tiempo restante
                    Swal.update({
                        html: 'Demasiados intentos fallidos. Espera <b>' + tiempoRestante + '</b> segundos antes de volver a intentar.'
                    });
                }
            }, 1000);

            Swal.fire({
                icon: 'error',
                title: 'Acceso bloqueado',
                html: 'Demasiados intentos fallidos. Espera <b>' + tiempoRestante + '</b> segundos antes de volver a intentar.',
                timer: tiempoRestante * 1000,
                timerProgressBar: true,
                willClose: () => {
                    clearInterval(intervalo); // Asegurarse de limpiar el intervalo cuando se cierra la ventana
                }
            });
        } else {
            // Mostrar mensaje de error en otros casos
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        // Crear un mensaje de error más detallado
        var errorMessage = 'Hubo un problema con la solicitud.';
        if (jqXHR && jqXHR.status) {
            errorMessage += ' Estado: ' + jqXHR.status;
        }
        if (textStatus) {
            errorMessage += ' Texto del error: ' + textStatus;
        }
        if (errorThrown) {
            errorMessage += ' Error lanzado: ' + errorThrown;
        }
    
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage
        });
    });    
}