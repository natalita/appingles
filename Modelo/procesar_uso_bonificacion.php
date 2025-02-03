<?php
ob_start();
session_start();
session_regenerate_id(true);
include_once '../Modelo/zona_horaria.php';
include_once '../Config/conexion.php';

date_default_timezone_set($user_timezone);

// Obtener la zona horaria actualmente configurada
$current_timezone = date_default_timezone_get();

// Imprimir la zona horaria
echo "<script>console.log('La zona horaria actual es: " . $current_timezone . "');</script>";

$proximoDia = date("Y-m-d", strtotime('tomorrow')) . ' ' . date("H:i");
$proximoDomingo = date("Y-m-d 23:59:59", strtotime('next sunday'));
$hoy = date('Y-m-d', time());

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se ha iniciado sesión y si se ha enviado el ID de bonificación
    if (isset($_SESSION['id_usuario']) && isset($_POST['id_bonificacion'])) {
        // Obtener el ID de usuario, puntos del usuario y el ID de bonificación
        $id_usuario = $_SESSION['id_usuario'];
        $id_bonificacion = $_POST['id_bonificacion'];
        $nombre_bonificacion = $_POST['nombre_bonificacion'];

        // Verificar si el nombre de la bonificación es un 'Frame'
        if (strpos($nombre_bonificacion, 'Frame') !== false) {
            $estado = 'utilizada';

            // Primero, marcamos como 'no activada' todas las bonificaciones de tipo "Frame" que no están activas (estado diferente de 'activada')
            $queryUpdateFrames = "
                UPDATE usuario_bonificacion ub
                JOIN bonificacion b ON ub.id_bonificacion = b.id_bonificacion
                SET ub.estado = 'no utilizada'
                WHERE ub.id_usuario = '$id_usuario'
                AND b.nombre_bonificacion LIKE '%Frame%'"; // Condición para no afectar a las bonificaciones ya activadas
            $resultDesactivarFrames = mysqli_query($con, $queryUpdateFrames);

            // Luego, actualizamos la bonificación que acaba de ser activada
            $queryUpdateActivatedFrame = "
                UPDATE usuario_bonificacion
                SET estado = '$estado', fecha_uso = '$hoy'
                WHERE id_usuario = '$id_usuario'
                AND id_bonificacion = '$id_bonificacion'";
            mysqli_query($con, $queryUpdateActivatedFrame);

            if (!$resultDesactivarFrames) {
                // Si la consulta falla, muestra un error
                echo "Error al desactivar otros frames: " . mysqli_error($con);
                exit;
            }
        }else{
            $estado = 'utilizada';
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
                                ub.estado = '$estado' AND 
                                b.nombre_bonificacion = '$nombre_bonificacion' AND
                                ub.id_usuario = '$id_usuario'
                                LIMIT 1";
        $resultBoniActiva = mysqli_query($con, $queryBoniActiva);
        $rowBoniActiva = mysqli_fetch_assoc($resultBoniActiva);

        
        if (!$rowBoniActiva){

            $queryBonificacion = "SELECT ub.id_usuario_bonificacion, ub.id_bonificacion, ub.id_usuario, ub.estado, ub.fecha_uso, b.nombre_bonificacion FROM usuario_bonificacion ub JOIN bonificacion b ON ub.id_bonificacion = b.id_bonificacion WHERE ub.estado = 'no $estado' AND b.id_bonificacion = '$id_bonificacion' AND ub.id_usuario = '$id_usuario' ORDER BY ub.id_usuario_bonificacion LIMIT 1";

            $resultBonificacion = mysqli_query($con, $queryBonificacion);
            $rowBonificacion = mysqli_fetch_assoc($resultBonificacion);
            if($rowBonificacion){
                $id_usuario_bonificacion = $rowBonificacion['id_usuario_bonificacion'];
                $id_bonificacion = $rowBonificacion['id_bonificacion'];
                $id_usuario = $rowBonificacion['id_usuario'];
                $estado = $rowBonificacion['estado'];
                $nombre_bonificacion = $rowBonificacion['nombre_bonificacion'];

                if($id_usuario_bonificacion != null){
                    switch($nombre_bonificacion){
                        case 'Double points':
                            

                            $mensaje = "The bonus has been used successfully, it will be active until tomorrow at ".$proximoDia."";
                            
                            $fecha_uso = $proximoDia;         
                            
                            break;
                        case 'Weekend streak':
                            

                            $mensaje = "The bonus has been used successfully, it will be active until the Next Sunday: ".$proximoDomingo."";
                            
                            $fecha_uso = $proximoDomingo;
                            
                            break;
                    }
                    $query_uso_bonificacion = "UPDATE usuario_bonificacion SET estado = 'utilizada', fecha_uso = '$fecha_uso' WHERE id_usuario_bonificacion = $id_usuario_bonificacion";
                    $result_uso_bonificacion = mysqli_query($con, $query_uso_bonificacion);
                    echo json_encode(['status' => 'success', 'message' => $mensaje, 'estado' => $estado]);
                    header("Location: ../Vista/panel.php?modulo=bonificacion&mensaje=".$mensaje);
                }
            }else{
                header("Location: ../Vista/panel.php?modulo=bonificacion&mensaje=Error while using the bonus, it appears that you have not purchased any.");
            }
        }else{
            if (strpos($nombre_bonificacion, 'Frame') !== false){
                header("Location: ../Vista/panel.php?modulo=bonificacion&mensaje=You already have an active bonus of this type. This will be active");
            }
            else{
                header("Location: ../Vista/panel.php?modulo=bonificacion&mensaje=You already have an active bonus of this type. This will be active until ".$rowBoniActiva['fecha_uso']);
            }
        }


        
        // Cerrar la conexión a la base de datos
        mysqli_close($con);
    } else {
        // Si no se ha iniciado sesión o no se ha enviado el ID de bonificación, redireccionar a la página de inicio de sesión
        header("Location: ../Vista/panel.php?modulo=bonificacion&mensaje=id_bonificacion=".$_SESSION['id_usuario']);

        exit;
    }
} else {
    // Si se intenta acceder directamente a este archivo sin enviar el formulario, redireccionar a la página principal
    header("Location: ../Vista/panel.php?modulo=bonificacion&mensaje=error2");
    exit;
}
?>
<?php
ob_end_flush(); // Envía el contenido del búfer al navegador
?>