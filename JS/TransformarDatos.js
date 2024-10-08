    // Aqui van todas las funciones utilizadas para "transformar datos"    

function transformarDatosNumerico(inputId) {    // Transforma los datos a solo valores númericos 
    // Obtener el elemento del campo de texto
    var inputElement = document.getElementById(inputId);
    
    // Obtener el valor del campo de texto
    var valor = inputElement.value;
    
    // Eliminar caracteres no numéricos
    var valorLimpio = valor.replace(/\D/g, "");
    
    // Asignar el valor limpio al campo de texto
    inputElement.value = valorLimpio;
  }

  function transformarDatosNumerico2(inputId) {   // Transforma los datos a solo valores númericos sin ceros delante
    // Obtener el elemento del campo de texto
    var inputElement = document.getElementById(inputId);
    
    // Obtener el valor del campo de texto
    var valor = inputElement.value;
    
    // Eliminar caracteres no numéricos
    var valorLimpio = valor.replace(/\D/g, "");
    
    // Eliminar ceros iniciales
    valorLimpio = valorLimpio.replace(/^0+/, "");
    
    // Asignar el valor limpio al campo de texto
    inputElement.value = valorLimpio;
  }
  

function transformarDatosAlfaNumerico(inputId) {    // Transforma los datos a valores alfa númericos
    // Obtener el elemento del campo de texto
    var inputElement = document.getElementById(inputId);
    
    // Obtener el valor del campo de texto
    var valor = inputElement.value;
    
    // Eliminar caracteres especiales y convertir a mayúsculas
    var valorTransformado = valor
      .replace(/[áäàâ]/gi, "a")
      .replace(/[éëèê]/gi, "e")
      .replace(/[íïìî]/gi, "i")
      .replace(/[óöòô]/gi, "o")  
      .replace(/[úüùû]/gi, "u")  // Cambia las vocales acentuadas, por su equivalente sin acento
      .replace(/ {2,}/g, " ")  // Eliminar espacios dobles
      .trim()  // Eliminar espacios iniciales y finales    
      .replace(/[^A-Za-z0-9]/g, "")
      .toUpperCase();
    
    // Asignar el valor transformado al campo de texto
    inputElement.value = valorTransformado;
  }
  

  function transformarDatosMayusculas(campoId) {    // Transforma los datos a texto MAYÚSCULA
    var campoTexto = document.getElementById(campoId);
    var valor = campoTexto.value;
  
    var valorMayusculaTransformado = valor
      .replace(/[áäàâ]/gi, "a")
      .replace(/[éëèê]/gi, "e")
      .replace(/[íïìî]/gi, "i")
      .replace(/[óöòô]/gi, "o")
      .replace(/[úüùû]/gi, "u")
      .replace(/ {2,}/g, " ")
      .trim() // Eliminar espacios iniciales y finales
      .toUpperCase();
    
    campoTexto.value = valorMayusculaTransformado;
  }


  function transformarDatosNompropio(campoId) {
    var campoTexto = document.getElementById(campoId);
    var valor = campoTexto.value;
  
    var valorNompropioTransformado = valor
      .toLowerCase()
      .replace(/[áäàâ]/gi, "a")
      .replace(/[éëèê]/gi, "e")
      .replace(/[íïìî]/gi, "i")
      .replace(/[óöòô]/gi, "o")
      .replace(/[úüùû]/gi, "u")  // Reemplazar vocales acentuadas por sus contrapartes sin acentos
      .replace(/ {2,}/g, " ")  // Eliminar espacios dobles
      .trim()  // Eliminar espacios iniciales y finales
      .replace(/\b\w/g, function(match, offset, string) {
        // Solo capitaliza si no es después de 'ñ'
        if (offset > 0 && string[offset - 1] === 'ñ') {
          return match;
        }
        return match.toUpperCase();
      });
  
    campoTexto.value = valorNompropioTransformado;
  }  