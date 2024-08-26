// Esperar a que el DOM se cargue completamente
document.addEventListener('DOMContentLoaded', function () {
    // Intentar seleccionar los elementos por su ID
    var PARTES = document.getElementById('PARTES');
    var PVE = document.getElementById('PVE');
    var CONSULTAS = document.getElementById('CONSULTAS');

    // Comprobar si el elemento existe antes de agregar el manejador de eventos
    if (PARTES) {
        PARTES.addEventListener('click', function () {
            window.location.href = 'CargaPartes/index.php';
        });
    }
    if (PVE) {
        PVE.addEventListener('click', function () {
            window.location.href = 'CargaPVE/index.php';
        });
    }
    if (CONSULTAS) {
        CONSULTAS.addEventListener('click', function () {
            window.location.href = 'ConsultasExternas/index.php';
        });
    }
});