async function generarPDF(ID) {
    Swal.fire({
        title: 'Generando PDF...',
        html: 'Por favor, espere',
        showConfirmButton: false,
        allowOutsideClick: false,
        willOpen: () => {
            Swal.showLoading();
        },
    });

    try {
        if (!ID) {
            console.error("El campo ID está vacío.");
            Swal.close();
            return;
        }

        var formData = new FormData();
        formData.append('action', 'fetchDataIncidenciaPriorizada'); // Añadir el action esperado por el endpoint
        formData.append('ID', ID);

        // Ruta absoluta al endpoint en entorno local
        const endpointURL = 'http://localhost/SIC/CargaPartes/PHP/EndPoint_AJAX.php';

        // Ajuste importante: Asegúrate de usar 'fetch' con la opción 'body' para FormData
        const response = await fetch(endpointURL, {
            method: 'POST',
            body: formData
        });
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const responseData = await response.json(); // Conversión de la respuesta a JSON
        if (responseData.status === "success") {
            const data = responseData.data;

            // Extraer el nombre del PDF del campo Encabezado.Formulario
            const nombrePDF = data.Encabezado && data.Encabezado.Formulario ? data.Encabezado.Formulario : ID;

            // Instanciación de PDFHandler y procesamiento de datos
            const pdfHandler = new PDFHandler();
            let datosJSON = pdfHandler.ajustarFormatos(data); // Ajusta los formatos de los datos recibidos
            let lugaresDelHechoProcesados = pdfHandler.procesarLugaresDelHecho(datosJSON.Lugares); // Procesa lugares del hecho si los hay
            let contenidoPDF = []; // Inicializa el contenido del PDF como un array vacío

            // Metodos para construir las partes del PDF
            contenidoPDF = pdfHandler.construirEncabezado(datosJSON, lugaresDelHechoProcesados); // Construye el encabezado

            // Procesar cada persona de forma asíncrona
            if (datosJSON.Personas && datosJSON.Personas.length > 0) {
                for (const persona of datosJSON.Personas) {
                    let stackPersonas = await pdfHandler.construirPersonas(persona); // Espera a que se construya la sección de la persona
                    contenidoPDF.push(stackPersonas); // Agrega la sección de la persona al contenido del PDF
                }
            }

            // Procesar cada lugar de forma asíncrona
            if (datosJSON.Lugares && datosJSON.Lugares.length > 0) {
                for (const lugar of datosJSON.Lugares) {
                    let stackLugares = await pdfHandler.construirLugares(lugar); // Espera a que se construya la sección de los lugares
                    contenidoPDF.push(stackLugares); // Agrega la sección de los lugares al contenido del PDF
                }
            }

            // Procesar cada vehiculo de forma asíncrona
            if (datosJSON.Vehiculos && datosJSON.Vehiculos.length > 0) {
                for (const vehiculo of datosJSON.Vehiculos) {
                    let stackVehiculos = await pdfHandler.construirVehiculos(vehiculo); // Espera a que se construya la sección de los Vehiculos
                    contenidoPDF.push(stackVehiculos); // Agrega la sección de los Vehiculos al contenido del PDF
                }
            }

            // Procesar cada vehiculo de forma asíncrona
            if (datosJSON.Armas && datosJSON.Armas.length > 0) {
                for (const arma of datosJSON.Armas) {
                    let stackArmas = await pdfHandler.construirArmas(arma); // Espera a que se construya la sección de las Armas de fuego
                    contenidoPDF.push(stackArmas); // Agrega la sección de las Armas de fuego al contenido del PDF
                }
            }

            // Procesar cada Secuestros de forma asíncrona
            if (datosJSON.Secuestros && datosJSON.Secuestros.length > 0) {
                const totalSecuestros = datosJSON.Secuestros.length; // Total de Secuestros
                let indiceSecuestro = 1; // Inicia el índice en 1 para la numeración

                for (const secuestro of datosJSON.Secuestros) {
                    let stackSecuestros = await pdfHandler.construirSecuestros(secuestro, indiceSecuestro, totalSecuestros); // Pasar el índice y el total de Secuestros
                    contenidoPDF.push(stackSecuestros); // Agrega el mensaje al contenido del PDF
                    indiceSecuestro++; // Incrementa el índice para el próximo mensaje
                }
            }

            // Llamada a la función para crear el documento PDF y mostrarlo
            await crearDocumentoPDF(contenidoPDF, nombrePDF);
            Swal.close(); // Cierra el SweetAlert después de generar el PDF

        } else {
            // Manejo de errores del endpoint
            Swal.fire('Error', responseData.message || 'Error desconocido', 'error');
        }

    } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
        Swal.fire('Error', 'No se pudo generar el PDF', 'error');
    }
}

async function crearDocumentoPDF(data, nombrePDF) {
    const pdfHandler = new PDFHandler();
    var docDefinition = {
        pageMargins: [80, 70, 40, 60], // [izquierda, arriba, derecha, abajo]
        header: pdfHandler.agregarHeader(),
        footer: pdfHandler.agregarFooter(),
        content: data,
        styles: {
            title: {
                fontSize: 20,
                bold: true,
                decoration: 'underline',
            },
            subTitle: {
                fontSize: 16,
                bold: true,
                decoration: 'underline',
            },
            fixedText: {
                fontSize: 12,
                bold: true,
                decoration: 'underline',
                lineHeight: 1.5,
            },
            whiteSpace: {
            },
            dynamicText: {
                fontSize: 12,
            },
            fullText: {
                fontSize: 12,
                alignment: 'justify',
                lineHeight: 1.2,
            },
            tableHeader: {
                bold: true,
                fontSize: 12,
                color: 'black',
                alignment: 'center'
            },
        },
    };

    pdfMake.createPdf(docDefinition).download(`${nombrePDF}.pdf`);
}