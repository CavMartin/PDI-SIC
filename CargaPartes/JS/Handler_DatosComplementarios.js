let contenedorDC; // Definir contenedorDC globalmente
let contadorDC = 0; // Contador global para los IDs de los datos complementarios
let plantillaDC; // Variable para almacenar la plantilla HTML de datos complementarios
let opcionesTipoDCCache = null; // Variable para almacenar el primer JSON en cache
let cargaOpcionesTipoDCPromise = null; // Variable para evitar multiples llamadas a la funcion ASYNC para cargar opciones

document.addEventListener('DOMContentLoaded', function () {
  contenedorDC = document.getElementById("DatosComplementarios");

  // Carga la plantilla HTML para el formulario de datos complementarios
  fetch('../Templates/FormularioDatoComplementario.html')
    .then(response => response.text())
    .then(data => {
      plantillaDC = data;
      cargarDatosComplementariosExistentes();
    });

function cargarDatosComplementariosExistentes() {
  const ClavePrimaria = document.getElementById("ClavePrimaria").value;
  if (!ClavePrimaria) return;
  fetch(`PHP/EndPoint_AJAX.php?action=getDatosComplementarios&ClavePrimaria=${ClavePrimaria}`)
    .then(response => response.json())
    .then(data => {
      data.forEach(datoComplementario => agregarDatoComplementario(datoComplementario));
    })
    .catch(error => {
      console.error('Error al cargar los datos complementarios:', error);
    });
}

// Función para agregar un nuevo dato complementario al DOM
async function agregarDatoComplementario(datoComplementario) {
  try {
    const contadorActual = ++contadorDC; // Incrementa el contador y obtiene el valor actual

    // Cargar las opciones del tipo de dato complementario antes de crear el elemento
    const opcionesTipoDC = await cargarOpcionesTipoDC();

    const htmlDatoComplementario = await reemplazarMarcadoresDC(plantillaDC, contadorActual, datoComplementario);

    const nuevoDatoComplementario = document.createElement("div");
    nuevoDatoComplementario.id = `DatoComplementario${contadorActual}`;
    nuevoDatoComplementario.innerHTML = htmlDatoComplementario;
    contenedorDC.appendChild(nuevoDatoComplementario);

    const selectTipoDC = nuevoDatoComplementario.querySelector(`#DC_Tipo${contadorActual}`);
    if (selectTipoDC) {
      opcionesTipoDC.forEach(opcion => {
        const option = document.createElement('option');
        option.value = opcion.value;
        option.label = opcion.label;
        selectTipoDC.appendChild(option);
      });

      const valorRecuperado = datoComplementario.DC_Tipo;

      if (valorRecuperado !== null && valorRecuperado !== undefined) {
        // Si hay un valor asignado
        const opcionCorrespondiente = opcionesTipoDC.find(opcion => opcion.value === valorRecuperado);
        if (opcionCorrespondiente) {
          // Si el valor corresponde a una opción en la lista
          selectTipoDC.value = valorRecuperado;
        } else {
          // Si el valor no corresponde a una opción en la lista, selecciona "Otra opción no listada"
          selectTipoDC.value = 'Otra opción no listada';
        }
      } else {
        // Si el campo está vacío o es null, selecciona la opción deshabilitada por defecto
        selectTipoDC.value = '';
      }

      // Llama a HandlerTipoDato después de asignar el valor al select
      HandlerTipoDato(selectTipoDC);
    }
  } catch (error) {
    console.error('Error al agregar dato complementario:', error);
  }
}


// Función para cargar las opciones del tipo de dato complementario
async function cargarOpcionesTipoDC() {
  if (opcionesTipoDCCache) {
    return opcionesTipoDCCache;
  }
  
  if (cargaOpcionesTipoDCPromise) {
    // Espera a que la carga actual se complete
    await cargaOpcionesTipoDCPromise;
    return opcionesTipoDCCache;
  }

  cargaOpcionesTipoDCPromise = new Promise(async (resolve, reject) => {
    try {
      const response = await fetch('JSON/OpcionesDC.json');
      const data = await response.json();

    const formulario = document.querySelector('form');
    if (!formulario) throw new Error('No se encontró formulario en la página');

    let opcionesProcesadas;
    switch (formulario.id) {
      case 'CargarPersonas':
        opcionesProcesadas = data.Personas.map(opcion => {
          return typeof opcion === 'string' ? { value: opcion, label: opcion } : opcion;
        });
        break;
      case 'CargarLugares':
        opcionesProcesadas = data.Lugares.map(opcion => {
          return typeof opcion === 'string' ? { value: opcion, label: opcion } : opcion;
        });
        break;
      case 'CargarVehiculos':
        opcionesProcesadas = data.Vehiculos.map(opcion => {
          return typeof opcion === 'string' ? { value: opcion, label: opcion } : opcion;
        });
        break;
      case 'CargarArmas':
        opcionesProcesadas = data.Armas.map(opcion => {
          return typeof opcion === 'string' ? { value: opcion, label: opcion } : opcion;
        });
        break;
      default:
        throw new Error('Formulario desconocido');
    }

    // Almacena el resultado en la caché para su uso futuro
    opcionesTipoDCCache = opcionesProcesadas;
    resolve();
  } catch (error) {
    console.error('Error al cargar opciones:', error);
    reject(error);
  }
});

await cargaOpcionesTipoDCPromise;
return opcionesTipoDCCache;
}

// Función para reemplazar los marcadores en la plantilla con los datos de los datos complementarios
async function reemplazarMarcadoresDC(plantilla, contador, datosDatoComplementario = {}) {
  try {
    let datosHTML = plantilla.replace(/\${contadorDC}/g, contador);

    datosHTML = datosHTML.replace(/\${NumeroDeOrden}/g, datosDatoComplementario.NumeroDeOrden || '0');
    datosHTML = datosHTML.replace(/\${ID_DatoComplementario}/g, datosDatoComplementario.ID_DatoComplementario || '');
    datosHTML = datosHTML.replace(/\${DC_TipoEspecifique}/g, datosDatoComplementario.DC_Tipo || '');
    datosHTML = datosHTML.replace(/\${DC_Comentario}/g, datosDatoComplementario.DC_Comentario || '');
    datosHTML = datosHTML.replace(/\${DC_ImagenAdjunta}/g, datosDatoComplementario.DC_ImagenAdjunta || '');
    datosHTML = datosHTML.replace(/\${DC_ImagenAdjuntaPreview}/g, datosDatoComplementario.DC_ImagenAdjunta || 'CSS/Images/ImagenDefault.png');

    return datosHTML;
  } catch (error) {
    console.error('Error al reemplazar marcadores en la plantilla:', error);
    throw error;
  }
}

  // Evento para agregar un nuevo DatoComplementario al hacer clic en el botón correspondiente
  document.getElementById("AgregarDatoComplementario").addEventListener("click", () => agregarDatoComplementario({}));
});

// Función para eliminar un dato complementario del DOM
function eliminarDatoComplementario(contadorDC) {
  const datoComplementarioAEliminar = document.getElementById(`DatoComplementario${contadorDC}`);
  if (!datoComplementarioAEliminar) return;

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
      const idDato = datoComplementarioAEliminar.querySelector('[name^="ID_DatoComplementario"]').value;
      if (idDato) {
        fetch(`PHP/EndPoint_AJAX.php?action=eliminarDatoComplementario&ID_DatoComplementario=${idDato}`, { method: 'POST' })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              datoComplementarioAEliminar.parentNode.removeChild(datoComplementarioAEliminar);
              
              // Actualiza los contadores y atributos de los elementos restantes
              actualizarContadoresYElementosDC();
              
              Swal.fire('Eliminado!', 'El dato complementario ha sido eliminado.', 'success');
            } else {
              console.error('Error al eliminar el dato complementario:', data.message);
              Swal.fire('Error!', 'No se pudo eliminar el dato complementario.', 'error');
            }
          })
          .catch(error => {
            console.error('Error al realizar la petición AJAX:', error);
            Swal.fire('Error!', 'Ocurrió un error al realizar la solicitud.', 'error');
          });
      } else {
        contenedorDC.removeChild(datoComplementarioAEliminar);
        actualizarContadoresYElementosDC();
      }
    }
  });
}

// Función para actualizar los contadores y elementos después de la eliminación
function actualizarContadoresYElementosDC() {
  let nuevoNumeroDC = 0;
  const datosComplementarios = contenedorDC.querySelectorAll("div[id^='DatoComplementario']");
  datosComplementarios.forEach(datoComplementario => {
    nuevoNumeroDC++;
    datoComplementario.id = `DatoComplementario${nuevoNumeroDC}`;
    actualizarElementosDC(datoComplementario, nuevoNumeroDC);
  });
  contadorDC = nuevoNumeroDC;
}

// Funcion encargada de actualizar los contadores de todos los elementos
function actualizarElementosDC(datoComplementario, nuevoNumeroDC) {
  // Actualizar el título (si existe) y otros atributos de los elementos internos
  const tituloDatoComplementario = datoComplementario.querySelector('h2');
  if (tituloDatoComplementario) {
    tituloDatoComplementario.textContent = `Dato complementario #${nuevoNumeroDC}`;
  }

  const elementosDC = datoComplementario.querySelectorAll("input, select, button, label, img, textarea");

  elementosDC.forEach(elementoDC => {
    if (elementoDC.tagName.toLowerCase() === 'label') {
      const baseForDC = elementoDC.getAttribute('for').match(/^[A-Za-z_]+/)[0];
      elementoDC.setAttribute('for', `${baseForDC}${nuevoNumeroDC}`);
    }

    if (elementoDC.id) {
      const baseIdDC = elementoDC.id.match(/^[A-Za-z_]+/)[0];
      elementoDC.id = `${baseIdDC}${nuevoNumeroDC}`;
      if (elementoDC.tagName.toLowerCase() !== 'button') {
        elementoDC.name = `${baseIdDC}${nuevoNumeroDC}`;
      }

      if (elementoDC.tagName.toLowerCase() === 'button' && elementoDC.id.includes('quitarDato')) {
        elementoDC.setAttribute('onclick', `eliminarDatoComplementario('${nuevoNumeroDC}')`);
      }
    }
  });
}
