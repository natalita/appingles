<?php
include_once '../Config/conexion.php';

$response = array();

$data = json_decode(file_get_contents('php://input'), true);

try {
    $response['message'] = '0';
    // Iniciar transacción
    mysqli_begin_transaction($con);

    // Obtener el id_usuario desde la URL
    $id_usuario = $data['id_usuario'];
    $nota_actividad = $data['nota_actividad'];

    // Verificar si ya existen registros de logros para este usuario
    $query_check_logros = "SELECT * FROM usuario_logro WHERE id_usuario = $id_usuario AND id_logro BETWEEN 11 AND 14";

    $resultado_check_logros = mysqli_query($con, $query_check_logros);

    if (mysqli_num_rows($resultado_check_logros) == 0) {
        $response['message'] = '1';
        // Si no hay registros de logros para este usuario, crear los logros iniciales
        $logros = array(
            11 => 1,   // 1-No mistake activity
            12 => 5,   // 5-No mistake activity
            13 => 15,  // 15-No mistake activity
            14 => 30   // 30-No mistake activity
        );

        foreach ($logros as $id_logro => $cantidad) {
            list($completado, $cantidad, $fecha_del_logro) = ($id_logro == 11) ? [1, 1, 'NOW()'] : [0, 0, "'1900-01-01 00:00:00'"];
            
            $query_insert_logro = "INSERT INTO usuario_logro (id_usuario, id_logro, cantidad, completado, fecha_logro) VALUES ($id_usuario, $id_logro, $cantidad, $completado, $fecha_del_logro)";
            mysqli_query($con, $query_insert_logro);
        }
    } else {
        $response['message'] = '2';
        // Si ya existen registros de logros para este usuario, actualizar los logros según la cantidad de notas 10 obtenidas
        $query_notas_10 = "SELECT COUNT(*) AS total_notas_10 FROM nota WHERE id_usuario = $id_usuario AND nota = 10 AND tipo = 'Test'";
        $resultado_notas_10 = mysqli_query($con, $query_notas_10);
        $row_notas_10 = mysqli_fetch_assoc($resultado_notas_10);
        $total_notas_10 = $row_notas_10['total_notas_10'];

        // Obtener el último logro completado por el usuario
        $query_ultimo_logro_completado = "SELECT id_logro FROM usuario_logro WHERE id_usuario = $id_usuario AND id_logro IN (11, 12, 13, 14) AND completado = 1 ORDER BY id_logro DESC LIMIT 1";

        $resultado_ultimo_logro_completado = mysqli_query($con, $query_ultimo_logro_completado);

        if (mysqli_num_rows($resultado_ultimo_logro_completado) > 0) {
            $response['message'] = '3';
            $row_ultimo_logro_completado = mysqli_fetch_assoc($resultado_ultimo_logro_completado);
            $ultimo_logro_completado = $row_ultimo_logro_completado['id_logro'];

            // Actualizar los logros según la cantidad de notas 10 obtenidas
            $logros = array(
                12 => 5,   // 5-No mistake activity
                13 => 15,  // 15-No mistake activity
                14 => 30   // 30-No mistake activity
            );

            foreach ($logros as $id_logro => $cantidad) {
                if ($id_logro > $ultimo_logro_completado) {
                    $response['message'] = '4';



                    $logro_info = array();
                    $logro_info['ultimo_logro_completado'] = $ultimo_logro_completado;
                    $logro_info['id_logro'] = $id_logro;
                    $logro_info['total_notas_10'] = $total_notas_10;
                    $logro_info['cantidad_requerida'] = $cantidad;

                    $logros_response[] = $logro_info;







                    $query_update_logro = "UPDATE usuario_logro SET cantidad = $total_notas_10 WHERE id_usuario = $id_usuario AND id_logro = $id_logro";
                    mysqli_query($con, $query_update_logro);

                    if ($total_notas_10 == $cantidad) {
                        $response['message'] = '5';
                        $query_update_completado = "UPDATE usuario_logro SET completado = 1, fecha_logro = NOW() WHERE id_usuario = $id_usuario AND id_logro = $id_logro";
                        mysqli_query($con, $query_update_completado);
                    }
                }
            }
        }
    }

    // Confirmar la transacción
    mysqli_commit($con);

    // Cerrar la conexión a la base de datos
    mysqli_close($con);

    $response['status'] = 'success';
    
} catch (Exception $e) {
    // Rollback de la transacción en caso de error
    mysqli_rollback($con);

    // Cerrar la conexión a la base de datos
    mysqli_close($con);

    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}
$response['nota_actividad'] = $nota_actividad;
$response['id_usuario'] = $id_usuario;
$response['logros'] = $logros_response;

// Convertir la respuesta a JSON y enviarla
echo json_encode($response);
?>
