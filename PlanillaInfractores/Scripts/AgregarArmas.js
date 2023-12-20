var ContadorPrincipal = 1;
var EntidadPrincipal = document.getElementById("EntidadPrincipal");
var ContadorPrincipalInput = document.getElementById("ContadorPrincipal");

// Función para crear una nueva entidad
function crearNuevaEntidad(ContadorPrincipal) {
    var EntidadDiv = document.createElement("div");
    EntidadDiv.className = "EntidadPrincipal";

    // Agrega el subtítulo "Arma de fuego #"
    var EntidadTitle = document.createElement("h2");
    EntidadTitle.textContent = "Arma de fuego #" + ContadorPrincipal;
    EntidadDiv.appendChild(EntidadTitle);

    // Agrega los campos de la nueva entidad
    EntidadDiv.innerHTML += `
    <input type="hidden" id="NumeroDeOrden${ContadorPrincipal}" name="NumeroDeOrden${ContadorPrincipal}" value="${ContadorPrincipal}">

    <label for="AF_EsDeFabricacionCasera${ContadorPrincipal}" style="text-align: center;">¿Se trata de un arma de fabricación casera?</label>
    <select id="AF_EsDeFabricacionCasera${ContadorPrincipal}" name="AF_EsDeFabricacionCasera${ContadorPrincipal}" style="text-align: center;" onchange="esFabricacionCasera(${ContadorPrincipal})" required>
      <option value="" disabled selected>Seleccione una opción</option>
      <option value="1">Sí</option>
      <option value="0" selected>No</option>
    </select>

    <label for="AF_TipoAF${ContadorPrincipal}">Clasificación del arma de fuego:</label>
    <select id="AF_TipoAF${ContadorPrincipal}" name="AF_TipoAF${ContadorPrincipal}" required>
      <option value="" disabled selected>Seleccione un tipo de arma de fuego</option>
      <option value="1">Carabina</option>
      <option value="2">Escopeta</option>
      <option value="3">Fusil</option>
      <option value="4">Pistola</option>
      <option value="5">Pistola ametralladora</option>
      <option value="6">Pistolón</option>
      <option value="7">Revolver</option>
    </select>

    <label for="AF_Marca${ContadorPrincipal}" style="display: block">Marca / Fabricante:</label>
    <input type="text" id="AF_Marca${ContadorPrincipal}" name="AF_Marca${ContadorPrincipal}" style="display: block" maxlength="25" placeholder="Especifique la marca del arma de fuego" onchange="transformarDatosMayusculas('AF_Marca${ContadorPrincipal}')">

    <label for="AF_Modelo${ContadorPrincipal}" style="display: block">Modelo del arma:</label>
    <input type="text" id="AF_Modelo${ContadorPrincipal}" name="AF_Modelo${ContadorPrincipal}" style="display: block" maxlength="25" placeholder="Especifique el modelo del arma de fuego" onchange="transformarDatosMayusculas('AF_Modelo${ContadorPrincipal}')">

    <label for="AF_Calibre${ContadorPrincipal}" style="display: block">Calibre:</label>
    <input type="text" id="AF_Calibre${ContadorPrincipal}" name="AF_Calibre${ContadorPrincipal}" style="display: block" maxlength="10" placeholder="Especifique el calibre del arma de fuego" >

    <label for="AF_PoseeNumeracionVisible${ContadorPrincipal}" style="display: block">¿Posee numeración visible?</label>
    <select id="AF_PoseeNumeracionVisible${ContadorPrincipal}" name="AF_PoseeNumeracionVisible${ContadorPrincipal}" style="display: block" onchange="mostrarNumeracion(${ContadorPrincipal})">
      <option value="" disabled selected>Selecciona una opción</option>
      <option value="1">Sí</option>
      <option value="0" selected>No</option>
    </select>

    <label for="AF_NumeroDeSerie${ContadorPrincipal}" style="display: none">Número de serie:</label>
    <input type="text" id="AF_NumeroDeSerie${ContadorPrincipal}" name="AF_NumeroDeSerie${ContadorPrincipal}" maxlength="25" placeholder="Especifique el número de seria del arma de fuego" onchange="transformarDatosAlfaNumerico('AF_NumeroDeSerie${ContadorPrincipal}')" style="display: none">


    <div class="datosComplementariosContainer">
    <input type="hidden" name="ContadorSecundario${ContadorPrincipal}" id="ContadorSecundario${ContadorPrincipal}" value="0">
    </div>

    <div class="horizontal-container">
    <button type="button" class="CustomButton B_ElementosAdicionales1" onclick="agregarDatoComplementario()">Agregar dato complementario al "Arma de fuego #${ContadorPrincipal}"</button>
    <button type="button" class="CustomButton B_ElementosAdicionales1" onclick="quitarDatoComplementario(this)" style="margin-left: 4%;">Quitar dato complementario al "Arma de fuego #${ContadorPrincipal}"</button>
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

      <label for="Entidad${ContadorPrincipal}_DC_TipoDatoComplementario${ContadorSecundario}">Tipo de dato complementario #${ContadorSecundario}:</label>
      <select id="Entidad${ContadorPrincipal}_DC_TipoDatoComplementario${ContadorSecundario}" name="Entidad${ContadorPrincipal}_DC_TipoDatoComplementario${ContadorSecundario}" required>
          <option value="" disabled selected>Seleccione el tipo de dato complementario</option>
          <option value="7">1 - Imagen del arma de fuego</option>
          <option value="26">2 - Registros disponibles de armas de fuego secuestradas (Planilla D4)</option>
          <option value="27">3 - OSINT - Fuentes abiertas</option>
          <option value="30">4 - SIFCOP - Consulta sobre elementos</option>
          <option value="31">5 - Plataforma MIMINSEG - Registros de fuerzas federales</option>
          <option value="32">6 - Sistema de antecedentes de gendarmería nacional argentina</option>
          <option value="1">7 - Otra información complementaria</option>
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

      case '7': // Cuando el valor sea 7 (solo imagen visible)
          labelTextoDatoComplementario.style.display = 'none';
          textoDatoComplementario.style.display = 'none';
          labelImagenDatoComplementario.style.display = 'block';
          imagenDatoComplementario.style.display = 'block';

          // Resetear los campos de texto y de imagen
          textoDatoComplementario.value = '';
          imagenDatoComplementario.value = '';
          ImagenBase64DatoComplementario.value = '';

          break;

      case '26': // Cuando el valor sea 26 (solo texto visible)
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
        if (confirm("¿Estás seguro de que deseas quitar el último dato complementario agregado?")) {
            datosContainer.removeChild(datosComplementarios[datosComplementarios.length - 1]);

            // Disminuir el valor de ContadorSecundario
            const currentCount = parseInt(EntidadPrincipal.dataset.datoComplementarioContadorSecundario, 10) - 1;
            EntidadPrincipal.dataset.datoComplementarioContadorSecundario = currentCount;

            // Actualiza el valor del campo input
            var ContadorPrincipal = EntidadPrincipal.querySelector('[name^="NumeroDeOrden"]').value; // Obtener el número de orden del lugar
            const inputContadorSecundario = EntidadPrincipal.querySelector(`#ContadorSecundario${ContadorPrincipal}`);
            inputContadorSecundario.setAttribute('value', currentCount);
        }
    } else {
        alert("No hay datos complementarios para quitar.");
    }
}

// Función para deshacer la última instancia de lugar
function quitarEntidad() {
    if (ContadorPrincipal > 1) {
      if (confirm("¿Estás seguro de que deseas quitar el '" + "Arma de fuego #" + (ContadorPrincipal - 1) + "'? Esto borrará los datos cargados en esta instancia.")) {
        ContadorPrincipal--;
            EntidadPrincipal.removeChild(EntidadPrincipal.lastChild);
            ContadorPrincipalInput.value = ContadorPrincipal;
        }
    }
}


// Ocultar campos innesesarios para un arma de fabricacion casera
function esFabricacionCasera(ContadorPrincipal) {
  var select = document.getElementById("AF_EsDeFabricacionCasera" + ContadorPrincipal);
  var marcaLabel = document.querySelector('label[for="AF_Marca' + ContadorPrincipal + '"]');
  var marcaInput = document.getElementById("AF_Marca" + ContadorPrincipal);
  var modeloLabel = document.querySelector('label[for="AF_Modelo' + ContadorPrincipal + '"]');
  var modeloInput = document.getElementById("AF_Modelo" + ContadorPrincipal);
  var calibreLabel = document.querySelector('label[for="AF_Calibre' + ContadorPrincipal + '"]');
  var calibreInput = document.getElementById("AF_Calibre" + ContadorPrincipal);
  var numeracionVisibleLabel = document.querySelector('label[for="AF_PoseeNumeracionVisible' + ContadorPrincipal + '"]');
  var numeracionVisibleInput = document.getElementById("AF_PoseeNumeracionVisible" + ContadorPrincipal);

  if (select.value === "1") {
    marcaLabel.style.display = "none";
    marcaInput.style.display = "none";
    modeloLabel.style.display = "none";
    modeloInput.style.display = "none";
    calibreLabel.style.display = "none";
    calibreInput.style.display = "none";
    numeracionVisibleLabel.style.display = "none";
    numeracionVisibleInput.style.display = "none";
  } else {
    marcaLabel.style.display = "block";
    marcaInput.style.display = "block";
    modeloLabel.style.display = "block";
    modeloInput.style.display = "block";
    calibreLabel.style.display = "block";
    calibreInput.style.display = "block";
    numeracionVisibleLabel.style.display = "block";
    numeracionVisibleInput.style.display = "block";
  }
}

// Mostrar si el arma posee numeracion visible
function mostrarNumeracion(ContadorPrincipal) {
  var select = document.getElementById("AF_PoseeNumeracionVisible" + ContadorPrincipal);
  var numeroSerieLabel = document.querySelector('label[for="AF_NumeroDeSerie' + ContadorPrincipal + '"]');
  var numeroSerieInput = document.getElementById("AF_NumeroDeSerie" + ContadorPrincipal);

  if (select.value === "1") {
    numeroSerieLabel.style.display = "block";
    numeroSerieInput.style.display = "block"

  } else {
    numeroSerieLabel.style.display = "none";
    numeroSerieInput.style.display = "none";
  }
}
