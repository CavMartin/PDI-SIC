function verIncidencia(formularioPVE) {
    // Crear un formulario temporal
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'Previsualizar_PVE.php';
    form.target = '_blank'; // Abrir en una nueva pestaña

    // Crear un campo de entrada oculto para el formularioPVE
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'formularioPVE';
    input.value = formularioPVE;

    // Agregar el campo al formulario y enviarlo
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
    
    // Eliminar el formulario después de enviar la solicitud
    document.body.removeChild(form);
}
