var ContadorPrincipal = 1;
var EntidadPrincipal = document.getElementById("EntidadPrincipal");
var ContadorPrincipalInput = document.getElementById("ContadorPrincipal");

// Función para crear una nueva entidad
function crearNuevaEntidad(ContadorPrincipal) {
    var EntidadDiv = document.createElement("div");
    EntidadDiv.className = "EntidadPrincipal";

    // Agrega el subtítulo "Lugar #"
    var EntidadTitle = document.createElement("h2");
    EntidadTitle.textContent = "Vehículo #" + ContadorPrincipal;
    EntidadDiv.appendChild(EntidadTitle);

    // Agrega los campos de la nueva entidad
    EntidadDiv.innerHTML += `
    <input type="hidden" id="NumeroDeOrden${ContadorPrincipal}" name="NumeroDeOrden${ContadorPrincipal}" value="${ContadorPrincipal}">

    <label for="V_Rol${ContadorPrincipal}">Rol del vehículo:</label>
    <select id="V_Rol${ContadorPrincipal}" name="V_Rol${ContadorPrincipal}" required>
      <option value="" disabled selected>Selecciona una relación</option>
      <option value="1">Vehículo atacado</option>
      <option value="2">Vehículo secuestrado</option>
      <option value="3" selected>Vehículo utilizado en el ílicito</option>
    </select>

    <label for="V_TipoVehiculo${ContadorPrincipal}">Tipo de vehículo:</label>
    <select id="V_TipoVehiculo${ContadorPrincipal}" name="V_TipoVehiculo${ContadorPrincipal}" required>
      <option value="" disabled selected>Seleccione un tipo de vehículo</option>
      <option value="2">Acoplado</option>
      <option value="3" selected>Automóvil</option>
      <option value="4">Avioneta</option>
      <option value="5">Bicicleta</option>
      <option value="6">Bicicleta eléctrica</option>
      <option value="7">Camión</option>
      <option value="8">Camioneta</option>
      <option value="9">Chasis de camión</option>
      <option value="10">Ciclomotor</option>
      <option value="11">Cuatriciclo</option>
      <option value="12">Ómnibus / Colectivo / Micro</option>
      <option value="13">Embarcación a motor</option>
      <option value="14">Furgón de carga</option>
      <option value="15">Lancha</option>
      <option value="16">Máquina agrícola</option>
      <option value="17">Máquina de construcción</option>
      <option value="18">Máquina de servicios</option>
      <option value="19">Moto vehículo</option>
      <option value="20">Moto vehículo acuático</option>
      <option value="21">Tractor</option>
      <option value="22">Triciclo</option>
      <option value="23">Vehículo oficial</option>
      <option value="24">Vehículo a tracción animal (Carros)</option>
      <option value="1">Otro tipo de vehículo</option>
    </select>

    <label for="V_Color${ContadorPrincipal}">Color:</label>
    <input type="text" id="V_Color${ContadorPrincipal}" name="V_Color${ContadorPrincipal}" list="colores" maxlength="25" placeholder="Color del vehículo" onchange="transformarDatosNompropio('V_Color${ContadorPrincipal}')">
    <datalist id="colores">
      <option value="Sin Datos">Sin Datos</option>
      <option value="Amarillo">Amarillo</option>
      <option value="Anaranjado">Anaranjado</option>
      <option value="Azul">Azul</option>
      <option value="Blanco">Blanco</option>
      <option value="Gris">Gris</option>
      <option value="Marrón">Marrón</option>
      <option value="Negro">Negro</option>
      <option value="Rojo">Rojo</option>
      <option value="Rosado">Rosado</option>
      <option value="Verde">Verde</option>
      <option value="Violeta">Violeta</option>
    </datalist>    

    <label for="V_Marca${ContadorPrincipal}">Marca:</label>
    <input type="text" id="V_Marca${ContadorPrincipal}" name="V_Marca${ContadorPrincipal}" maxlength="25" placeholder="Marca del vehículo" onchange="transformarDatosMayusculas('V_Marca${ContadorPrincipal}')">

    <label for="V_Modelo${ContadorPrincipal}">Modelo:</label>
    <input type="text" id="V_Modelo${ContadorPrincipal}" name="V_Modelo${ContadorPrincipal}" maxlength="25" placeholder="Modelo del vehículo" onchange="transformarDatosMayusculas('V_Modelo${ContadorPrincipal}')">

    <label for="V_Año${ContadorPrincipal}">Año:</label>
    <input type="text" id="V_Año${ContadorPrincipal}" name="V_Año${ContadorPrincipal}" maxlength="4" placeholder="Año del vehículo" onchange="transformarDatosNumerico('V_Año${ContadorPrincipal}')">

    <label for="V_Dominio${ContadorPrincipal}">Dominio:</label>
    <input type="text" id="V_Dominio${ContadorPrincipal}" name="V_Dominio${ContadorPrincipal}" maxlength="20" placeholder="Dominio identificatorio del vehículo" onchange="transformarDatosAlfaNumerico('V_Dominio${ContadorPrincipal}')">

    <label for="V_NumeroChasis${ContadorPrincipal}">Número de chasis:</label>
    <input type="text" id="V_NumeroChasis${ContadorPrincipal}" name="V_NumeroChasis${ContadorPrincipal}" maxlength="25" onchange="transformarDatosAlfaNumerico('V_NumeroChasis${ContadorPrincipal}')">

    <label for="V_NumeroMotor${ContadorPrincipal}">Número de motor:</label>
    <input type="text" id="V_NumeroMotor${ContadorPrincipal}" name="V_NumeroMotor${ContadorPrincipal}" maxlength="25" onchange="transformarDatosAlfaNumerico('V_NumeroMotor${ContadorPrincipal}')">

    <div class="datosComplementariosContainer">
    <input type="hidden" name="ContadorSecundario${ContadorPrincipal}" id="ContadorSecundario${ContadorPrincipal}" value="0">
    </div>

    <div class="horizontal-container">
    <button type="button" class="CustomButton B_ElementosAdicionales1" onclick="agregarDatoComplementario()">Agregar dato complementario al "Vehículo #${ContadorPrincipal}"</button>
    <button type="button" class="CustomButton B_ElementosAdicionales1" onclick="quitarDatoComplementario(this)" style="margin-left: 4%;">Quitar dato complementario al "Vehículo #${ContadorPrincipal}"</button>
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
          <option value="6">1 - Imagen del vehículo</option>
          <option value="9">2 - Registros de cartas de incidencias por sistema 911</option>
          <option value="10">3 - Registros de incidencias priorizadas</option>
          <option value="15">4 - Partes de subjefatura de la URII</option>
          <option value="17">5 - Partes de la agencia de investigación criminal</option>
          <option value="22">6 - Sistema condor - Consulta por vehículos</option>
          <option value="23">7 - Registros en base de datos ACRIM</option>
          <option value="24">8 - Registros en el sistema integral provincial de análisis criminal (SIPAC)</option>
          <option value="25">9 - Registros en base de datos de los centro territoriales de denuncias (CTD)</option>
          <option value="27">10 - OSINT - Fuentes abiertas</option>
          <option value="29">11 - SIFCOP - Consulta sobre vehículos</option>
          <option value="31">12 - Plataforma MIMINSEG - Registros de fuerzas federales</option>
          <option value="32">13 - Sistema de antecedentes de gendarmería nacional argentina</option>
          <option value="1">14 - Otra información complementaria</option>
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

      case '6': // Cuando el valor sea 6 (solo imagen visible)
      case '22': // Cuando el valor sea 6 (solo imagen visible)
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

// Función para quitar un dato complementario de un vehículo
function quitarDatoComplementario(button) {
    const EntidadPrincipal = button.closest('.EntidadPrincipal');
    const datosContainer = EntidadPrincipal.querySelector('.datosComplementariosContainer');
    const datosComplementarios = datosContainer.querySelectorAll('.DatosComplementarios');

    if (datosComplementarios.length > 0) {
        // Obtener el número de orden del lugar y el ContadorPrincipal complementario para el mensaje
        const numeroDeOrden = EntidadPrincipal.querySelector('[name^="NumeroDeOrden"]').value;
        const ContadorSecundario = EntidadPrincipal.dataset.datoComplementarioContadorSecundario;

        // Mensaje más descriptivo
        const mensajeConfirmacion = `¿Estás seguro de que deseas quitar el dato complementario #${ContadorSecundario} del vehículo #${numeroDeOrden}?`;

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

// Función para deshacer la última instancia de vehículo
function quitarEntidad() {
    if (ContadorPrincipal > 1) {
        if (confirm("¿Estás seguro de que deseas quitar el vehículo #" + (ContadorPrincipal - 1) + "? Esto borrará los datos cargados en esta instancia.")) {
            ContadorPrincipal--;
            EntidadPrincipal.removeChild(EntidadPrincipal.lastChild);
            ContadorPrincipalInput.value = ContadorPrincipal;
        }
    }
}
