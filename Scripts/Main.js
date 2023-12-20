function ConfirmacionCerrarIncidencia(dispositivoSIACIP) {
    Swal.fire({
        title: 'Cerrar incidencia',
        text: '¿Esta seguro que desea cerrar la incidencia?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, cerrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Agregar aquí cualquier dato adicional que necesites enviar por POST
            const formData = new FormData();
            formData.append('CerrarIncidencia', dispositivoSIACIP);
  
            // Crear un formulario oculto
            const hiddenForm = document.createElement('form');
            hiddenForm.method = 'post';
            hiddenForm.action = 'Main.php'; // Cambia la URL según corresponda
            hiddenForm.style.display = 'none';
  
            // Agregar datos al formulario
            for (const pair of formData.entries()) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = pair[0];
                input.value = pair[1];
                hiddenForm.appendChild(input);
            }
  
            // Agregar el formulario al cuerpo del documento
            document.body.appendChild(hiddenForm);
  
            // Enviar el formulario
            hiddenForm.submit();
        }
    });
    return false; // Evitar que el formulario se envíe automáticamente
  }
  