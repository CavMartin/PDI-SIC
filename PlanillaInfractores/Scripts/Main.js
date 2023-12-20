// Función para ocultar todos los contenedores excepto el seleccionado
function MostrarContenedor(id) {
    var contenedores = document.querySelectorAll('.Contenedor_Entidades');
    for (var i = 0; i < contenedores.length; i++) {
        if (contenedores[i].id === id) {
            contenedores[i].style.display = 'block';
        } else {
            contenedores[i].style.display = 'none';
        }
    }
}