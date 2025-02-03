<?php
include_once '../Modelo/zona_horaria.php';
include_once '../Config/conexion.php';

date_default_timezone_set($user_timezone);

// Obtener la zona horaria actualmente configurada
$current_timezone = date_default_timezone_get();

// Imprimir la zona horaria
echo "<script>console.log('La zona horaria actual es: " . $current_timezone . "');</script>";


// Inicializar variables con valores predeterminados
$racha_actual = "N/A";
$fecha_inicial = "N/A";
$ultima_actividad = "N/A";

// Obtener el ID del usuario desde la sesión
$userId = $_SESSION['id_usuario'];

// Obtener la fecha de hoy
$hoy = date('Y-m-d');

// Consulta SQL para obtener la última entrada de la tabla racha para el usuario actual
$sql = "SELECT * FROM racha WHERE id_usuario = $userId";
$resultado = mysqli_query($con, $sql);

// Verificar si se encontró alguna entrada en la tabla racha para el usuario actual
if (mysqli_num_rows($resultado) > 0) {
    $row = mysqli_fetch_assoc($resultado);
    $fecha_inicial = date('Y-m-d', strtotime($row['start_date']));
    $ultima_actividad = $row['end_date'];
    $racha_actual = $row['num_racha'];
}

// Cerrar la conexión a la base de datos
mysqli_close($con);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <div class="col-lg-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Welcome to Streaks!</h3>
                        </div>
                    </div>
                    <!-- /.card -->

                    <!-- Racha Section -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Streak Information</h3>
                        </div>
                        <div class="card-body">
                            <h2>Current Streak: <?= $racha_actual; ?> days</h2>

                            <div>
                                <p>Start Date of Streak:</p>
                                <div id="start">
                                    <?php echo $fecha_inicial; ?>
                                </div>
                            </div>
                            <br>
                            <div hidden>
                                <div id="end">
                                    <?php echo $ultima_actividad; ?>
                                </div>
                            </div>
                            <br>
                            
                            <div id="calendars" class="calendar"></div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
