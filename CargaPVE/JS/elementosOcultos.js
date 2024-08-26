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

// Maneja la visibilidad del DIV de la Persona Rol Especifique
function HandlerRolPersona(selectElement) {
  const numeroDeOrden = selectElement.id.match(/\d+/)[0]; // Extraer el número de orden del ID
  const contenedorEspecifique = document.getElementById(`P_Rol${numeroDeOrden}Especifique`);

  if (selectElement.value === "Otra opción no listada") {
    // Mostrar el div si la opción seleccionada es 'Otro'
    contenedorEspecifique.style.display = 'block';
  } else {
    // Ocultar el div si la opción seleccionada no es 'Otro'
    contenedorEspecifique.style.display = 'none';
  }
}

// Maneja la visibilidad del DIV del Vehiculo Rol Especifique
function HandlerRolVehiculo(selectElement) {
  const numeroDeOrden = selectElement.id.match(/\d+/)[0]; // Extraer el número de orden del ID
  const contenedorEspecifique = document.getElementById(`V_Rol${numeroDeOrden}Especifique`);

  if (selectElement.value === "Otra opción no listada") {
    // Mostrar el div si la opción seleccionada es 'Otro'
    contenedorEspecifique.style.display = 'block';
  } else {
    // Ocultar el div si la opción seleccionada no es 'Otro'
    contenedorEspecifique.style.display = 'none';
  }
}