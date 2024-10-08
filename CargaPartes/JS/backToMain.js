function backToMain() {
        Swal.fire({
            title: '¿Está seguro que desea volver a la página principal?',
            text: 'Los cambios no guardados se perderan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#0d6efd',
            confirmButtonText: 'Sí, volver',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Obtener el valor del elemento oculto por su ID
                var ID = document.getElementById("ID").value;
                var formularioID = document.getElementById("formularioID").value;

                // Crear un formulario dinámico
                var form = document.createElement("form");
                form.action = "Main.php";
                form.method = "post";

                // Agregar un campo oculto con el valor obtenido
                var input = document.createElement("input");
                input.type = "hidden";
                input.name = "ID";
                input.value = ID;
                form.appendChild(input);

                var input = document.createElement("input");
                input.type = "hidden";
                input.name = "formularioID";
                input.value = formularioID;
                form.appendChild(input);

                // Adjuntar el formulario al cuerpo del documento y enviarlo
                document.body.appendChild(form);
                form.submit();
            }
        });
    };
