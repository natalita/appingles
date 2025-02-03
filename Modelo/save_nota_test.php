<?php
include_once '../Modelo/zona_horaria.php';
include_once '../Config/conexion.php';

session_start();

date_default_timezone_set($user_timezone);

// Obtener la zona horaria actualmente configurada
$current_timezone = date_default_timezone_get();

// Obtener datos del cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

// Asignar valores a variables
$id_usuario = $data['id_usuario'];
$id_unidad = $data['id_unidad'];
$nota = $data['nota'];
$tipo = $data['tipo'];
$actividadIds = $data['actividadIds'];
$puntosGanados = $data['puntosGanados'];

// Mensajes de depuración
error_log('id_usuario: ' . $id_usuario);
error_log('id_unidad: ' . $id_unidad);
error_log('nota: ' . $nota);
error_log('tipo: ' . $tipo);
error_log('puntosGanados: ' . $puntosGanados);
error_log('actividadIds: ' . json_encode($actividadIds));

// Inicializar el array de respuesta
$response = array('status' => 'success', 'message' => 'Nota guardada exitosamente', 'error' => '');

// Iniciar una transacción
mysqli_begin_transaction($con);

try {
    // Insertar la nota en la tabla nota
    $query = "INSERT INTO nota (id_usuario, id_unidad, nota, tipo) VALUES ('$id_usuario', '$id_unidad', '$nota', '$tipo')";
    $result = mysqli_query($con, $query);

    if (!$result) {
        throw new Exception('Error al insertar en la tabla nota: ' . mysqli_error($con));
    }

    // Obtener el ID de la última nota insertada
    $id_nota = mysqli_insert_id($con);

    // Insertar las relaciones en la tabla nota_actividad
    foreach ($actividadIds as $actividadId) {
        $query = "INSERT INTO nota_actividad (id_nota, id_actividad) VALUES ('$id_nota', '$actividadId')";
        $result = mysqli_query($con, $query);

        if (!$result) {
            throw new Exception('Error al insertar en la tabla nota_actividad: ' . mysqli_error($con));
        }
        
        // Insertar en la tabla progreso con completado = 1
        $queryProgreso = "INSERT INTO progreso (id_usuario, id_actividad, completado) 
                          VALUES ('$id_usuario', '$actividadId', 1)";
        $resultProgreso = mysqli_query($con, $queryProgreso);

        if (!$resultProgreso) {
            throw new Exception('Error al insertar en la tabla progreso: ' . mysqli_error($con));
        }
    }

    // Verificar si el usuario tiene bonificación activa
    $queryBoniActiva = "SELECT 
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
                            ub.fecha_uso < DATE_ADD(CURDATE(), INTERVAL 2 DAY) AND 
                            TIME(ub.fecha_uso) <= '23:59:59' AND 
                            ub.estado = 'utilizada' AND 
                            b.nombre_bonificacion = 'Double points' AND
                            ub.id_usuario = '$id_usuario'
                            LIMIT 1";
    $resultBoniActiva = mysqli_query($con, $queryBoniActiva);
    $rowBoniActiva = mysqli_fetch_assoc($resultBoniActiva);

    if ($rowBoniActiva) {
        $fecha_uso = strtotime($rowBoniActiva['fecha_uso']);
        $fechaActual = time();
        $diferenciaTiempo = $fechaActual - $fecha_uso;

        if ($diferenciaTiempo < 0) {
            $puntosGanados = $puntosGanados * 2;
        }
    }

    // Actualizar los puntos ganados del usuario
    $queryActPuntosGanados = "UPDATE usuario SET puntos = puntos + $puntosGanados WHERE id_usuario = $id_usuario";
    $resultActPuntosGanados = mysqli_query($con, $queryActPuntosGanados);

    if (!$resultActPuntosGanados) {
        throw new Exception('Error al actualizar los puntos del usuario: ' . mysqli_error($con));
    }

    // Actualizar la tabla racha si es necesario
    $now = time();
    $hoy = date('Y-m-d', $now);

    $sql_fecha_actividad = "SELECT end_date, first_activity_date FROM racha WHERE id_usuario = $id_usuario";
    $resultado_fecha = mysqli_query($con, $sql_fecha_actividad);

    if (mysqli_num_rows($resultado_fecha) > 0) {
        $row = mysqli_fetch_assoc($resultado_fecha);
        $end_date = strtotime($row['end_date']);
        $first_activity_date = strtotime($row['first_activity_date']);

        if (date('Y-m-d', $end_date) === $hoy) {
            $sql_update = "UPDATE racha SET end_date = NOW() WHERE id_usuario = $id_usuario";
        } else {
            $sql_update = "UPDATE racha 
                        SET num_racha = CASE WHEN TIMESTAMPDIFF(SECOND, end_date, NOW()) > 93600 THEN 0 ELSE num_racha + 1 END, 
                            end_date = NOW(), 
                            first_activity_date = NOW(),
                            start_date = CASE WHEN TIMESTAMPDIFF(SECOND, end_date, NOW()) > 93600 THEN NOW() ELSE start_date END
                        WHERE id_usuario = $id_usuario";
        }

        if (!mysqli_query($con, $sql_update)) {
            throw new Exception('Error al actualizar la tabla racha: ' . mysqli_error($con));
        }
    } else {
        $sql_insert = "INSERT INTO racha (id_usuario, end_date, num_racha, start_date, first_activity_date) 
                       VALUES ($id_usuario, NOW(), 0, NOW(), FROM_UNIXTIME($now))";

        if (!mysqli_query($con, $sql_insert)) {
            throw new Exception('Error al insertar en la tabla racha: ' . mysqli_error($con));
        }
    }

    // Confirmar la transacción
    mysqli_commit($con);

    // Mensaje de respuesta
    $response['message'] = 'Grade saved successfully, ' . $nota . ' also you have earned ' . $puntosGanados . ' points';
    $response['nota'] = $nota;
    $_SESSION['puntos'] += $puntosGanados;

} catch (Exception $e) {
    // Si ocurre un error, revertir la transacción
    mysqli_rollback($con);
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
    error_log('Error: ' . $e->getMessage());
}

// Cerrar la conexión
mysqli_close($con);

// Enviar respuesta al cliente
echo json_encode($response);
?>
