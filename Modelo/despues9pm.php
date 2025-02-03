<?php
include_once '../Modelo/zona_horaria.php';
include_once '../Config/conexion.php';

session_start();

// Establecer la zona horaria
date_default_timezone_set($user_timezone);

// Obtener la zona horaria actualmente configurada
$current_timezone = date_default_timezone_get();

// Obtener los valores de la solicitud POST
$id_usuario = $_POST['userId'];

$logros_response[] = array();

$mensaje = '';


$logros = array(
    15 => 5,   // 5-late activity
    16 => 10,   // 10-late activity
    17 => 20,  // 20-late activity
    18 => 30,   // 30-late activity
    19 => 40   // 40-late activity
);

try {
    // Iniciar transacción
    mysqli_begin_transaction($con);

    // Verificar si ya existen registros de logros para este usuario
    $query_check_logros = "SELECT * FROM usuario_logro WHERE id_usuario = $id_usuario AND id_logro BETWEEN 15 AND 19";

    $resultado_check_logros = mysqli_query($con, $query_check_logros);

    if (mysqli_num_rows($resultado_check_logros) == 0) {
        // Si no hay registros de logros para este usuario, crear los logros iniciales
        
        foreach ($logros as $id_logro => $cantidad) {
            $cantidad = ($id_logro == 15) ? 1 : 0;

            $query_insert_logro = "INSERT INTO usuario_logro (id_usuario, id_logro, cantidad, completado, fecha_logro) VALUES ($id_usuario, $id_logro, $cantidad, 0, '1900-01-01 00:00:00')";
            mysqli_query($con, $query_insert_logro);
        }
    } else {

        // Obtener el último logro completado por el usuario
        
        $query_ultimo_logro_completado = "SELECT id_logro, cantidad FROM usuario_logro WHERE id_usuario = $id_usuario AND id_logro IN (15, 16, 17, 18, 19) AND completado = 1 ORDER BY id_logro DESC LIMIT 1;";

        $resultado_ultimo_logro_completado = mysqli_query($con, $query_ultimo_logro_completado);

        if (mysqli_num_rows($resultado_ultimo_logro_completado) > 0) {
            $response['message'] = '3';
            $row_ultimo_logro_completado = mysqli_fetch_assoc($resultado_ultimo_logro_completado);
            $ultimo_logro_completado = $row_ultimo_logro_completado['id_logro'];
            $cantidad_utlimo_logro_completado = $row_ultimo_logro_completado['cantidad'];

            if($ultimo_logro_completado != 19){
                foreach ($logros as $id_logro => $cantidad) {
                    if ($id_logro >= $ultimo_logro_completado) {



                        $logro_info = array();
                        $logro_info['ultimo_logro_completado'] = $ultimo_logro_completado;
                        $logro_info['id_logro'] = $id_logro;
                        $logro_info['cantidad_requerida'] = $cantidad;
                        $logro_info['cantidad_utlimo_logro_completado + 1'] = $cantidad_utlimo_logro_completado + 1;

                        $logros_response[] = $logro_info;


                        $query_update_logro = "UPDATE usuario_logro SET cantidad = $cantidad_utlimo_logro_completado + 1 WHERE id_usuario = $id_usuario AND id_logro = $id_logro";
                        mysqli_query($con, $query_update_logro);

                        if ($cantidad_utlimo_logro_completado + 1 == $cantidad) {
                            $query_update_completado = "UPDATE usuario_logro SET completado = 1, fecha_logro = NOW() WHERE id_usuario = $id_usuario AND id_logro = $id_logro";
                            mysqli_query($con, $query_update_completado);
                        }
                    }
                }
            }
            
        }else{
            $mensaje = 'no hay registros de logros completados';
            $query_logro_15 = "SELECT id_logro, cantidad, completado FROM usuario_logro WHERE id_usuario = $id_usuario AND id_logro = 15";

            $resultado_logro_15 = mysqli_query($con, $query_logro_15);

            if (mysqli_num_rows($resultado_logro_15) > 0) {
                $row_logro_15 = mysqli_fetch_assoc($resultado_logro_15);
                $logro_15 = $row_logro_15['id_logro'];
                $cantidad_logro_15 = $row_logro_15['cantidad'];
                $completado_logro_15 = $row_logro_15['completado'];
                if($completado_logro_15 != 1){
                    $mensaje = 'lo encontro y tiene estos valores: '.$logro_15.' y '.$cantidad_logro_15; 

                    $query_update_logro_15 = "UPDATE usuario_logro SET cantidad = cantidad + 1 WHERE id_usuario = $id_usuario AND id_logro = 15";
                    mysqli_query($con, $query_update_logro_15);
                    if($cantidad_logro_15 + 1 == $logros[15]){
                        $query_update_completado_15 = "UPDATE usuario_logro SET completado = 1, fecha_logro = NOW() WHERE id_usuario = $id_usuario AND id_logro = 15";
                        mysqli_query($con, $query_update_completado_15);
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





// Crear un array con los valores recibidos
$response = array(
    'userId' => $id_usuario,
    'logros_response' => $logros_response,
    'si' => 'si 9pm',
    'mensaje' => $mensaje
);





















// Devolver los valores como JSON
echo json_encode($response);
exit;

?>
