let contenedorDomicilios; // Definir contenedorDomicilios globalmente
let contadorDomicilio = 0; // Contador global para los IDs de los domicilios
let plantillaDomicilio; // Variable para almacenar la plantilla HTML de domicilio

document.addEventListener('DOMContentLoaded', function () {
  contenedorDomicilios = document.getElementById("DomiciliosRelacionados");

  // Carga la plantilla HTML para el formulario de domicilio
  fetch('../Templates/FormularioDomicilioPVE.html')
      .then(response => response.text())
      .then(data => {
          plantillaDomicilio = data;
          cargarDomiciliosExistentes();
      });

  function cargarDomiciliosExistentes() {
      const formularioPVE = document.getElementById("formularioPVE").value;
      if (!formularioPVE) return;

      fetch('PHP/EndPoint.php?action=fetchDataDomicilios', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({ formularioPVE: formularioPVE })
      })
      .then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              data.data.forEach(domicilio => agregarDomicilio(domicilio));
          } else {
              console.error('Error al cargar los domicilios:', data.message);
          }
      })
      .catch(error => {
          console.error('Error al cargar los domicilios:', error);
      });
  }
    

// Agregar un nuevo div del template al DOM
  function agregarDomicilio(domicilio) {
    contadorDomicilio++;
    const htmlDomicilio = reemplazarMarcadoresLugares(plantillaDomicilio, contadorDomicilio, domicilio);
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
function reemplazarMarcadoresLugares(plantilla, contador, datosDomicilio = {}) {
  let datosHTML = plantilla.replace(/\${contadorDomicilio}/g, contador);

  const opcionesRol = [
    { value: "No especificado", label: "No especificado" },
    { value: "Lugar de acopio", label: "Lugar de acopio" },
    { value: "Lugar de comercialización", label: "Lugar de comercialización" },
    { value: "Lugar de distribución", label: "Lugar de distribución" },
    { value: "Lugar de producción", label: "Lugar de producción" },
    { value: "Lugar mencionado", label: "Lugar mencionado" }
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
  
  const opcionesTipo = [
    { value: "Vía pública", label: "Vía pública" },
    { value: "Plaza / Parque", label: "Plaza / Parque" },
    { value: "Ruta / camino", label: "Ruta / camino" },
    { value: "Cochera / playa de estacionamiento", label: "Cochera / playa de estacionamiento" },
    { value: "Descampado", label: "Descampado" },
    { value: "Exterior de asociación civil", label: "Exterior de asociación civil" },
    { value: "Interior de asociación civil", label: "Interior de asociación civil" },
    { value: "Exterior de comercio", label: "Exterior de comercio" },
    { value: "Interior de comercio", label: "Interior de comercio" },
    { value: "Exterior de dependencia pública", label: "Exterior de dependencia pública" },
    { value: "Interior de dependencia pública", label: "Interior de dependencia pública" },
    { value: "Exterior de industria", label: "Exterior de industria" },
    { value: "Interior de industria", label: "Interior de industria" },
    { value: "Exterior de inmueble", label: "Exterior de inmueble" },
    { value: "Interior de inmueble", label: "Interior de inmueble" },
    { value: "Exterior de institución pública", label: "Exterior de institución pública" },
    { value: "Interior de institución pública", label: "Interior de institución pública" },
    { value: "Exterior de vehículo", label: "Exterior de vehículo" },
    { value: "Interior de vehículo", label: "Interior de vehículo" },
    { value: "No especificado", label: "No especificado" }
  ];  

  // Preparar la lista de opciones de selección del dropdown
  let opcionesTipoHTML = opcionesTipo.map(opcion => {
    let seleccionado = datosDomicilio.L_Tipo === opcion.value ? 'selected' : '';
    return `<option value="${opcion.value}" ${seleccionado}>${opcion.label}</option>`;
  }).join('\n');

  datosHTML = datosHTML.replace(/\${Opciones_Rol}/g, opcionesHTML + `<option value="Otra opción no listada" ${seleccionarOtraOpcion}>Otra opción no listada</option>`);
  datosHTML = datosHTML.replace(/\${Display_L_RolEspecifique}/g, displayEspecifique);
  datosHTML = datosHTML.replace(/\${L_RolEspecifique}/g, valorEspecifique);

  // Reemplazar otros marcadores con datos proporcionados o valores predeterminados
  datosHTML = datosHTML.replace(/\${ID_Lugar}/g, datosDomicilio.ID_Lugar || '');
  datosHTML = datosHTML.replace(/\${NumeroDeOrden}/g, datosDomicilio.NumeroDeOrden || '0');
  datosHTML = datosHTML.replace(/\${Opciones_Tipo}/g, opcionesTipoHTML);
  datosHTML = datosHTML.replace(/\${L_SubTipo}/g, datosDomicilio.L_SubTipo || '');
  datosHTML = datosHTML.replace(/\${L_Calle}/g, datosDomicilio.L_Calle || '');
  datosHTML = datosHTML.replace(/\${L_AlturaCatastral}/g, datosDomicilio.L_AlturaCatastral || '');
  datosHTML = datosHTML.replace(/\${L_CalleDetalle}/g, datosDomicilio.L_CalleDetalle || '');
  datosHTML = datosHTML.replace(/\${L_Interseccion1}/g, datosDomicilio.L_Interseccion1 || '');
  datosHTML = datosHTML.replace(/\${L_Interseccion2}/g, datosDomicilio.L_Interseccion2 || '');
  datosHTML = datosHTML.replace(/\${L_Barrio}/g, datosDomicilio.L_Barrio || '');
  datosHTML = datosHTML.replace(/\${L_Localidad}/g, datosDomicilio.L_Localidad || '');
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
        // Crear el FormData para enviar en la solicitud POST
        const formData = new FormData();
        formData.append('action', 'eliminarDomicilio');
        formData.append('ClavePrimaria', idLugar);

        // Realizar petición AJAX para eliminar el domicilio de la base de datos
        fetch('PHP/EndPoint.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            // Eliminar el elemento del DOM
            domicilioAEliminar.parentNode.removeChild(domicilioAEliminar);
            actualizarContadoresDomicilios();
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
        actualizarContadoresDomicilios();
      }
    }
  });
}

// Funcion para actualizar los contadores
function actualizarContadoresDomicilios() {
  let contadorDomiciliosActualizado = 0;
  // Recorrer todos los elementos de domicilios y actualizar sus IDs
  const domicilios = contenedorDomicilios.querySelectorAll("div[id^='Domicilio']");
  domicilios.forEach((domicilio) => {
    contadorDomiciliosActualizado++;
    domicilio.id = `Domicilio${contadorDomiciliosActualizado}`;
    actualizarElementosLugares(domicilio, contadorDomiciliosActualizado);
  });
  contadorDomicilio = contadorDomiciliosActualizado; // Actualizar el contador global
}

// Funcion encargada de actualizar los contadores de todos los elementos
function actualizarElementosLugares(domicilio, nuevoNumeroDomicilio) {
  // Actualizar el título (si existe) y otros atributos de los elementos internos
  const tituloDomicilio = domicilio.querySelector('h2');
  if (tituloDomicilio) {
    tituloDomicilio.textContent = `Domicilio #${nuevoNumeroDomicilio}`;
  }

  const elementos = domicilio.querySelectorAll("input, select, button, label");

  elementos.forEach(elemento => {
    if (elemento.tagName.toLowerCase() === 'label') {
      const baseFor = elemento.getAttribute('for').match(/^[A-Za-z_]+/)[0];
      elemento.setAttribute('for', `${baseFor}${nuevoNumeroDomicilio}`);
      if (elemento.textContent.includes('ROL DEL DOMICILIO')) {
        elemento.textContent = `ROL DEL DOMICILIO #${nuevoNumeroDomicilio}:`;
      }
    }

    if (elemento.id) {
      const baseId = elemento.id.match(/^[A-Za-z_]+/)[0];
      elemento.id = `${baseId}${nuevoNumeroDomicilio}`;
      if (elemento.tagName.toLowerCase() !== 'button') {
        elemento.name = `${baseId}${nuevoNumeroDomicilio}`;
      }

      if (elemento.tagName.toLowerCase() === 'button' && elemento.id.includes('quitarDomicilio')) {
        elemento.setAttribute('onclick', `eliminarDomicilio('${nuevoNumeroDomicilio}')`);
      }
    }
  });
}