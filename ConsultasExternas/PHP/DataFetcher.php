<?php
// Clase encargada de obtener los datos de las entidades a mostrar en la pagina principal
class DataFetcher {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Método utilizado para enviar los datos a las tablas del sistema de consultas
    private function fetchDataQuery($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conn->error);
        }
    
        // Verifica si hay parámetros para enmascarar
        if (!empty($params)) {
            // Construye una cadena con tipos de datos
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, ...$params);
        }
    
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

    public function fetchDataQuery911($params) {
        $queryParams = [];
        $sql = "SELECT Nota, Fecha, Region, Localidad, Direccion, `0800/911`, Tipificacion, Denunciados, Relato FROM pve911";
        $conditions = [];
    
        $fechaDesde = $params['FechaDesde'] ?? '';
        $fechaHasta = $params['FechaHasta'] ?? '';
    
        // Remover 'FechaDesde' y 'FechaHasta' de $params para no interferir con el bucle foreach siguiente
        unset($params['FechaDesde'], $params['FechaHasta']);
    
        // Manejar FechaDesde y FechaHasta
        if (!empty($fechaDesde) && !empty($fechaHasta)) {
            $conditions[] = "Fecha BETWEEN ? AND ?";
            $queryParams[] = $fechaDesde;
            $queryParams[] = $fechaHasta;
        } elseif (!empty($fechaDesde)) {
            $conditions[] = "Fecha >= ?";
            $queryParams[] = $fechaDesde;
        } elseif (!empty($fechaHasta)) {
            $conditions[] = "Fecha <= ?";
            $queryParams[] = $fechaHasta;
        }
    
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $conditions[] = "$key LIKE ?";
                $queryParams[] = "%" . $value . "%";
            }
        }
    
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $sql .= " ORDER BY Fecha";
    
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return ['status' => 'error', 'message' => 'Error en la preparación de la consulta.'];
        }
    
        if (!empty($queryParams)) {
            $types = str_repeat('s', count($queryParams));
            $stmt->bind_param($types, ...$queryParams);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
    
        $DatosMain['pve911'] = $result->fetch_all(MYSQLI_ASSOC);
    
        $stmt->close();
    
        return $DatosMain;
    }   
    
    public function fetchDataQueryAUOP($params) {
        $queryParams = [];
        $sql = "SELECT Fecha, Hora, OtraDependencia, DelitoAUOP, LugardelHecho, Barrio, Victima, Imputado, RelatoDelHecho FROM auop";
        $conditions = [];
    
        $fechaDesde = $params['FechaDesde'] ?? '';
        $fechaHasta = $params['FechaHasta'] ?? '';
    
        // Remover 'FechaDesde' y 'FechaHasta' de $params para no interferir con el bucle foreach siguiente
        unset($params['FechaDesde'], $params['FechaHasta']);
    
        // Manejar FechaDesde y FechaHasta
        if (!empty($fechaDesde) && !empty($fechaHasta)) {
            $conditions[] = "Fecha BETWEEN ? AND ?";
            $queryParams[] = $fechaDesde;
            $queryParams[] = $fechaHasta;
        } elseif (!empty($fechaDesde)) {
            $conditions[] = "Fecha >= ?";
            $queryParams[] = $fechaDesde;
        } elseif (!empty($fechaHasta)) {
            $conditions[] = "Fecha <= ?";
            $queryParams[] = $fechaHasta;
        }
    
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $conditions[] = "$key LIKE ?";
                $queryParams[] = "%" . $value . "%";
            }
        }
    
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $sql .= " ORDER BY Fecha";
    
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return ['status' => 'error', 'message' => 'Error en la preparación de la consulta.'];
        }
    
        if (!empty($queryParams)) {
            $types = str_repeat('s', count($queryParams));
            $stmt->bind_param($types, ...$queryParams);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
    
        $DatosMain['auop'] = $result->fetch_all(MYSQLI_ASSOC);
    
        $stmt->close();
    
        return $DatosMain;
    }   

}

?>
