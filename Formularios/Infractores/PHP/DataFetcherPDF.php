<?php
// Clase encargada de obtener los datos para el PDF
class DataFetcherPDF {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function fetchData($query, $param) {
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conn->error);
        }
        $stmt->bind_param("s", $param);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    private function construirDirecciones($lugarHecho) {
        $texto = $lugarHecho['L_Calle'];

        // Construcción del texto de la dirección
        if (!empty($lugarHecho['L_AlturaCatastral'])) {
            $texto .= ' N° ' . $lugarHecho['L_AlturaCatastral'];
        }
        if (!empty($lugarHecho['L_CalleDetalle'])) {
            $texto .= ', ' . $lugarHecho['L_CalleDetalle'];
        }
        if (!empty($lugarHecho['L_Interseccion1'])) {
            $texto .= !empty($lugarHecho['L_Interseccion2']) ? ', entre ' . $lugarHecho['L_Interseccion1'] . ' y ' . $lugarHecho['L_Interseccion2'] : ' y ' . $lugarHecho['L_Interseccion1'];
        }
        $texto .= ', ' . $lugarHecho['L_Localidad'];

        return $texto;
    }

    public function fetchDataPDF($ID) {
        $DatosPDF = $this->ObtenerDatosPDF($ID);
        $this->enviarJSON($DatosPDF);
    }

    private function ObtenerDatosPDF($ID) {
        $DatosPDF = [];

        // Consulta para entidad encabezado
        $Encabezado = $this->fetchData("SELECT ID, Fecha, Tipo, Juzgado, Dependencia, Causa, Relato FROM entidad_encabezado WHERE ID = ?", $ID);
            if(count($Encabezado) > 0) {
                $encabezadoDatos = $Encabezado[0];
                $DatosPDF['Encabezado'] = $encabezadoDatos;
            } else {
                $DatosPDF['Encabezado'] = null;
            }

            // Procesamiento de Lugares del Hecho
            $lugaresHechos = $this->fetchData("SELECT ID_Lugar, L_Rol, L_TipoLugar, L_NombreLugarEspecifico, L_Calle, L_AlturaCatastral, L_CalleDetalle, L_Interseccion1, L_Interseccion2, L_Barrio, L_Localidad FROM entidad_lugares WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden ASC", $ID);
            $DatosPDF['Lugares'] = [];

            foreach ($lugaresHechos as $lugarHecho) {
                $direccion = $this->construirDirecciones($lugarHecho);
            
                // Consulta para Datos Complementarios del Lugar
                $datosComplementarios = $this->fetchData("SELECT DC_Tipo, DC_ImagenAdjunta, DC_Comentario FROM entidad_datos_complementarios WHERE FK_Lugar = ? ORDER BY NumeroDeOrden ASC", $lugarHecho['ID_Lugar']);
            
                // Agregar los datos complementarios al arreglo de lugar si existen
                if (!empty($direccion)) {
                    $DatosPDF['Lugares'][] = [
                        "Rol" => $lugarHecho['L_Rol'],
                        "Tipo" => $lugarHecho['L_TipoLugar'],
                        "Nombre" => $lugarHecho['L_NombreLugarEspecifico'],
                        "Calle" => $lugarHecho['L_Calle'],
                        "AlturaCatastral" => $lugarHecho['L_AlturaCatastral'],
                        "CalleDetalle" => $lugarHecho['L_CalleDetalle'],
                        "Interseccion1" => $lugarHecho['L_Interseccion1'],
                        "Interseccion2" => $lugarHecho['L_Interseccion2'],
                        "Barrio" => $lugarHecho['L_Barrio'],
                        "Localidad" => $lugarHecho['L_Localidad'],
                        "Direccion" => $direccion,
                        "DatosComplementarios" => $datosComplementarios
                    ];
                }
            }

        // Consulta para entidad Personas y sus entidades complementarias
        $personas = $this->fetchData("SELECT ID_Persona, P_FotoPersona, P_Rol, P_Apellido, P_Nombre, P_Alias, P_Genero, P_DNI, P_Edad, P_EstadoCivil, P_Pais FROM entidad_personas WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $ID);
            foreach ($personas as $key => $persona) {
                $domicilios = $this->fetchData("SELECT L_Rol, L_Calle, L_AlturaCatastral, L_CalleDetalle, L_Localidad, L_Provincia, L_Pais FROM entidad_lugares WHERE FK_Persona = ? ORDER BY NumeroDeOrden ASC", $persona['ID_Persona']);
                // Procesar cada domicilio a través de construirDirecciones
                $domiciliosProcesados = array_map(function($domicilio) {
                    return [
                        "Rol" => $domicilio['L_Rol'],
                        "Direccion" => $this->construirDirecciones($domicilio)
                    ];
                }, $domicilios);
                $personas[$key]['Domicilios'] = $domiciliosProcesados;

                $datosComplementarios = $this->fetchData("SELECT DC_Tipo, DC_ImagenAdjunta, DC_Comentario FROM entidad_datos_complementarios WHERE FK_Persona = ? ORDER BY NumeroDeOrden ASC", $persona['ID_Persona']);
                $personas[$key]['DatosComplementarios'] = $datosComplementarios;
            }
        $DatosPDF['Personas'] = $personas;

        // Consulta para entidad Vehiculo y agregado de datos complementarios
        $vehiculos = $this->fetchData("SELECT ID_Vehiculo, NumeroDeOrden, V_Rol, V_TipoVehiculo, V_Color, V_Marca, V_Modelo, V_Año, V_Dominio, V_NumeroChasis, V_NumeroMotor FROM entidad_vehiculos WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $ID);
            foreach ($vehiculos as $key => $vehiculo) {
                $datosComplementariosVehiculo = $this->fetchData("SELECT DC_Tipo, DC_ImagenAdjunta, DC_Comentario FROM entidad_datos_complementarios WHERE FK_Vehiculo = ? ORDER BY NumeroDeOrden ASC", $vehiculo['ID_Vehiculo']);
                $vehiculos[$key]['DatosComplementarios'] = $datosComplementariosVehiculo;
            }
        $DatosPDF['Vehiculos'] = $vehiculos;

        // Consulta para entidad Arma y agregado de datos complementarios
        $armas = $this->fetchData("SELECT ID_Arma, NumeroDeOrden, AF_TipoAF, AF_EsDeFabricacionCasera, AF_Marca, AF_Modelo, AF_Calibre FROM entidad_armas WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $ID);
            foreach ($armas as $key => $arma) {
                $datosComplementariosArma = $this->fetchData("SELECT DC_Tipo, DC_ImagenAdjunta, DC_Comentario FROM entidad_datos_complementarios WHERE FK_Arma = ? ORDER BY NumeroDeOrden ASC", $arma['ID_Arma']);
                $armas[$key]['DatosComplementarios'] = $datosComplementariosArma;
            }
        $DatosPDF['Armas'] = $armas;

        return $DatosPDF;
    }

    // Método para enviar datos como JSON
    private function enviarJSON($data) {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}

?>
