// Ventanas modales y solicitudes AJAX
function CambiarContraseña() {
    var formData = new FormData(document.getElementById('changePasswordForm'));

    fetch('Back_CambiarContraseña.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.message,
                allowOutsideClick: false,
                width: '600px'
            }).then((result) => {
                if (result.value) {
                    window.location.href = 'Main.php';
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                allowOutsideClick: false,
                width: '600px'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });

    return false; // Evita el envío tradicional del formulario
}