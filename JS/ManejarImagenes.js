// Función para procesar y mostrar previsualización de imágenes
async function procesarImagen(event, previewId, base64Id) {
  const input = event.target;
  const archivo = input.files[0];
  const preview = document.getElementById(previewId);
  const base64Input = document.getElementById(base64Id);

  if (!archivo) {
    console.error('No se ha seleccionado ningún archivo');
    return;
  }

  try {
    const img = await cargarImagen(archivo);
    const base64Image = await redimensionarYConvertirABase64(img, 640, 'image/jpeg', 0.92);
    mostrarPrevisualizacion(preview, base64Image);
    base64Input.value = base64Image;
  } catch (error) {
    console.error('Error procesando la imagen:', error);
  }
}

// Función auxiliar para cargar una imagen
function cargarImagen(archivo) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => {
      const img = new Image();
      img.src = reader.result;
      img.onload = () => resolve(img);
      img.onerror = reject;
    };
    reader.onerror = reject;
    reader.readAsDataURL(archivo);
  });
}

// Función para redimensionar y convertir una imagen a Base64
function redimensionarYConvertirABase64(img, desiredWidth, format = 'image/jpeg', quality = 0.92) {
  return new Promise((resolve, reject) => {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    let {width, height} = img;

    if (width > desiredWidth) {
      const aspectRatio = width / height;
      width = desiredWidth;
      height = width / aspectRatio;
    }

    canvas.width = width;
    canvas.height = height;
    ctx.drawImage(img, 0, 0, width, height);

    canvas.toBlob(blob => {
      const reader = new FileReader();
      reader.onload = () => resolve(reader.result);
      reader.onerror = reject;
      reader.readAsDataURL(blob);
    }, format, quality);
  });
}

// Función para mostrar la previsualización en un elemento de imagen
function mostrarPrevisualizacion(preview, base64Image) {
    preview.src = base64Image;
    preview.style.display = 'block';
}
