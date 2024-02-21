// Ventanas modales y solicitudes AJAX
function RegistrarUsuario() {
    var formData = new FormData(document.getElementById('registerUserForm'));

    fetch('Back_CrearUsuario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Éxito',
                text: data.message,
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
                    window.location.href = 'Main.php';
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