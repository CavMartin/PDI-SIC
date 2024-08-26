<?php
// Clase encargada de obtener los datos de incidencias a mostrar en la pagina principal
class MainPageHandler {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Método genérico para ejecutar consultas y obtener datos
    public function fetchDataQuery($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conn->error);
        }

        // Comprobar si hay parámetros para vincular
        if (!empty($params)) {
            // Crear una cadena con tipos de datos para bind_param (asumiendo todos 's' para simplificar)
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

    // Método específico para obtener los datos de la página principal para el grupo SIACIP
    public function fetchDataForMainPage() {
        $sql = "SELECT ID, Formulario, Clasificacion, Estado, Division, FechaDeCreacion 
                FROM entidad_encabezado 
                WHERE Estado = 1 
                ORDER BY FechaDeCreacion DESC 
                LIMIT 10";
        return $this->fetchDataQuery($sql);
    }

    // Método para generar la tabla para SIACIP
    public function generateTableForMainPage($datosMainPage) {
        $html = '<div class="MainTableSIACIP">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark fs-5" style="vertical-align: middle;">
                            <tr>
                                <th>FORMULARIO</th>
                                <th>DIVISIÓN</th>
                                <th>ESTADO</th>
                                <th>CLASIFICACIÓN</th>
                                <th>FECHA</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody style="vertical-align: middle;">';

        foreach ($datosMainPage as $row) {
            $fechaFormateada = date("d/m/Y - H:i", strtotime($row["FechaDeCreacion"]));
            $ID = htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8');
            $formularioID = htmlspecialchars($row["Formulario"], ENT_QUOTES, 'UTF-8');
            $division = htmlspecialchars($row["Division"], ENT_QUOTES, 'UTF-8');
            $incidenciaTipo = htmlspecialchars($row["Clasificacion"], ENT_QUOTES, 'UTF-8');

            $html .= '<tr id="fila-' . $formularioID . '">
                          <td class="fw-bold fs-5">' . $formularioID . '</td>
                          <td class="fw-bold fs-5">' . $division . '</td>
                          <td class="fw-bold fs-5"><button type="button" class="btn btn-outline-primary" onclick="cambiarEstado(\'' . $ID . '\', \'' . $formularioID . '\')">EN PROCESO</button></td>
                          <td class="fw-bold fs-5">' . $incidenciaTipo . '</td>
                          <td class="fw-bold fs-5">' . $fechaFormateada . '</td>
                          <td class="fw-bold fs-5" style="min-width: 15rem">
                              <div class="d-flex justify-content-center">
                                <form action="Main.php" method="POST" class="mr-2">
                                  <input type="hidden" name="ID" value="' . $ID . '">
                                  <input type="hidden" name="formularioID" value="' . $formularioID . '">
                                  <input type="submit" class="btn btn-primary mx-2" value="FORMULARIO">
                                </form>
                                <button type="button" class="btn btn-danger mx-2" id="generatePDF" onclick="generarPDF(\'' . htmlspecialchars($row["ID"], ENT_QUOTES, 'UTF-8') . '\')">GENERAR PDF</button>
                              </div>
                          </td>
                      </tr>';
        }

        $html .= '</tbody>
                </table>
              </div>';

        return $html;
    }

}

?>
