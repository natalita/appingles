<?php
include_once '../Modelo/zona_horaria.php';
include_once '../Config/conexion.php';

session_start();

$puntajeMaximo = 10;

$n_intentos = '';
$n_opciones = '';

$buenasMC = '';
$malasMC = '';
$totalMC = '';
$totalMTot = '';

$n_intentosO = '';
$n_opcionesO = '';

$n_intentosC = '';
$n_opcionesC = '';

$n_intentosMat = '';
$n_opcionesMat = '';

$n_intentosNum = '';
$n_opcionesNum = '';

$nota_actividad = 0;

// Establecer la zona horaria
date_default_timezone_set($user_timezone);

// Obtener la zona horaria actualmente configurada
$current_timezone = date_default_timezone_get();

// Obtener los valores de la solicitud POST
$tipoPregunta = $_POST['tipoPregunta'];
$id_usuario = $_POST['userId'];
$id_unidad = $_POST['id_unidad'];
$id_actividad = $_POST['id_actividad'];
$puntosGanados = $_POST['puntosGanados'];


switch($tipoPregunta){
    case 'select':
        $n_intentos = $_POST['n_intentos'];
        $n_opciones = $_POST['n_opciones'];

        // Si el número de intentos es igual a 1, el estudiante obtiene el puntaje máximo
        if ($n_intentos == 1) {
            $nota_actividad = $puntajeMaximo;
        } else {

            $porcentajePerdidaPorIntento = 0.15;

            $factorOpciones = 1 - (1 / ($n_opciones+1)); // Ajuste para que a mayor número de opciones, menor sea el descuento

            $puntajePerdidoPorIntento = $puntajeMaximo * ($porcentajePerdidaPorIntento * $factorOpciones);

            $puntajeFinal = $puntajeMaximo - (($n_intentos - 1) * $puntajePerdidoPorIntento);

            $puntajeFinal = max(min($puntajeFinal, $puntajeMaximo), 0);

            $nota_actividad = round($puntajeFinal, 2);
        }

        break;
    case 'multiple choice':
        $buenasMC = $_POST['buenasMC'];
        $malasMC = $_POST['malasMC'];
        $totalMC = $_POST['totalMC'];
        $totalMTot = $_POST['totalMTot'];

        $puntajeFinal = $puntajeMaximo * ($buenasMC / $totalMC);

        // Penalizamos las selecciones incorrectas, varía en función del total de opciones presentadas al usuario
        // Si hay muchas opciones, la penalización será menor; si hay pocas opciones, la penalización será mayor
        $penalizacionPorIncorrectas = ($malasMC / $totalMTot) * (10 / $totalMTot);
        $puntajeFinal -= $penalizacionPorIncorrectas;

        $puntajeFinal = max(min($puntajeFinal, $puntajeMaximo), 0);

        $nota_actividad = round($puntajeFinal, 2);
        break;
    case 'order':
        $n_intentosO = $_POST['n_intentosO'];
        $n_opcionesO = $_POST['n_opcionesO'];

        $porcentajePerdidaPorIntento = 0.15;

        // Calculamos el puntaje perdido por cada intento adicional
        $puntajePerdidoPorIntento = $puntajeMaximo * $porcentajePerdidaPorIntento * $n_intentosO;

        // Calculamos el puntaje final basado en la cantidad de palabras en la frase
        $puntajeFinal = $puntajeMaximo - ($puntajePerdidoPorIntento * ($n_intentosO / $n_opcionesO));

        $nota_actividad = round(max(min($puntajeFinal, $puntajeMaximo), 0), 2);
        break;
    case 'complete':
        $n_intentosC = $_POST['n_intentosC'];
        $n_opcionesC = $_POST['n_opcionesC'];
        
        $porcentajePerdidaPorIntento = 0.15;

        // Calculamos el puntaje perdido por cada intento adicional
        $puntajePerdidoPorIntento = $puntajeMaximo * $porcentajePerdidaPorIntento * $n_intentosC;

        // Calculamos el puntaje final basado en la cantidad de frases a completar
        $puntajeFinal = $puntajeMaximo - ($puntajePerdidoPorIntento * ($n_intentosC / $n_opcionesC));

        $nota_actividad = round(max(min($puntajeFinal, $puntajeMaximo), 0), 2);
        break;
    case 'match':
        $n_intentosMat = $_POST['n_intentosMat'];
        $n_opcionesMat = $_POST['n_opcionesMat'];


        $porcentajePerdidaPorIntento = 0.15;

        // Calculamos el puntaje perdido por cada intento adicional
        $puntajePerdidoPorIntento = $puntajeMaximo * $porcentajePerdidaPorIntento * $n_intentosMat;

        // Calculamos el puntaje final basado en la cantidad de opciones en los pares que debe emparejar
        $puntajeFinal = $puntajeMaximo - ($puntajePerdidoPorIntento * ($n_intentosMat / $n_opcionesMat));

        $nota_actividad = round(max(min($puntajeFinal, $puntajeMaximo), 0), 2);
        break;
    case 'number':
        $n_intentosNum = $_POST['n_intentosNum'];
        $n_opcionesNum = $_POST['n_opcionesNum'];

        // Porcentaje de puntaje perdido por cada intento adicional
        $porcentajePerdidaPorIntento = 0.15;

        // Calculamos el puntaje perdido por cada intento adicional
        $puntajePerdidoPorIntento = $puntajeMaximo * $porcentajePerdidaPorIntento * $n_intentosNum;

        // Calculamos el puntaje final basado en la cantidad de palabras en la frase
        $puntajeFinal = $puntajeMaximo - ($puntajePerdidoPorIntento * ($n_intentosNum / $n_opcionesNum));

        $nota_actividad = round(max(min($puntajeFinal, $puntajeMaximo), 0), 2);
        break;
    default:
        break;
}

$tipoPregunta = 'Activity';

// Verifica si la nota es perfecta y duplica los puntos ganados
if($nota_actividad == 10){
    $puntosGanados = $puntosGanados * 2;
}

// Iniciar una transacción
mysqli_begin_transaction($con);

// Insertar la nota en la tabla nota
$query = "INSERT INTO nota (id_usuario, id_unidad, nota, tipo) VALUES ('$id_usuario', '$id_unidad', '$nota_actividad', '$tipoPregunta')";
$result = mysqli_query($con, $query);

if (!$result) {
    // Si hay un error, revertir la transacción y devolver un mensaje de error
    mysqli_rollback($con);
    $error = mysqli_error($con);
    $response['status'] = 'error';
    $response['message'] = 'Error al insertar en la tabla nota';
    $response['error'] = $error;

    // Imprimir mensaje en la consola del navegador
    echo json_encode($response);  // Devolver respuesta JSON incluso en error
    error_log('Error al insertar en la tabla nota: ' . $error); // Agregar mensaje al log de errores

    exit;
}

// Obtener el ID de la última nota insertada
$id_nota = mysqli_insert_id($con);

// Insertar las relaciones en la tabla nota_actividad
$query = "INSERT INTO nota_actividad (id_nota, id_actividad) VALUES ('$id_nota', '$id_actividad')";
$result = mysqli_query($con, $query);

if (!$result) {
    // Si hay un error, revertir la transacción y devolver un mensaje de error
    mysqli_rollback($con);
    $error = mysqli_error($con);
    $response['status'] = 'error';
    $response['message'] = 'Error al insertar en la tabla nota_actividad';
    $response['error'] = $error;

    // Imprimir mensaje en la consola del navegador
    echo json_encode($response);  // Devolver respuesta JSON incluso en error
    error_log('Error al insertar en la tabla nota_actividad: ' . $error); // Agregar mensaje al log de errores

    exit;
}

// Consultar si el usuario ya tiene una bonificación activa de este tipo
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

if ($rowBoniActiva){
    $fecha_uso = strtotime($rowBoniActiva['fecha_uso']);
    $fechaActual = time();
    $diferenciaTiempo = $fechaActual - $fecha_uso;

    if($diferenciaTiempo < 0){
        $puntosGanados = $puntosGanados * 2;
    }
}

$queryActPuntosGanados = "UPDATE usuario SET puntos = puntos + $puntosGanados WHERE id_usuario = $id_usuario";
$resultActPuntosGanados = mysqli_query($con, $queryActPuntosGanados);

if (!$resultActPuntosGanados) {
    // Si hay un error, revertir la transacción y devolver un mensaje de error
    mysqli_rollback($con);
    $error = mysqli_error($con);
    $response['status'] = 'error';
    $response['message'] = 'Error al actualizar los puntos del usuario';
    $response['error'] = $error;

    // Imprimir mensaje en la consola del navegador
    echo json_encode($response);  // Devolver respuesta JSON incluso en error
    error_log('Error al actualizar los puntos del usuario: ' . $error); // Agregar mensaje al log de errores

    exit;
}

// Insertar el progreso de la actividad en la tabla progreso
$queryProgreso = "INSERT INTO progreso (id_usuario, id_actividad, completado) 
                  VALUES ('$id_usuario', '$id_actividad', 1) 
                  ON DUPLICATE KEY UPDATE completado = 1";
$resultProgreso = mysqli_query($con, $queryProgreso);

if (!$resultProgreso) {
    mysqli_rollback($con);
    $error = mysqli_error($con);
    $response['status'] = 'error';
    $response['message'] = 'Error al insertar el progreso de la actividad';
    $response['error'] = $error;

    echo json_encode($response);
    error_log('Error al insertar el progreso de la actividad: ' . $error);
    exit;
}

// Si no hay errores, confirmar la transacción
mysqli_commit($con);

// Cerrar la conexión a la base de datos
mysqli_close($con);

$hora_actual = date("H:i:s");

// Crear un array con los valores recibidos
$response = array(
    'tipoPregunta' => $tipoPregunta,
    'userId' => $id_usuario,
    'n_intentos' => $n_intentos,
    'n_opciones' => $n_opciones,
    'buenasMC' => $buenasMC,
    'malasMC' => $malasMC,
    'totalMC' => $totalMC,
    'totalMTot' => $totalMTot,
    'n_intentosO' => $n_intentosO,
    'n_opcionesO' => $n_opcionesO,
    'n_intentosC' => $n_intentosC,
    'n_opcionesC' => $n_opcionesC,
    'n_intentosMat' => $n_intentosMat,
    'n_opcionesMat' => $n_opcionesMat,
    'n_intentosNum' => $n_intentosNum,
    'n_opcionesNum' => $n_opcionesNum,
    'nota_actividad' => $nota_actividad,
    'id_unidad' => $id_unidad,
    'id_actividad' => $id_actividad,
    'puntosGanados' => $puntosGanados,

    //'fecha_uso' => $fecha_uso,
    //'diferenciaTiempo' => $diferenciaTiempo,
    'hora' => $hora_actual
);

$_SESSION['puntos'] += $puntosGanados;

// Devolver los valores como JSON
echo json_encode($response);
exit;

?>
