let contenedorDomicilios; // Definir contenedorDomicilios globalmente
let contadorDomicilio = 0; // Contador global para los IDs de los domicilios
let plantillaDomicilio; // Variable para almacenar la plantilla HTML de domicilio

document.addEventListener('DOMContentLoaded', function () {
  contenedorDomicilios = document.getElementById("DomiciliosRelacionados");

  // Carga la plantilla HTML para el formulario de domicilio
  fetch('../Templates/FormularioDomicilio.html')
    .then(response => response.text())
    .then(data => {
      plantillaDomicilio = data;
      cargarDomiciliosExistentes();
    });

  function cargarDomiciliosExistentes() {
    const ID_Persona = document.getElementById("ClavePrimaria").value;
    if (!ID_Persona) return;
    fetch(`PHP/EndPoint_AJAX.php?action=getDomicilios&ID_Persona=${ID_Persona}`)
      .then(response => response.json())
      .then(data => {
        data.forEach(domicilio => agregarDomicilio(domicilio));
      })
      .catch(error => {
        console.error('Error al cargar los domicilios:', error);
      });
  }

// Agregar un nuevo div del template al DOM
  function agregarDomicilio(domicilio) {
    contadorDomicilio++;
    const htmlDomicilio = reemplazarMarcadores(plantillaDomicilio, contadorDomicilio, domicilio);
    const nuevoDomicilio = document.createElement("div");
    nuevoDomicilio.id = `Domicilio${contadorDomicilio}`;
    nuevoDomicilio.innerHTML = htmlDomicilio;
    contenedorDomicilios.appendChild(nuevoDomicilio);
  
    // Lógica para mostrar el campo OtroEspecifique si el valor seleccionado es 'Otro'
    const selectRol = nuevoDomicilio.querySelector(`#L_Rol${contadorDomicilio}`);
    const divOtroEspecifique = nuevoDomicilio.querySelector(`#L_Rol${contadorDomicilio}Especifique`);
    if (selectRol && divOtroEspecifique) {
      if (selectRol.value === 'Otra opción no listada') {
        divOtroEspecifique.style.display = 'block';
      }
    }
}

// Función para reemplazar los marcadores en la plantilla con los datos del domicilio
function reemplazarMarcadores(plantilla, contador, datosDomicilio = {}) {
  let datosHTML = plantilla.replace(/\${contadorDomicilio}/g, contador);

  const opcionesRol = [
    { value: "RENAPER", label: "Domicilio registrado según RENAPER" },
    { value: "Declarado por la persona", label: "Domicilio aportado por la persona" },
    { value: "Aportado por terceros", label: "Domicilio aportado por terceros" },
    { value: "Bases de datos policiales", label: "Domicilio registrado en bases de datos policiales" },
    { value: "Padrón electoral provincial 2017", label: "Domicilio registrado según padrón electoral provincial 2017" },
    { value: "Padrón electoral provincial 2023", label: "Domicilio registrado según padrón electoral provincial 2023" },
    { value: "Fuentes abiertas", label: "Domicilio registrado según fuentes abiertas / periodísticas" }
  ];

  // Preparar la lista de opciones de selección del dropdown
  let opcionesHTML = opcionesRol.map(opcion => {
    let seleccionado = datosDomicilio.L_Rol === opcion.value ? 'selected' : '';
    return `<option value="${opcion.value}" ${seleccionado}>${opcion.label}</option>`;
  }).join('\n');
  
  // Determinar si se debe mostrar el campo de especificación de 'Otra opción no listada'
  let valorNoListado = opcionesRol.every(opcion => opcion.value !== datosDomicilio.L_Rol);
  let mostrarEspecifique = datosDomicilio.L_Rol && valorNoListado;
  let displayEspecifique = mostrarEspecifique ? 'block' : 'none';
  let valorEspecifique = mostrarEspecifique ? datosDomicilio.L_Rol : '';
  
  // Asegurarse de que "Otra opción no listada" se selecciona si el valor no coincide
  let seleccionarOtraOpcion = valorNoListado ? 'selected' : '';
  
  datosHTML = datosHTML.replace(/\${Opciones_Rol}/g, opcionesHTML + `<option value="Otra opción no listada" ${seleccionarOtraOpcion}>Otra opción no listada</option>`);
  datosHTML = datosHTML.replace(/\${Display_L_RolEspecifique}/g, displayEspecifique);
  datosHTML = datosHTML.replace(/\${L_RolEspecifique}/g, valorEspecifique);

  // Reemplazar otros marcadores con datos proporcionados o valores predeterminados
  datosHTML = datosHTML.replace(/\${ID_Lugar}/g, datosDomicilio.ID_Lugar || '');
  datosHTML = datosHTML.replace(/\${NumeroDeOrden}/g, datosDomicilio.NumeroDeOrden || '0');
  datosHTML = datosHTML.replace(/\${L_Calle}/g, datosDomicilio.L_Calle || '');
  datosHTML = datosHTML.replace(/\${L_AlturaCatastral}/g, datosDomicilio.L_AlturaCatastral || '');
  datosHTML = datosHTML.replace(/\${L_CalleDetalle}/g, datosDomicilio.L_CalleDetalle || '');
  datosHTML = datosHTML.replace(/\${L_Barrio}/g, datosDomicilio.L_Barrio || '');
  datosHTML = datosHTML.replace(/\${L_Localidad}/g, datosDomicilio.L_Localidad || 'Rosario');
  datosHTML = datosHTML.replace(/\${L_Provincia}/g, datosDomicilio.L_Provincia || 'Santa Fe');
  datosHTML = datosHTML.replace(/\${L_Pais}/g, datosDomicilio.L_Pais || 'ARGENTINA');
  datosHTML = datosHTML.replace(/\${L_Coordenadas}/g, datosDomicilio.L_Coordenadas || '');

  return datosHTML;
}

  // Evento para agregar un nuevo domicilio al hacer clic en el botón correspondiente
  document.getElementById("AgregarDomicilio").addEventListener("click", () => agregarDomicilio({}));
});

// Función para eliminar el Div del DOM
function eliminarDomicilio(contador) {
  const domicilioAEliminar = document.getElementById(`Domicilio${contador}`);
  if (!domicilioAEliminar) return;

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
      const idLugar = domicilioAEliminar.querySelector('[name^="ID_Lugar"]').value;
      if (idLugar) {
        // Realizar petición AJAX para eliminar el domicilio de la base de datos
        fetch(`PHP/EndPoint_AJAX.php?action=eliminarDomicilio&ID_Lugar=${idLugar}`, { method: 'POST' })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              // Eliminar el elemento del DOM
              domicilioAEliminar.parentNode.removeChild(domicilioAEliminar);
              actualizarContadores();
              Swal.fire(
                'Eliminado!',
                'El domicilio ha sido eliminado.',
                'success'
              );
            } else {
              console.error('Error al eliminar el domicilio:', data.message);
              Swal.fire(
                'Error!',
                'No se pudo eliminar el domicilio.',
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
        // Si ID_Lugar está vacío, simplemente eliminar el elemento del DOM
        contenedorDomicilios.removeChild(domicilioAEliminar);
        actualizarContadores();
      }
    }
  });
}

// Funcion para actualizar los contadores
function actualizarContadores() {
  let contadorActualizacion = 0;
  // Recorrer todos los elementos de domicilios y actualizar sus IDs
  const domicilios = contenedorDomicilios.querySelectorAll("div[id^='Domicilio']");
  domicilios.forEach((domicilio) => {
    contadorActualizacion++;
    domicilio.id = `Domicilio${contadorActualizacion}`;
    actualizarElementos(domicilio, contadorActualizacion);
  });
  contadorDomicilio = contadorActualizacion; // Actualizar el contador global
}

// Funcion encargada de actualizar los contadores de todos los elementos
function actualizarElementos(domicilio, nuevoNumero) {
  // Actualizar el título (si existe) y otros atributos de los elementos internos
  const tituloDomicilio = domicilio.querySelector('h2');
  if (tituloDomicilio) {
    tituloDomicilio.textContent = `Domicilio #${nuevoNumero}`;
  }

  const elementos = domicilio.querySelectorAll("input, select, button, label");

  elementos.forEach(elemento => {
    if (elemento.tagName.toLowerCase() === 'label') {
      const baseFor = elemento.getAttribute('for').match(/^[A-Za-z_]+/)[0];
      elemento.setAttribute('for', `${baseFor}${nuevoNumero}`);
      if (elemento.textContent.includes('CLASIFICACIÓN DEL DOMICILIO')) {
        elemento.textContent = `CLASIFICACIÓN DEL DOMICILIO #${nuevoNumero}:`;
      }
    }

    if (elemento.id) {
      const baseId = elemento.id.match(/^[A-Za-z_]+/)[0];
      elemento.id = `${baseId}${nuevoNumero}`;
      if (elemento.tagName.toLowerCase() !== 'button') {
        elemento.name = `${baseId}${nuevoNumero}`;
      }

      if (elemento.tagName.toLowerCase() === 'button' && elemento.id.includes('quitarDomicilio')) {
        elemento.setAttribute('onclick', `eliminarDomicilio('${nuevoNumero}')`);
      }
    }
  });
}
