async function generarPDF(IP_Numero) {
    // Inicio de SweetAlert para mostrar al usuario que se está generando el PDF
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
        // Recolección y validación de datos del formulario
        if (!IP_Numero) {
            console.error("El campo IP_Numero está vacío.");
            Swal.close(); // Cierra el SweetAlert si hay un error
            return;
        }

        // Creación de FormData y añadiendo datos para la petición AJAX
        var formData = new FormData();
        formData.append('IP_Numero', IP_Numero);

        // Operación AJAX para obtener los datos del servidor
        const response = await fetch('Formularios/Infractores/PHP/EndPoint_AJAX.php', { method: 'POST', body: formData });
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json(); // Conversión de la respuesta a JSON

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

        // Llamada a la función para crear el documento PDF y mostrarlo
        await crearDocumentoPDF(contenidoPDF, IP_Numero);
        Swal.close(); // Cierra el SweetAlert después de generar el PDF
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
                fontSize: 24,
                bold: true,
                decoration: 'underline',
                alignment: 'center',
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