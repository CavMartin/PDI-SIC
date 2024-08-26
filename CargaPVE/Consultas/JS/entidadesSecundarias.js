let joinLugaresParams = {};  // Variable global para almacenar filtros de lugares
let joinPersonasParams = {};  // Variable global para almacenar filtros de personas
let joinVehiculosParams = {};  // Variable global para almacenar filtros de vehículos

document.addEventListener('DOMContentLoaded', function() {
    // Event listener para 'Lugares'
    document.getElementById('joinLugaresParams').addEventListener('click', function() {
        Swal.fire({
            title: 'Filtrar por lugares',
            html: `
                <div class="input-group mb-3">
                    <label for="L_Rol" class="input-group-text fw-bold col-4">Rol</label>
                    <select id="L_Rol" class="form-select">
                        <option value="">Indistinto</option>
                        <option value="No especificado">No especificado</option>
                        <option value="Lugar de acopio">Lugar de acopio</option>
                        <option value="Lugar de comercialización">Lugar de comercialización</option>
                        <option value="Lugar de distribución">Lugar de distribución</option>
                        <option value="Lugar de producción">Lugar de producción</option>
                        <option value="Lugar mencionado">Lugar mencionado</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <label for="L_Tipo" class="input-group-text fw-bold col-4">Tipo</label>
                    <select id="L_Tipo" class="form-select">
                        <option value="">Indistinto</option>
                        <option value="Vía pública">Vía pública</option>
                        <option value="Plaza / Parque">Plaza / Parque</option>
                        <option value="Ruta / camino">Ruta / camino</option>
                        <option value="Cochera / playa de estacionamiento">Cochera / playa de estacionamiento</option>
                        <option value="Descampado">Descampado</option>
                        <option value="Exterior de asociación civil">Exterior de asociación civil</option>
                        <option value="Interior de asociación civil">Interior de asociación civil</option>
                        <option value="Exterior de comercio">Exterior de comercio</option>
                        <option value="Interior de comercio">Interior de comercio</option>
                        <option value="Exterior de dependencia pública">Exterior de dependencia pública</option>
                        <option value="Interior de dependencia pública">Interior de dependencia pública</option>
                        <option value="Exterior de industria">Exterior de industria</option>
                        <option value="Interior de industria">Interior de industria</option>
                        <option value="Exterior de inmueble">Exterior de inmueble</option>
                        <option value="Interior de inmueble">Interior de inmueble</option>
                        <option value="Exterior de institución pública">Exterior de institución pública</option>
                        <option value="Interior de institución pública">Interior de institución pública</option>
                        <option value="Exterior de vehículo">Exterior de vehículo</option>
                        <option value="Interior de vehículo">Interior de vehículo</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <label for="L_Calle" class="input-group-text fw-bold col-4">Calle</label>
                    <input type="text" id="L_Calle" class="form-control" placeholder="Nombre de la calle">
                </div>
                <div class="input-group mb-3">
                    <label for="L_AlturaCatastral" class="input-group-text fw-bold col-4">Altura Catastral</label>
                    <input type="text" id="L_AlturaCatastral" class="form-control" placeholder="Altura catastral">
                </div>
                <div class="input-group mb-3">
                    <label for="L_Barrio" class="input-group-text fw-bold col-4">Barrio</label>
                    <input type="text" id="L_Barrio" class="form-control" placeholder="Barrio">
                </div>
                <div class="input-group mb-3">
                    <label for="L_Localidad" class="input-group-text fw-bold col-4">Localidad</label>
                    <input type="text" id="L_Localidad" class="form-control" placeholder="Ciudad">
                </div>
            `,
            focusConfirm: false,
            showDenyButton: true,
            confirmButtonText: 'Aplicar filtros',
            confirmButtonColor: '#0d6efd',
            denyButtonText: 'Limpiar filtros',
            denyButtonColor: '#dc3545',
            allowOutsideClick: false,
            didOpen: () => {
                // Cargar valores almacenados en el formulario si existen
                if (joinLugaresParams.L_Rol) document.getElementById('L_Rol').value = joinLugaresParams.L_Rol;
                if (joinLugaresParams.L_Tipo) document.getElementById('L_Tipo').value = joinLugaresParams.L_Tipo;
                if (joinLugaresParams.L_Calle) document.getElementById('L_Calle').value = joinLugaresParams.L_Calle;
                if (joinLugaresParams.L_AlturaCatastral) document.getElementById('L_AlturaCatastral').value = joinLugaresParams.L_AlturaCatastral;
                if (joinLugaresParams.L_Barrio) document.getElementById('L_Barrio').value = joinLugaresParams.L_Barrio;
                if (joinLugaresParams.L_Localidad) document.getElementById('L_Localidad').value = joinLugaresParams.L_Localidad;
            },
            preConfirm: () => {
                // Almacenar valores en la variable global
                joinLugaresParams = {
                    L_Rol: document.getElementById('L_Rol').value,
                    L_Tipo: document.getElementById('L_Tipo').value,
                    L_Calle: document.getElementById('L_Calle').value,
                    L_AlturaCatastral: document.getElementById('L_AlturaCatastral').value,
                    L_Barrio: document.getElementById('L_Barrio').value,
                    L_Localidad: document.getElementById('L_Localidad').value
                };
            },
            preDeny: () => {
                // Limpiar los filtros
                joinLugaresParams = {}; // Vaciar los filtros
            }
        });
    });

    // Event listener para 'Personas'
    document.getElementById('joinPersonasParams').addEventListener('click', function() {
        Swal.fire({
            title: 'Filtrar por personas',
            html: `
                <div class="input-group mb-3">
                    <label for="P_Rol" class="input-group-text fw-bold col-4">Rol</label>
                    <select id="P_Rol" class="form-select">
                        <option value="">Indistinto</option>
                        <option value="No especificado">No especificado</option>
                        <option value="Mencionado como soldadito">Mencionado como soldadito</option>
                        <option value="Mencionado como delivery">Mencionado como delivery</option>
                        <option value="Mencionado como quien haría de campana">Mencionado como quien haría de campana</option>
                        <option value="Mencionado como vendedor">Mencionado como vendedor</option>
                        <option value="Mencionado como detenido en comisaría">Mencionado como detenido en comisaría</option>
                        <option value="Mencionado como detenido en servicio penitenciario">Mencionado como detenido en servicio penitenciario</option>
                        <option value="Mencionado como empleado policial">Mencionado como empleado policial</option>
                        <option value="Mencionado como ex empleado policial">Mencionado como ex empleado policial</option>
                        <option value="Mencionado como empleado penitenciario">Mencionado como empleado penitenciario</option>
                        <option value="Mencionado como ex empleado penitenciario">Mencionado como ex empleado penitenciario</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <label for="P_Apellido" class="input-group-text fw-bold col-4">Apellido</label>
                    <input type="text" id="P_Apellido" class="form-control" placeholder="Apellido de la persona">
                </div>
                <div class="input-group mb-3">
                    <label for="P_Nombre" class="input-group-text fw-bold col-4">Nombre</label>
                    <input type="text" id="P_Nombre" class="form-control" placeholder="Nombre de la persona">
                </div>
                <div class="input-group mb-3">
                    <label for="P_Alias" class="input-group-text fw-bold col-4">Alias</label>
                    <input type="text" id="P_Alias" class="form-control" placeholder="Alias de la persona">
                </div>
            `,
            focusConfirm: false,
            showDenyButton: true,
            confirmButtonText: 'Aplicar filtros',
            confirmButtonColor: '#0d6efd',
            denyButtonText: 'Limpiar filtros',
            denyButtonColor: '#dc3545',
            allowOutsideClick: false,
            didOpen: () => {
                // Cargar valores almacenados en el formulario si existen
                if (joinPersonasParams.P_Rol) document.getElementById('P_Rol').value = joinPersonasParams.P_Rol;
                if (joinPersonasParams.P_Apellido) document.getElementById('P_Apellido').value = joinPersonasParams.P_Apellido;
                if (joinPersonasParams.P_Nombre) document.getElementById('P_Nombre').value = joinPersonasParams.P_Nombre;
                if (joinPersonasParams.P_Alias) document.getElementById('P_Alias').value = joinPersonasParams.P_Alias;
            },
            preConfirm: () => {
                // Almacenar valores en la variable global
                joinPersonasParams = {
                    P_Rol: document.getElementById('P_Rol').value,
                    P_Apellido: document.getElementById('P_Apellido').value,
                    P_Nombre: document.getElementById('P_Nombre').value,
                    P_Alias: document.getElementById('P_Alias').value
                };
            },
            preDeny: () => {
                // Limpiar los filtros
                joinPersonasParams = {}; // Vaciar los filtros
            }
        });
    });

    // Event listener para 'Vehículos'
    document.getElementById('joinVehiculosParams').addEventListener('click', function() {
        Swal.fire({
            title: 'Filtrar por vehículos',
            html: `
                <div class="input-group mb-3">
                    <label for="V_Rol" class="input-group-text fw-bold col-4">Rol</label>
                    <select id="V_Rol" class="form-select">
                        <option value="">Indistinto</option>
                        <option value="Vehículo mencionado">Vehículo mencionado</option>
                        <option value="Vehículo utilizado para el almacenamiento">Vehículo utilizado para el almacenamiento</option>
                        <option value="Vehículo que frecuenta la zona">Vehículo que frecuenta la zona</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <label for="V_Tipo" class="input-group-text fw-bold col-4">Tipo</label>
                    <select id="V_Tipo" class="form-select">
                        <option value="">Indistinto</option>
                        <option value="Acoplado">Acoplado</option>
                        <option value="Automóvil">Automóvil</option>
                        <option value="Avioneta">Avioneta</option>
                        <option value="Bicicleta">Bicicleta</option>
                        <option value="Bicicleta eléctrica">Bicicleta eléctrica</option>
                        <option value="Camión">Camión</option>
                        <option value="Camioneta">Camioneta</option>
                        <option value="Chasis de camión">Chasis de camión</option>
                        <option value="Ciclomotor">Ciclomotor</option>
                        <option value="Cuatriciclo">Cuatriciclo</option>
                        <option value="Ómnibus / Colectivo / Micro">Ómnibus / Colectivo / Micro</option>
                        <option value="Embarcación a motor">Embarcación a motor</option>
                        <option value="Furgón de carga">Furgón de carga</option>
                        <option value="Lancha">Lancha</option>
                        <option value="Máquina agrícola">Máquina agrícola</option>
                        <option value="Máquina de construcción">Máquina de construcción</option>
                        <option value="Máquina de servicios">Máquina de servicios</option>
                        <option value="Moto vehículo">Moto vehículo</option>
                        <option value="Moto vehículo acuático">Moto vehículo acuático</option>
                        <option value="Tractor">Tractor</option>
                        <option value="Triciclo">Triciclo</option>
                        <option value="Vehículo oficial">Vehículo oficial</option>
                        <option value="Vehículo a tracción animal (Carros)">Vehículo a tracción animal (Carros)</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <label for="V_Color" class="input-group-text fw-bold col-4">Color</label>
                    <input type="text" id="V_Color" class="form-control" placeholder="Color del vehículo">
                </div>
                <div class="input-group mb-3">
                    <label for="V_Marca" class="input-group-text fw-bold col-4">Marca</label>
                    <input type="text" id="V_Marca" class="form-control" placeholder="Marca del vehículo">
                </div>
                <div class="input-group mb-3">
                    <label for="V_Modelo" class="input-group-text fw-bold col-4">Modelo</label>
                    <input type="text" id="V_Modelo" class="form-control" placeholder="Modelo del vehículo">
                </div>
                <div class="input-group mb-3">
                    <label for="V_Dominio" class="input-group-text fw-bold col-4">Dominio</label>
                    <input type="text" id="V_Dominio" class="form-control" placeholder="Dominio del vehículo">
                </div>
            `,
            focusConfirm: false,
            showDenyButton: true,
            confirmButtonText: 'Aplicar filtros',
            confirmButtonColor: '#0d6efd',
            denyButtonText: 'Limpiar filtros',
            denyButtonColor: '#dc3545',
            allowOutsideClick: false,
            didOpen: () => {
                // Cargar valores almacenados en el formulario si existen
                if (joinVehiculosParams.V_Rol) document.getElementById('V_Rol').value = joinVehiculosParams.V_Rol;
                if (joinVehiculosParams.V_Tipo) document.getElementById('V_Tipo').value = joinVehiculosParams.V_Tipo;
                if (joinVehiculosParams.V_Color) document.getElementById('V_Color').value = joinVehiculosParams.V_Color;
                if (joinVehiculosParams.V_Marca) document.getElementById('V_Marca').value = joinVehiculosParams.V_Marca;
                if (joinVehiculosParams.V_Modelo) document.getElementById('V_Modelo').value = joinVehiculosParams.V_Modelo;
                if (joinVehiculosParams.V_Dominio) document.getElementById('V_Dominio').value = joinVehiculosParams.V_Dominio;
            },
            preConfirm: () => {
                // Almacenar valores en la variable global
                joinVehiculosParams = {
                    V_Rol: document.getElementById('V_Rol').value,
                    V_Tipo: document.getElementById('V_Tipo').value,
                    V_Color: document.getElementById('V_Color').value,
                    V_Marca: document.getElementById('V_Marca').value,
                    V_Modelo: document.getElementById('V_Modelo').value,
                    V_Dominio: document.getElementById('V_Dominio').value
                };
            },
            preDeny: () => {
                // Limpiar los filtros
                joinVehiculosParams = {}; // Vaciar los filtros
            }
        });
    });
});
