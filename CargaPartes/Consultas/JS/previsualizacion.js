function verIncidencia(ID) {
    let url = "Previsualizar_Formulario.php?ID=" + encodeURIComponent(ID); // Construir la URL con el parámetro GET
    window.open(url, '_blank'); // Abrir la nueva pestaña con la URL
}
