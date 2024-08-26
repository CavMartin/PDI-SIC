function changePassword() {
    // Obtener los valores del formulario
    const currentPassword = document.getElementById("current_password").value;
    const newPassword = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    const csrfToken = document.querySelector("input[name='csrf_token']").value;

    // Validar los campos
    if (newPassword !== confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Las contraseñas no coinciden. Intente nuevamente.'
        });
        return;
    }

    // Crear el objeto de datos a enviar
    const data = new URLSearchParams();
    data.append('action', 'changePassword');
    data.append('current_password', currentPassword);
    data.append('password', newPassword);
    data.append('confirm_password', confirmPassword);
    data.append('csrf_token', csrfToken);

    // Realizar la solicitud AJAX
    fetch('PHP/UserEndPoint.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'Contraseña cambiada exitosamente.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonColor: '#198754',
                confirmButtonText: 'Volver a la página principal'
            }).then(() => {
                // Redirigir al usuario a index.php después de que haga clic en el botón OK
                window.location.href = 'index.php';
            });
            document.getElementById("changePasswordForm").reset(); // Reinicia el formulario
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error. Intente nuevamente.'
        });
    });
}