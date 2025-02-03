<?php
   include_once '../Modelo/zona_horaria.php';
   include_once '../Config/conexion.php';

   date_default_timezone_set($user_timezone);
   
   // Obtener la zona horaria actualmente configurada
    $current_timezone = date_default_timezone_get();

    // Imprimir la zona horaria
    echo "<script>console.log('La zona horaria actual es: " . $current_timezone . "');</script>";

    if (isset($_POST['save'])) {
        $tipo = 'Activity';

        $pregunta = trim($_POST['pregunta']); // Eliminar espacios en blanco al inicio y final
        $opciones = trim($_POST['opciones']); // Eliminar espacios en blanco al inicio y final
        $respuesta = trim($_POST['respuesta']); // Eliminar espacios en blanco al inicio y final
        $descripcion = trim($_POST['descripcion']); // Eliminar espacios en blanco al inicio y final

        // Procesar las opciones para eliminar espacios extra y normalizar el formato
        $opciones = preg_replace('/\s*,\s*/', ',', $opciones); // Eliminar espacios alrededor de las comas
        $opciones = preg_replace('/\s+/', ' ', $opciones); // Reemplazar múltiples espacios por uno solo dentro de cada palabra

        // Procesar la respuesta de la misma forma que las opciones
        $respuesta = preg_replace('/\s*,\s*/', ',', $respuesta); // Eliminar espacios alrededor de las comas
        $respuesta = preg_replace('/\s+/', ' ', $respuesta); // Reemplazar múltiples espacios por uno solo dentro de cada palabra

        switch ($descripcion) {
            case '0':
                $descripcion = 'Select';
                break;
            case '1':
                $descripcion = 'Order';
                break;
            case '2':
                $descripcion = 'Match';
                break;
            case '3':
                $descripcion = 'Complete';
                break;
            case '4':
                $descripcion = 'Multiple Choice';
                break;
            case '5':
                $descripcion = 'Number';
            default:
                $tipo = 'Test';
                break;
        }
        
        $id_recurso = $_POST['id_recurso'];

        // Verificar si el recurso existe en la tabla recurso
        $query = "SELECT id_recurso FROM recurso WHERE id_recurso = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $id_recurso);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            // El recurso existe, realizar la inserción en actividad
            $query = "INSERT INTO actividad (id_recurso, pregunta, opciones, respuesta, descripcion, tipo) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "isssss", $id_recurso, $pregunta, $opciones, $respuesta, $descripcion, $tipo);
            mysqli_stmt_execute($stmt);

            // Verificar si la inserción fue exitosa
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                // La inserción fue exitosa
                header("Location: ../Vista/panel.php?modulo=recursos&mensaje=Actividad guardada correctamente '" . $descripcion . "'");


                exit();
            } else {
                // Ocurrió un error durante la inserción
                header("Location: ../Vista/panel.php?modulo=recursos&mensaje=Error al guardar la actividad");
                exit();
            }
        } else {
            // El recurso no existe en la tabla recurso
            header("Location: ../Vista/panel.php?modulo=recursos&mensaje=El recurso asociado no existe");
            exit();
        }
    }

    // Cerrar la conexión a la base de datos
    mysqli_close($con);
?>