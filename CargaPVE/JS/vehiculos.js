let contenedorVehiculos; // Definir contenedorVehiculos globalmente
let contadorVehiculo = 0; // Contador global para los IDs de los vehiculos
let plantillaVehiculo; // Variable para almacenar la plantilla HTML de vehiculo

document.addEventListener('DOMContentLoaded', function () {
    contenedorVehiculos = document.getElementById("VehiculosRelacionados");

    // Carga la plantilla HTML para el formulario de vehiculo
    fetch('../Templates/FormularioVehiculoPVE.html')
        .then(response => response.text())
        .then(data => {
            plantillaVehiculo = data;
            cargarVehiculosExistentes();
        });

    function cargarVehiculosExistentes() {
        const formularioPVE = document.getElementById("formularioPVE").value;
        if (!formularioPVE) return;

        fetch('PHP/EndPoint.php?action=fetchDataVehiculos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({ formularioPVE: formularioPVE })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                data.data.forEach(vehiculo => agregarVehiculo(vehiculo));
            } else {
                console.error('Error al cargar los vehículos:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al cargar los vehículos:', error);
        });
    }

    // Agregar un nuevo div del template al DOM
    function agregarVehiculo(vehiculo) {
        contadorVehiculo++;
        const htmlVehiculo = reemplazarMarcadoresVehiculos(plantillaVehiculo, contadorVehiculo, vehiculo);
        const nuevaVehiculo = document.createElement("div");
        nuevaVehiculo.id = `Vehiculo${contadorVehiculo}`;
        nuevaVehiculo.innerHTML = htmlVehiculo;
        contenedorVehiculos.appendChild(nuevaVehiculo);
    
        // Lógica para mostrar el campo OtroEspecifique si el valor seleccionado es 'Otra opción no listada'
        const selectRol = nuevaVehiculo.querySelector(`#V_Rol${contadorVehiculo}`);
        const divOtroEspecifique = nuevaVehiculo.querySelector(`#V_Rol${contadorVehiculo}Especifique`);
        if (selectRol && divOtroEspecifique) {
            if (selectRol.value === 'Otra opción no listada') {
                divOtroEspecifique.style.display = 'block';
            }
        }
    }

    // Función para reemplazar los marcadores en la plantilla con los datos del vehículo
    function reemplazarMarcadoresVehiculos(plantilla, contador, datosVehiculo = {}) {
        let datosHTML = plantilla.replace(/\${contadorVehiculo}/g, contador);

        const opcionesRol = [
            { value: "Vehículo mencionado", label: "Vehículo mencionado" },
            { value: "Vehículo utilizado para el delivery", label: "Vehículo utilizado para el delivery" },
            { value: "Vehículo utilizado para el almacenamiento", label: "Vehículo utilizado para el almacenamiento" },
            { value: "Vehículo que frecuenta la zona", label: "Vehículo que frecuenta la zona" }
        ];

        // Preparar la lista de opciones de selección del dropdown
        let opcionesHTML = opcionesRol.map(opcion => {
            let seleccionado = datosVehiculo.V_Rol === opcion.value ? 'selected' : '';
            return `<option value="${opcion.value}" ${seleccionado}>${opcion.label}</option>`;
        }).join('\n');
        
        // Determinar si se debe mostrar el campo de especificación de 'Otra opción no listada'
        let valorNoListado = opcionesRol.every(opcion => opcion.value !== datosVehiculo.V_Rol);
        let mostrarEspecifique = datosVehiculo.V_Rol && valorNoListado;
        let displayEspecifique = mostrarEspecifique ? 'block' : 'none';
        let valorEspecifique = mostrarEspecifique ? datosVehiculo.V_Rol : '';
        
        // Asegurarse de que "Otra opción no listada" se selecciona si el valor no coincide
        let seleccionarOtraOpcion = valorNoListado ? 'selected' : '';
        
        const opcionesTipo = [
            { value: "Acoplado", label: "Acoplado" },
            { value: "Automóvil", label: "Automóvil" },
            { value: "Avioneta", label: "Avioneta" },
            { value: "Bicicleta", label: "Bicicleta" },
            { value: "Bicicleta eléctrica", label: "Bicicleta eléctrica" },
            { value: "Camión", label: "Camión" },
            { value: "Camioneta", label: "Camioneta" },
            { value: "Chasis de camión", label: "Chasis de camión" },
            { value: "Ciclomotor", label: "Ciclomotor" },
            { value: "Cuatriciclo", label: "Cuatriciclo" },
            { value: "Ómnibus / Colectivo / Micro", label: "Ómnibus / Colectivo / Micro" },
            { value: "Embarcación a motor", label: "Embarcación a motor" },
            { value: "Furgón de carga", label: "Furgón de carga" },
            { value: "Lancha", label: "Lancha" },
            { value: "Máquina agrícola", label: "Máquina agrícola" },
            { value: "Máquina de construcción", label: "Máquina de construcción" },
            { value: "Máquina de servicios", label: "Máquina de servicios" },
            { value: "Moto vehículo", label: "Moto vehículo" },
            { value: "Moto vehículo acuático", label: "Moto vehículo acuático" },
            { value: "Tractor", label: "Tractor" },
            { value: "Triciclo", label: "Triciclo" },
            { value: "Vehículo oficial", label: "Vehículo oficial" },
            { value: "Vehículo a tracción animal (Carros)", label: "Vehículo a tracción animal (Carros)" }
        ];        
        
          // Preparar la lista de opciones de selección del dropdown
          let opcionesTipoHTML = opcionesTipo.map(opcion => {
            let seleccionado = datosVehiculo.V_Tipo === opcion.value ? 'selected' : '';
            return `<option value="${opcion.value}" ${seleccionado}>${opcion.label}</option>`;
          }).join('\n');

        datosHTML = datosHTML.replace(/\${Opciones_Rol}/g, opcionesHTML + `<option value="Otra opción no listada" ${seleccionarOtraOpcion}>Otra opción no listada</option>`);
        datosHTML = datosHTML.replace(/\${Display_V_RolEspecifique}/g, displayEspecifique);
        datosHTML = datosHTML.replace(/\${V_RolEspecifique}/g, valorEspecifique);

        // Reemplazar otros marcadores con datos proporcionados o valores predeterminados
        datosHTML = datosHTML.replace(/\${ID_Vehiculo}/g, datosVehiculo.ID_Vehiculo || '');
        datosHTML = datosHTML.replace(/\${NumeroDeOrden}/g, datosVehiculo.NumeroDeOrden || '0');
        datosHTML = datosHTML.replace(/\${Opciones_Tipo}/g, opcionesTipoHTML);
        datosHTML = datosHTML.replace(/\${V_Color}/g, datosVehiculo.V_Color || '');
        datosHTML = datosHTML.replace(/\${V_Marca}/g, datosVehiculo.V_Marca || '');
        datosHTML = datosHTML.replace(/\${V_Modelo}/g, datosVehiculo.V_Modelo || '');
        datosHTML = datosHTML.replace(/\${V_Dominio}/g, datosVehiculo.V_Dominio || '');

        return datosHTML;
    }

    // Evento para agregar un nuevo vehículo al hacer clic en el botón correspondiente
    document.getElementById("AgregarVehiculo").addEventListener("click", () => agregarVehiculo({}));
});

// Función para eliminar el Div del DOM
function eliminarVehiculo(contador) {
    const vehiculoAEliminar = document.getElementById(`Vehiculo${contador}`);
    if (!vehiculoAEliminar) return;

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
            const idVehiculo = vehiculoAEliminar.querySelector('[name^="ID_Vehiculo"]').value;
            if (idVehiculo) {
                // Crear el FormData para enviar en la solicitud POST
                const formData = new FormData();
                formData.append('action', 'eliminarVehiculo');
                formData.append('ID_Vehiculo', idVehiculo);

                // Realizar petición AJAX para eliminar el vehículo de la base de datos
                fetch('PHP/EndPoint.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Eliminar el elemento del DOM
                        vehiculoAEliminar.parentNode.removeChild(vehiculoAEliminar);
                        actualizarContadoresVehiculos();
                        Swal.fire(
                            'Eliminado!',
                            'El vehículo ha sido eliminado.',
                            'success'
                        );
                    } else {
                        console.error('Error al eliminar el vehículo:', data.message);
                        Swal.fire(
                            'Error!',
                            'No se pudo eliminar el vehículo.',
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
                // Si ID_Vehiculo está vacío, simplemente eliminar el elemento del DOM
                contenedorVehiculos.removeChild(vehiculoAEliminar);
                actualizarContadoresVehiculos();
            }
        }
    });
}

// Función para actualizar los contadores
function actualizarContadoresVehiculos() {
    let contadorVehiculosActualizado = 0;
    // Recorrer todos los elementos de vehiculos y actualizar sus IDs
    const vehiculos = contenedorVehiculos.querySelectorAll("div[id^='Vehiculo']");
    vehiculos.forEach((vehiculo) => {
        contadorVehiculosActualizado++;
        vehiculo.id = `Vehiculo${contadorVehiculosActualizado}`;
        actualizarElementosVehiculos(vehiculo, contadorVehiculosActualizado);
    });
    contadorVehiculo = contadorVehiculosActualizado; // Actualizar el contador global
}

// Función encargada de actualizar los contadores de todos los elementos
function actualizarElementosVehiculos(vehiculo, nuevoNumeroVehiculo) {
    // Actualizar el título (si existe) y otros atributos de los elementos internos
    const tituloVehiculo = vehiculo.querySelector('h2');
    if (tituloVehiculo) {
        tituloVehiculo.textContent = `Vehiculo #${nuevoNumeroVehiculo}`;
    }

    const elementos = vehiculo.querySelectorAll("input, select, button, label");

    elementos.forEach(elemento => {
        if (elemento.tagName.toLowerCase() === 'label') {
            const baseFor = elemento.getAttribute('for').match(/^[A-Za-z_]+/)[0];
            elemento.setAttribute('for', `${baseFor}${nuevoNumeroVehiculo}`);
            if (elemento.textContent.includes('ROL DEL VEHÍCULO')) {
                elemento.textContent = `ROL DEL VEHÍCULO #${nuevoNumeroVehiculo}:`;
            }
        }

        if (elemento.id) {
            const baseId = elemento.id.match(/^[A-Za-z_]+/)[0];
            elemento.id = `${baseId}${nuevoNumeroVehiculo}`;
            if (elemento.tagName.toLowerCase() !== 'button') {
                elemento.name = `${baseId}${nuevoNumeroVehiculo}`;
            }

            if (elemento.tagName.toLowerCase() === 'button' && elemento.id.includes('quitarVehiculo')) {
                elemento.setAttribute('onclick', `eliminarVehiculo('${nuevoNumeroVehiculo}')`);
            }
        }
    });
}
