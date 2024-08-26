// Maneja la visibilidad del DIV de las opciones del tipo "Otro"
function mostrarElementoOculto(selectId, divId) {
  var select = document.getElementById(selectId);
  var div = document.getElementById(divId);

  if (select.value === "Otra opción no listada" || select.value === "1") {
    div.style.display = "flex";
  } else {
      div.style.display = "none";
  }
}

// Maneja la visibilidad de los DIV si se trata de una AF de fabricación casera
function ocultarFabricacionCasera(selectId, divId1, divId2) {
  var select = document.getElementById(selectId);
  var div1 = document.getElementById(divId1);
  var div2 = document.getElementById(divId2);

  if (select.value === "1") {
    div1.style.display = "none";
    div2.style.display = "none";
  } else {
    div1.style.display = "flex";
    div2.style.display = "flex";
  }
}

// Maneja la visibilidad del DIV de Lugar Rol Especifique
function HandlerRolLugar(selectElement) {
  const numeroDeOrden = selectElement.id.match(/\d+/)[0]; // Extraer el número de orden del ID
  const contenedorEspecifique = document.getElementById(`L_Rol${numeroDeOrden}Especifique`);

  if (selectElement.value === "Otra opción no listada") {
    // Mostrar el div si la opción seleccionada es 'Otro'
    contenedorEspecifique.style.display = 'block';
  } else {
    // Ocultar el div si la opción seleccionada no es 'Otro'
    contenedorEspecifique.style.display = 'none';
  }
}

// Maneja la visibilidad del DIV de Tipo de dato complementario Especifique
function HandlerTipoDato(selectElement) {
  const numeroDeOrden = selectElement.id.match(/\d+/)[0]; // Extraer el número de orden del ID
  const contenedorEspecifique = document.getElementById(`DC_Tipo${numeroDeOrden}Especifique`);

  if (selectElement.value === "Otra opción no listada") {
    // Mostrar el div si la opción seleccionada es 'Otro'
    contenedorEspecifique.style.display = 'block';
  } else {
    // Ocultar el div si la opción seleccionada no es 'Otro'
    contenedorEspecifique.style.display = 'none';
  }
}

