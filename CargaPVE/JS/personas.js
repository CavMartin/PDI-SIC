let contenedorPersonas; // Definir contenedorPersonas globalmente
let contadorPersona = 0; // Contador global para los IDs de las personas
let plantillaPersona; // Variable para almacenar la plantilla HTML de persona

document.addEventListener('DOMContentLoaded', function () {
    contenedorPersonas = document.getElementById("PersonasRelacionadas");

    // Carga la plantilla HTML para el formulario de persona
    fetch('../Templates/FormularioPersonaPVE.html')
        .then(response => response.text())
        .then(data => {
            plantillaPersona = data;
            cargarPersonasExistentes();
        });

    function cargarPersonasExistentes() {
        const formularioPVE = document.getElementById("formularioPVE").value;
        if (!formularioPVE) return;
  
        fetch('PHP/EndPoint.php?action=fetchDataPersonas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({ formularioPVE: formularioPVE })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                data.data.forEach(domicilio => agregarPersona(domicilio));
            } else {
                console.error('Error al cargar las personas:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al cargar las personas:', error);
        });
    }

    // Agregar un nuevo div del template al DOM
    function agregarPersona(persona) {
        contadorPersona++;
        const htmlPersona = reemplazarMarcadoresPersonas(plantillaPersona, contadorPersona, persona);
        const nuevaPersona = document.createElement("div");
        nuevaPersona.id = `Persona${contadorPersona}`;
        nuevaPersona.innerHTML = htmlPersona;
        contenedorPersonas.appendChild(nuevaPersona);
    
        // Lógica para mostrar el campo OtroEspecifique si el valor seleccionado es 'Otra opción no listada'
        const selectRol = nuevaPersona.querySelector(`#P_Rol${contadorPersona}`);
        const divOtroEspecifique = nuevaPersona.querySelector(`#P_Rol${contadorPersona}Especifique`);
        if (selectRol && divOtroEspecifique) {
            if (selectRol.value === 'Otra opción no listada') {
                divOtroEspecifique.style.display = 'block';
            }
        }
    }

    // Función para reemplazar los marcadores en la plantilla con los datos de la persona
    function reemplazarMarcadoresPersonas(plantilla, contador, datosPersona = {}) {
        let datosHTML = plantilla.replace(/\${contadorPersona}/g, contador);

        const opcionesRol = [
            { value: "No especificado", label: "No especificado" },
            { value: "Mencionado como soldadito", label: "Mencionado como soldadito" },
            { value: "Mencionado como delivery", label: "Mencionado como delivery" },
            { value: "Mencionado como quien haría de campana", label: "Mencionado como quien haría de campana" },
            { value: "Mencionado como vendedor", label: "Mencionado como vendedor" },
            { value: "Mencionado como detenido en comisaría", label: "Mencionado como detenido en comisaría" },
            { value: "Mencionado como detenido en servicio penitenciario", label: "Mencionado como detenido en servicio penitenciario" },
            { value: "Mencionado como empleado policial", label: "Mencionado como empleado policial" },
            { value: "Mencionado como ex empleado policial", label: "Mencionado como ex empleado policial" },
            { value: "Mencionado como empleado penitenciario", label: "Mencionado como empleado penitenciario" },
            { value: "Mencionado como ex empleado penitenciario", label: "Mencionado como ex empleado penitenciario" }
        ];

        // Preparar la lista de opciones de selección del dropdown
        let opcionesHTML = opcionesRol.map(opcion => {
            let seleccionado = datosPersona.P_Rol === opcion.value ? 'selected' : '';
            return `<option value="${opcion.value}" ${seleccionado}>${opcion.label}</option>`;
        }).join('\n');
        
        // Determinar si se debe mostrar el campo de especificación de 'Otra opción no listada'
        let valorNoListado = opcionesRol.every(opcion => opcion.value !== datosPersona.P_Rol);
        let mostrarEspecifique = datosPersona.P_Rol && valorNoListado;
        let displayEspecifique = mostrarEspecifique ? 'block' : 'none';
        let valorEspecifique = mostrarEspecifique ? datosPersona.P_Rol : '';
        
        // Asegurarse de que "Otra opción no listada" se selecciona si el valor no coincide
        let seleccionarOtraOpcion = valorNoListado ? 'selected' : '';
        
        datosHTML = datosHTML.replace(/\${Opciones_Rol}/g, opcionesHTML + `<option value="Otra opción no listada" ${seleccionarOtraOpcion}>Otra opción no listada</option>`);
        datosHTML = datosHTML.replace(/\${Display_P_RolEspecifique}/g, displayEspecifique);
        datosHTML = datosHTML.replace(/\${P_RolEspecifique}/g, valorEspecifique);

        // Reemplazar otros marcadores con datos proporcionados o valores predeterminados
        datosHTML = datosHTML.replace(/\${ID_Persona}/g, datosPersona.ID_Persona || '');
        datosHTML = datosHTML.replace(/\${NumeroDeOrden}/g, datosPersona.NumeroDeOrden || '0');
        datosHTML = datosHTML.replace(/\${P_Nombre}/g, datosPersona.P_Nombre || '');
        datosHTML = datosHTML.replace(/\${P_Apellido}/g, datosPersona.P_Apellido || '');
        datosHTML = datosHTML.replace(/\${P_Alias}/g, datosPersona.P_Alias || '');

        return datosHTML;
    }

    // Evento para agregar un nuevo Persona al hacer clic en el botón correspondiente
    document.getElementById("AgregarPersona").addEventListener("click", () => agregarPersona({}));
});

// Función para eliminar el Div del DOM
function eliminarPersona(contador) {
    const personaAEliminar = document.getElementById(`Persona${contador}`);
    if (!personaAEliminar) return;

    // Alerta de SweetAlert para confirmación
    Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esta acción",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#0d6efd',
        confirmButtonText: 'Sí, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Continuar con la eliminación si se confirma
            const idPersona = personaAEliminar.querySelector('[name^="ID_Persona"]').value;
            if (idPersona) {
                // Crear el FormData para enviar en la solicitud POST
                const formData = new FormData();
                formData.append('action', 'eliminarPersona');
                formData.append('ID_Persona', idPersona);

                // Realizar petición AJAX para eliminar la persona de la base de datos
                fetch('PHP/EndPoint.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Eliminar el elemento del DOM
                        personaAEliminar.parentNode.removeChild(personaAEliminar);
                        actualizarContadoresPersonas();
                        Swal.fire(
                            'Eliminado!',
                            'La persona ha sido eliminada.',
                            'success'
                        );
                    } else {
                        console.error('Error al eliminar la persona:', data.message);
                        Swal.fire(
                            'Error!',
                            'No se pudo eliminar la persona.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error al realizar la petición AJAX:', error);
                    Swal.fire(
                        'Error!',
                        'Ocurrió un error al realizar la solicitud.',
                        'error'
                    );
                });
            } else {
                // Si ID_Persona está vacío, simplemente eliminar el elemento del DOM
                contenedorPersonas.removeChild(personaAEliminar);
                actualizarContadoresPersonas();
            }
        }
    });
}

// Función para actualizar los contadores
function actualizarContadoresPersonas() {
    let contadorPersonasActualizado = 0;
    // Recorrer todos los elementos de personas y actualizar sus IDs
    const personas = contenedorPersonas.querySelectorAll("div[id^='Persona']");
    personas.forEach((persona) => {
        contadorPersonasActualizado++;
        persona.id = `Persona${contadorPersonasActualizado}`;
        actualizarElementosPersonas(persona, contadorPersonasActualizado);
    });
    contadorPersona = contadorPersonasActualizado; // Actualizar el contador global
}

// Función encargada de actualizar los contadores de todos los elementos
function actualizarElementosPersonas(persona, nuevoNumeroPersona) {
    // Actualizar el título (si existe) y otros atributos de los elementos internos
    const tituloPersona = persona.querySelector('h2');
    if (tituloPersona) {
        tituloPersona.textContent = `Persona #${nuevoNumeroPersona}`;
    }

    const elementos = persona.querySelectorAll("input, select, button, label");

    elementos.forEach(elemento => {
        if (elemento.tagName.toLowerCase() === 'label') {
            const baseFor = elemento.getAttribute('for').match(/^[A-Za-z_]+/)[0];
            elemento.setAttribute('for', `${baseFor}${nuevoNumeroPersona}`);
            if (elemento.textContent.includes('ROL DE LA PERSONA')) {
                elemento.textContent = `ROL DE LA PERSONA #${nuevoNumeroPersona}:`;
            }
        }

        if (elemento.id) {
            const baseId = elemento.id.match(/^[A-Za-z_]+/)[0];
            elemento.id = `${baseId}${nuevoNumeroPersona}`;
            if (elemento.tagName.toLowerCase() !== 'button') {
                elemento.name = `${baseId}${nuevoNumeroPersona}`;
            }

            if (elemento.tagName.toLowerCase() === 'button' && elemento.id.includes('quitarPersona')) {
                elemento.setAttribute('onclick', `eliminarPersona('${nuevoNumeroPersona}')`);
            }
        }
    });
}
