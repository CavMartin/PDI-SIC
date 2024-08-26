document.addEventListener('DOMContentLoaded', (event) => {
    const URLSEARCH = new URLSearchParams(window.location.search);
    const getID = URLSEARCH.get('ID'); // Obtiene el valor original del parámetro 'ID' de la URL

    // Primera solicitud AJAX: Obtener los datos del encabezado
    $.ajax({
        url: '../PHP/EndPoint_AJAX.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'fetchDataIncidenciaPriorizada',
            ID: getID
        },
        success: function(response) {
            if (response.status === "success") {
                const ENCABEZADO = response.data.Encabezado; // Acceder a los datos del ENCABEZADO
                // Formatear la fecha y la hora
                let fechaFormato = ENCABEZADO.Fecha.split('-').reverse().join('/'); // AAAA-MM-DD a DD/MM/AAAA
                // Construir y mostrar el HTML del encabezado
                let encabezadoHTML = `
                        <div class="fs-5 border border-black rounded m-2 p-2">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col"><p><b>NÚMERO DE FORMULARIO:</b> ${ENCABEZADO.Formulario || 'Sin datos'}</p></div>
                                    <div class="col"><p><b>DIVISIÓN:</b> ${ENCABEZADO.Division || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>FECHA DEL HECHO:</b> ${fechaFormato || 'Sin datos'}</p></div>
                                    <div class="col"><p><b>HORA DEL HECHO:</b> ${ENCABEZADO.Hora || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>CLASIFICACIÓN:</b> ${ENCABEZADO.Clasificacion || 'Sin datos'}</p></div>
                                    <div class="col"><p><b>DEPENDENCIA:</b> ${ENCABEZADO.Dependencia || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>CAUSA:</b> ${ENCABEZADO.Causa || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>JUZGADO INTERVINIENTE:</b> ${ENCABEZADO.Juzgado || 'Sin datos'}</p></div>
                                    <div class="col"><p><b>FISCAL INTERVINIENTE:</b> ${ENCABEZADO.Fiscal || 'Sin datos'}</p></div>
                                </div>
                                <div class="border border-black rounded p-2">
                                    <p><b>RESULTADO DE LA INCIDENCIA:</b> ${ENCABEZADO.Relato || 'Sin datos'}</p>
                                </div>
                            </div>
                        </div>`;
                    $('#encabezadoContainer').append(encabezadoHTML);
                    if (response.data.Lugares && response.data.Lugares.length > 0) {
                        response.data.Lugares.forEach((lugar, indexLugar) => {
                            // Determina si incluir el índice en el título
                            let tituloLugar = response.data.Lugares.length > 1 ? `LUGAR N° ${indexLugar + 1} RELACIONADO A LA INCIDENCIA` : 'LUGAR RELACIONADO A LA INCIDENCIA';
                    
                            // Construcción del texto de la dirección
                            let textoDireccion = lugar.Calle || '';
                    
                            // Detalles adicionales de dirección
                            if (lugar.AlturaCatastral) textoDireccion += ' N° ' + lugar.AlturaCatastral;
                            if (lugar.CalleDetalle) textoDireccion += ', ' + lugar.CalleDetalle;
                            if (lugar.Interseccion1) {
                                textoDireccion += lugar.Interseccion2 ? `, entre ${lugar.Interseccion1} y ${lugar.Interseccion2}` : ' y ' + lugar.Interseccion1;
                            }
                    
                            let lugaresHTML = `
                                <div class="fs-5 border border-black rounded m-2 p-2">
                                    <h5 class="fw-bold text-decoration-underline">${tituloLugar}</h5>
                                    <div class="row">
                                        <div class="col"><p><b>ROL DEL LUGAR:</b> ${lugar.Rol || 'Sin datos'}</p></div>
                                        <div class="col"><p><b>TIPO DE LUGAR:</b> ${lugar.Tipo || 'Sin datos'}</p></div>
                                    </div>
                                    <p><b>DIRECCIÓN:</b> ${textoDireccion || 'Sin datos'}</p>
                                    <div class="row">
                                        <div class="col"><p><b>BARRIO:</b> ${lugar.Barrio || 'Sin datos'}</p></div>
                                        <div class="col"><p><b>LOCALIDAD:</b> ${lugar.Localidad || 'Sin datos'}</p></div>
                                    </div>`;
                    
                            // Agregar Datos Complementarios si existen
                            if (lugar.DatosComplementarios && lugar.DatosComplementarios.length > 0) {
                                lugar.DatosComplementarios.forEach((dato, indexDato) => {
                                    let tieneMultiplesDatos = lugar.DatosComplementarios.length > 1;
                                    let subtitulo = tieneMultiplesDatos ? `DATO COMPLEMENTARIO #${indexDato + 1}:` : 'DATO COMPLEMENTARIO:';
                                    let imagenHTML = dato.DC_ImagenAdjunta ? `<div class="text-center"><img src="${dato.DC_ImagenAdjunta}" alt="Imagen Complementaria" class="img-fluid mb-2"></div>` : '';
                                    lugaresHTML += `
                                        <div class="border border-black rounded m-2 p-2">
                                            <h5 class="fw-bold mb-2"><u>${subtitulo}</u> ${' ' + (dato.DC_Tipo || 'Sin datos')}</h5>
                                            ${imagenHTML}
                                            <p><b>DESCRIPCIÓN:</b> ${dato.DC_Comentario || 'Sin datos'}</p>
                                        </div>`;
                                });
                            }
                            lugaresHTML += `</div>`; // Cerrar el div de este lugar
                            $('#lugaresContainer').append(lugaresHTML);
                        });
                    } else {
                        $('#lugaresContainer').hide();
                    }
                    if (response.data.Personas && response.data.Personas.length > 0) {
                        response.data.Personas.forEach((persona, indexPersona) => {
                            // Determina si incluir el índice en el título
                            let tituloPersona = response.data.Personas.length > 1 ? `PERSONA N° ${indexPersona + 1} RELACIONADA A LA INCIDENCIA` : 'PERSONA RELACIONADA A LA INCIDENCIA';
                            // Comprobar si hay una imagen en Base64; de lo contrario, usar una imagen por defecto
                            const IMAGEN_DEFAULT = '../CSS/Images/PersonaDefault.jpg';
                            const SRC_IMAGEN = persona.P_FotoPersona || IMAGEN_DEFAULT;
                            let personasHTML = `
                                <div class="fs-5 border border-black rounded m-2 p-2">
                                    <h5 class="fw-bold text-decoration-underline">${tituloPersona}</h5>
                                    <div class="row">
                                        <div class="col-md-5 d-flex justify-content-center">
                                            <img src="${SRC_IMAGEN}" alt="Foto de la Persona" class="mt-4 img-fluid" style="height: 300px; width: 300px; border-radius: 50%; border: 0.3vw solid rgb(0, 0, 0);">
                                        </div>
                                        <div class="col-md-5">
                                            <p><b>ROL:</b> ${persona.P_Rol || 'Sin datos'}</p>
                                            <p><b>APELLIDO:</b> ${persona.P_Apellido || 'Sin datos'}</p>
                                            <p><b>NOMBRE:</b> ${persona.P_Nombre || 'Sin datos'}</p>
                                            <p><b>ALIAS:</b> ${persona.P_Alias || 'Sin datos'}</p>
                                            <p><b>GÉNERO:</b> ${persona.P_Genero || 'Sin datos'}</p>
                                            <p><b>DNI:</b> ${persona.P_DNI || 'Sin datos'}</p>
                                            <p><b>EDAD:</b> ${persona.P_Edad || 'Sin datos'}</p>
                                            <p><b>ESTADO CIVIL:</b> ${persona.P_EstadoCivil || 'Sin datos'}</p>
                                        </div>
                                    </div>`;
                    
                            // Agregar Domicilios si existen
                            if (persona.Domicilios && persona.Domicilios.length > 0) {
                                persona.Domicilios.forEach((domicilio) => {
                                    personasHTML += `
                                        <div class="border border-black rounded mb-2 mx-2 p-2">
                                            <p class="m-0"><b>${domicilio.Rol}:</b> ${domicilio.Direccion || 'Sin datos'}</p>
                                        </div>`;
                                });
                            }
                            // Agregar Datos Complementarios si existen
                            if (persona.DatosComplementarios && persona.DatosComplementarios.length > 0) {
                                persona.DatosComplementarios.forEach((dato, indexDato) => {
                                    let tieneMultiplesDatos = persona.DatosComplementarios.length > 1;
                                    let subtitulo = tieneMultiplesDatos ? `DATO COMPLEMENTARIO #${indexDato + 1}:` : 'DATO COMPLEMENTARIO:';
                                    let imagenHTML = dato.DC_ImagenAdjunta ? `<div class="text-center"><img src="${dato.DC_ImagenAdjunta}" alt="Imagen Complementaria" class="img-fluid mb-2"></div>` : '';
                                    personasHTML += `
                                        <div class="border border-black rounded m-2 p-2">
                                            <h5 class="fw-bold mb-2"><u>${subtitulo}</u> ${' ' + (dato.DC_Tipo || 'Sin datos')}</h5>
                                            ${imagenHTML}
                                            <p><b>DESCRIPCIÓN:</b> ${dato.DC_Comentario || 'Sin datos'}</p>
                                        </div>`;
                                });
                            }
                            personasHTML += `</div>`; // Cerrar el div de esta persona
                            $('#personasContainer').append(personasHTML);
                        });
                    } else {
                        $('#personasContainer').hide();
                    }
                    if (response.data.Vehiculos && response.data.Vehiculos.length > 0) {
                        response.data.Vehiculos.forEach((vehiculo, indexVehiculo) => {
                            // Determina si incluir el índice en el título
                            let tituloVehiculo = response.data.Vehiculos.length > 1 ? `VEHÍCULO N° ${indexVehiculo + 1} RELACIONADO A LA INCIDENCIA` : 'VEHÍCULO RELACIONADO A LA INCIDENCIA';
                                           
                            let vehiculosHTML = `
                                <div class="fs-5 border border-black rounded m-2 p-2">
                                    <h5 class="fw-bold text-decoration-underline">${tituloVehiculo}</h5>
                                    <div class="row">
                                        <div class="col"><p><b>ROL DEL VEHÍCULO:</b> ${vehiculo.V_Rol || 'Sin datos'}</p></div>
                                        <div class="col"><p><b>TIPO DE VEHÍCULO:</b> ${vehiculo.V_TipoVehiculo || 'Sin datos'}</p></div>
                                    </div>
                                    <div class="row">
                                        <div class="col"><p><b>MARCA:</b> ${vehiculo.V_Marca || 'Sin datos'}</p></div>
                                        <div class="col"><p><b>MODELO:</b> ${vehiculo.V_Modelo || 'Sin datos'}</p></div>
                                        <div class="col"><p><b>AÑO:</b> ${vehiculo.V_Año || 'Sin datos'}</p></div>
                                    </div>
                                    <div class="row">
                                        <div class="col"><p><b>COLOR:</b> ${vehiculo.V_Color || 'Sin datos'}</p></div>
                                        <div class="col"><p><b>DOMINIO:</b> ${vehiculo.V_Dominio || 'Sin datos'}</p></div>
                                    </div>
                                    <div class="row">
                                        <div class="col"><p><b>NÚMERO DE CHASIS:</b> ${vehiculo.V_NumeroChasis || 'Sin datos'}</p></div>
                                        <div class="col"><p><b>NÚMERO DE MOTOR:</b> ${vehiculo.V_NumeroMotor || 'Sin datos'}</p></div>
                                    </div>`;
                    
                            // Agregar Datos Complementarios si existen
                            if (vehiculo.DatosComplementarios && vehiculo.DatosComplementarios.length > 0) {
                                vehiculo.DatosComplementarios.forEach((dato, indexDato) => {
                                    let tieneMultiplesDatos = vehiculo.DatosComplementarios.length > 1;
                                    let subtitulo = tieneMultiplesDatos ? `DATO COMPLEMENTARIO #${indexDato + 1}:` : 'DATO COMPLEMENTARIO:';
                                    let imagenHTML = dato.DC_ImagenAdjunta ? `<div class="text-center"><img src="${dato.DC_ImagenAdjunta}" alt="Imagen Complementaria" class="img-fluid mb-2"></div>` : '';
                                    vehiculosHTML += `
                                        <div class="border border-black rounded m-2 p-2">
                                            <h5 class="fw-bold mb-2"><u>${subtitulo}</u> ${' ' + (dato.DC_Tipo || 'Sin datos')}</h5>
                                            ${imagenHTML}
                                            <p><b>DESCRIPCIÓN:</b> ${dato.DC_Comentario || 'Sin datos'}</p>
                                        </div>`;
                                });
                            }
                            vehiculosHTML += `</div>`; // Cerrar el div de este vehiculo
                            $('#vehiculosContainer').append(vehiculosHTML);
                        });
                    } else {
                        $('#vehiculosContainer').hide();
                    }
                    if (response.data.Armas && response.data.Armas.length > 0) {
                        response.data.Armas.forEach((arma, indexArma) => {
                            // Determina si incluir el índice en el título
                            let tituloArma = response.data.Armas.length > 1 ? `ARMA DE FUEGO N° ${indexArma + 1} RELACIONADA A LA INCIDENCIA` : 'ARMA DE FUEGO RELACIONADA A LA INCIDENCIA';
                            let armasHTML = `
                                <div class="fs-5 border border-black rounded m-2 p-2">
                                    <h5 class="fw-bold text-decoration-underline">${tituloArma}</h5>
                                    <div class="col"><p><b>CLASIFICACIÓN:</b> ${arma.AF_TipoAF || 'Sin datos'}</p></div>
                                    <div class="row">
                                        <div class="col"><p><b>CALIBRE:</b> ${arma.AF_Calibre || 'Sin datos'}</p></div>
                                        <div class="col"><p><b>MARCA:</b> ${arma.AF_Marca || 'Sin datos'}</p></div>
                                        <div class="col"><p><b>MODELO:</b> ${arma.AF_Marca || 'Sin datos'}</p></div>
                                    </div>
                                    <div class="col"><p><b>NÚMERO DE serie:</b> ${arma.AF_NumeroDeSerie || 'Sin datos'}</p></div>
                                    `;
                    
                            // Agregar Datos Complementarios si existen
                            if (arma.DatosComplementarios && arma.DatosComplementarios.length > 0) {
                                arma.DatosComplementarios.forEach((dato, indexDato) => {
                                    let tieneMultiplesDatos = arma.DatosComplementarios.length > 1;
                                    let subtitulo = tieneMultiplesDatos ? `DATO COMPLEMENTARIO #${indexDato + 1}:` : 'DATO COMPLEMENTARIO:';
                                    let imagenHTML = dato.DC_ImagenAdjunta ? `<div class="text-center"><img src="${dato.DC_ImagenAdjunta}" alt="Imagen Complementaria" class="img-fluid mb-2"></div>` : '';
                                    armasHTML += `
                                        <div class="border border-black rounded m-2 p-2">
                                            <h5 class="fw-bold mb-2"><u>${subtitulo}</u> ${' ' + (dato.DC_Tipo || 'Sin datos')}</h5>
                                            ${imagenHTML}
                                            <p><b>DESCRIPCIÓN:</b> ${dato.DC_Comentario || 'Sin datos'}</p>
                                        </div>`;
                                });
                            }
                            armasHTML += `</div>`; // Cerrar el div de esta arma de fuego
                            $('#armasContainer').append(armasHTML);
                        });
                    } else {
                        $('#armasContainer').hide();
                    }
                    if (response.data.Secuestros && response.data.Secuestros.length > 0) {
                        response.data.Secuestros.forEach((secuestro, indexSecuestro) => {
                            // Determina si incluir el índice en el título
                            let tituloSecuestro = response.data.Secuestros.length > 1 ? `SECUESTRO N° ${indexSecuestro + 1} RELACIONADO` : 'SECUESTRO RELACIONADO';
                            let SecuestrosHTML = `
                                <div class="fs-5 border border-black rounded m-2 p-2">
                                    <h5 class="fw-bold text-decoration-underline">${tituloSecuestro}</h5>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <p><b>TIPO DE SECUESTRO:</b> ${secuestro.DC_Tipo || 'Sin datos'}</p>
                                        </div>
                                        <p><b>DETALLES DEL SECUESTRO:</b> ${secuestro.DC_Comentario || 'Sin datos'}</p>
                                        <img src="${secuestro.DC_ImagenAdjunta || ''}" class="img-fluid mb-2">
                                    </div>
                            </div>`; // Cerrar el div de este secuestro
                            $('#secuestrosContainer').append(SecuestrosHTML);
                        });
                    } else {
                        $('#secuestrosContainer').hide();
                    }
                // Construir y mostrar el HTML del encabezado
                let disclaimerHTML = `
                        <div class="fs-5 border border-black rounded m-2 p-2">
                            <p><b>TODA INFORMACIÓN RELEVADA EN ESTE INFORME QUEDA SUJETA A SU POSTERIOR COMPROBACIÓN.</b></p>
                        </div>`;
                    $('#disclaimerContainer').append(disclaimerHTML);
            } else {
                // Manejo de errores para la primera solicitud
                mostrarError(response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al realizar la primera solicitud:', error);
        }
    });
});

function mostrarError(mensaje) {
    // Función para mostrar el mensaje de error
    $('#encabezadoContainer').empty();
    const ERROR_MSG = `<p class="fs-1 fw-bold text-center text-danger">${mensaje}</p>`;
    $('#encabezadoContainer').html(ERROR_MSG);
    $('#lugaresContainer').hide();
    $('#personasContainer').hide();
    $('#vehiculosContainer').hide();
    $('#armasContainer').hide();
    $('#mensajesContainer').hide();
    $('#disclaimerContainer').hide();
}