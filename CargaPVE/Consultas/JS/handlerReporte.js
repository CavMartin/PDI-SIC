document.addEventListener('DOMContentLoaded', function() {
    let formularioPVE = window.formularioPVE;

    // Primera solicitud AJAX: Obtener los datos del encabezado
    $.ajax({
        url: '../PHP/EndPoint.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'fetchDataPVE',
            formularioPVE: formularioPVE
        },
        success: function(response) {
            if (response.status === "success") {
                const ENCABEZADO = response.data.Encabezado; // Acceder a los datos del ENCABEZADO
                // Formatear la fecha y la hora
                let fechaFormato = ENCABEZADO.Fecha.split('-').reverse().join('/'); // AAAA-MM-DD a DD/MM/AAAA
                // Convertir 0/1 a NO/SÍ para las preguntas
                let posiblesUsurpacionesTexto = ENCABEZADO.PosiblesUsurpaciones === '1' ? 'SÍ' : 'NO';
                let connivenciaPolicialTexto = ENCABEZADO.ConnivenciaPolicial === '1' ? 'SÍ' : 'NO';
                let usoAFTexto = ENCABEZADO.UsoAF === '1' ? 'SÍ' : 'NO';
                let participacionDeMenoresTexto = ENCABEZADO.ParticipacionDeMenores === '1' ? 'SÍ' : 'NO';
                let participacionOrgCrimTexto = ENCABEZADO.ParticipacionOrgCrim === '1' ? 'SÍ' : 'NO';

                // Construir y mostrar el HTML del encabezado
                let encabezadoHTML = `
                        <div class="fs-5 border border-black rounded m-2 p-2">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col"><p><b>NÚMERO DE FORMULARIO:</b> ${ENCABEZADO.Formulario || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>FECHA:</b> ${fechaFormato || 'Sin datos'}</p></div>
                                    <div class="col"><p><b>HORA:</b> ${ENCABEZADO.Hora || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>FUENTE:</b> ${ENCABEZADO.Fuente || 'Sin datos'}</p></div>
                                    <div class="col"><p><b>REPORTE ASOCIADO</b> ${ENCABEZADO.ReporteAsociado || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>TIPOLOGÍA:</b> ${ENCABEZADO.Tipologia || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>MODALIDAD COMISIVA:</b> ${ENCABEZADO.ModalidadComisiva || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>TIPO DE ESTUPEFACIENTE:</b> ${ENCABEZADO.TipoEstupefaciente || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>¿SE MENCIONA CONNIVENCIA POLICIAL?:</b> ${connivenciaPolicialTexto || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>¿SE MENCIONAN POSIBLES USURPACIONES?:</b> ${posiblesUsurpacionesTexto || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>¿SE MENCIONA EL USO DE ARMAS DE FUEGO?:</b> ${usoAFTexto || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>¿SE MENCIONA LA PARTICIPACIÓN DE MENORES?:</b> ${participacionDeMenoresTexto || 'Sin datos'}</p></div>
                                </div>
                                <div class="row">
                                    <div class="col"><p><b>¿SE MENCIONA LA PARTICIPACIÓN DE ALGUNA ORGANIZACIÓN CRIMINAL?:</b> ${participacionOrgCrimTexto || 'Sin datos'}</p></div>
                                </div>
                                <div class="border border-black rounded p-2 mb-3">
                                    <p><b>RESULTADO DE LA INCIDENCIA:</b> ${ENCABEZADO.Relato || 'Sin datos'}</p>
                                </div>
                                <div class="border border-black rounded p-2">
                                    <p><b>VALORACIÓN DE ANÁLISIS:</b> ${ENCABEZADO.Valoracion || 'Sin valoración de análisis'}</p>
                                </div>
                            </div>
                        </div>`;
                    $('#encabezadoContainer').append(encabezadoHTML);
                    if (response.data.Lugares && response.data.Lugares.length > 0) {
                        response.data.Lugares.forEach((lugar, indexLugar) => {
                            // Determina si incluir el índice en el título
                            let tituloLugar = response.data.Lugares.length > 1 ? `LUGAR N° ${indexLugar + 1} MENCIONADO EN EL LLAMADO` : 'LUGAR MENCIONADO EN EL LLAMADO';
                    
                            // Construcción del texto de la dirección
                            let textoDireccion = lugar.L_Calle || '';
                    
                            // Detalles adicionales de dirección
                            if (lugar.L_AlturaCatastral) textoDireccion += ' N° ' + lugar.L_AlturaCatastral;
                            if (lugar.L_CalleDetalle) textoDireccion += ', ' + lugar.L_CalleDetalle;
                            if (lugar.L_Interseccion1) {
                                textoDireccion += lugar.L_Interseccion2 ? `, entre ${lugar.L_Interseccion1} y ${lugar.L_Interseccion2}` : ' y ' + lugar.L_Interseccion1;
                            }
                    
                            let lugaresHTML = `
                                <div class="fs-5 border border-black rounded m-2 p-2">
                                    <h5 class="fw-bold text-decoration-underline">${tituloLugar}</h5>
                                    <div class="row">
                                        <div class="col"><p><b>ROL DEL LUGAR:</b> ${lugar.L_Rol || 'Sin datos'}</p></div>
                                        <div class="col"><p><b>TIPO DE LUGAR:</b> ${lugar.L_Tipo || 'Sin datos'}</p></div>
                                        <div class="col"><p><b>SUBTIPO DE LUGAR:</b> ${lugar.L_SubTipo || 'Sin datos'}</p></div>
                                    </div>
                                    <p><b>DIRECCIÓN MENCIONADA:</b> ${textoDireccion || 'Sin datos'}</p>
                                    <div class="row">
                                        <div class="col"><p><b>BARRIO:</b> ${lugar.L_Barrio || 'Sin datos'}</p></div>
                                        <div class="col"><p><b>LOCALIDAD:</b> ${lugar.L_Localidad || 'Sin datos'}</p></div>
                                    </div>
                                </div>`; // Cerrar el div de este lugar
                            $('#lugaresContainer').append(lugaresHTML);
                        });
                    } else {
                        $('#lugaresContainer').hide();
                    }
                    if (response.data.Personas && response.data.Personas.length > 0) {
                        response.data.Personas.forEach((persona, indexPersona) => {
                            // Determina si incluir el índice en el título
                            let tituloPersona = response.data.Personas.length > 1 ? `PERSONA N° ${indexPersona + 1} MENCIONADA EN EL LLAMADO` : 'PERSONA MENCIONADA EN EL LLAMADO';
                            // Comprobar si hay una imagen en Base64; de lo contrario, usar una imagen por defecto
                            let personasHTML = `
                                <div class="fs-5 border border-black rounded m-2 p-2">
                                    <h5 class="fw-bold text-decoration-underline">${tituloPersona}</h5>
                                    <div class="row">
                                        <div>
                                            <p><b>ROL:</b> ${persona.P_Rol || 'Sin datos'}</p>
                                            <div class="row">
                                                <div class="col"><p><b>APELLIDO:</b> ${persona.P_Apellido || 'Sin datos'}</p></div>
                                                <div class="col"><p><b>NOMBRE:</b> ${persona.P_Nombre || 'Sin datos'}</p></div>
                                                <div class="col"><p><b>ALIAS:</b> ${persona.P_Alias || 'Sin datos'}</p></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`; // Cerrar el div de esta persona
                            $('#personasContainer').append(personasHTML);
                        });
                    } else {
                        $('#personasContainer').hide();
                    }
                    if (response.data.Vehiculos && response.data.Vehiculos.length > 0) {
                        response.data.Vehiculos.forEach((vehiculo, indexVehiculo) => {
                            // Determina si incluir el índice en el título
                            let tituloVehiculo = response.data.Vehiculos.length > 1 ? `VEHÍCULO N° ${indexVehiculo + 1} MENCIONADO EN EL LLAMADO` : 'VEHÍCULO MENCIONADO EN EL LLAMADO';
                                           
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
                                </div>`; // Cerrar el div de este vehiculo
                            $('#vehiculosContainer').append(vehiculosHTML);
                        });
                    } else {
                        $('#vehiculosContainer').hide();
                    }
                // Construir y mostrar el HTML del encabezado
                let disclaimerHTML = `
                        <div class="fs-5 border border-black rounded m-2 p-2">
                            <p class="text-center"><b>TODA LA INFORMACIÓN RELEVADA EN ESTE INFORME ESTÁ BASADA EN LOS DICHOS DE UN LLAMANTE ANÓNIMO.</b></p>
                            <p class="text-center"><b>LA VERACIDAD DE LA INFORMACIÓN NO HA SIDO VERIFICADA Y SE DEBE TRATAR CON LA DEBIDA DISCRECIÓN.</b></p>
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
    $('#disclaimerContainer').hide();
}