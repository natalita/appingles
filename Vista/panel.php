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

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');


if (isset($_GET['sesion']) && $_GET['sesion'] == 'cerrar') {
  session_destroy();
  header("location: ../index.php");
  exit;
}

if (!isset($_SESSION['id_usuario'])) {
  header("location: ../index.php");
  exit;
}
// Obtener el ID de usuario desde la sesión
$id_usuario = $_SESSION['id_usuario'];
$mensB = false;

if (!isset($_SESSION['code_executed'])) {

    $query_bonif_streak = "SELECT ub.id_usuario_bonificacion, ub.estado, b.nombre_bonificacion FROM usuario_bonificacion ub left join bonificacion b on ub.id_bonificacion = b.id_bonificacion WHERE ub.id_usuario = '$id_usuario' and ub.estado = 'no utilizada' and b.nombre_bonificacion = 'No lose streak'";
    $result_bonif_streak = mysqli_query($con, $query_bonif_streak);
    $row_count = mysqli_num_rows($result_bonif_streak);
    echo '<script>console.log("Cantidad de filas: ' . $row_count . '")</script>';

    $query_fecha_racha = "SELECT end_date, first_activity_date FROM racha WHERE id_usuario = $id_usuario";
    $result_fecha_racha = mysqli_query($con, $query_fecha_racha);
    $row_fecha_racha = mysqli_fetch_assoc($result_fecha_racha);
    if(mysqli_num_rows($result_fecha_racha) > 0){
      $end_date = $row_fecha_racha['end_date'];
      $first_activity_date = $row_fecha_racha['first_activity_date'];
      
      $mensB = True;


      $hoy = new DateTime(); // Crear un objeto DateTime para la fecha actual
      //$hoy = new DateTime('2024-03-09 20:00:00'); // Crear un objeto DateTime para pruebas
      $end_date = new DateTime($end_date); // Crear un objeto DateTime para $end_date

      // Restar las fechas
      $diferencia = $hoy->diff($end_date);

      // Obtener la diferencia en días, horas, minutos y segundos
      $dias = $diferencia->days;

      //
      $horas = $diferencia->h;
      $minutos = $diferencia->i;
      $segundos = $diferencia->s;
      $total_segundos = ($dias * 24 * 60 * 60) + ($horas * 60 * 60) + ($minutos * 60) + $segundos;

      $mens = '';

      echo '<script>console.log("end_date: ' . $end_date->format('Y-m-d H:i:s') . ' hoy: ' . $hoy->format('Y-m-d H:i:s') . '")</script>';

      echo '<script>console.log("Diferencia: ' . $dias . ' días, ' . $horas . ' horas, ' . $minutos . ' minutos, ' . $segundos . ' segundos")</script>';

      echo '<script>console.log("Total de segundos: ' . $total_segundos . '")</script>';

      // Verificar si la cantidad de filas es mayor o igual a un valor determinado
      if ($total_segundos > 93600) {
        if($row_count >= $dias){
          // Cambiar el estado de las filas a 'utilizada' en la base de datos
          $query_update_estado = "UPDATE usuario_bonificacion AS ub
                                  LEFT JOIN bonificacion AS b ON ub.id_bonificacion = b.id_bonificacion
                                  SET ub.estado = 'utilizada', ub.fecha_uso = NOW()
                                  WHERE ub.id_usuario = $id_usuario AND ub.estado = 'no utilizada' AND b.nombre_bonificacion = 'No lose streak' LIMIT $dias;
                                  ";
          for ($i = 0; $i < $dias; $i++) {
              echo '<script>console.log("Query: '.$i.'")</script>';
              mysqli_query($con, $query_update_estado);
          }
          $query_act_fechas = "UPDATE racha SET end_date = NOW(), first_activity_date = DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00'), num_racha = num_racha + $dias WHERE id_usuario = $id_usuario";
          $result_act_fechas = mysqli_query($con, $query_act_fechas);
          if(mysqli_affected_rows($con) > 0){
            echo '<script>console.log("Your streak was protected")</script>'; 
            $mens .= 'Your streak was protected, ' . $dias . ' bonifications were used';
          }
        }else{
          // La racha se actualiza a 0
          echo '<script>console.log("Racha actualizada a 0")</script>'; 
          $sql_update = "UPDATE racha SET num_racha = 0, end_date = NOW(), start_date = NOW(), first_activity_date = NOW() WHERE id_usuario = $id_usuario";
          mysqli_query($con, $sql_update);
          $mens .= 'Yout streak was reset, You did not perform any activity in '. $dias .' days';
        }
      }
    }
 
    ?>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          <?php
            //Mostrar mensaje de actualización de racha
            if(!empty($mens) && !empty($mensB) && !isset($_SESSION['code_executed'])): ?>
            var mensaje = '<?php echo $mens; ?>';
                var mensajeDiv = document.createElement('div');
                mensajeDiv.className = 'alert alert-primary alert-dismissible fade show';
                mensajeDiv.style.position = 'absolute';
                mensajeDiv.style.top = '8%';
                mensajeDiv.style.right = '20px';
                mensajeDiv.style.zIndex = '9999';
                mensajeDiv.role = 'alert';
                mensajeDiv.innerHTML = `
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    ${mensaje}
                `;
                document.body.appendChild(mensajeDiv);
            <?php endif; ?>
        });
      </script>
    <?php
    
    $_SESSION['code_executed'] = true;
    echo '<script>console.log("Código ejecutado: ' . htmlspecialchars($_SESSION['code_executed']) . '")</script>';

}else{
    echo '<script>console.log("Código ya ejecutado: ' . htmlspecialchars($_SESSION['code_executed']) . '")</script>';
}

$modulo = isset($_GET['modulo']) ? $_GET['modulo'] : '';

function obtenerPreguntas($tipo) {
    global $con;
    $query = "SELECT a.*, r.location, u.id_unidad 
              FROM actividad a
              JOIN recurso r ON a.id_recurso = r.id_recurso
              JOIN unidad u ON r.id_unidad = u.id_unidad
              WHERE a.tipo = '$tipo'";
    $result = mysqli_query($con, $query);

    $preguntas = array();
    $id = 1;

   while ($row = mysqli_fetch_assoc($result)) {
        $opciones = explode(',', $row['opciones']);
        $preguntas[] = array(
            'id' => $id,
            'tipo' => $row['tipo'],
            'id_recurso' => intval($row['id_recurso']),
            'id_actividad' => intval($row['id_actividad']),
            'id_unidad' => intval($row['id_unidad']), // Agregar el campo id_unidad
            'descripcion' => $row['descripcion'],
            'pregunta' => $row['pregunta'],
            'respuesta' => $row['respuesta'],
            'opciones' => $opciones,
            'ruta_video' => $row['location']
        );
        $id++;
    }

    return $preguntas;
}

$preguntasPrueba = obtenerPreguntas('Test');
$preguntasActividad = obtenerPreguntas('Activity');

$preguntasJsonPrueba = json_encode($preguntasPrueba, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
$preguntasJsonActividad = json_encode($preguntasActividad, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

$filePrueba = fopen('../Publico/js/PreguntasPrueba.js', 'w');
fwrite($filePrueba, 'let preguntas = ' . $preguntasJsonPrueba . ';');
fclose($filePrueba);

$fileActividad = fopen('../Publico/js/PreguntasActividad.js', 'w');
fwrite($fileActividad, 'let preguntasActividad = ' . $preguntasJsonActividad . ';');
fclose($fileActividad);


function getFrameImage($id_usuario, $con) {
    // Consulta para obtener la bonificación activada del usuario
    $query = "SELECT b.imagen 
              FROM usuario_bonificacion ub
              JOIN bonificacion b ON ub.id_bonificacion = b.id_bonificacion
              WHERE ub.id_usuario = '$id_usuario' 
              AND ub.estado = 'utilizada' 
              AND b.nombre_bonificacion LIKE 'Custom Frame%' 
              LIMIT 1";

    // Ejecutar la consulta
    $result = mysqli_query($con, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['imagen']; // Devuelve la ruta de la imagen SVG del marco
    } else {
        return null; // Si no tiene marco activado
    }
}

?>
<!DOCTYPE html>
<html lang="en"></head>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>App Ingles</title>


  <!-- Font Awesome -->
  <link rel="stylesheet" href="../Publico/plugins/fontawesome-free/css/all.min.css" />
  <!-- FontAweome CDN Link for Icons-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../Publico/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css" />
  <!-- iCheck -->
  <link rel="stylesheet" href="../Publico/plugins/icheck-bootstrap/icheck-bootstrap.min.css" />
  <!-- JQVMap -->
  <link rel="stylesheet" href="../Publico/plugins/jqvmap/jqvmap.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../Publico/css/adminlte.min.css" />
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../Publico/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../Publico/plugins/daterangepicker/daterangepicker.css" />
  <!-- summernote -->
  <link rel="stylesheet" href="../Publico/plugins/summernote/summernote-bs4.min.css" />
  <!-- DataTables -->
  <!-- <link rel="stylesheet" href="../Publico/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../Publico/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../Publico/plugins/datatables-buttons/css/buttons.bootstrap4.min.css"> -->


  <!-- My css -->
  <link rel="stylesheet" href="../Publico/css/style.css">
  <link rel="stylesheet" href="../Publico/css/my-css.css" />
  <link rel="stylesheet" href="../Publico/css/prueba.css">
  <link rel="stylesheet" href="../Publico/css/racha.css">
  <link rel="stylesheet" href="../Publico/css/ahorcado.css">
  <link rel="stylesheet" href="../Publico/css/crucigrama.css">
  <link rel="stylesheet" href="../Publico/css/sopaLetras.css">
  <link rel="stylesheet" href="../Publico/css/logros.css">


  <!-- Revisar este link Bootstrap 4 -->
  <link rel="stylesheet" href="../Publico/ext/bootstrap/css/bootstrap.min.css">

  <!-- Ventana modal para eliminar usuarios -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">
  <div class="wrapper">
    <!-- Preloader -->
    <!-- <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="../Publico/img/logo.svg" alt="My App Logo" height="60" width="60" />
    </div> -->

    <!-- Navbar -->
    <nav id="navBarHidden" class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Boton de racha -->
        <li class="nav-item">
          <a class="nav-link" href="panel.php?modulo=racha">
            <i class="fas fa-fire text-dark"></i>
            <span class="badge badge-danger navbar-badge" style="position: relative; top: -10px; right: 7px;">
              <?php
              // Consulta para obtener el número de racha del usuario
              $userId = $_SESSION['id_usuario'];
              $rachaQuery = "SELECT num_racha AS racha
                            FROM racha
                            WHERE id_usuario = $userId";
              $rachaResult = mysqli_query($con, $rachaQuery);

              if (mysqli_num_rows($rachaResult) > 0) {
                $rachaData = mysqli_fetch_assoc($rachaResult);
                echo $rachaData['racha'];
              } else {
                echo "0";
              }
              ?>
            </span>
          </a>
        </li>
        <!-- Boton de puntos -->
        <li class="nav-item">
          <a href="panel.php?modulo=bonificacion" class="nav-link text-dark">
            <i class="fas fa-coins"></i>
            <span id="puntos-counter" class="badge badge-danger navbar-badge" style="position: relative; top: -10px; right: 7px;">
              <?php
                echo $_SESSION['puntos'];
              ?>
            </span>
          </a>
        </li>
        <!-- Boton de usuario -->
        <a href="panel.php?modulo=perfil" class="nav-link text-dark">
          <i class=" far fa-user"></i>
        </a>
        <!-- Boton de salida -->
        <a class="nav-link text-danger" href="panel.php?modulo=&sesion=cerrar" title="Sign out">
          <i class="fa fa-door-closed"></i>
        </a>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a id="sideBarHidden2" href="panel.php?modulo=inicio" class="brand-link">
        <img src='../Publico/img/logo.svg' alt='My App Logo' class="brand-image" style='opacity: 0.8; border-radius: 30%; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5)' />

        <span class="brand-text font-weight-light">Flamma</span>
      </a>

      <!-- Sidebar -->
      <div id="sideBarHidden" class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 mb-2 pb-3 d-flex align-items-center">
            <div class="image">
                <div class="frame-container">
                    <?php
                    // Obtener la imagen del marco activo
                    $frameImage = getFrameImage($id_usuario, $con); // Aquí debes obtener la URL del marco activo

                    // Si hay un marco activado, mostrarlo sobre la foto de perfil
                    if ($frameImage) {
                        echo "<div class='frame' style='background-image: url(\"$frameImage\");'></div>";
                    }

                    // Mostrar la imagen de perfil del usuario
                    echo "<img src='" . $_SESSION['foto_perfil'] . "' alt='User Image' class='profile-image'>";
                    ?>
                </div>
            </div>
            <div class="info">
                <a href="panel.php?modulo=perfil" class="d-block">
                    <?php echo $_SESSION['username']; ?>
                </a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item menu-open">
              <a href="#" class="nav-link active">
                <i class="fas fa-book-open nav-icon" aria-hidden="true"></i>
                <p>
                  Menu
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="panel.php?modulo=actividades"
                    class="nav-link <?php echo ($modulo == "actividades" || $modulo == "inicio" || $modulo == "") ? " active " : " "; ?>">
                    <i class="fas fa-pencil-alt nav-icon" aria-hidden="true"></i>
                    <p>Activities</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="panel.php?modulo=prueba"
                    class="nav-link <?php echo ($modulo == "prueba" || $modulo == "inicio" || $modulo == "") ? " active " : " "; ?>">
                    <i class="fas fa-file-signature nav-icon" aria-hidden="true"></i>
                    <p>Tests</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="panel.php?modulo=repaso"
                    class="nav-link <?php echo ($modulo == "repaso" || $modulo == "inicio" || $modulo == "") ? " active " : " "; ?>">
                    <i class="fas fa-dumbbell nav-icon" aria-hidden="true"></i>
                    <p>Training</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="panel.php?modulo=ranking"
                    class="nav-link <?php echo ($modulo == "ranking" || $modulo == "inicio" || $modulo == "") ? " active " : " "; ?>">
                    <i class="fas fa-crown nav-icon" aria-hidden="true"></i>
                    <p>Ranking</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="panel.php?modulo=logros"
                    class="nav-link <?php echo ($modulo == "logros" || $modulo == "inicio" || $modulo == "") ? " active " : " "; ?>">
                    <i class="fas fa-trophy nav-icon" aria-hidden="true"></i>
                    <p>Achivements</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="panel.php?modulo=bonificacion"
                    class="nav-link <?php echo ($modulo == "bonificacion" || $modulo == "inicio" || $modulo == "") ? " active " : " "; ?>">
                    <i class="fas fa-star nav-icon" aria-hidden="true"></i>
                    <p>Rewards</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="panel.php?modulo=racha"
                    class="nav-link <?php echo ($modulo == "racha" || $modulo == "inicio" || $modulo == "") ? " active " : " "; ?>">
                    <i class="fas fa-fire nav-icon" aria-hidden="true"></i>
                    <p>Streak</p>
                  </a>
                </li>
                <?php
                if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'teacher') {
                  ?>
                  <li class="nav-item">
                    <a href="panel.php?modulo=recursos"
                      class="nav-link <?php echo ($modulo == "recursos" || $modulo == "inicio" || $modulo == "") ? " active " : " "; ?>">
                      <i class="fas fa-film nav-icon" aria-hidden="true"></i>
                      <p>Resources</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="panel.php?modulo=usuarios"
                      class="nav-link <?php echo ($modulo == "usuarios" || $modulo == "inicio" || $modulo == "") ? " active " : " "; ?>">
                      <i class="fas fa-users nav-icon" aria-hidden="true"></i>
                      <p>Users</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="panel.php?modulo=acerca_de"
                      class="nav-link <?php echo ($modulo == "acerca_de" || $modulo == "inicio" || $modulo == "") ? " active " : " "; ?>">
                      <i class="fas fa-info-circle nav-icon" aria-hidden="true"></i>
                      <p>Acerca de</p>
                    </a>
                  </li>
                  <?php
                }
                ?>
              </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>
    <?php
    if (isset($_GET['mensaje'])) {
      ?>
      <div id="mensajeAlert" class="alert alert-primary alert-dismissible fade show"
        style="position: absolute; top: 8%; right: 20px; z-index: 9999;" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only">Close</span>
        </button>
        <?php echo $_REQUEST['mensaje']; ?>
      </div>
      <?php
    }
    if ($modulo == 'inicio' || $modulo == '') {
      include_once 'inicio.php';
    }
    if ($modulo == 'usuarios') {
      include_once 'usuarios.php';
    }
    if ($modulo == 'actividades') {
      include_once 'actividades.php';
    }
    if ($modulo == 'repaso') {
      include_once 'repaso.php';
    }
    if ($modulo == 'ranking') {
      include_once 'ranking.php';
    }
    if ($modulo == 'logros') {
      include_once 'logros.php';
    }
    if ($modulo == 'bonificacion') {
      include_once 'bonificacion.php';
    }
    if ($modulo == 'racha') {
      include_once 'racha.php';
    }
    if ($modulo == 'perfil') {
      include_once 'perfil.php';
    }
    if ($modulo == 'editarUsuario') {
      include_once 'editarUsuario.php';
    }
    if ($modulo == 'ahorcado') {
      include_once 'ahorcado.php';
    }
    if ($modulo == 'crucigrama') {
      include_once 'crucigrama.php';
    }
    if ($modulo == 'sopaLetras') {
      include_once 'sopaLetras.php';
    }
    if ($modulo == 'recursos') {
      include_once 'recursos.php';
    }
    if ($modulo == 'ver_recurso') {
      include_once 'ver_recurso.php';
    }
    if ($modulo == 'eliminar_recurso') {
      include_once 'eliminar_recurso.php';
    }
    if ($modulo == 'prueba') {
      include_once 'prueba.php';
    }
    if ($modulo == 'acerca_de') {
      include_once 'acerca_de.php';
    }
    ?>

    <!-- /.content-wrapper -->

    <footer class="main-footer">
      <strong>Copyright &copy; 2022-<?php echo date('Y'); ?>
        <a href="panel.php?modulo=inicio">English App</a>.</strong>
      All rights reserved.
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0
      </div>
    </footer>

  </div>
  <script src="../Publico/"></script>
  <script src="../Publico/js/jquery-3.7.0.min.js"></script>
  <script src="../Publico/js/bootstrap.min.js"></script>
  <!-- jQuery -->
  <script src="../Publico/plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="../Publico/plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge("uibutton", $.ui.button);
  </script>
  <!-- Bootstrap 4 -->
  <script src="../Publico/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="../Publico/plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="../Publico/plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="../Publico/plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="../Publico/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="../Publico/plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="../Publico/plugins/moment/moment.min.js"></script>
  <script src="../Publico/plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="../Publico/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="../Publico/plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../Publico/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../Publico/js/adminlte.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../Publico/js/demo.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="../Publico/js/pages/dashboard.js"></script>
  <!-- DataTables  & Plugins -->
  <script src="../Publico/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../Publico/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="../Publico/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="../Publico/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="../Publico/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
  <script src="../Publico/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
  <script src="../Publico/plugins/jszip/jszip.min.js"></script>
  <script src="../Publico/plugins/pdfmake/pdfmake.min.js"></script>
  <script src="../Publico/plugins/pdfmake/vfs_fonts.js"></script>
  <script src="../Publico/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
  <script src="../Publico/plugins/datatables-buttons/js/buttons.print.min.js"></script>
  <script src="../Publico/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
  <!-- Page specific script -->
  <script>
    $(function () {
      $("#example2").DataTable({
        paging: true,
        lengthChange: false,
        searching: false,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
      });
    });
  </script>

  <!-- Script para confirmar la eliminacion de un registro -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>

  <!-- Script para confirmar la eliminacion de un registro -->
  <script>
    $(document).ready(function () {
      $(".borrarU").click(function (e) {
        e.preventDefault();
        var link = $(this).attr("href");
        $("#confirmModal").modal("show");
        $("#confirmBtn").click(function () {
          window.location = link;
        });
      });
    });
  </script>

  <!-- My Scripts -->
  <script src="../Publico/js/my-scripts.js"></script>

  <!-- Inside this JavaScript file I've inserted Questions and Options only -->
  <script src="../Publico/js/PreguntasPrueba.js"></script>
  <script src="../Publico/js/PreguntasActividad.js"></script>
  <script src="../Publico/js/prueba.js"></script>
  <script src="../Publico/js/racha.js"></script>
  <script src="../Publico/js/ahorcado.js"></script>
  <script src="../Publico/js/crucigrama.js"></script>
  <script src="../Publico/js/sopaLetras.js"></script>
</body>

</html>
<?php
ob_end_flush(); // Envía el contenido del búfer al navegador
?>