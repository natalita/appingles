<?php
include_once '../Modelo/zona_horaria.php';
include_once '../Config/conexion.php';

session_start();

// Verificar si se recibió una solicitud POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener los datos enviados en formato JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar si se recibieron los datos esperados
    if (isset($data['unidadId'])) {
        // Obtener el ID de la unidad desde los datos recibidos
        $unidadId = $data['unidadId'];
        $id_usuario = $_SESSION['id_usuario'];


        // 1. Consulta para obtener todas las actividades de tipo Test asociadas a la unidad específica
        $sql_all_activities = "SELECT a.id_actividad
                                FROM actividad a
                                INNER JOIN recurso r ON a.id_recurso = r.id_recurso
                                WHERE a.tipo = 'Test' AND r.id_unidad = '$unidadId'";

        $result_all_activities = mysqli_query($con, $sql_all_activities);

        // 2. Consulta para obtener las actividades de tipo Test con notas asociadas para el usuario especificado
        $sql_completed_activities = "SELECT a.id_actividad
                                        FROM actividad a
                                        LEFT JOIN recurso r ON a.id_recurso = r.id_recurso
                                        LEFT JOIN nota_actividad na ON na.id_actividad = a.id_actividad
                                        LEFT JOIN nota n ON n.id_nota = na.id_nota
                                        WHERE a.tipo = 'Test' AND r.id_unidad = '$unidadId' AND n.id_usuario = '$id_usuario'";

        $result_completed_activities = mysqli_query($con, $sql_completed_activities);

        // 3. Comparar los resultados y obtener las ID de las actividades que el usuario aún no ha completado
        $completed_activity_ids = array();
        while ($row = mysqli_fetch_assoc($result_completed_activities)) {
            $completed_activity_ids[] = $row['id_actividad'];
        }

        $remaining_activity_ids = array();
        while ($row = mysqli_fetch_assoc($result_all_activities)) {
            if (!in_array($row['id_actividad'], $completed_activity_ids)) {
                $remaining_activity_ids[] = $row['id_actividad'];
            }
        }

        // 4. Devolver las ID de las actividades restantes como parte de la respuesta
        $response = array(
            'remaining_activity_ids' => $remaining_activity_ids
        );

        // Devolver la respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($response);



    } else {
        // Si no se recibieron los datos esperados, devolver un error
        http_response_code(400); // Bad Request
        echo json_encode(array('error' => 'Falta el ID de la unidad en la solicitud.'));
    }
} else {
    // Si no se recibió una solicitud POST, devolver un error
    http_response_code(405); // Method Not Allowed
    echo json_encode(array('error' => 'Se esperaba una solicitud POST.'));
}


?>