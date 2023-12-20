function GenerarPDF() {
    let doc;
    let PosicionX;
    let PosicionY;
    const anchoImagenesDefault = 160; //Utilizado por la entidad datos complementarios para manejar el ancho de las imagenes

    // Función para agregar encabezado
    function addHeader(doc) {
        const headerImgData = 'css/Images/Header.jpeg'; // Ruta de la imagen de encabezado
        doc.addImage(headerImgData, 'JPEG', 10, 0, 190, 25); // Ajusta las coordenadas y dimensiones según tu diseño
    }

    // Función para agregar el footer
    function addFooter(doc) {
        const footerImgData = 'css/Images/Footer.jpeg'; // Ruta de la imagen de pie de página
        doc.addImage(footerImgData, 'JPEG', 10, 280, 190, 20); // Cambia las coordenadas y el tamaño según tu diseño
    }

    // Función para dibujar texto estático con línea debajo
    function drawStaticText(doc, text, PosicionX, PosicionY) {
        doc.setFont("helvetica", "bold");
        doc.setFontSize(12);
        doc.text(text, PosicionX, PosicionY);
        const textWidth = doc.getTextWidth(text);
        doc.line(PosicionX, PosicionY + 1, PosicionX + textWidth, PosicionY + 1);
        return textWidth;
    }

    // Función para dibujar texto dinámico
    function drawDynamicText(doc, text, PosicionX, PosicionY) {
        doc.setFont("helvetica", "normal");
        doc.setFontSize(12);
        doc.text(text, PosicionX, PosicionY);
    }

    // Función para agregar titulos centrados al PDF y con recuadro a su alrededor
    function drawTitle(doc, title, PosicionY, fontSize = 18, padding = 3) {
        doc.setFont("helvetica", "bold");
        doc.setFontSize(fontSize);
    
        const titleWidth = doc.getTextWidth(title);
        const centerX = (doc.internal.pageSize.width / 2) - (titleWidth / 2);
        PosicionY += 2;
    
        // Dibujar título
        doc.text(title, centerX, PosicionY);
    
        // Dibujar rectángulo alrededor del título
        doc.rect(centerX - padding, PosicionY - 8, titleWidth + (2 * padding), 8 + padding);
    
        // Devolver la nueva posición Y
        return PosicionY + 10;  // El 10 es un valor arbitrario para el espacio entre el título y el siguiente elemento
    }

    // Función para agregar subtítulos centrados al PDF
    function drawSubTitle(doc, text, PosicionY) {
        doc.setFont("helvetica", "bold");
        doc.setFontSize(15);
        doc.text(text, PosicionX, PosicionY);
        const lineY = PosicionY + 1;
        doc.line(PosicionX, lineY, PosicionX + doc.getTextWidth(text), lineY);
    }

    // Función para agregar un cuadro de texto con borde al PDF
    function drawTextBox(doc, textoFijo, textoVariable, PosicionX, PosicionY) {
        if (!textoVariable || textoVariable.trim() === '') return PosicionY;
        drawStaticText(doc, textoFijo, PosicionX, PosicionY);
        PosicionY += 7;

        const lineHeight = 6;
        const recuadroAncho = 170;
        doc.setFontSize(12); // Asegúrate de que el tamaño de fuente es el correcto
        doc.setFont("helvetica", "normal");
        const lines = doc.splitTextToSize(textoVariable, recuadroAncho - 10);
        let recuadroStartY = PosicionY - 12;

        lines.forEach((line) => {
            // Comprueba si el contenido sobrepasa el límite de la página
            if (PosicionY + lineHeight > doc.internal.pageSize.height - 20) {
                // Dibuja el recuadro hasta el final de la página
                doc.setDrawColor(0);
                doc.rect(PosicionX - 5, recuadroStartY, recuadroAncho, PosicionY - recuadroStartY);
                // Cambia a la siguiente página
                doc.addPage();
                addHeader(doc); // Agregar el encabezado en la nueva página
                addFooter(doc); // Agregar el footer en la nueva página
                recuadroStartY = 30; // La nueva posición Y de inicio del recuadro
                PosicionY = recuadroStartY + 10; // La nueva posición Y para el texto
            }
        
            doc.text(line, PosicionX, PosicionY);
            PosicionY += lineHeight; // Aumentar PosicionY para la siguiente línea
        });

        // Dibuja el recuadro final
        doc.setDrawColor(0);
        doc.rect(PosicionX - 5, recuadroStartY, recuadroAncho, PosicionY - recuadroStartY - lineHeight + 10);

        PosicionY += 5; // Espacio después del cuadro de texto

        return PosicionY; // Devuelve la nueva Posición Y
    }

    // Función para dibujar un recuadro para encapsular contenido
    function drawEnclosingRectangle(doc, startX, startY, endX, endY) {
        let padding = 2; // Espacio adicional alrededor del contenido
        let rectWidth = endX - startX + padding * 2;
        let rectHeight = endY - startY + padding * 2;
    
        // Dibuja un rectángulo
        doc.rect(startX - padding, startY - padding, rectWidth, rectHeight);
    }
    
    // Función para agregar una imagen al PDF
    function addImageToPDF(base64ImageData, PosicionX, PosicionY, width, height) {
        // Agrega la imagen al PDF directamente desde la cadena Base64
        doc.addImage(base64ImageData, 'JPEG', PosicionX, PosicionY, width, height);
    }

    // Función para verificar si necesitamos un salto de página y agregar encabezado y footer
    function checkPageBreak(doc, PosicionY, increment) {
        const pageHeight = 280 - 25; // Límite vertical de la página menos altura del footer
        const headerHeight = 25; // Altura del encabezado

        if (PosicionY + increment > pageHeight) {
            doc.addPage();
            addHeader(doc); // Agregar el encabezado en la nueva página
            addFooter(doc); // Agregar el footer en la nueva página
            return headerHeight + 5; // Ajustar la coordenada vertical para comenzar debajo del encabezado
        }

        return PosicionY;
    }

    // Función para construir la logica del campo Lugar del hecho / Domicilio
    function construirDirecciones(lugarEncabezado) {
        let texto = lugarEncabezado.L_Calle;
    
        if (lugarEncabezado.L_AlturaCatastral) {
            texto += ' N° ' + lugarEncabezado.L_AlturaCatastral;
        }
    
        if (lugarEncabezado.L_CalleDetalle) {
            texto += ', ' + lugarEncabezado.L_CalleDetalle;
        }
    
        if (lugarEncabezado.L_Interseccion1) {
            if (lugarEncabezado.L_Interseccion2) {
                texto += ', entre ' + lugarEncabezado.L_Interseccion1 + ' y ' + lugarEncabezado.L_Interseccion2;
            } else {
                texto += ' y ' + lugarEncabezado.L_Interseccion1;
            }
        }
    
        texto += ', ' + lugarEncabezado.L_Localidad;
    
        return texto;
    }

    // Obtener los datos y generar el PDF
    let IP_Numero = document.getElementById("IP_Numero").value;

    if (!IP_Numero) {
        alert("Por favor, ingresa un número de incidencia antes de generar el PDF.");
        return;
    }

    fetch('ObtenerDatosPDF.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'IP_Numero=' + encodeURIComponent(IP_Numero) // Añadir encodeURIComponent para seguridad
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // Crea el PDF con los datos obtenidos
        const { jsPDF } = window.jspdf;
        doc = new jsPDF();

        doc.setFontSize(12); // Establece el tamaño de la fuente

        // Llama a la función para agregar el encabezado
        addHeader(doc);
        addFooter(doc);

        PosicionX = 30;
        PosicionY = 30;

        // INCIDENCIA PRIORIZADA NRO
        const textIP_Numero = 'INCIDENCIA PRIORIZADA NRO:';
        let ipNumeroFormatted = data.encabezado.IP_Numero;

        // Reemplazar "-A" por " Ampliación " si existe
        if (ipNumeroFormatted.includes("-A")) {
            ipNumeroFormatted = ipNumeroFormatted.replace("-A", " Ampliación ");
        }

        const textWidth = drawStaticText(doc, textIP_Numero, PosicionX, PosicionY);
        drawDynamicText(doc, ipNumeroFormatted, PosicionX + textWidth + 2, PosicionY);
        PosicionY += 8;

        // TIPO DE INCIDENCIA
        const textIP_TipoHecho = 'TIPO DE INCIDENCIA:';
        const widthIP_TipoHecho = drawStaticText(doc, textIP_TipoHecho, PosicionX, PosicionY);
        drawDynamicText(doc, `${data.encabezado.IP_TipoHecho}`, PosicionX + widthIP_TipoHecho + 2, PosicionY);
        PosicionY += 8;

        // ESPECIFIQUE EL TIPO - SI EXISTE
        if (data.encabezado.IP_OtroTipo) {
            const textIP_OtroTipo = 'TIPO ESPECIFICO:';
            const widthIP_OtroTipo = drawStaticText(doc, textIP_OtroTipo, PosicionX, PosicionY);
            drawDynamicText(doc, data.encabezado.IP_OtroTipo, PosicionX + widthIP_OtroTipo + 2, PosicionY);
            PosicionY += 8;
        }
        // FECHA DEL HECHO
        let fecha = new Date(data.encabezado.IP_Fecha + "T00:00:00"); // Agregar hora para considerar la fecha en zona horaria local
        let formattedDate = fecha.getDate().toString().padStart(2, '0') + '/' + (fecha.getMonth() + 1).toString().padStart(2, '0') + '/' + fecha.getFullYear();
        let parts = data.encabezado.IP_Hora.split(":");
        let formattedTime = parts[0] + ":" + parts[1];
        const textIP_Fecha = 'FECHA DEL HECHO:';
        const widthIP_Fecha = drawStaticText(doc, textIP_Fecha, PosicionX, PosicionY);
        drawDynamicText(doc, formattedDate, PosicionX + widthIP_Fecha + 2, PosicionY);
        PosicionY += 8;


        // HORA DE INGRESO
        const textIP_Hora = 'HORA DE INGRESO:';
        const widthIP_Hora = drawStaticText(doc, textIP_Hora, PosicionX, PosicionY);
        drawDynamicText(doc, formattedTime, PosicionX + widthIP_Hora + 2, PosicionY);
        PosicionY += 8;

        // PROCESAR LUGAR DEL HECHO
        // Determinar la cantidad de lugares para cada tipo
        const cantidadLugaresHecho = data.lugaresHechos.filter(lugar => lugar.L_Rol === 1).length;
        const cantidadLugaresFinalizacion = data.lugaresHechos.filter(lugar => lugar.L_Rol === 2).length;

        // Procesar lugares del hecho
        data.lugaresHechos.filter(lugar => lugar.L_Rol === 1).forEach((lugarEncabezado, index) => {
            const tituloLugar = cantidadLugaresHecho > 1 ? `LUGAR DEL HECHO ${index + 1}:` : 'LUGAR DEL HECHO:';
            procesarLugarDelHecho(doc, lugarEncabezado, tituloLugar);
        });

        // Procesar lugares de finalización
        data.lugaresHechos.filter(lugar => lugar.L_Rol === 2).forEach((lugarEncabezado, index) => {
            const tituloLugar = cantidadLugaresFinalizacion > 1 ? `LUGAR DE FINALIZACIÓN DEL HECHO ${index + 1}:` : 'LUGAR DE FINALIZACIÓN DEL HECHO:';
            procesarLugarDelHecho(doc, lugarEncabezado, tituloLugar);
        });

        // Función para procesar cada lugar del hecho
        function procesarLugarDelHecho(doc, lugarEncabezado, tituloLugar) {
            const widthTitulo = drawStaticText(doc, tituloLugar, PosicionX, PosicionY);
            PosicionX += widthTitulo + 2; // Incrementa la posición X después del texto estático

            // Construcción del texto del lugar del hecho
            let lugarDelHecho = construirDirecciones(lugarEncabezado);

            // Comprobar si el texto excede el ancho máximo permitido
            let lineas = doc.splitTextToSize(lugarDelHecho, 170 - widthTitulo);
    
            // Dibujar el texto, ajustando la posición Y según sea necesario
            lineas.forEach((linea, indiceLinea) => {
                if (indiceLinea > 0) PosicionX = 30; // Ajustar PosicionX para líneas subsiguientes
                drawDynamicText(doc, linea, PosicionX, PosicionY);
                PosicionY += 8; // Aumentar PosicionY para la siguiente línea
            });

            // Restablecer PosicionX y aumentar PosicionY después de cada lugar
            PosicionX = 30;
        }

        // CARTA 911 - SI EXISTE
        if (data.encabezado.IP_Carta911) {
            const textCarta911 = 'NÚMERO DE CARTA 911:';
            const widthCarta911 = drawStaticText(doc, textCarta911, PosicionX, PosicionY);
            drawDynamicText(doc, data.encabezado.IP_Carta911, PosicionX + widthCarta911 + 2, PosicionY);
            PosicionY += 8;
        }

        // MÓVIL ASIGNADO - SI EXISTE
        if (data.encabezado.IP_MovilAsignado) {
            const textMovilAsignado = 'MÓVIL ASIGNADO:';
            const widthMovilAsignado = drawStaticText(doc, textMovilAsignado, PosicionX, PosicionY);
            drawDynamicText(doc, data.encabezado.IP_MovilAsignado, PosicionX + widthMovilAsignado + 2, PosicionY);
            PosicionY += 8;
        }

        // ZONA PRIORIZADA - SI/NO
        if (data.encabezado.IP_ZonaPriorizada) {
            const textIP_ZonaPriorizada = '¿SE TRATA DE UNA ZONA PRIORIZADA?:';
            const widthIP_ZonaPriorizada = drawStaticText(doc, textIP_ZonaPriorizada, PosicionX, PosicionY);
                const responseText = data.encabezado.IP_ZonaPriorizada == 1 ? 'SÍ' : 'NO';
            drawDynamicText(doc, responseText, PosicionX + widthIP_ZonaPriorizada + 2, PosicionY);
            PosicionY += 8;
        }

        // ESPECIFIQUE LA ZONA PRIORIZADA - SI EXISTE
        if (data.encabezado.IP_ZonaPriorizadaEspecifique) {
            const textIP_ZonaPriorizadaEspecifique = 'CUADRANTE PRIORIZADO:';
            const widthIP_ZonaPriorizadaEspecifique = drawStaticText(doc, textIP_ZonaPriorizadaEspecifique, PosicionX, PosicionY);
            drawDynamicText(doc, data.encabezado.IP_ZonaPriorizadaEspecifique, PosicionX + widthIP_ZonaPriorizadaEspecifique + 2, PosicionY);
            PosicionY += 8;
        }

        PosicionY += 4;

        // RESULTADO DE LA INCIDENCIA
        PosicionY = drawTextBox(
            doc,'RESULTADO DE LA INCIDENCIA:',
            data.encabezado.IP_ResultadoDeLaIncidencia, 
            PosicionX,
            PosicionY
        );

        PosicionY += 10;

        // Verifica si el encabezado ocupó más de la mitad de la página. Si ocupo mas de la mitad de la pagina, crea una pagina nueva y mueva la posición de Y justo despues del encabezado
        const mitadDeLaPagina = doc.internal.pageSize.height / 2;
        if (PosicionY > mitadDeLaPagina) {
            doc.addPage();
            addHeader(doc);
            addFooter(doc);
            PosicionY = 30;
        }

        // CARGAR ENTIDAD SECUNDARIA - PERSONAS
        if (data.personas && data.personas.length > 0) {
            PosicionY = checkPageBreak(doc, PosicionY, 80);
            PosicionY = drawTitle(doc, "PERSONAS RELACIONADAS", PosicionY);
            PosicionY += 2;

            // Bucle a través de las personas relacionadas
            data.personas.forEach((persona) => {
                PosicionY = checkPageBreak(doc, PosicionY, 60);

                // Comenzar a dibujar el recuadro
                let startX = PosicionX;
                let startY = PosicionY;

                // ${persona.P_Rol} se convierte en el subtítulo
                drawSubTitle(doc, `${persona.P_Rol}`, PosicionY);
                PosicionY += 12;

                // PERSONAS - IMAGEN DE LA PERSONA
                const P_FotoPersonaData = persona.P_FotoPersona && persona.P_FotoPersona.trim() !== "" ? 
                persona.P_FotoPersona : 'css/Images/NoImage.jpeg'; // Ruta de imagen por defecto
                addImageToPDF(P_FotoPersonaData, PosicionX -1, PosicionY - 5, 50, 50); // Ajusta la posición Y según sea necesario

                // PERSONAS - APELLIDO
                const textP_Apellido = 'APELLIDO:';
                const widthP_Apellido = drawStaticText(doc, textP_Apellido, PosicionX + 55, PosicionY);
                drawDynamicText(doc, `${persona.P_Apellido}`, PosicionX + 55 + widthP_Apellido + 2, PosicionY);
                PosicionY += 8;

                // PERSONAS - NOMBRE
                const textP_Nombre = 'NOMBRE:';
                const widthP_Nombre = drawStaticText(doc, textP_Nombre, PosicionX + 55, PosicionY);
                drawDynamicText(doc, `${persona.P_Nombre}`, PosicionX + 55 + widthP_Nombre + 2, PosicionY);
                PosicionY += 8;

                // PERSONAS - ALIAS
                const textAlias = 'ALIAS:';
                const widthAlias = drawStaticText(doc, textAlias, PosicionX + 55, PosicionY);
                drawDynamicText(doc, persona.P_Alias && persona.P_Alias.trim() !== "" ? `${persona.P_Alias}` : 'Sin datos', PosicionX + 55 + widthAlias + 2, PosicionY);
                PosicionY += 8;

                // PERSONAS - GENERO
                const textP_Genero = 'GÉNERO:';
                const widthP_Genero = drawStaticText(doc, textP_Genero, PosicionX + 55, PosicionY);
                drawDynamicText(doc, `${persona.P_Genero}`, PosicionX + 55 + widthP_Genero + 2, PosicionY);
                PosicionY += 8;

                // PERSONAS - DNI
                const textDNI = 'DNI:';
                const widthDNI = drawStaticText(doc, textDNI, PosicionX + 55, PosicionY);
                drawDynamicText(doc, persona.P_DNI && persona.P_DNI.trim() !== "" ? `${persona.P_DNI}` : 'Sin datos', PosicionX + 55 + widthDNI + 2, PosicionY);
                PosicionY += 8;

                // PERSONAS - ESTADO CIVIL
                const textP_EstadoCivil = 'ESTADO CIVIL:';
                const widthP_EstadoCivil = drawStaticText(doc, textP_EstadoCivil, PosicionX + 55, PosicionY);
                drawDynamicText(doc, `${persona.P_EstadoCivil}`, PosicionX + 55 + widthP_EstadoCivil + 2, PosicionY);
                PosicionY += 12;

                // Dibujar domicilios para cada persona
                if (persona.domicilios && persona.domicilios.length > 0) {
                    persona.domicilios.forEach((domicilio) => {
                        // Parte en negrita: L_Rol
                        doc.setFont("helvetica", "bold");
                        doc.text(domicilio.L_Rol + ': ', PosicionX, PosicionY);

                        // Calcular el ancho del texto en negrita para ajustar la posición del texto normal
                        let boldTextWidth = doc.getTextWidth(domicilio.L_Rol + ': ');

                        // Resto de la dirección en texto normal
                        doc.setFont("helvetica", "normal");
                        let direccion = `${domicilio.L_Calle} ${domicilio.L_AlturaCatastral}`;
                        if (domicilio.L_CalleDetalle) {
                            direccion += `, ${domicilio.L_CalleDetalle}`;
                        }
                        direccion += `, ${domicilio.L_Localidad}`;
                        direccion += `, ${domicilio.L_Provincia}`;
                        if (domicilio.L_Pais && domicilio.L_Pais !== "ARGENTINA") {
                            direccion += `, ${domicilio.L_Pais}`;
                        }

                        // Divide el texto si es demasiado largo
                        const maxWidth = 160 - boldTextWidth; // Espacio disponible considerando el ancho total de 170
                        const splitDireccion = doc.splitTextToSize(direccion, maxWidth);

                        // Dibuja cada línea de la dirección
                        splitDireccion.forEach((line, index) => {
                            doc.text(line, PosicionX + (index === 0 ? boldTextWidth : 0), PosicionY);
                            PosicionY += 6; // Espacio entre líneas de dirección
                        });

                        // Incrementar PosicionY para la siguiente dirección
                        PosicionY += 2; // Ajusta este valor según el espaciado deseado entre direcciones
                    });
                }


                // Ahora calcula el endX y endY basándote en el último PosicionY utilizado
                let endX = PosicionX + 160; // Define el ancho
                let endY = PosicionY; // endY es el último valor de PosicionY

                // Dibuja el recuadro
                drawEnclosingRectangle(doc, startX - 3, startY - 4, endX + 3, endY - 4);

                PosicionY += 10;
                PosicionY = checkPageBreak(doc, PosicionY, 10);

                // Dibujar datos complementarios para cada persona
                if (persona.datos_complementarios && persona.datos_complementarios.length > 0) {
                    persona.datos_complementarios.forEach((datoComplementario) => {
                        // Estimar espacio para el subtítulo y primer elemento (imagen o comentario)
                        let espacioParaSubtituloYElemento = 8; // Espacio inicial para el subtítulo
                        let primerElementoAlto = 0;
                        let linesComentario;

                        // Estimar espacio para la imagen si existe
                        if (datoComplementario.DC_ImagenAdjunta && datoComplementario.DC_ImagenAdjunta.trim() !== "") {
                            const relacionDeAspecto = datoComplementario.anchoImagen / datoComplementario.altoImagen;
                            primerElementoAlto = Math.min(anchoImagenesDefault / relacionDeAspecto, doc.internal.pageSize.height * 0.6);
                            espacioParaSubtituloYElemento += primerElementoAlto + 10;
                        } else if (datoComplementario.DC_Comentario) {
                            linesComentario = doc.splitTextToSize(datoComplementario.DC_Comentario, 160);
                            primerElementoAlto = linesComentario.length * 6; // Altura por línea
                            espacioParaSubtituloYElemento += primerElementoAlto;
                        }

                        // Verificar si hay suficiente espacio para el subtítulo y el primer elemento
                        PosicionY = checkPageBreak(doc, PosicionY, espacioParaSubtituloYElemento);

                        // Dibuja el subtítulo
                        PosicionX = 20;
                        drawSubTitle(doc, `* ${datoComplementario.DC_Tipo}`, PosicionY);
                        PosicionX = 30;
                        doc.setFont("helvetica", "normal");
                        doc.setFontSize(12);
                        PosicionY += 8;

                        if (datoComplementario.DC_ImagenAdjunta && datoComplementario.DC_ImagenAdjunta.trim() !== "") {
                            // Dibujar imagen
                            const DC_ImagenAdjuntaData = datoComplementario.DC_ImagenAdjunta;
                            addImageToPDF(DC_ImagenAdjuntaData, PosicionX, PosicionY, anchoImagenesDefault, primerElementoAlto);
                            PosicionY += primerElementoAlto + 10;
                            }
                        if (datoComplementario.DC_Comentario) {
                            // Dibujar comentario
                            let linesComentario = doc.splitTextToSize(datoComplementario.DC_Comentario, 160);
                            linesComentario.forEach(line => {
                                // Verifica si es necesario un salto de página antes de dibujar la línea
                                PosicionY = checkPageBreak(doc, PosicionY, 6); // 6 es la altura de una línea de comentario
                            
                                // Dibuja la línea de comentario
                                doc.text(line, PosicionX, PosicionY);
                                PosicionY += 6; // Espacio entre líneas de comentario
                            });
                            
                            // Espacio después del comentario
                            PosicionY += 10; // Ajustar según sea necesario
                        }
                        // Asegúrate de verificar si necesitas un salto de página después de dibujar el primer elemento
                        PosicionY = checkPageBreak(doc, PosicionY, 10);
                    });
                }
            });
        } // Cierre del primer bucle "IF" para personas

                // CARGAR ENTIDAD SECUNDARIA - LUGAR DEL HECHO
                if (data.lugaresHechos && data.lugaresHechos.length > 0) {
                    data.lugaresHechos.forEach((lugarHecho) => {
                        // Inicialmente asumir que no hay necesidad de agregar el título
                        let agregarTitulo = false;

                        // Comprobar si hay al menos un dato complementario
                        if (lugarHecho.datos_complementarios && lugarHecho.datos_complementarios.length >= 1) {
                            agregarTitulo = true;

                            // Estimar el espacio necesario para el título y el primer dato complementario
                            let espacioParaTituloYPrimerDato = 20 + 70; // Espacio para el título + espacio estimado para el primer dato

                            // Verificar si hay suficiente espacio para el título y el primer dato en la página actual
                            PosicionY = checkPageBreak(doc, PosicionY, espacioParaTituloYPrimerDato);

                            // Agregar el título solo si hay datos complementarios y hay espacio
                            PosicionY = drawTitle(doc, "LUGAR DEL HECHO - Datos de interés", PosicionY);
                            PosicionY += 2;
                        }

                // Dibujar datos complementarios para cada lugarHecho
                if (lugarHecho.datos_complementarios && lugarHecho.datos_complementarios.length > 0) {
                    lugarHecho.datos_complementarios.forEach((datoComplementario) => {
                        // Estimar espacio para el subtítulo y primer elemento (imagen o comentario)
                        let espacioParaSubtituloYElemento = 8; // Espacio inicial para el subtítulo
                        let primerElementoAlto = 0;
                        let linesComentario;

                        // Estimar espacio para la imagen si existe
                        if (datoComplementario.DC_ImagenAdjunta && datoComplementario.DC_ImagenAdjunta.trim() !== "") {
                            const relacionDeAspecto = datoComplementario.anchoImagen / datoComplementario.altoImagen;
                            primerElementoAlto = Math.min(anchoImagenesDefault / relacionDeAspecto, doc.internal.pageSize.height * 0.6);
                            espacioParaSubtituloYElemento += primerElementoAlto + 10;
                        } else if (datoComplementario.DC_Comentario) {
                            linesComentario = doc.splitTextToSize(datoComplementario.DC_Comentario, 160);
                            primerElementoAlto = linesComentario.length * 6; // Altura por línea
                            espacioParaSubtituloYElemento += primerElementoAlto;
                        }

                        // Verificar si hay suficiente espacio para el subtítulo y el primer elemento
                        PosicionY = checkPageBreak(doc, PosicionY, espacioParaSubtituloYElemento);

                        // Dibuja el subtítulo
                        PosicionX = 20;
                        drawSubTitle(doc, `* ${datoComplementario.DC_Tipo}`, PosicionY);
                        PosicionX = 30;
                        doc.setFont("helvetica", "normal");
                        doc.setFontSize(12);
                        PosicionY += 8;

                        if (datoComplementario.DC_ImagenAdjunta && datoComplementario.DC_ImagenAdjunta.trim() !== "") {
                            // Dibujar imagen
                            const DC_ImagenAdjuntaData = datoComplementario.DC_ImagenAdjunta;
                            addImageToPDF(DC_ImagenAdjuntaData, PosicionX, PosicionY, anchoImagenesDefault, primerElementoAlto);
                            PosicionY += primerElementoAlto + 10;
                            }
                        if (datoComplementario.DC_Comentario) {
                            // Dibujar comentario
                            let linesComentario = doc.splitTextToSize(datoComplementario.DC_Comentario, 160);
                            linesComentario.forEach(line => {
                                // Verifica si es necesario un salto de página antes de dibujar la línea
                                PosicionY = checkPageBreak(doc, PosicionY, 6); // 6 es la altura de una línea de comentario
                            
                                // Dibuja la línea de comentario
                                doc.text(line, PosicionX, PosicionY);
                                PosicionY += 6; // Espacio entre líneas de comentario
                            });
                            
                            // Espacio después del comentario
                            PosicionY += 10; // Ajustar según sea necesario
                        }
                        // Asegúrate de verificar si necesitas un salto de página después de dibujar el primer elemento
                        PosicionY = checkPageBreak(doc, PosicionY, 10);
                    });
                }
            });
        } // Cierre del primer bucle "IF" para lugares del hecho

        // CARGAR ENTIDAD SECUNDARIA - VEHÍCULOS
        if (data.vehiculos && data.vehiculos.length > 0) {
            let agregarTitulo = true;  // Control para agregar el título solo una vez

            data.vehiculos.forEach((vehiculo) => {
                let espacioNecesario = agregarTitulo ? 20 : 0;  // Espacio para el título solo para el primer vehículo

                // Estimar el espacio necesario para cada vehículo
                espacioNecesario += 70; // Estimado para cada vehículo

                // Verificar si hay suficiente espacio para el vehículo en la página actual
                if (doc.internal.pageSize.height - PosicionY < espacioNecesario) {
                    doc.addPage();
                    addHeader(doc);
                    addFooter(doc);
                    PosicionY = 30;
                    agregarTitulo = true;
                }

                // Agregar el título si es el primer vehículo o después de un salto de página
                if (agregarTitulo) {
                    PosicionY = drawTitle(doc, "VEHÍCULOS INVOLUCRADOS", PosicionY);
                    PosicionY += 2;
                    agregarTitulo = false;  // No agregar el título de nuevo hasta el próximo salto de página
                }

                // Comenzar a dibujar el recuadro
                let startX = PosicionX;
                let startY = PosicionY;

                // ${persona.V_Rol} se convierte en el subtítulo
                drawSubTitle(doc, `${vehiculo.V_Rol}`, PosicionY);
                PosicionY += 12;

                // VEHÍCULOS - TIPO
                const textTipoVehiculo = 'TIPO DE VEHÍCULO:';
                const widthTipoVehiculo = drawStaticText(doc, textTipoVehiculo, PosicionX, PosicionY);
                drawDynamicText(doc, `${vehiculo.V_TipoVehiculo }`, PosicionX + widthTipoVehiculo + 2, PosicionY);
                PosicionY += 8;

                // VEHÍCULOS - MARCA
                const textV_Marca = 'MARCA:';
                const widthV_Marca = drawStaticText(doc, textV_Marca, PosicionX, PosicionY);
                drawDynamicText(doc, vehiculo.V_Marca && vehiculo.V_Marca.trim() !== "" ? `${vehiculo.V_Marca}` : 'Sin datos', PosicionX + widthV_Marca + 2, PosicionY);
                PosicionY += 8;

                // VEHÍCULOS - MODELO
                const textV_Modelo = 'MODELO:';
                const widthV_Modelo = drawStaticText(doc, textV_Modelo, PosicionX, PosicionY);
                drawDynamicText(doc, vehiculo.V_Modelo && vehiculo.V_Modelo.trim() !== "" ? `${vehiculo.V_Modelo}` : 'Sin datos', PosicionX + widthV_Modelo + 2, PosicionY);
                PosicionY += 8;

                // VEHÍCULOS - AÑO
                const textV_Año = 'AÑO:';
                const widthV_Año = drawStaticText(doc, textV_Año, PosicionX, PosicionY);
                drawDynamicText(doc, vehiculo.V_Año && vehiculo.V_Año.trim() !== "" ? `${vehiculo.V_Año}` : 'Sin datos', PosicionX + widthV_Año + 2, PosicionY);
                PosicionY += 8;

                // VEHÍCULOS - DOMINIO
                const textV_Dominio = 'DOMINIO:';
                const widthV_Dominio = drawStaticText(doc, textV_Dominio, PosicionX, PosicionY);
                drawDynamicText(doc, vehiculo.V_Dominio && vehiculo.V_Dominio.trim() !== "" ? `${vehiculo.V_Dominio}` : 'Sin datos', PosicionX + widthV_Dominio + 2, PosicionY);
                PosicionY += 8;

                // VEHÍCULOS - NÚMERO DE CHASIS
                const textV_NumeroChasis = 'NÚMERO DE CHASIS:';
                const widthV_NumeroChasis = drawStaticText(doc, textV_NumeroChasis, PosicionX, PosicionY);
                drawDynamicText(doc, vehiculo.V_NumeroChasis && vehiculo.V_NumeroChasis.trim() !== "" ? `${vehiculo.V_NumeroChasis}` : 'Sin datos', PosicionX + widthV_NumeroChasis + 2, PosicionY);
                PosicionY += 8;

                // VEHÍCULOS - NÚMERO DE MOTOR
                const textV_NumeroMotor = 'NÚMERO DE MOTOR:';
                const widthV_NumeroMotor = drawStaticText(doc, textV_NumeroMotor, PosicionX, PosicionY);
                drawDynamicText(doc, vehiculo.V_NumeroMotor && vehiculo.V_NumeroMotor.trim() !== "" ? `${vehiculo.V_NumeroMotor}` : 'Sin datos', PosicionX + widthV_NumeroMotor + 2, PosicionY);
                PosicionY += 8;

                // Ahora calcula el endX y endY basándote en el último PosicionY utilizado
                let endX = PosicionX + 160; // Define el ancho
                let endY = PosicionY; // endY es el último valor de PosicionY

                // Dibuja el recuadro
                drawEnclosingRectangle(doc, startX - 3, startY - 4, endX + 3, endY - 4);

                PosicionY += 10;
                PosicionY = checkPageBreak(doc, PosicionY, 10);

                // Dibujar datos complementarios para cada vehiculo
                if (vehiculo.datos_complementarios && vehiculo.datos_complementarios.length > 0) {
                    vehiculo.datos_complementarios.forEach((datoComplementario) => {
                        // Estimar espacio para el subtítulo y primer elemento (imagen o comentario)
                        let espacioParaSubtituloYElemento = 8; // Espacio inicial para el subtítulo
                        let primerElementoAlto = 0;
                        let linesComentario;

                        // Estimar espacio para la imagen si existe
                        if (datoComplementario.DC_ImagenAdjunta && datoComplementario.DC_ImagenAdjunta.trim() !== "") {
                            const relacionDeAspecto = datoComplementario.anchoImagen / datoComplementario.altoImagen;
                            primerElementoAlto = Math.min(anchoImagenesDefault / relacionDeAspecto, doc.internal.pageSize.height * 0.6);
                            espacioParaSubtituloYElemento += primerElementoAlto + 10;
                        } else if (datoComplementario.DC_Comentario) {
                            linesComentario = doc.splitTextToSize(datoComplementario.DC_Comentario, 160);
                            primerElementoAlto = linesComentario.length * 6; // Altura por línea
                            espacioParaSubtituloYElemento += primerElementoAlto;
                        }

                        // Verificar si hay suficiente espacio para el subtítulo y el primer elemento
                        PosicionY = checkPageBreak(doc, PosicionY, espacioParaSubtituloYElemento);

                        // Dibuja el subtítulo
                        PosicionX = 20;
                        drawSubTitle(doc, `* ${datoComplementario.DC_Tipo}`, PosicionY);
                        PosicionX = 30;
                        doc.setFont("helvetica", "normal");
                        doc.setFontSize(12);
                        PosicionY += 8;

                        if (datoComplementario.DC_ImagenAdjunta && datoComplementario.DC_ImagenAdjunta.trim() !== "") {
                            // Dibujar imagen
                            const DC_ImagenAdjuntaData = datoComplementario.DC_ImagenAdjunta;
                            addImageToPDF(DC_ImagenAdjuntaData, PosicionX, PosicionY, anchoImagenesDefault, primerElementoAlto);
                            PosicionY += primerElementoAlto + 10;
                            }
                        if (datoComplementario.DC_Comentario) {
                            // Dibujar comentario
                            let linesComentario = doc.splitTextToSize(datoComplementario.DC_Comentario, 160);
                            linesComentario.forEach(line => {
                                // Verifica si es necesario un salto de página antes de dibujar la línea
                                PosicionY = checkPageBreak(doc, PosicionY, 6); // 6 es la altura de una línea de comentario
                            
                                // Dibuja la línea de comentario
                                doc.text(line, PosicionX, PosicionY);
                                PosicionY += 6; // Espacio entre líneas de comentario
                            });
                            
                            // Espacio después del comentario
                            PosicionY += 10; // Ajustar según sea necesario
                        }
                        // Asegúrate de verificar si necesitas un salto de página después de dibujar el primer elemento
                        PosicionY = checkPageBreak(doc, PosicionY, 10);
                    });
                }
            });
        } // Cierre del primer bucle "IF" para vehiculos

        // CARGAR ENTIDAD SECUNDARIA - ARMAS DE FUEGO
        if (data.armas && data.armas.length > 0) {
            let agregarTitulo = true;  // Control para agregar el título solo una vez

            data.armas.forEach((arma) => {
                let espacioNecesario = agregarTitulo ? 20 : 0;  // Espacio para el título solo para el primer arma

                // Estimar el espacio necesario para cada arma
                espacioNecesario += 50; // Estimado para cada arma

                // Verificar si hay suficiente espacio para el arma en la página actual
                if (doc.internal.pageSize.height - PosicionY < espacioNecesario) {
                    doc.addPage();
                    addHeader(doc);  // Agregar el encabezado en la nueva página
                    addFooter(doc);  // Agregar el footer en la nueva página
                    PosicionY = 30;  // Reajustar PosicionY para la nueva página
                    agregarTitulo = true;  // Restablecer para agregar el título en la nueva página si es necesario
                }

                // Agregar el título si es la primer arma o después de un salto de página
                if (agregarTitulo) {
                    PosicionY = drawTitle(doc, "ARMAS DE FUEGO", PosicionY);
                    PosicionY += 2;
                    agregarTitulo = false;  // No agregar el título de nuevo hasta el próximo salto de página
                }

                // Comenzar a dibujar el recuadro
                let startX = PosicionX;
                let startY = PosicionY;

                // ARMA DE FUEGO - TIPO
                const textAF_TipoAF = 'TIPO DE ARMA DE FUEGO:';
                const widthAF_TipoAF = drawStaticText(doc, textAF_TipoAF, PosicionX, PosicionY);
                drawDynamicText(doc, `${arma.AF_TipoAF }`, PosicionX + widthAF_TipoAF + 2, PosicionY);
                PosicionY += 8;

                // VEHÍCULOS - MARCA
                const textAF_Marca = 'MARCA / FABRICANTE:';
                const widthAF_Marca = drawStaticText(doc, textAF_Marca, PosicionX, PosicionY);
                drawDynamicText(doc, arma.AF_Marca && arma.AF_Marca.trim() !== "" ? `${arma.AF_Marca}` : 'Sin datos', PosicionX + widthAF_Marca + 2, PosicionY);
                PosicionY += 8;

                // ARMA DE FUEGO - MODELO
                const textAF_Modelo = 'MODELO:';
                const widthAF_Modelo = drawStaticText(doc, textAF_Modelo, PosicionX, PosicionY);
                drawDynamicText(doc, arma.AF_Modelo && arma.AF_Modelo.trim() !== "" ? `${arma.AF_Modelo}` : 'Sin datos', PosicionX + widthAF_Modelo + 2, PosicionY);
                PosicionY += 8;

                // ARMA DE FUEGO - CALIBRE
                const textAF_Calibre = 'CALIBRE:';
                const widthAF_Calibre = drawStaticText(doc, textAF_Calibre, PosicionX, PosicionY);
                drawDynamicText(doc, arma.AF_Calibre && arma.AF_Calibre.trim() !== "" ? `${arma.AF_Calibre}` : 'Sin datos', PosicionX + widthAF_Calibre + 2, PosicionY);
                PosicionY += 8;

                // ARMA DE FUEGO - NÚMERO DE SERIE
                const textAF_NumeroDeSerie = 'NÚMERO DE SERIE:';
                const widthAF_NumeroDeSerie = drawStaticText(doc, textAF_NumeroDeSerie, PosicionX, PosicionY);
                drawDynamicText(doc, arma.AF_NumeroDeSerie && arma.AF_NumeroDeSerie.trim() !== "" ? `${arma.AF_NumeroDeSerie}` : 'Sin datos', PosicionX + widthAF_NumeroDeSerie + 2, PosicionY);
                PosicionY += 8;

                // Ahora calcula el endX y endY basándote en el último PosicionY utilizado
                let endX = PosicionX + 160; // Define el ancho
                let endY = PosicionY; // endY es el último valor de PosicionY

                // Dibuja el recuadro
                drawEnclosingRectangle(doc, startX - 3, startY - 4, endX + 3, endY - 4);

                PosicionY += 10;
                PosicionY = checkPageBreak(doc, PosicionY, 10);

                // Dibujar datos complementarios para cada arma
                if (arma.datos_complementarios && arma.datos_complementarios.length > 0) {
                    arma.datos_complementarios.forEach((datoComplementario) => {
                        // Estimar espacio para el subtítulo y primer elemento (imagen o comentario)
                        let espacioParaSubtituloYElemento = 8; // Espacio inicial para el subtítulo
                        let primerElementoAlto = 0;
                        let linesComentario;

                        // Estimar espacio para la imagen si existe
                        if (datoComplementario.DC_ImagenAdjunta && datoComplementario.DC_ImagenAdjunta.trim() !== "") {
                            const relacionDeAspecto = datoComplementario.anchoImagen / datoComplementario.altoImagen;
                            primerElementoAlto = Math.min(anchoImagenesDefault / relacionDeAspecto, doc.internal.pageSize.height * 0.6);
                            espacioParaSubtituloYElemento += primerElementoAlto + 10;
                        } else if (datoComplementario.DC_Comentario) {
                            linesComentario = doc.splitTextToSize(datoComplementario.DC_Comentario, 160);
                            primerElementoAlto = linesComentario.length * 6; // Altura por línea
                            espacioParaSubtituloYElemento += primerElementoAlto;
                        }

                        // Verificar si hay suficiente espacio para el subtítulo y el primer elemento
                        PosicionY = checkPageBreak(doc, PosicionY, espacioParaSubtituloYElemento);

                        // Dibuja el subtítulo
                        PosicionX = 20;
                        drawSubTitle(doc, `* ${datoComplementario.DC_Tipo}`, PosicionY);
                        PosicionX = 30;
                        doc.setFont("helvetica", "normal");
                        doc.setFontSize(12);
                        PosicionY += 8;

                        if (datoComplementario.DC_ImagenAdjunta && datoComplementario.DC_ImagenAdjunta.trim() !== "") {
                            // Dibujar imagen
                            const DC_ImagenAdjuntaData = datoComplementario.DC_ImagenAdjunta;
                            addImageToPDF(DC_ImagenAdjuntaData, PosicionX, PosicionY, anchoImagenesDefault, primerElementoAlto);
                            PosicionY += primerElementoAlto + 10;
                            }
                        if (datoComplementario.DC_Comentario) {
                            // Dibujar comentario
                            let linesComentario = doc.splitTextToSize(datoComplementario.DC_Comentario, 160);
                            linesComentario.forEach(line => {
                                // Verifica si es necesario un salto de página antes de dibujar la línea
                                PosicionY = checkPageBreak(doc, PosicionY, 6); // 6 es la altura de una línea de comentario
                            
                                // Dibuja la línea de comentario
                                doc.text(line, PosicionX, PosicionY);
                                PosicionY += 6; // Espacio entre líneas de comentario
                            });
                            
                            // Espacio después del comentario
                            PosicionY += 10; // Ajustar según sea necesario
                        }
                        // Asegúrate de verificar si necesitas un salto de página después de dibujar el primer elemento
                        PosicionY = checkPageBreak(doc, PosicionY, 10);
                    });
                }
            });
        } // Cierre del primer bucle "IF" para armas de fuego

        // CARGAR ENTIDAD SECUNDARIA - MENSAJES EXTORSIVOS
        if (data.mensajes && data.mensajes.length > 0) {
            let agregarTitulo = true;  // Variable para controlar la adición del título solo una vez

            data.mensajes.forEach((mensaje, index) => {
                let espacioNecesario = 20; // Espacio para el contenido del mensaje

                // Calcular el espacio necesario para el contenido del mensaje
                const linesContenido = doc.splitTextToSize(mensaje.ME_Contenido, 170);
                espacioNecesario += linesContenido.length * 6;  // asumiendo que lineHeight es 6

                // Calcular el alto proporcional si hay imagen
                if (mensaje.ME_Imagen && mensaje.ME_Imagen.trim() !== "") {
                    const anchoImagenesDefault = 160;  // Ancho fijo para la imagen
                    let altoProporcional = 90;  // Alto predeterminado para la imagen
                    if (mensaje.anchoImagen && mensaje.altoImagen) {
                        const relacionDeAspecto = mensaje.anchoImagen / mensaje.altoImagen;
                        altoProporcional = anchoImagenesDefault / relacionDeAspecto;
                    }
                    espacioNecesario += altoProporcional + 20;  // Sumar espacio para la imagen
                }

                // Verificar si hay suficiente espacio para el mensaje en la página actual
                // Incluir espacio para el título si es el primer mensaje
                if (index === 0 && agregarTitulo) {
                    espacioNecesario += 20; // Aumentar espacio para el título
                }

                // Verificar si hay suficiente espacio para el mensaje en la página actual
                if (doc.internal.pageSize.height - PosicionY < espacioNecesario) {
                    doc.addPage();
                    addHeader(doc);  // Agregar el encabezado en la nueva página
                    addFooter(doc);  // Agregar el footer en la nueva página
                    PosicionY = 30;  // Reajustar PosicionY para la nueva página
                    agregarTitulo = true;  // Restablecer para agregar el título en la nueva página si es necesario
                }

                // Agregar el título si es el primer mensaje o después de un salto de página
                if (index === 0 && agregarTitulo) {
                    PosicionY = drawTitle(doc, "MENSAJES EXTORSIVOS", PosicionY);
                    PosicionY += 2;
                    agregarTitulo = false; // No agregar el título de nuevo hasta el próximo salto de página
                }

                // MENSAJES - MEDIO DE ENTREGA
                const textME_Medio = 'MEDIO DE ENTREGA:';
                const widthME_Medio = drawStaticText(doc, textME_Medio, PosicionX, PosicionY);
                drawDynamicText(doc, `${mensaje.ME_Medio}`, PosicionX + widthME_Medio + 2, PosicionY);
                PosicionY += 8;
                PosicionY = checkPageBreak(doc, PosicionY, 8);

                // MENSAJES - OTRO MEDIO, SI EXISTE
                if (mensaje.ME_OtroMedio && mensaje.ME_OtroMedio.trim() !== "") {
                    const textME_OtroMedio = 'ESPECIFIQUE EL MEDIO:';
                    const widthME_OtroMedio = drawStaticText(doc, textME_OtroMedio, PosicionX, PosicionY);
                    drawDynamicText(doc, `${mensaje.ME_OtroMedio}`, PosicionX + widthME_OtroMedio + 2, PosicionY);
                    PosicionY += 8;
                    PosicionY = checkPageBreak(doc, PosicionY, 8);
                }

                // MENSAJES - DATOS DE CONTACTO, SI EXISTE
                if (mensaje.ME_InfoContacto && mensaje.ME_InfoContacto.trim() !== "") {
                    const textME_InfoContacto = 'DATOS DE CONTACTO:';
                    const widthME_InfoContacto = drawStaticText(doc, textME_InfoContacto, PosicionX, PosicionY);
                    drawDynamicText(doc, `${mensaje.ME_InfoContacto}`, PosicionX + widthME_InfoContacto + 2, PosicionY);
                    PosicionY += 8;
                    PosicionY = checkPageBreak(doc, PosicionY, 8);
                }

                // MENSAJES - FIRMA PARTICULAR, SI EXISTE
                if (mensaje.ME_Firma && mensaje.ME_Firma.trim() !== "") {
                const textME_Firma = 'FIRMA PARTICULAR:';
                    const widthME_Firma = drawStaticText(doc, textME_Firma, PosicionX, PosicionY);
                    drawDynamicText(doc, `${mensaje.ME_Firma}`, PosicionX + widthME_Firma + 2, PosicionY);
                    PosicionY += 8;
                    PosicionY = checkPageBreak(doc, PosicionY, 8);
                }

                // CONTENIDO DEL MENSAJE
                PosicionY = drawTextBox(
                    doc,
                    'CONTENIDO DEL MENSAJE:', 
                    mensaje.ME_Contenido, 
                    PosicionX,
                    PosicionY
                );

                // MENSAJES - IMAGEN DEL MENSAJE, SI EXISTE
                if (mensaje.ME_Imagen && mensaje.ME_Imagen.trim() !== "") {
                    const ME_ImagenData = mensaje.ME_Imagen;
                    const anchoImagenesDefault = 160; // Ancho fijo para la imagen
                    let altoProporcional = 90; // Un alto predeterminado si no hay datos de dimensiones

                    if (mensaje.anchoImagen && mensaje.altoImagen) {
                        // Calcular el alto proporcional basado en la relación de aspecto
                        const relacionDeAspecto = mensaje.anchoImagen / mensaje.altoImagen;
                        altoProporcional = anchoImagenesDefault / relacionDeAspecto;
                    }

                    // Ajustar el espacio después de la imagen
                    let espacioParaImagen = altoProporcional + 20;
    
                    // Verificar si hay suficiente espacio en la página actual para la imagen
                    if (doc.internal.pageSize.height - PosicionY < espacioParaImagen) {
                        doc.addPage();
                        PosicionY = 30; // Reajustar PosicionY para la nueva página
                        addHeader(doc); // Agregar el encabezado en la nueva página
                        addFooter(doc); // Agregar el footer en la nueva página
                    }

                    addImageToPDF(ME_ImagenData, PosicionX, PosicionY + 5, anchoImagenesDefault, altoProporcional);
                    PosicionY += espacioParaImagen; // Ajustar el espacio después de la imagen
                }

                PosicionY += 5;
                PosicionY = checkPageBreak(doc, PosicionY, 10);
            });
        }

        // CONFECCIONADA POR
        const textIP_EquipoCreador = 'CONFECCIONADO POR:';
        const widthIP_EquipoCreador = drawStaticText(doc, textIP_EquipoCreador, PosicionX, PosicionY);
        drawDynamicText(doc, `${data.encabezado.IP_EquipoCreador}`, PosicionX + widthIP_EquipoCreador + 2, PosicionY);
        PosicionY += 15;


// Aqui iran las fuentes consultadas

        doc.save(IP_Numero + ".pdf");
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al generar el PDF. Por favor, inténtalo de nuevo más tarde. Si el error persiste, contacte al administrador del sistema');
    });
}


