<?php
    // Clase encargada de las operaciones DELETE de la base de datos
    class DataBaseManager_Delete {
        private $conn;
    
        public function __construct($conn) {
            $this->conn = $conn;
        }
    
        public function DELETE_Persona($ClavePrimaria) {
            $stmt = $this->conn->prepare("DELETE FROM entidad_personas WHERE ID_Persona = ?");
            $stmt->bind_param("s", $ClavePrimaria);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
    
        public function DELETE_Lugar($ClavePrimaria) {
            $stmt = $this->conn->prepare("DELETE FROM entidad_lugares WHERE ID_Lugar = ?");
            $stmt->bind_param("s", $ClavePrimaria);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
    
        public function DELETE_Vehiculo($ClavePrimaria) {
            $stmt = $this->conn->prepare("DELETE FROM entidad_vehiculos WHERE ID_Vehiculo = ?");
            $stmt->bind_param("s", $ClavePrimaria);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
    }    

?>
