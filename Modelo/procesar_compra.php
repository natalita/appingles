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

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se ha iniciado sesión y si se ha enviado el ID de bonificación
    if (isset($_SESSION['id_usuario']) && isset($_POST['id_bonificacion'])) {
        // Obtener el ID de usuario, puntos del usuario y el ID de bonificación
        $id_usuario = $_SESSION['id_usuario'];
        $puntos_usuario = $_SESSION['puntos'];
        $id_bonificacion = $_POST['id_bonificacion'];
        $costo_bonificacion = $_POST['costo_bonificacion'];
        $nombre_bonificacion = $_POST['nombre_bonificacion'];

        if(strpos($nombre_bonificacion, 'Frame') !== false){
            $estado = 'no activada';
        }else{
            $estado = 'no utilizada';
        }
        
        $estado = ($estado === 'utilizada' || $estado === 'no utilizada') ? $estado : 'no utilizada';
        
        // Verificar si el usuario tiene suficientes puntos para comprar la bonificación
        if ($puntos_usuario >= $costo_bonificacion) {
            // Verificar si el usuario ya tiene el máximo de bonificaciones para este tipo
            $query_cantidad_bonificaciones = "SELECT COUNT(*) AS cantidad FROM usuario_bonificacion WHERE id_usuario = $id_usuario AND id_bonificacion = $id_bonificacion AND estado = '$estado'";
            $result_cantidad_bonificaciones = mysqli_query($con, $query_cantidad_bonificaciones);
            $row_cantidad_bonificaciones = mysqli_fetch_assoc($result_cantidad_bonificaciones);
            $cantidad_bonificaciones_usuario = $row_cantidad_bonificaciones['cantidad'];

            // Obtener el máximo permitido para esta bonificación
            $query_maximo_bonificacion = "SELECT maximo FROM bonificacion WHERE id_bonificacion = $id_bonificacion";
            $result_maximo_bonificacion = mysqli_query($con, $query_maximo_bonificacion);
            $row_maximo_bonificacion = mysqli_fetch_assoc($result_maximo_bonificacion);
            $maximo_bonificacion = $row_maximo_bonificacion['maximo'];

            // Verificar si el usuario puede adquirir más bonificaciones de este tipo
            if ($cantidad_bonificaciones_usuario < $maximo_bonificacion) {
                // Insertar la nueva bonificación adquirida en la tabla usuario_bonificacion
                $fecha_adquisicion = date("Y-m-d H:i:s");
                $fecha_uso = date("Y-m-d H:i:s");
                $query_insertar_bonificacion = "INSERT INTO usuario_bonificacion (id_usuario, id_bonificacion, fecha_adquisicion, fecha_uso, estado) VALUES ($id_usuario, $id_bonificacion, '$fecha_adquisicion', '$fecha_uso', '$estado')";
                $result_insertar_bonificacion = mysqli_query($con, $query_insertar_bonificacion);

                if ($result_insertar_bonificacion) {
                    // Actualizar la cantidad de puntos del usuario restando el costo de la bonificación
                    $nuevos_puntos = $puntos_usuario - $costo_bonificacion;

                    // Actualizar los puntos del usuario en la base de datos
                    $query_actualizar_puntos = "UPDATE usuario SET puntos = $nuevos_puntos WHERE id_usuario = $id_usuario";
                    $result_actualizar_puntos = mysqli_query($con, $query_actualizar_puntos);

                    if ($result_actualizar_puntos) {
                        $_SESSION['puntos'] = $nuevos_puntos;
                        echo "<script>alert('Bonificación adquirida con éxito.');</script>";
                        // Redirigir a la página de bonificaciones
                        mysqli_close($con);
                        header("Location: ../Vista/panel.php?modulo=bonificacion");
                        exit;
                    } else {
                        // Manejar el error
                        echo "Error al actualizar los puntos del usuario.";
                    }
                } else {
                    // Manejar el error
                    echo "Error al procesar la compra. Por favor, inténtalo de nuevo más tarde.";
                }
            } else {
                // El usuario ya tiene el máximo de bonificaciones permitidas para este tipo
                echo "No puedes adquirir más bonificaciones de este tipo en este momento.";
            }
        } else {
            // El usuario no tiene suficientes puntos para comprar la bonificación
            echo "No tienes suficientes puntos para comprar esta bonificación.";
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