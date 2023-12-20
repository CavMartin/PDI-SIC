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

    // Agrega el subtítulo "Persona #"
    var EntidadTitle = document.createElement("h2");
    EntidadTitle.textContent = "Persona #" + ContadorPrincipal;
    EntidadDiv.appendChild(EntidadTitle);

    // Agrega los campos de la nueva entidad
    EntidadDiv.innerHTML += `
    <input type="hidden" id="NumeroDeOrden${ContadorPrincipal}" name="NumeroDeOrden${ContadorPrincipal}" value="${ContadorPrincipal}">

    <label for="P_Rol${ContadorPrincipal}">Rol de la persona:</label>
    <select id="P_Rol${ContadorPrincipal}" name="P_Rol${ContadorPrincipal}" required>
      <option value="" disabled selected>Seleccione el rol de la persona</option>
      <option value="1">Persona aprehendida en el hecho</option>
      <option value="2">Persona domiciliada en el lugar del hecho</option>
      <option value="3">Víctima de amenazas</option>
      <option value="4">Víctima de abuso de arma de fuego</option>
      <option value="5">Víctima de lesiones</option>
      <option value="6">Víctima de heridas de arma de fuego</option>
      <option value="7">Víctima de homicidio</option>
      <option value="14">Víctima de robo calificado</option>
      <option value="15">Víctima de secuestro</option>
      <option value="8">Testigo del hecho</option>
      <option value="9">Tercero entrevistado</option>
      <option value="10">Familiar de la víctima</option>
      <option value="11">Familiar de la persona aprehendida</option>
      <option value="12">Aparente suicida - Muerte dudosa</option>
      <option value="13">Evadido</option>
    </select>

    <label for="P_FotoPersona${ContadorPrincipal}">Foto de la persona:</label>
    <input type="file" id="P_FotoPersona${ContadorPrincipal}" name="P_FotoPersona${ContadorPrincipal}" accept="image/*" onchange="procesarImagen(event, 'previewP_FotoPersona${ContadorPrincipal}', 'Base64P_FotoPersona${ContadorPrincipal}')">
    <img id="previewP_FotoPersona${ContadorPrincipal}" class="preview1" src="" alt="Previsualización de imagen">
    <textarea id="Base64P_FotoPersona${ContadorPrincipal}" name="Base64P_FotoPersona${ContadorPrincipal}" hidden></textarea>
    
    <label for="P_Apellido${ContadorPrincipal}">Apellido/s:</label>
    <input type="text" id="P_Apellido${ContadorPrincipal}" name="P_Apellido${ContadorPrincipal}" maxlength="50" onchange="transformarDatosMayusculas('P_Apellido${ContadorPrincipal}')">

    <label for="P_Nombre${ContadorPrincipal}">Nombre/s:</label>
    <input type="text" id="P_Nombre${ContadorPrincipal}" name="P_Nombre${ContadorPrincipal}" maxlength="50" onchange="transformarDatosNompropio('P_Nombre${ContadorPrincipal}')">

    <label for="P_Alias${ContadorPrincipal}">Alias:</label>
    <input type="text" id="P_Alias${ContadorPrincipal}" name="P_Alias${ContadorPrincipal}" maxlength="50" onchange="transformarDatosNompropio('P_Alias${ContadorPrincipal}')">

    <label for="P_Genero${ContadorPrincipal}">Género:</label>
    <select id="P_Genero${ContadorPrincipal}" name="P_Genero${ContadorPrincipal}" required>
      <option value="" disabled selected>Selecciona el género de la persona</option>
      <option value="1" selected>Varón</option>
      <option value="2">Mujer</option>
      <option value="3">Otro</option>
      <option value="4">Desconocido</option>
    </select>

    <label for="P_DNI${ContadorPrincipal}">Número de documento:</label>
    <input type="text" id="P_DNI${ContadorPrincipal}" name="P_DNI${ContadorPrincipal}" maxlength="10" onchange="transformarDatosNumerico('P_DNI${ContadorPrincipal}')">

    <label for="P_EstadoCivil${ContadorPrincipal}">Estado civil:</label>
    <select id="P_EstadoCivil${ContadorPrincipal}" name="P_EstadoCivil${ContadorPrincipal}" required>
      <option value="" disabled selected>Selecciona una opción</option>
      <option value="1" selected>Sin datos / Desconocido</option>
      <option value="2">Casada/o</option>
      <option value="3">Concubinato</option>
      <option value="4">Conviviente</option>
      <option value="5">Divorciada/o</option>
      <option value="6">Soltera/o</option>
      <option value="7">Unión civil</option>
      <option value="8">Viuda/o</option>
    </select>

    <label for="P_Pais${ContadorPrincipal}">País de origen / Nacionalidad:</label>
    <input type="text" id="P_Pais${ContadorPrincipal}" name="P_Pais${ContadorPrincipal}" value="ARGENTINA"  maxlength="50" onchange="transformarDatosMayusculas('P_Pais${ContadorPrincipal}')" >

    <div class="lugaresContainer">
    <input type="hidden" name="ContadorSecundario${ContadorPrincipal}" id="ContadorSecundario${ContadorPrincipal}" value="0">
    </div>

    <div class="horizontal-container">
    <button type="button" class="CustomButton B_ElementosAdicionales1" onclick="agregarLugar()">Agregar domicilio a la "Persona #${ContadorPrincipal}"</button>
    <button type="button" class="CustomButton B_ElementosAdicionales1" onclick="quitarLugar(this)" style="margin-left: 4%;">Quitar domicilio a la "Persona #${ContadorPrincipal}"</button>
    </div>

    <div class="datosComplementariosContainer">
    <input type="hidden" name="ContadorSecundario2${ContadorPrincipal}" id="ContadorSecundario2${ContadorPrincipal}" value="0">
    </div>

    <div class="horizontal-container">
    <button type="button" class="CustomButton B_ElementosAdicionales2" onclick="agregarDatoComplementario()">Agregar dato complementario a la "Persona #${ContadorPrincipal}"</button>
    <button type="button" class="CustomButton B_ElementosAdicionales2" onclick="quitarDatoComplementario(this)" style="margin-left: 4%;">Quitar dato complementario a la "Persona #${ContadorPrincipal}"</button>
    </div>
    `;

    return EntidadDiv;
}

// Función para agregar una nueva entidad principal
function agregarEntidad() {
    const EntidadDiv = crearNuevaEntidad(ContadorPrincipal);

    const lugaresContainer = EntidadDiv.querySelector('.lugaresContainer');
    lugaresContainer.dataset.datoComplementarioContadorSecundario = 0;

    const btnAgregarLugar = EntidadDiv.querySelector('.CustomButton.B_ElementosAdicionales1');
    btnAgregarLugar.onclick = function() {
      agregarLugar(EntidadDiv, ContadorPrincipal);
    };

    const datosComplementariosContainer = EntidadDiv.querySelector('.datosComplementariosContainer');
    datosComplementariosContainer.dataset.datoComplementarioContadorSecundario2 = 0;

    const btnAgregarDato = EntidadDiv.querySelector('.CustomButton.B_ElementosAdicionales2');
    btnAgregarDato.onclick = function() {
        agregarDatoComplementario(EntidadDiv, ContadorPrincipal);
    };

    EntidadPrincipal.appendChild(EntidadDiv);
    ContadorPrincipal++;
    ContadorPrincipalInput.value = ContadorPrincipal;
}

// Función para agregar un lugar a la entidad principal
function agregarLugar(EntidadPrincipal, ContadorPrincipal) {
  const lugarDiv = document.createElement("div");
  lugarDiv.className = "Lugares";

  // Obtener el número de orden del lugar
  var ContadorPrincipal = EntidadPrincipal.querySelector('[name^="NumeroDeOrden"]').value;

  let ContadorSecundario = parseInt(EntidadPrincipal.dataset.datoComplementarioContadorSecundario, 10) || 0;
  ContadorSecundario++;
  EntidadPrincipal.dataset.datoComplementarioContadorSecundario = ContadorSecundario;

  // Actualiza el valor del campo input
  const inputContadorSecundario = EntidadPrincipal.querySelector(`#ContadorSecundario${ContadorPrincipal}`);
  inputContadorSecundario.setAttribute('value', ContadorSecundario);

  lugarDiv.innerHTML = `
  <h3>Persona #${ContadorPrincipal} - Domicilio #${ContadorSecundario}</h3>

  <input type="hidden" id="Entidad${ContadorPrincipal}_L_NumeroDeOrden${ContadorSecundario}" name="Entidad${ContadorPrincipal}_L_NumeroDeOrden${ContadorSecundario}" value="${ContadorSecundario}">

  <label for="Entidad${ContadorPrincipal}_L_Rol${ContadorSecundario}">Clasificación del domicilio ${ContadorSecundario}:</label>
  <select id="Entidad${ContadorPrincipal}_L_Rol${ContadorSecundario}" name="Entidad${ContadorPrincipal}_L_Rol${ContadorSecundario}" required>
  <option value="" disabled selected>Seleccione el tipo de domicilio</option>
      <option value="3" selected>Domicilio registrado según RENAPER</option>
      <option value="4">Domicilio aportado por la persona</option>
      <option value="5">Domicilio aportado por terceros</option>
      <option value="6">Domicilio registrado en bases de datos policiales</option>
      <option value="7">Domicilio registrado según padrón electoral provincial 2017</option>
      <option value="8">Domicilio registrado según padrón electoral provincial 2023</option>
      <option value="9">Domicilio registrado según fuentes abiertas / periodísticas</option>
  </select>
  
  <input type="hidden" id="Entidad${ContadorPrincipal}_L_TipoLugar${ContadorSecundario}" name="Entidad${ContadorPrincipal}_L_TipoLugar${ContadorSecundario}" value="21">
 
  <label for="Entidad${ContadorPrincipal}_L_Calle${ContadorSecundario}">Domicilio ${ContadorSecundario} - Calle:</label>
  <input type="text" id="Entidad${ContadorPrincipal}_L_Calle${ContadorSecundario}" name="Entidad${ContadorPrincipal}_L_Calle${ContadorSecundario}" maxlength="50" placeholder="Nombre de la calle / ruta" onchange="transformarDatosNompropio('Entidad${ContadorPrincipal}_L_Calle${ContadorSecundario}')" required>
  
  <label for="Entidad${ContadorPrincipal}_L_AlturaCatastral${ContadorSecundario}">Domicilio ${ContadorSecundario} - Altura catastral:</label>
  <input type="text" id="Entidad${ContadorPrincipal}_L_AlturaCatastral${ContadorSecundario}" name="Entidad${ContadorPrincipal}_L_AlturaCatastral${ContadorSecundario}" maxlength="5" placeholder="Número correspondiente a la altura catastral deldomicilio" onchange="transformarDatosNumerico('Entidad${ContadorPrincipal}_L_AlturaCatastral${ContadorSecundario}')">
  
  <label for="Entidad${ContadorPrincipal}_L_CalleDetalle${ContadorSecundario}">Domicilio ${ContadorSecundario} - Detalle:</label>
  <input type="text" id="Entidad${ContadorPrincipal}_L_CalleDetalle${ContadorSecundario}" name="Entidad${ContadorPrincipal}_L_CalleDetalle${ContadorSecundario}" maxlength="50" placeholder="Detalle adicional del domicilio. Ej: Bis / Piso N° / Depto N°">
  
  <label for="Entidad${ContadorPrincipal}_L_Barrio${ContadorSecundario}">Domicilio ${ContadorSecundario} - Barrio:</label>
  <input type="text" id="Entidad${ContadorPrincipal}_L_Barrio${ContadorSecundario}" name="Entidad${ContadorPrincipal}_L_Barrio${ContadorSecundario}" onchange="transformarDatosNompropio('Entidad${ContadorPrincipal}_L_Barrio${ContadorSecundario}')">
  
  <label for="Entidad${ContadorPrincipal}_L_Localidad${ContadorSecundario}">Domicilio ${ContadorSecundario} - Localidad:</label>
  <input type="text" id="Entidad${ContadorPrincipal}_L_Localidad${ContadorSecundario}" name="Entidad${ContadorPrincipal}_L_Localidad${ContadorSecundario}" list="globalSugerenciasCiudades" value="Rosario" maxlength="50">
  <datalist id="sugerenciasCiudades">
  </datalist>
  
  <label for="Entidad${ContadorPrincipal}_L_Provincia${ContadorSecundario}">Domicilio ${ContadorSecundario} - Provincia:</label>
  <input type="text" id="Entidad${ContadorPrincipal}_L_Provincia${ContadorSecundario}" name="Entidad${ContadorPrincipal}_L_Provincia${ContadorSecundario}" list="globalSugerenciasProvincias" value="Santa Fe" maxlength="50">
  <datalist id="sugerenciasProvincias">
  </datalist>
  
  <label for="Entidad${ContadorPrincipal}_L_Pais${ContadorSecundario}">Domicilio ${ContadorSecundario} - País:</label>
  <input type="text" id="Entidad${ContadorPrincipal}_L_Pais${ContadorSecundario}" name="Entidad${ContadorPrincipal}_L_Pais${ContadorSecundario}" value="ARGENTINA"  maxlength="50" onchange="transformarDatosMayusculas('Entidad${ContadorPrincipal}_L_Pais${ContadorSecundario}')" >
  
  <label for="Entidad${ContadorPrincipal}_L_Coordenadas${ContadorSecundario}">Domicilio ${ContadorSecundario} - Coordenadas:</label>
  <input type="text" id="Entidad${ContadorPrincipal}_L_Coordenadas${ContadorSecundario}" name="Entidad${ContadorPrincipal}_L_Coordenadas${ContadorSecundario}">
  `;

  EntidadPrincipal.querySelector('.lugaresContainer').appendChild(lugarDiv);
}


// Función para quitar un dato domicilio de una persona
function quitarLugar(button) {
  const EntidadPrincipal = button.closest('.EntidadPrincipal');
  const lugaresContainer = EntidadPrincipal.querySelector('.lugaresContainer');
  const lugares = lugaresContainer.querySelectorAll('.Lugares');

  if (lugares.length > 0) {
      if (confirm("¿Estás seguro de que deseas quitar el último domicilio agregado a la persona ?")) {
        lugaresContainer.removeChild(lugares[lugares.length - 1]);

          // Disminuir el valor de ContadorSecundario
          const currentCount = parseInt(EntidadPrincipal.dataset.datoComplementarioContadorSecundario, 10) - 1;
          EntidadPrincipal.dataset.datoComplementarioContadorSecundario = currentCount;

          // Actualiza el valor del campo input
          var ContadorPrincipal = EntidadPrincipal.querySelector('[name^="NumeroDeOrden"]').value; // Obtener el número de orden del lugar
          const inputContadorSecundario = EntidadPrincipal.querySelector(`#ContadorSecundario${ContadorPrincipal}`);
          inputContadorSecundario.setAttribute('value', currentCount);
      }
  } else {
      alert("No hay domicilios para quitar.");
  }
}

// Función para agregar un dato complementario a la entidad principal
function agregarDatoComplementario(EntidadPrincipal, ContadorPrincipal) {
  const datoComplementarioDiv = document.createElement("div");
  datoComplementarioDiv.className = "DatosComplementarios";

  // Obtener el número de orden del lugar
  var ContadorPrincipal = EntidadPrincipal.querySelector('[name^="NumeroDeOrden"]').value;

  let ContadorSecundario2 = parseInt(EntidadPrincipal.dataset.datoComplementarioContadorSecundario2, 10) || 0;
  ContadorSecundario2++;
  EntidadPrincipal.dataset.datoComplementarioContadorSecundario2 = ContadorSecundario2;

  // Actualiza el valor del campo input
  const inputContadorSecundario2 = EntidadPrincipal.querySelector(`#ContadorSecundario2${ContadorPrincipal}`);
  inputContadorSecundario2.setAttribute('value', ContadorSecundario2);

  datoComplementarioDiv.innerHTML = `
  <input type="hidden" id="Entidad${ContadorPrincipal}_DC_NumeroDeOrden${ContadorSecundario2}" name="Entidad${ContadorPrincipal}_DC_NumeroDeOrden${ContadorSecundario2}" value="${ContadorSecundario2}">

      <label for="Entidad${ContadorPrincipal}_DC_TipoDatoComplementario${ContadorSecundario2}" style="text-align: center;">Tipo de dato complementario #${ContadorSecundario2}:</label>
      <select id="Entidad${ContadorPrincipal}_DC_TipoDatoComplementario${ContadorSecundario2}" name="Entidad${ContadorPrincipal}_DC_TipoDatoComplementario${ContadorSecundario2}" required>
          <option value="" disabled selected>Seleccione el tipo de dato complementario</option>
          <option value="2">1 - Otras imagenes de la persona</option>
          <option value="9">2 - Registros de cartas de incidencias por sistema 911</option>
          <option value="10">3 - Registros de incidencias priorizadas</option>
          <option value="11">4 - Registros policiales de analisis criminal - Registros de detenidos</option>
          <option value="12">5 - Registros policiales de analisis criminal - Planilla de antecedentes</option>
          <option value="13">6 - Registros policiales de analisis criminal - Planilla de secuestro de armas de fuego</option>
          <option value="14">7 - Registros policiales de analisis criminal - Planilla de homicidios y suicidios</option>
          <option value="16">8 - Partes de A.U.O.P de la URI</option>
          <option value="15">9 - Partes de subjefatura de la URII</option>
          <option value="17">10 - Partes de la agencia de investigación criminal</option>
          <option value="19">11 - Padrón electoral provincial 2017 (Posibles convivientes)</option>
          <option value="20">12 - Padrón electoral provincial 2023 (Posibles convivientes)</option>
          <option value="21">13 - Sistema condor - Consulta por personas</option>
          <option value="33">14 - Sistema condor - Ficha prontuarial</option>
          <option value="23">15 - Registros en base de datos ACRIM</option>
          <option value="24">16 - Registros en el sistema integral provincial de análisis criminal (SIPAC)</option>
          <option value="25">17 - Registros en base de datos de los centro territoriales de denuncias (CTD)</option>
          <option value="27">18 - OSINT - Fuentes abiertas</option>
          <option value="28">19 - SIFCOP - Consulta sobre personas</option>
          <option value="31">20 - Plataforma MIMINSEG - Registros de fuerzas federales</option>
          <option value="32">21 - Sistema de antecedentes de gendarmería nacional argentina</option>
          <option value="1">22 - Otra información complementaria</option>
      </select>

      <label for="Entidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario2}" style="display: none;">Imagen adjunta:</label>
      <input type="file" id="Entidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario2}" name="Entidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario2}" accept="image/*" style="display: none;" onchange="procesarImagen(event, 'previewEntidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario2}', 'Entidad${ContadorPrincipal}_Base64DC_Imagen${ContadorSecundario2}')">
      <img id="previewEntidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario2}" class="preview2" src="" alt="Previsualización de imagen">
      <textarea id="Entidad${ContadorPrincipal}_Base64DC_Imagen${ContadorSecundario2}" name="Entidad${ContadorPrincipal}_Base64DC_Imagen${ContadorSecundario2}" hidden></textarea>

      <label for="Entidad${ContadorPrincipal}_DC_Texto${ContadorSecundario2}" style="display: none;">Descripción:</label>
      <textarea id="Entidad${ContadorPrincipal}_DC_Texto${ContadorSecundario2}" name="Entidad${ContadorPrincipal}_DC_Texto${ContadorSecundario2}" style="display: none;"></textarea>
  `;

  EntidadPrincipal.querySelector('.datosComplementariosContainer').appendChild(datoComplementarioDiv);

  // Obtener referencias a los campos de texto y de imagen
  const tipoDatoComplementario = EntidadPrincipal.querySelector(`#Entidad${ContadorPrincipal}_DC_TipoDatoComplementario${ContadorSecundario2}`);
  const labelTextoDatoComplementario = EntidadPrincipal.querySelector(`label[for="Entidad${ContadorPrincipal}_DC_Texto${ContadorSecundario2}"]`);
  const textoDatoComplementario = EntidadPrincipal.querySelector(`#Entidad${ContadorPrincipal}_DC_Texto${ContadorSecundario2}`);
  const labelImagenDatoComplementario = EntidadPrincipal.querySelector(`label[for="Entidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario2}"]`);
  const imagenDatoComplementario = EntidadPrincipal.querySelector(`#Entidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario2}`);
  const imagenDatoComplementarioPreview = EntidadPrincipal.querySelector(`#previewEntidad${ContadorPrincipal}_DC_Imagen${ContadorSecundario2}`);
  const ImagenBase64DatoComplementario = EntidadPrincipal.querySelector(`#Entidad${ContadorPrincipal}_Base64DC_Imagen${ContadorSecundario2}`);

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

      case '2': // Cuando el valor sea 2 (solo imagen visible)
      case '19': // Cuando el valor sea 19 (solo imagen visible)
      case '20': // Cuando el valor sea 20 (solo imagen visible)
      case '21': // Cuando el valor sea 20 (solo imagen visible)

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
      case '11': // Cuando el valor sea 11 (solo texto visible)
      case '12': // Cuando el valor sea 12 (solo texto visible)
      case '13': // Cuando el valor sea 13 (solo texto visible)
      case '14': // Cuando el valor sea 14 (solo texto visible)
      case '15': // Cuando el valor sea 15 (solo texto visible)
      case '16': // Cuando el valor sea 16 (solo texto visible)
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

// Función para quitar un dato complementario de una persona
function quitarDatoComplementario(button) {
    const EntidadPrincipal = button.closest('.EntidadPrincipal');
    const datosContainer = EntidadPrincipal.querySelector('.datosComplementariosContainer');
    const datosComplementarios = datosContainer.querySelectorAll('.DatosComplementarios');

    if (datosComplementarios.length > 0) {
        if (confirm("¿Estás seguro de que deseas quitar el dato complementario agregado a la persona ?")) {
            datosContainer.removeChild(datosComplementarios[datosComplementarios.length - 1]);

            // Disminuir el valor de ContadorSecundario2
            const currentCount = parseInt(EntidadPrincipal.dataset.datoComplementarioContadorSecundario2, 10) - 1;
            EntidadPrincipal.dataset.datoComplementarioContadorSecundario2 = currentCount;

            // Actualiza el valor del campo input
            var ContadorPrincipal = EntidadPrincipal.querySelector('[name^="NumeroDeOrden"]').value; // Obtener el número de orden del lugar
            const inputContadorSecundario2 = EntidadPrincipal.querySelector(`#ContadorSecundario2${ContadorPrincipal}`);
            inputContadorSecundario2.setAttribute('value', currentCount);
        }
    } else {
        alert("No hay datos complementarios para quitar.");
    }
}

// Función para deshacer la última instancia de una persona
function quitarEntidad() {
    if (ContadorPrincipal > 1) {
      if (confirm("¿Estás seguro de que deseas quitar la '" + "Persona #" + (ContadorPrincipal - 1) + "'? Esto borrará los datos cargados en esa instancia.")) {
        ContadorPrincipal--;
            EntidadPrincipal.removeChild(EntidadPrincipal.lastChild);
            ContadorPrincipalInput.value = ContadorPrincipal;
        }
    }
}

