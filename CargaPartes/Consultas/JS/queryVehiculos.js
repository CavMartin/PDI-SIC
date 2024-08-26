$(document).ready(function(){
    var table = $('#queryTable').DataTable({
        // Configuraciones de DataTables aquí
        language: {
            url: '../../Resources/DataTables/Spanish.json',
        },
        pageLength: 25, // Establece la cantidad de registros por página predeterminada
        searching: false, // Habilita el campo de búsqueda de DataTables
        lengthChange: false, // Desactiva el selector de cantidad de registros por página de DataTables
    });

    $("#consultaBtn").click(function(){
        Swal.fire({
            text: "¿Esta seguro de que desea realizar la consulta?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, realizar consulta',
            cancelButtonText: 'No, Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../PHP/EndPoint_AJAX.php',
                    type: 'POST',
                    data: {
                        action: 'fetchDataQueryVehiculos',
                        V_Rol: $("#V_Rol").val(),
                        V_TipoVehiculo: $("#V_TipoVehiculo").val(),
                        V_Marca: $("#V_Marca").val(),
                        V_Modelo: $("#V_Modelo").val(),
                        V_Año: $("#V_Año").val(),
                        V_Color: $("#V_Color").val(),
                        V_Dominio: $("#V_Dominio").val(),
                        V_NumeroChasis: $("#V_NumeroChasis").val(),
                        V_NumeroMotor: $("#V_NumeroMotor").val()
                    },
                    success: function(response) {

                        // Limpiar datos anteriores
                        table.clear();

                        // Añadir nuevos datos
                        response.data.Vehiculos.forEach(function(item) {
                            table.row.add([
                                '<div class="text-center"><button type="button" class="btn btn-outline-danger" style="min-width: 9rem;" onclick="verIncidencia(\'' + item.FK_Encabezado + '\')"><i class="bi bi-eye"></i> Ir al reporte</button></div>',
                                item.V_Rol || '',
                                item.V_TipoVehiculo || '',
                                item.V_Marca || '',
                                item.V_Modelo || '',
                                item.V_Año || '',
                                item.V_Color || '',
                                item.V_Dominio || '',
                                item.V_NumeroChasis || '',
                                item.V_NumeroMotor || '',
                                '<div class="text-center"><button type="button" class="btn btn-success btn-editar p-2" onclick="buscarEntidad(\'' + item.ID_Vehiculo + '\')"> <i class="bi bi-eye"></i> Previsualizar</button></div>'
                            ]);
                        });
                        
                        // Dibujar la tabla con los nuevos datos
                        table.draw();
                    },
                    error: function() {
                        Swal.fire('Error', 'La solicitud no pudo completarse', 'error');
                    }
                });
            }
        })
    });
});

function buscarEntidad(ID_Vehiculo) {
    $.ajax({
        url: '../PHP/EndPoint_AJAX.php',
        type: 'POST',
        data: {
            action: 'getDataVehiculo',
            ID_Vehiculo: ID_Vehiculo
        },
        success: function(response) {
            mostrarVentanaModal(response.data); // Aquí se construye la ventana modal con los datos recibidos.
        },
        error: function() {
            alert('Error al recuperar la información de la persona.');
        }
    });
}

function cargarDatosComplementarios(ID_Vehiculo) {
    $.ajax({
        url: '../PHP/EndPoint_AJAX.php',
        type: 'GET',
        data: {
            action: 'getDatosComplementarios',
            ClavePrimaria: ID_Vehiculo
        },
        success: function(datosComplementarios) {
            $('#contenedorDatosComplementarios').empty(); // Limpiar contenedor
            if (datosComplementarios.length > 0) { // Verifica si hay datos complementarios
                datosComplementarios.forEach(function(dato, index) {
                    let tieneMultiplesDatos = datosComplementarios.length > 1;
                    let subtitulo = tieneMultiplesDatos ? `DATO COMPLEMENTARIO #${index + 1}:` : 'DATO COMPLEMENTARIO:';
                    let imagenHTML = dato.DC_ImagenAdjunta ? `<img src="${dato.DC_ImagenAdjunta}" alt="Imagen Complementaria" class="img-fluid mb-2">` : '';
                    var datoHTML = `
                        <div class="fs-5 border border-black rounded mb-2 mx-2 p-2">
                            <h4 class="mb-2"><u>${subtitulo}</u> ${' ' + (dato.DC_Tipo || 'Sin datos')}</h4>
                            ${imagenHTML}
                            <p><b>DESCRIPCIÓN:</b> ${dato.DC_Comentario || 'Sin datos'}</p>
                        </div>`;
                    $('#contenedorDatosComplementarios').append(datoHTML);
                });
            } else {
                $('#contenedorDatosComplementarios').html('<h4 class="fs-4 border border-black rounded mb-2 mx-2 p-2">No hay datos complementarios registrados para este vehículo.</h4>');
            }
        },
        error: function() {
            $('#contenedorDatosComplementarios').html('<h4 class="fs-4 border border-black rounded mb-2 mx-2 p-2">Error al cargar datos complementarios para este vehículo.</h4>');
        }
    });
}

function mostrarVentanaModal(data) {
    // Comprobar si hay una imagen en Base64; de lo contrario, usar una imagen por defecto
    $('#ModalRol').html('<b>ROL DEL VEHÍCULO: </b>' + (data.V_Rol || 'Sin datos'));
    $('#ModalTipo').html('<b>TIPO DE VEHÍCULO: </b>' + (data.V_TipoVehiculo || 'Sin datos'));
    $('#ModalMarca').html('<b>MARCA: </b>' + (data.V_Marca || 'Sin datos'));
    $('#ModalModelo').html('<b>MODELO: </b>' + (data.V_Modelo || 'Sin datos'));
    $('#ModalAño').html('<b>AÑO: </b>' + (data.V_Año || 'Sin datos'));
    $('#ModalColor').html('<b>COLOR: </b>' + (data.V_Color || 'Sin datos'));
    $('#ModalDominio').html('<b>DOMINIO: </b>' + (data.V_Dominio || 'Sin datos'));
    $('#ModalMotor').html('<b>NÚMERO DE MOTOR: </b>' + (data.V_NumeroChasis || 'Sin datos'));
    $('#ModalChasis').html('<b>NÚMERO DE CHASIS: </b>' + (data.V_NumeroMotor || 'Sin datos'));

    // Carga de domicilios y datos complementarios
    cargarDatosComplementarios(data.ID_Vehiculo);

    // Mostrar la ventana modal
    $('#ventanaModalEntidad').modal('show');
}