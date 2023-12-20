var ContadorPrincipal = 1;
var EntidadPrincipal = document.getElementById("EntidadPrincipal");
var ContadorPrincipalInput = document.getElementById("ContadorPrincipal");

// Crear y llenar los datalist globales
function inicializarDataLists() {
    const datalistCiudades = document.querySelector('#globalSugerenciasCiudades');
    const datalistProvincias = document.querySelector('#globalSugerenciasProvincias');
    
    ciudades.forEach(ciudad => {
        const optionCiudades = document.createElement('option');
        optionCiudades.value = ciudad;
        datalistCiudades.appendChild(optionCiudades);
    });

    provincias.forEach(provincia => {
        const optionProvincias = document.createElement('option');
        optionProvincias.value = provincia;
        datalistProvincias.appendChild(optionProvincias);
    });
}

// Función para crear una nueva entidad
function crearNuevaEntidad(ContadorPrincipal) {
    var EntidadDiv = document.createElement("div");
    EntidadDiv.className = "EntidadPrincipal";

    // Agrega el subtítulo "Lugar #"
    var EntidadTitle = document.createElement("h2");
    EntidadTitle.textContent = "Lugar #" + ContadorPrincipal;
    EntidadDiv.appendChild(EntidadTitle);

    // Agrega los campos de la nueva entidad
    EntidadDiv.innerHTML += `
    <input type="hidden" id="NumeroDeOrden${ContadorPrincipal}" name="NumeroDeOrden${ContadorPrincipal}" value="${ContadorPrincipal}">

    <label for="L_Rol${ContadorPrincipal}">Rol del lugar:</label>
    <select id="L_Rol${ContadorPrincipal}" name="L_Rol${ContadorPrincipal}" required>
    <option value="" disabled selected>Seleccione el rol del lugar</option>
        <option value="1" selected>Lugar del hecho</option>
        <option value="2">Lugar de finalización del hecho</option>
    </select>

    <label for="L_TipoLugar${ContadorPrincipal}">Tipo de lugar:</label>
    <select id="L_TipoLugar${ContadorPrincipal}" name="L_TipoLugar${ContadorPrincipal}" required>
        <option value="" disabled selected>Seleccione el tipo de lugar</option>
        <option value="1">Sin clasificar</option>
        <option value="2" selected>Vía pública</option>
        <option value="3">Plaza / Parque</option>
        <option value="4">Ruta / camino</option>
        <option value="5">Cochera / playa de estacionamiento</option>
        <option value="6">Descampado</option>
        <option value="7">Exterior de asociación civil</option>
        <option value="8">Interior de asociación civil</option>
        <option value="9">Exterior de comercio</option>
        <option value="10">Interior de comercio</option>
        <option value="11">Exterior de dependencia pública</option>
        <option value="12">Interior de dependencia pública</option>
        <option value="13">Exterior de industria</option>
        <option value="14">Interior de industria</option>
        <option value="15">Exterior de inmueble</option>
        <option value="16">Interior de inmueble</option>
        <option value="17">Exterior de institución pública</option>
        <option value="18">Interior de institución pública</option>
        <option value="19">Exterior de vehículo</option>
        <option value="20">Interior de vehículo</option>
    </select>

    <label for="L_NombreLugarEspecifico${ContadorPrincipal}">Nombre del lugar:</label>
    <input type="text" id="L_NombreLugarEspecifico${ContadorPrincipal}" name="L_NombreLugarEspecifico${ContadorPrincipal}" maxlength="50" placeholder="Nombre del comercio, institución o lugar involucrado">

    <label for="L_Calle${ContadorPrincipal}">Calle:</label>
    <input type="text" id="L_Calle${ContadorPrincipal}" name="L_Calle${ContadorPrincipal}" maxlength="50" placeholder="Nombre de la calle / ruta" onchange="transformarDatosNompropio('L_Calle${ContadorPrincipal}')" required>

    <label for="L_AlturaCatastral${ContadorPrincipal}">Altura catastral:</label>
    <input type="text" id="L_AlturaCatastral${ContadorPrincipal}" name="L_AlturaCatastral${ContadorPrincipal}" maxlength="5" placeholder="Número correspondiente a la altura catastral deldomicilio" onchange="transformarDatosNumerico('L_AlturaCatastral${ContadorPrincipal}')">

    <label for="L_CalleDetalle${ContadorPrincipal}">Detalle del domicilio:</label>
    <input type="text" id="L_CalleDetalle${ContadorPrincipal}" name="L_CalleDetalle${ContadorPrincipal}" maxlength="50" placeholder="Detalle adicional del domicilio. Ej: Bis / Piso N° / Depto N°">

    <label for="L_Interseccion1${ContadorPrincipal}">Intersección 1:</label>
    <input type="text" id="L_Interseccion1${ContadorPrincipal}" name="L_Interseccion1${ContadorPrincipal}" placeholder="Entre calle... " onchange="transformarDatosNompropio('L_Interseccion1${ContadorPrincipal}')">

    <label for="L_Interseccion2${ContadorPrincipal}">Intersección 2:</label>
    <input type="text" id="L_Interseccion2${ContadorPrincipal}" name="L_Interseccion2${ContadorPrincipal}" placeholder="y calle..." onchange="transformarDatosNompropio('L_Interseccion2${ContadorPrincipal}')">

    <label for="L_Barrio${ContadorPrincipal}">Barrio:</label>
    <input type="text" id="L_Barrio${ContadorPrincipal}" name="L_Barrio${ContadorPrincipal}" onchange="transformarDatosNompropio('L_Barrio${ContadorPrincipal}')">

    <label for="L_Localidad${ContadorPrincipal}">Localidad:</label>
    <input type="text" id="L_Localidad${ContadorPrincipal}" name="L_Localidad${ContadorPrincipal}" list="globalSugerenciasCiudades" value="Rosario" maxlength="50">
    <datalist id="sugerenciasCiudades">
    </datalist>

    <label for="L_Provincia${ContadorPrincipal}">Provincia:</label>
    <input type="text" id="L_Provincia${ContadorPrincipal}" name="L_Provincia${ContadorPrincipal}" list="globalSugerenciasProvincias" value="Santa Fe" maxlength="50">
    <datalist id="sugerenciasProvincias">
    </datalist>

    <label for="L_Pais${ContadorPrincipal}">País:</label>
    <input type="text" id="L_Pais${ContadorPrincipal}" name="L_Pais${ContadorPrincipal}" value="ARGENTINA"  maxlength="50" onchange="transformarDatosMayusculas('L_Pais${ContadorPrincipal}')" >

    <label for="L_Coordenadas${ContadorPrincipal}">Coordenadas:</label>
    <input type="text" id="L_Coordenadas${ContadorPrincipal}" name="L_Coordenadas${ContadorPrincipal}">

    <div class="datosComplementariosContainer">
    <input type="hidden" name="ContadorSecundario${ContadorPrincipal}" id="ContadorSecundario${ContadorPrincipal}" value="0">
    </div>

    <div class="horizontal-container">
    <button type="button" class="CustomButton B_ElementosAdicionales1" onclick="agregarDatoComplementario()">Agregar dato complementario al "Lugar #${ContadorPrincipal}"</button>
    <button type="button" class="CustomButton B_ElementosAdicionales1" onclick="quitarDatoComplementario(this)" style="margin-left: 4%;">Quitar dato complementario al "Lugar #${ContadorPrincipal}"</button>
    </div>
    `;

    return EntidadDiv;
}

// Función para agregar una nueva entidad principal
function agregarEntidad() {
    const EntidadDiv = crearNuevaEntidad(ContadorPrincipal);

    const datosComplementariosContainer = EntidadDiv.querySelector('.datosComplementariosContainer');
    datosComplementariosContainer.dataset.datoComplementarioContadorSecundario = 0;

    const btnAgregarDato = EntidadDiv.querySelector('.CustomButton.B_ElementosAdicionales1');
    btnAgregarDato.onclick = function() {
        agregarDatoComplementario(EntidadDiv, ContadorPrincipal);
    };

    EntidadPrincipal.appendChild(EntidadDiv);
    ContadorPrincipal++;
    ContadorPrincipalInput.value = ContadorPrincipal;
}

// Función para agregar un dato complementario a la entidad principal
function agregarDatoComplementario(EntidadPrincipal, ContadorPrincipal) {
  const datoComplementarioDiv = document.createElement("div");
  datoComplementarioDiv.className = "DatosComplementarios";

  // Obtener el número de orden del lugar
  var ContadorPrincipal = EntidadPrincipal.querySelector('[name^="NumeroDeOrden"]').value;

  let ContadorSecundario = parseInt(EntidadPrincipal.dataset.datoComplementarioContadorSecundario, 10) || 0;
  ContadorSecundario++;
  EntidadPrincipal.dataset.datoComplementarioContadorSecundario = ContadorSecundario;

  // Actualiza el valor del campo input
  const inputContadorSecundario = EntidadPrincipal.querySelector(`#ContadorSecundario${ContadorPrincipal}`);
  inputContadorSecundario.setAttribute('value', ContadorSecundario);

  datoComplementarioDiv.innerHTML = `
  <input type="hidden" id="Entidad${ContadorPrincipal}_DC_NumeroDeOrden${ContadorSecundario}" name="Entidad${ContadorPrincipal}_DC_NumeroDeOrden${ContadorSecundario}" value="${ContadorSecundario}">

      <label for="Entidad${ContadorPrincipal}_DC_TipoDatoComplementario${ContadorSecundario}" style="text-align: center;">Tipo de dato complementario #${ContadorSecundario}:</label>
      <select id="Entidad${ContadorPrincipal}_DC_TipoDatoComplementario${ContadorSecundario}" name="Entidad${ContadorPrincipal}_DC_TipoDatoComplementario${ContadorSecundario}" style="text-align: center; font-size: 1.05vw;" required>
          <option value="" disabled selected>Seleccione el tipo de dato complementario</option>
          <option value="3">1 - Imagen del lugar obtenida por fuentes abiertas</option>
          <option value="4">2 - Imagen del lugar aportada por el personal policial actuante</option>
          <option value="5">3 - Imagen satelital del lugar (Google Maps)</option>
          <option value="19">4 - Padrón electoral provincial 2017 (Posibles convivientes)</option>
          <option value="20">5 - Padrón electoral provincial 2023 (Posibles convivientes)</option>
          <option value="18">6 - Registros de PM y MR cercanos al lugar (500m)</option>
          <option value="9">7 - Registros de cartas de incidencias por sistema 911</option>
          <option value="10">8 - Registros de incidencias priorizadas</option>
          <option value="15">9 - Partes de subjefatura de la URII</option>
          <option value="17">10 - Partes de la agencia de investigación criminal</option>
          <option value="23">11 - Registros en base de datos ACRIM</option>
          <option value="24">12 - Registros en el sistema integral provincial de análisis criminal (SIPAC)</option>
          <option value="25">13 - Registros en base de datos de los centro territoriales de denuncias (CTD)</option>
          <option value="27">14 - OSINT - Fuentes abiertas</option>
          <option value="1">15 - Otra información complementaria</option>
      </select>

      <label for="Entidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario}" style="display: none;">Imagen adjunta:</label>
      <input type="file" id="Entidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario}" name="Entidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario}" accept="image/*" style="display: none;" onchange="procesarImagen(event, 'previewEntidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario}', 'Entidad${ContadorPrincipal}_Base64DC_Imagen${ContadorSecundario}')">
      <img id="previewEntidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario}" class="preview2" src="" alt="Previsualización de imagen">
      <textarea id="Entidad${ContadorPrincipal}_Base64DC_Imagen${ContadorSecundario}" name="Entidad${ContadorPrincipal}_Base64DC_Imagen${ContadorSecundario}" hidden></textarea>

      <label for="Entidad${ContadorPrincipal}_DC_Texto${ContadorSecundario}" style="display: none;">Descripción:</label>
      <textarea id="Entidad${ContadorPrincipal}_DC_Texto${ContadorSecundario}" name="Entidad${ContadorPrincipal}_DC_Texto${ContadorSecundario}" style="display: none;"></textarea>
  `;

  EntidadPrincipal.querySelector('.datosComplementariosContainer').appendChild(datoComplementarioDiv);

  // Obtener referencias a los campos de texto y de imagen
  const tipoDatoComplementario = EntidadPrincipal.querySelector(`#Entidad${ContadorPrincipal}_DC_TipoDatoComplementario${ContadorSecundario}`);
  const labelTextoDatoComplementario = EntidadPrincipal.querySelector(`label[for="Entidad${ContadorPrincipal}_DC_Texto${ContadorSecundario}"]`);
  const textoDatoComplementario = EntidadPrincipal.querySelector(`#Entidad${ContadorPrincipal}_DC_Texto${ContadorSecundario}`);
  const labelImagenDatoComplementario = EntidadPrincipal.querySelector(`label[for="Entidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario}"]`);
  const imagenDatoComplementario = EntidadPrincipal.querySelector(`#Entidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario}`);
  const imagenDatoComplementarioPreview = EntidadPrincipal.querySelector(`#previewEntidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario}`);
  const ImagenBase64DatoComplementario = EntidadPrincipal.querySelector(`#Entidad${ContadorPrincipal}_Base64DC_Imagen${ContadorSecundario}`);

// Agregar un evento "change" al campo tipoDatoComplementario
tipoDatoComplementario.addEventListener('change', function() {
  // Obtener el valor seleccionado
  const valorSeleccionado = tipoDatoComplementario.value;

  // Ocultar previsualización de imagen antes de restablecer los campos
  imagenDatoComplementarioPreview.style.display = 'none';

  // Mostrar u ocultar los campos de texto y de imagen según el valor seleccionado
  switch (valorSeleccionado) {
      case '': // Cuando el valor sea "nulo" (Caso por defecto preseleccionado)
          labelTextoDatoComplementario.style.display = 'none';
          textoDatoComplementario.style.display = 'none';
          labelImagenDatoComplementario.style.display = 'none';
          imagenDatoComplementario.style.display = 'none';

          break;

      case '3': // Cuando el valor sea 3 (solo imagen visible)
      case '4': // Cuando el valor sea 4 (solo imagen visible)
      case '5': // Cuando el valor sea 5 (solo imagen visible)
      case '19': // Cuando el valor sea 19 (solo imagen visible)
      case '20': // Cuando el valor sea 20 (solo imagen visible)

          labelTextoDatoComplementario.style.display = 'none';
          textoDatoComplementario.style.display = 'none';
          labelImagenDatoComplementario.style.display = 'block';
          imagenDatoComplementario.style.display = 'block';

          // Resetear los campos de texto y de imagen
          textoDatoComplementario.value = '';
          imagenDatoComplementario.value = '';
          ImagenBase64DatoComplementario.value = '';

          break;

      case '9': // Cuando el valor sea 9 (solo texto visible)
      case '10': // Cuando el valor sea 10 (solo texto visible)
      case '15': // Cuando el valor sea 15 (solo texto visible)
      case '17': // Cuando el valor sea 17 (solo texto visible)
      case '23': // Cuando el valor sea 23 (solo texto visible)
      case '24': // Cuando el valor sea 24 (solo texto visible)
      case '25': // Cuando el valor sea 25 (solo texto visible)
      
          labelImagenDatoComplementario.style.display = 'none';
          imagenDatoComplementario.style.display = 'none';
          labelTextoDatoComplementario.style.display = 'block';
          textoDatoComplementario.style.display = 'block';

          // Resetear los campos de texto y de imagen
          textoDatoComplementario.value = '';
          imagenDatoComplementario.value = '';
          ImagenBase64DatoComplementario.value = '';

          break;

      default: // Cualquier otro caso (ambos visibles)
          labelTextoDatoComplementario.style.display = 'block';
          textoDatoComplementario.style.display = 'block';
          labelImagenDatoComplementario.style.display = 'block';
          imagenDatoComplementario.style.display = 'block';

          // Resetear los campos de texto y de imagen
          textoDatoComplementario.value = '';
          imagenDatoComplementario.value = '';
          ImagenBase64DatoComplementario.value = '';

          break;
  }
});

// Agregar un evento "change" al campo de selección de imagen
imagenDatoComplementario.addEventListener('change', function() {
  // Actualizar la visibilidad de la previsualización de imagen según si se ha seleccionado una imagen
  if (imagenDatoComplementario.value) {
    imagenDatoComplementarioPreview.style.display = 'block';
  } else {
    imagenDatoComplementarioPreview.style.display = 'none';
  }
});

  // Asegúrate de que el estado inicial coincida con el valor seleccionado
  tipoDatoComplementario.dispatchEvent(new Event('change'));
}

// Función para quitar un dato complementario de un lugar
function quitarDatoComplementario(button) {
    const EntidadPrincipal = button.closest('.EntidadPrincipal');
    const datosContainer = EntidadPrincipal.querySelector('.datosComplementariosContainer');
    const datosComplementarios = datosContainer.querySelectorAll('.DatosComplementarios');

    if (datosComplementarios.length > 0) {
        // Obtener el número de orden del lugar y el ContadorPrincipal complementario para el mensaje
        const numeroDeOrden = EntidadPrincipal.querySelector('[name^="NumeroDeOrden"]').value;
        const ContadorSecundario = EntidadPrincipal.dataset.datoComplementarioContadorSecundario;

        // Mensaje más descriptivo
        const mensajeConfirmacion = `¿Estás seguro de que deseas quitar el dato complementario #${ContadorSecundario} del lugar #${numeroDeOrden}?`;

        if (confirm(mensajeConfirmacion)) {
            datosContainer.removeChild(datosComplementarios[datosComplementarios.length - 1]);

            // Disminuir el valor de ContadorSecundario
            const currentCount = parseInt(ContadorSecundario, 10) - 1;
            EntidadPrincipal.dataset.datoComplementarioContadorSecundario = currentCount;

            // Actualiza el valor del campo input
            const inputContadorSecundario = EntidadPrincipal.querySelector(`#ContadorSecundario${numeroDeOrden}`);
            inputContadorSecundario.setAttribute('value', currentCount);
        }
    } else {
        alert("No hay datos complementarios para quitar en la entidad principal #" + EntidadPrincipal.querySelector('[name^="NumeroDeOrden"]').value + ".");
    }
}

// Función para deshacer la última instancia de lugar
function quitarEntidad() {
    if (ContadorPrincipal > 1) {
        if (confirm("¿Estás seguro de que deseas quitar el '" + "Lugar #" + (ContadorPrincipal - 1) + "'? Esto borrará los datos cargados en esa instancia.")) {
            ContadorPrincipal--;
            EntidadPrincipal.removeChild(EntidadPrincipal.lastChild);
            ContadorPrincipalInput.value = ContadorPrincipal;
        }
    }
}
