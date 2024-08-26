<?php
    // Clase encargada de la auditoria de los Logins
    class AuditManager {
        private $conn;
    
        public function __construct($conn) {
            $this->conn = open_database_connection('sistema_horus');

        }

    }

?>
