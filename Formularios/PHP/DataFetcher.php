<?php
// Clase encargada de obtener los datos de las entidades a mostrar en la pagina principal
class DataFetcher {
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

    public function fetchDataMainIP($IP_Numero) {
        return $this->ObtenerDatosMainIP($IP_Numero);
    }

    private function ObtenerDatosMainIP($IP_Numero) {
        $DatosMain = [];

        // Consulta para entidad Personas
        $DatosMain['Personas'] = $this->fetchData("SELECT ID_Persona, NumeroDeOrden, P_Apellido, P_Nombre, P_Genero, P_DNI FROM entidad_personas WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $IP_Numero);

        // Consulta para entidad Lugares
        $DatosMain['Lugares'] = $this->fetchData("SELECT ID_Lugar, NumeroDeOrden, L_TipoLugar, L_Calle, L_AlturaCatastral, L_Interseccion1, L_Interseccion2 FROM entidad_lugares WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $IP_Numero);

        // Consulta para entidad Arma
        $DatosMain['Armas'] = $this->fetchData("SELECT ID_Arma, NumeroDeOrden, AF_TipoAF, AF_Marca, AF_Modelo, AF_Calibre FROM entidad_armas WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $IP_Numero);

        // Consulta para entidad Vehiculo
        $DatosMain['Vehiculos'] = $this->fetchData("SELECT ID_Vehiculo, NumeroDeOrden, V_TipoVehiculo, V_Marca, V_Modelo, V_Dominio FROM entidad_vehiculos WHERE FK_Encabezado = ? ORDER BY NumeroDeOrden", $IP_Numero);

        return $DatosMain;
    }

    public function fetchDataEncabezado($ClavePrimaria) {
        return $this->obtenerDatosEncabezado($ClavePrimaria);
    }

    private function obtenerDatosEncabezado($ClavePrimaria) {
        $IPData = null;
        // Consulta SQL para obtener los datos de una persona específica por su ID
        $stmt = $this->conn->prepare("SELECT * FROM entidad_encabezado WHERE ID = ?");
        $stmt->bind_param("s", $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $IPData = $result->fetch_assoc();
        }

        return $IPData;
    }

    public function fetchDataPersona($ClavePrimaria) {
        return $this->obtenerDatosPersona($ClavePrimaria);
    }

    private function obtenerDatosPersona($ClavePrimaria) {
        $datosPersona = null;
        // Consulta SQL para obtener los datos de una persona específica por su ID
        $stmt = $this->conn->prepare("SELECT * FROM entidad_personas WHERE ID_Persona = ?");
        $stmt->bind_param("s", $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $datosPersona = $result->fetch_assoc();
        }

        return $datosPersona;
    }

    private function fetchDataDomicilios($ID_Persona) {
        // Consulta SQL para obtener los domicilios relacionados a una persona
        $stmt = $this->conn->prepare("SELECT * FROM entidad_lugares WHERE FK_Persona = ? ORDER BY NumeroDeOrden ASC");
        $stmt->bind_param("s", $ID_Persona);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            while ($domicilio = $result->fetch_assoc()) {
                $domicilios[] = $domicilio;
            }
        }
    
        $stmt->close();
        return $domicilios;
    }

    public function getDomiciliosJSON($ID_Persona) { // Para obtener los domicilios ya cargados por AJAX
        $domicilios = $this->fetchDataDomicilios($ID_Persona);
        header('Content-Type: application/json');
        echo json_encode($domicilios);
    }

    public function fetchDataLugar($ClavePrimaria) {
        return $this->obtenerDatosLugar($ClavePrimaria);
    }

    private function obtenerDatosLugar($ClavePrimaria) {
        $datosLugar = null;
        // Consulta SQL para obtener los datos de un lugar del hecho específico por su ID
        $stmt = $this->conn->prepare("SELECT * FROM entidad_lugares WHERE ID_Lugar = ?");
        $stmt->bind_param("s", $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $datosLugar = $result->fetch_assoc();
        }
    
        $stmt->close();
        return $datosLugar;
    }

    public function fetchDataVehiculo($ClavePrimaria) {
        return $this->obtenerDatosVehiculo($ClavePrimaria);
    }

    private function obtenerDatosVehiculo($ClavePrimaria) {
        $datosVehiculo = null;
        // Consulta SQL para obtener los datos de un Vehiculo específico por su ID
        $stmt = $this->conn->prepare("SELECT * FROM entidad_vehiculos WHERE ID_Vehiculo = ?");
        $stmt->bind_param("s", $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $datosVehiculo = $result->fetch_assoc();
        }
    
        $stmt->close();
        return $datosVehiculo;
    }

    public function fetchDataAF($ClavePrimaria) {
        return $this->obtenerDatosAF($ClavePrimaria);
    }

    private function obtenerDatosAF($ClavePrimaria) {
        $datosAF = null;
        // Consulta SQL para obtener los datos de un arma de fuego específica por su ID
        $stmt = $this->conn->prepare("SELECT * FROM entidad_armas WHERE ID_Arma = ?");
        $stmt->bind_param("s", $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $datosAF = $result->fetch_assoc();
        }
    
        $stmt->close();
        return $datosAF;
    }

    public function fetchDataMensajes($ClavePrimaria) {
        return $this->obtenerDatosMensajes($ClavePrimaria);
    }

    private function obtenerDatosMensajes($ClavePrimaria) {
        $datosMensajes = null;
        // Consulta SQL para obtener los datos de un mensaje extorsivo específico por su ID
        $stmt = $this->conn->prepare("SELECT * FROM entidad_mensajes_extorsivos WHERE ID_Mensaje = ?");
        $stmt->bind_param("s", $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $datosMensajes = $result->fetch_assoc();
        }
    
        $stmt->close();
        return $datosMensajes;
    }

    private function fetchDataDatosComplementarios($ClavePrimaria) {
        $stmt = $this->conn->prepare("SELECT * FROM entidad_datos_complementarios WHERE (FK_Persona = ? OR FK_Lugar = ? OR FK_Vehiculo = ? OR FK_Arma = ?) ORDER BY NumeroDeOrden ASC");
        $stmt->bind_param("ssss", $ClavePrimaria, $ClavePrimaria, $ClavePrimaria, $ClavePrimaria);
        $stmt->execute();
        $result = $stmt->get_result();
        $datosComplementarios = [];
    
        if ($result->num_rows > 0) {
            while ($complementario = $result->fetch_assoc()) {
                $datosComplementarios[] = $complementario;
            }
        }
    
        $stmt->close();
        return $datosComplementarios;
    }

    public function getDatosComplementariosJSON($ClavePrimaria) { // Para obtener los datos complementarios ya cargados por AJAX
        $datosComplementarios = $this->fetchDataDatosComplementarios($ClavePrimaria);
        header('Content-Type: application/json');
        echo json_encode($datosComplementarios);
    }

}

?>
