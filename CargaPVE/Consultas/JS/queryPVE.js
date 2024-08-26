$(document).ready(function() {
    $("#consultaBtn").click(function() {
        Swal.fire({
            text: "¿Está seguro de que desea realizar la consulta?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar modal de carga
                Swal.fire({
                    title: 'Realizando la consulta...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Recoger los datos del formulario, excluyendo los campos vacíos
                let data = {
                    action: 'fetchDataQueryPVE',
                    FechaDesde: $("#FechaDesde").val(),
                    FechaHasta: $("#FechaHasta").val(),
                    Fuente: $("#Fuente").val(),
                    ReporteAsociado: $("#ReporteAsociado").val(),
                    Relevancia: $("#Relevancia").val(),
                    OperadorSQL: $("#OperadorSQL").val(),
                    Tipologia: $("#Tipologia").val(),
                    ModalidadComisiva: $("#ModalidadComisiva").val(),
                    TipoEstupefaciente: $("#TipoEstupefaciente").val(),
                    ConnivenciaPolicial: $("#ConnivenciaPolicial").val(),
                    PosiblesUsurpaciones: $("#PosiblesUsurpaciones").val(),
                    UsoAF: $("#UsoAF").val(),
                    ParticipacionDeMenores: $("#ParticipacionDeMenores").val(),
                    ParticipacionOrgCrim: $("#ParticipacionOrgCrim").val(),
                    OrganizacionCriminal: $("#OrganizacionCriminal").val(),
                    Relato: $("#Relato").val(),
                    joinLugaresParams: JSON.stringify(joinLugaresParams),
                    joinPersonasParams: JSON.stringify(joinPersonasParams),
                    joinVehiculosParams: JSON.stringify(joinVehiculosParams)
                };

                // Filtrar los campos vacíos
                data = Object.fromEntries(Object.entries(data).filter(([_, v]) => v !== '' && v !== null && v !== undefined));

                // Realizar la solicitud AJAX
                $.ajax({
                    url: '../PHP/EndPoint.php',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        queryData = response.data; // Almacenar los datos en la variable global

                        Swal.close(); // Cerrar el modal de carga tras obtener la respuesta

                        // Calcular el número de formularios únicos en la respuesta
                        let uniqueFormularios = new Set(response.data.map(item => item.Formulario));
                        let resultCount = uniqueFormularios.size;

                        // Crear el texto de resultado
                        let resultText = `Su consulta ha obtenido un total de <b>${resultCount}</b> registros únicos.`;

                        if (resultCount > 0) {
                            // Si hay resultados, mostrar opciones con botones estilizados
                            Swal.fire({
                                title: 'Consulta exitosa',
                                icon: 'success',
                                showCancelButton: false,
                                showConfirmButton: false,
                                width: '50rem',
                                html: `
                                    <p>${resultText}</p>
                                    <div class="container-fluid p-3">
                                        <div class="row justify-content-center">
                                            <div class="col-md-4 justify-content-center m-2">
                                                <button type="button" class="btn btn-info btn-lg" style="width: 15rem;" id="viewMapBtn">
                                                    <i class="bi bi-map"></i> <b>VER MAPA</b>
                                                </button>
                                            </div>
                                            <div class="col-md-4 justify-content-center m-2">
                                                <button type="button" class="btn btn-success btn-lg" style="width: 15rem;" id="viewTableBtn">
                                                    <i class="bi bi-table"></i> <b>VER TABLA</b>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `
                            });
                            
                            // Asignar eventos a los botones dentro del SweetAlert2
                            document.getElementById('viewTableBtn').addEventListener('click', function() {
                                Swal.close(); // Cerrar el modal actual antes de mostrar el siguiente
                                checkAndShowSectionTable(response.data);
                            });
                            
                            document.getElementById('viewMapBtn').addEventListener('click', function() {
                                Swal.close(); // Cerrar el modal actual antes de mostrar el siguiente
                                checkAndShowSectionMap(response.data);
                            });

                        } else {
                            // Si no hay resultados, mostrar información
                            Swal.fire({
                                title: 'Sin datos',
                                text: 'No se han encontrado resultados para los parámetros utilizados en su consulta.',
                                icon: 'info',
                                confirmButtonColor: '#0d6efd',
                                confirmButtonText: 'Aceptar',
                                width: '50rem'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error en la consulta',
                            text: 'Hubo un problema al realizar la consulta. Por favor, inténtelo de nuevo.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
});