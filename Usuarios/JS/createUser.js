function createUser() {
    // Validar que las contraseñas coincidan
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    if (password !== confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Las contraseñas no coinciden. Intente nuevamente.'
        });
        return;
    }

    // Crear el objeto FormData con los datos del formulario
    const formData = new FormData(document.getElementById('createUserForm'));
    formData.append('action', 'createUser'); // Agregar el action

    // Realizar la solicitud AJAX
    fetch('PHP/UserEndPoint.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Éxito',
                text: 'Usuario creado exitosamente.',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Crear otro usuario',
                cancelButtonText: 'Volver a la página principal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Recargar la página para crear otro usuario
                    window.location.reload();
                } else {
                    // Redirigir a la página principal
                    window.location.href = 'index.php';
                }
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo registrar el usuario.', 'error');
    });

    return false; // Evita que el formulario se envíe de la manera tradicional
}
