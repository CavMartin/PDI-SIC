document.addEventListener('DOMContentLoaded', function() {
    $("#loginBtn").click(function(event) {
        event.preventDefault();
        var formData = {
            action: 'login',
            username: $("#username").val(),
            password: $("#password").val()
        };

        $.ajax({
            type: "POST",
            url: "Usuarios/PHP/UserEndPoint.php",
            data: formData,
            dataType: "json"
        }).done(function(data) {
            if (data.success) {
                window.location.href = 'index.php';
            } else if (data.blocked) {
                let tiempoRestante = data.remainingTime;
                Swal.fire({
                    icon: 'error',
                    title: 'Acceso bloqueado',
                    html: `Demasiados intentos fallidos. Espera <b>${tiempoRestante}</b> segundos antes de volver a intentar.`,
                    showConfirmButton: false,
                    timer: tiempoRestante * 1000,
                    timerProgressBar: true,
                    didOpen: () => {
                        const interval = setInterval(() => {
                            tiempoRestante -= 1;
                            if(tiempoRestante < 0) {
                                clearInterval(interval);
                                Swal.close();
                            } else {
                                Swal.update({
                                    html: `Demasiados intentos fallidos. Espera <b>${tiempoRestante}</b> segundos antes de volver a intentar.`
                                });
                            }
                        }, 1000);
                    },
                });
            } else {
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
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Selecciona el formulario y el botón de login
    var form = document.getElementById('loginForm');
    var loginBtn = document.getElementById('loginBtn');

    // Evento que se dispara al presionar una tecla en cualquier input dentro del formulario
    form.addEventListener('keypress', function(event) {
        // Comprueba si la tecla presionada es ENTER
        if (event.key === 'Enter') {
            event.preventDefault(); // Previene la acción predeterminada para no enviar el formulario
            loginBtn.click(); // Simula un clic en el botón de inicio de sesión
        }
    });
});
