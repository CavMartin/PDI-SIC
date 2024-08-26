// Esperar a que el DOM se cargue completamente
document.addEventListener('DOMContentLoaded', function () {
    // Seleccionar el elemento por su ID
    var queryEncabezado = document.getElementById('Encabezado');
    var queryPersonas = document.getElementById('Personas');
    var queryLugares = document.getElementById('Lugares');
    var queryVehiculos = document.getElementById('Vehiculos');
    var queryArmas = document.getElementById('Armas');
    var querySecuestros = document.getElementById('Secuestros');

    // Agregar un evento de clic al elemento
    queryEncabezado.addEventListener('click', function () {
        window.location.href = 'Encabezado.php';
    });
    queryPersonas.addEventListener('click', function () {
        window.location.href = 'Personas.php';
    });
    queryLugares.addEventListener('click', function () {
        window.location.href = 'Lugares.php';
    });
    queryVehiculos.addEventListener('click', function () {
        window.location.href = 'Vehiculos.php';
    });
    queryArmas.addEventListener('click', function () {
        window.location.href = 'Armas.php';
    });
    querySecuestros.addEventListener('click', function () {
        window.location.href = 'Secuestros.php';
    });
});