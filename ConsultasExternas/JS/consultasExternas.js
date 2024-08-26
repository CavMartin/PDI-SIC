// Esperar a que el DOM se cargue completamente
document.addEventListener('DOMContentLoaded', function () {
    // Seleccionar el elemento por su ID
    var queryPDI = document.getElementById('PDI');
    var queryAUOP = document.getElementById('AUOP');
    var queryPVE = document.getElementById('PVE');

    // Agregar un evento de clic al elemento
    queryPDI.addEventListener('click', function () {
        window.location.href = 'ConsultasPDI.php';
    });
    queryAUOP.addEventListener('click', function () {
        window.location.href = 'AUOP.php';
    });
    queryPVE.addEventListener('click', function () {
        window.location.href = 'Pve911.php';
    });
});