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
            return $stmt->execute();
        }

        public function eliminarDomicilio($idLugar) {
            $stmt = $this->conn->prepare("DELETE FROM entidad_lugares WHERE ID_Lugar = ?");
            $stmt->bind_param("s", $idLugar);
            return $stmt->execute();
        }

        public function DELETE_Lugar($ClavePrimaria) {
            $stmt = $this->conn->prepare("DELETE FROM entidad_lugares WHERE ID_Lugar = ?");
            $stmt->bind_param("s", $ClavePrimaria);
            return $stmt->execute();
        }

        public function DELETE_Vehiculo($ClavePrimaria) {
            $stmt = $this->conn->prepare("DELETE FROM entidad_vehiculos WHERE ID_Vehiculo = ?");
            $stmt->bind_param("s", $ClavePrimaria);
            return $stmt->execute();
        }

        public function DELETE_AF($ClavePrimaria) {
            $stmt = $this->conn->prepare("DELETE FROM entidad_armas WHERE ID_Arma = ?");
            $stmt->bind_param("s", $ClavePrimaria);
            return $stmt->execute();
        }

        public function DELETE_Mensaje($ClavePrimaria) {
            $stmt = $this->conn->prepare("DELETE FROM entidad_mensajes_extorsivos WHERE ID_Mensaje = ?");
            $stmt->bind_param("s", $ClavePrimaria);
            return $stmt->execute();
        }

        public function eliminarDatoComplementario($idDato) {
            $stmt = $this->conn->prepare("DELETE FROM entidad_datos_complementarios WHERE ID_DatoComplementario = ?");
            return $stmt->execute([$idDato]);
        }

    }

?>
