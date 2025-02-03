<?php
include_once '../Modelo/zona_horaria.php';
include_once '../Config/conexion.php';

date_default_timezone_set($user_timezone);

// Obtener la zona horaria actualmente configurada
$current_timezone = date_default_timezone_get();



$end_date = '';
$first_activity_date = '';
$num_racha = '';
$start_date = '';
$diferencia_tiempo = '';
$consulta = '';
$dayOfWeek = '';
$cTEMPORAL = array(); // Inicializar el array temporal

// Obtener el ID del usuario desde la solicitud POST
$userId = $_POST['userId'];
//$userId = '2';
$hoy = date('Y-m-d', time());
$response['message'] = '';
$diferencia_tiempo = obtenerDiferenciaTIempo($con, $userId);
$sql_update = "UPDATE racha 
                SET num_racha = CASE WHEN $diferencia_tiempo > 93600 THEN 0 ELSE num_racha + 1 END, 
                    end_date = NOW(), 
                    first_activity_date = NOW(),
                    start_date = CASE 
                                    WHEN $diferencia_tiempo > 93600 THEN NOW() ELSE start_date 
                                END
                WHERE id_usuario = $userId";

// Función para obtener el nuevo valor de num_racha
function obtenerNuevoNumRacha($con, $userId) {
    $query = "SELECT num_racha FROM racha WHERE id_usuario = $userId";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['num_racha'];
}
function obtenerDiferenciaTIempo($con, $userId) {
    $query = "SELECT TIMESTAMPDIFF(SECOND, end_date, NOW()) as diferencia_tiempo FROM racha WHERE id_usuario = $userId";
    $result = mysqli_query($con, $query);
    if(mysqli_num_rows( $result ) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row['diferencia_tiempo'];
    }else{
        return 1;
    }
}

$sql_fecha_actividad = "SELECT num_racha, end_date, first_activity_date, start_date FROM racha WHERE id_usuario = $userId";
$resultado_fecha = mysqli_query($con, $sql_fecha_actividad);

if (mysqli_num_rows($resultado_fecha) > 0) {
    $response['message'] = '1';
    $row = mysqli_fetch_assoc($resultado_fecha);
    //$end_date = strtotime($row['end_date']);

    $end_date = $row['end_date'];
    // Obtener la fecha en formato de cadena
    $end_date_timestamp = strtotime($end_date); // Convertir la cadena de fecha en un timestamp
    
    //$first_activity_date = strtotime($row['first_activity_date']);
    $first_activity_date = $row['first_activity_date'];
    $num_racha = $row['num_racha'];
    //$start_date = strtotime($row['start_date']);
    $start_date = $row['start_date'];


    // Consulta para saber si tiene Weekend streak proteccion
    $queryRacWeekActivada = "SELECT 
                                ub.id_usuario_bonificacion, 
                                ub.id_usuario, 
                                ub.id_bonificacion, 
                                ub.fecha_uso, 
                                ub.estado, 
                                b.id_bonificacion, 
                                b.nombre_bonificacion 
                            FROM 
                                usuario_bonificacion ub 
                            JOIN 
                                bonificacion b 
                            ON 
                                ub.id_bonificacion = b.id_bonificacion 
                            WHERE 
                                ub.fecha_uso >= CURDATE() AND 
                                ub.fecha_uso < DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND
                                DAYOFWEEK(ub.fecha_uso) = 1 AND
                                TIME(ub.fecha_uso) <= '23:59:59' AND 
                                ub.estado = 'utilizada' AND 
                                b.nombre_bonificacion = 'Weekend streak' AND
                                ub.id_usuario = '$userId'
                                LIMIT 1";
    $result_RacWeekActivada = mysqli_query($con, $queryRacWeekActivada);
    $row_result_RacWeekActivada = mysqli_fetch_assoc($result_RacWeekActivada);
    
    if($row_result_RacWeekActivada) {
        $response['message'] = '2';
        // Si tiene Weekend streak proteccion
        $dayOfWeek = date('N'); // 6 para sábado, 7 para domingo
        if ($dayOfWeek == 6 || $dayOfWeek == 7) {
            $response['message'] = '3';
            // Si la última actividad fue hoy, solo actualizar end_date
            if (date('Y-m-d', $end_date) === $hoy) {
                $response['message'] = '4';
                $sql_update = "UPDATE racha SET end_date = NOW() WHERE id_usuario = $userId";
            } else {
                $response['message'] = '5';
                // Si es la primera actividad del día, incrementar num_racha y actualizar first_activity_date
                $sql_update = "UPDATE racha 
                        SET num_racha = num_racha + 1, 
                            end_date = NOW(), 
                            first_activity_date = NOW() 
                        WHERE id_usuario = $userId";

            }
        }
    }else{
        $response['message'] = '6';
        //NO TIENE WEEKEND STREAK PROTECCION
        // Si la última actividad fue hoy, solo actualizar end_date
        if (date('Y-m-d', strtotime($end_date)) === $hoy) {
            $response['message'] = '7';
            $sql_update = "UPDATE racha SET end_date = NOW() WHERE id_usuario = $userId";
        }else{
            $response['message'] = '8';
        }
    }



    
    $consulta = $sql_update;

    // Ejecutar la consulta de actualización
    $result_update = mysqli_query($con, $sql_update);


    if ($result_update) {
    //if (1>0) {
        //"La fecha de última actividad y num_racha se han actualizado correctamente.";
        $response['message'] = '9';






        // Obtener el nuevo valor de num_racha
        $new_num_racha = obtenerNuevoNumRacha($con, $userId);
        
        
        $logros = array(
            1 => 3,   // 3-day Streak
            2 => 7,   // 7-day Streak
            3 => 14,  // 14-day Streak
            4 => 30,  // 30-day Streak
            5 => 50,  // 50-day Streak
            6 => 75,  // 75-day Streak
            7 => 125, // 125-day Streak
            8 => 180, // 180-day Streak
            9 => 250, // 250-day Streak
            10 => 365 // 365-day Streak
        );

        // Iterar sobre los casos de logros
        foreach ($logros as $logro => $dias) {
            $vTEMPORAL = '0';
            $mensajeLogro = '0';

            // Verificar si el nuevo valor de num_racha coincide con el caso de logro
            if ($new_num_racha == $dias) {                
                // Consultar si ya existe un registro para este usuario y logro en usuario_logro
                $query_usuario_logro = "SELECT cantidad FROM usuario_logro WHERE id_usuario = $userId AND id_logro = $logro";
                $result_usuario_logro = mysqli_query($con, $query_usuario_logro);
                $row_usuario_logro = mysqli_fetch_assoc($result_usuario_logro);

                if ($row_usuario_logro) {
                    // Si ya existe un registro, actualizar la cantidad si es menor al nuevo valor de num_racha
                    if ($row_usuario_logro['cantidad'] < $new_num_racha) {
                        $vTEMPORAL = 1;
                        $query_update_logro = "UPDATE usuario_logro SET cantidad = $new_num_racha WHERE id_usuario = $userId AND id_logro = $logro";
                        //mysqli_query($con, $query_update_logro);
                        $mensajeLogro = 'Achievement '.$logro.' unlocked';
                    }
                } else {
                    $vTEMPORAL = 3;
                    // Si no existe un registro, insertar uno nuevo con el nuevo valor de num_racha
                    $query_insert_logro = "INSERT INTO usuario_logro (id_usuario, id_logro, cantidad, fecha_logro) VALUES ($userId, $logro, $new_num_racha, NOW())";
                    
                    //mysqli_query($con, $query_insert_logro);
                    $mensajeLogro = 'Achievement '.$logro.' unlocked';
                }
            }
            $cTEMPORAL[$logro] = array(
            'dias' => $dias,
            'vTEMPORAL' => $vTEMPORAL,
            'mensajeLogro' => $mensajeLogro
            );
        }
        

    } else {
        //echo "Error al actualizar la fecha de última actividad y num_racha: " . mysqli_error($con);
        $error = mysqli_error($con);
        $response['status'] = 'error';
        $response['message'] = 'Error al actualizar la tabla racha';
        $response['error'] = $error;

        // Imprimir mensaje en la consola del navegador
        exit;
    }


    

}else {
    $response['message'] = '7';
    $consulta = $sql_update;

    // Si no hay un registro para el usuario, insertar uno nuevo con las fechas correspondientes
    $sql_insert = "INSERT INTO racha (id_usuario, end_date, num_racha, start_date, first_activity_date) VALUES ($userId, NOW(), 0, NOW(), FROM_UNIXTIME($hoy))";

    
    if (!mysqli_query($con, $sql_insert)) {
        $error = mysqli_error($con);
        $response['status'] = 'error';
        $response['message'] = 'Error al insertar en la tabla racha';
        $response['error'] = $error;

        exit;
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($con);

// Crear un array con los valores recibidos
$response = array(
    'userId' => $userId,
    'message' => $response["message"],
    'end_date' => $end_date,
    'first_activity_date' => $first_activity_date,
    'num_racha' => $num_racha,
    'start_date' => $start_date,
    'hoy' => $hoy,
    'diferencia_tiempo' => $diferencia_tiempo,
    'consulta' => $consulta, 
    'dayOfWeek' => $dayOfWeek,
    'logrosTemporales' => $cTEMPORAL
);

// Devolver los valores como JSON
echo json_encode($response);


?>