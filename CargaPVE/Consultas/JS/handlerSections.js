// Variable global para almacenar los datos de la consulta
let queryData = null;

// Función para mostrar una sección específica y ocultar las demás
function showSection(sectionId) {
    // Ocultar todas las secciones
    document.querySelectorAll('section').forEach(section => {
        section.style.display = 'none';
    });

    // Mostrar la sección seleccionada
    document.getElementById(sectionId).style.display = 'block';
}

// Función para mostrar una sección específica y ocultar las demás con un modal de carga
function showSectionAndWait(sectionId) {
    // Mostrar un modal de carga
    Swal.fire({
        title: 'Cargando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Agregar un pequeño retraso (1 segundo) antes de mostrar la sección seleccionada
    setTimeout(() => {
        // Ocultar todas las secciones
        document.querySelectorAll('section').forEach(section => {
            section.style.display = 'none';
        });

        // Mostrar la sección seleccionada
        document.getElementById(sectionId).style.display = 'block';

        // Cerrar el modal de carga después de mostrar la sección
        Swal.close();
    }, 500); // 1000 ms = 1 segundo
}

// Función para mostrar la tabla y cargarla si hay datos disponibles
function checkAndShowSectionTable(data = null) {
    const section = document.getElementById('tableSection');

    // Usar los datos globales si no se pasaron datos específicos
    if (!data) {
        data = queryData;
    }

    // Verificación de si hay datos disponibles
    if (!data) {
        // Si no hay datos, mostrar advertencia y no permitir el acceso a la tabla
        Swal.fire({
            icon: 'warning',
            title: 'Sin datos',
            text: 'Primero debes realizar una consulta para ver la tabla.',
            confirmButtonColor: '#3085d6',
        });
        return;
    }

    Swal.fire({
        title: 'Procesando tabla...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    populateTable(data); // Popular la tabla con los nuevos datos

    // Mostrar la sección seleccionada
    setTimeout(() => {
        showSection('tableSection');
        setTimeout(() => {
            Swal.close(); // Cerrar el modal de carga después de un pequeño retraso
        }, 1000); // 1 segundo de retraso antes de cerrar el modal
    }, 500);
}

// Función para mostrar el mapa y cargarlo si hay datos disponibles
function checkAndShowSectionMap(data = null) {
    document.getElementById('mapSection');

    // Usar los datos globales si no se pasaron datos específicos
    if (!data) {
        data = queryData;
    }

    // Verificación de si hay datos disponibles
    if (!data && !mapHandler.isPopulated) {
        // Si no hay datos y el mapa no está populado, mostrar advertencia y no permitir el acceso al mapa
        Swal.fire({
            icon: 'warning',
            title: 'Sin datos',
            text: 'Primero debes realizar una consulta para ver el mapa.',
            confirmButtonColor: '#3085d6',
        });
        return;
    }

    Swal.fire({
        title: 'Procesando mapa...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Inicializar el mapa solo si no está ya inicializado
    if (!mapHandler.map) {
        mapHandler.initializeMap();
    }

    mapHandler.populateMap(data); // Popular el mapa con los nuevos datos

    // Mostrar la sección seleccionada y luego forzar el redimensionamiento del mapa
    setTimeout(() => {
        showSection('mapSection');
        setTimeout(() => {
            mapHandler.forceResizeMap(); // Forzar el redimensionamiento del mapa después de que la sección sea visible
            setTimeout(() => {
                Swal.close(); // Cerrar el modal de carga después de un pequeño retraso
            }, 1000); // 1 segundo de retraso antes de cerrar el modal
        }, 200); // Un pequeño retraso para asegurarse de que la sección es visible
    }, 500);
}

// Quitar todos los filtros
function removeAllFilters() {
    // Limpiar los filtros de las entidades secundarias
    joinLugaresParams = {};
    joinPersonasParams = {};
    joinVehiculosParams = {};

    // Limpiar los campos del formulario principal
    document.querySelectorAll('#formSection input, #formSection select').forEach(function(element) {
        if (element.type === 'text' || element.type === 'number' || element.type === 'date' || element.type === 'time') {
            element.value = ''; // Restablecer campos de texto, número, fecha y hora
        } else if (element.tagName === 'SELECT') {
            element.selectedIndex = 0; // Restablecer selects
        }
    });

    // Reestablecer opciones de selectize
    $('#Tipologia').selectize()[0].selectize.clear();
    $('#ModalidadComisiva').selectize()[0].selectize.clear();
    $('#TipoEstupefaciente').selectize()[0].selectize.clear();

    // Actualizar la interfaz
    Swal.fire({
        title: 'Filtros removidos',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
}