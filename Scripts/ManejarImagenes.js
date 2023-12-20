// Función para procesar y mostrar previsualización de imágenes
function procesarImagen(event, previewId, base64Id) {
  const input = event.target;
  const archivo = input.files[0];
  const preview = document.getElementById(previewId);
  const base64Input = document.getElementById(base64Id);

  const reader = new FileReader();

  reader.onload = function (e) {
    const img = new Image();
    img.src = e.target.result;

    img.onload = function () {
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');

      // Define el ancho deseado
      const desiredWidth = 480;

      let width = img.width;
      let height = img.height;

      // Verifica si la imagen supera el ancho deseado
      if (width > desiredWidth) {
        const aspectRatio = width / height;
        width = desiredWidth;
        height = width / aspectRatio;
      }

      canvas.width = width;
      canvas.height = height;
      ctx.drawImage(img, 0, 0, width, height);

      // Convierte la imagen redimensionada a formato JPEG
      canvas.toBlob(function (blob) {
        // Convierte el blob a Base64
        convertirABase64(blob, function (base64Image) {
          // Mostrar la previsualización
          mostrarPrevisualizacion(preview, base64Image);

          // Almacena el valor en base64 en el campo de texto oculto correspondiente
          base64Input.value = base64Image;
        }, 'image/jpeg');
      }, 'image/jpeg');
    };
  };

  if (archivo) {
    reader.readAsDataURL(archivo);
  }
}

// Función para mostrar la previsualización en un elemento de imagen
function mostrarPrevisualizacion(preview, base64Image) {
  preview.src = base64Image;
  preview.style.display = 'block';
}

// Función para convertir un Blob a Base64
function convertirABase64(blob, callback) {
  const reader = new FileReader();
  reader.onload = function (e) {
    const base64 = e.target.result;
    callback(base64);
  };
  reader.readAsDataURL(blob);
}

// Función para cargar y mostrar imágenes base64 almacenadas al cargar la página
function cargarImagenesAlmacenadas() {
  // Seleccionar todos los elementos de imagen relevantes
  const imagenes = document.querySelectorAll('.preview1, .preview2');
  imagenes.forEach(imagen => {
      const base64Input = imagen.nextElementSibling;
      if (base64Input && base64Input.value) {
          mostrarPrevisualizacion(imagen, base64Input.value);
      }
  });
}

// Llamar a la función cuando la ventana se carga completamente
window.onload = function() {
  cargarImagenesAlmacenadas();
};
