<?php
// Conexión a la base de datos
include_once '../Modelo/zona_horaria.php';
include_once '../Config/conexion.php';

// Establecer la zona horaria
date_default_timezone_set($user_timezone);

// Obtener el ID de usuario desde la sesión
$id_usuario = $_SESSION['id_usuario'];

// Obtener la zona horaria actualmente configurada
$current_timezone = date_default_timezone_get();
echo '<script>console.log("Zona horaria actual: ' . $current_timezone . '")</script>';

// Consulta para obtener el total de actividades completadas por el usuario actual
$query_actividades = "SELECT COUNT(*) as total_actividades
                  FROM progreso p
                  INNER JOIN actividad a ON p.id_actividad = a.id_actividad
                    WHERE p.id_usuario = $id_usuario 
                    AND p.completado = 1 
                    AND a.tipo = 'Activity'";

$result_actividades = mysqli_query($con, $query_actividades);
$row_actividades = mysqli_fetch_assoc($result_actividades);
$total_actividades = $row_actividades['total_actividades'];
echo '<script>console.log("Total actividades completadas: ' . $total_actividades . '")</script>';


// Consulta para obtener el total de pruebas completadas por el usuario actual
$query_pruebas = "SELECT COUNT(*) as total_pruebas
                  FROM progreso p
                  INNER JOIN actividad a ON p.id_actividad = a.id_actividad
                    WHERE p.id_usuario = $id_usuario 
                    AND p.completado = 1 
                    AND a.tipo = 'Test'";

$result_pruebas = mysqli_query($con, $query_pruebas);
$row_pruebas = mysqli_fetch_assoc($result_pruebas);
$total_pruebas = $row_pruebas['total_pruebas'];
echo '<script>console.log("Total pruebas completadas: ' . $total_pruebas . '")</script>';



// Consulta para contar el número total de actividades disponibles en la base de datos
$query_total_actividades = "SELECT COUNT(*) AS total_actividades FROM actividad WHERE tipo = 'Activity'";
$result_total_actividades = mysqli_query($con, $query_total_actividades);
$row_total_actividades = mysqli_fetch_assoc($result_total_actividades);
$total_actividades_totales = $row_total_actividades['total_actividades'];
echo '<script>console.log("Total actividades disponibles: ' . $total_actividades_totales . '")</script>';


// Consulta para contar el número total de pruebas disponibles en la base de datos
$query_total_pruebas = "SELECT COUNT(*) AS total_pruebas FROM actividad WHERE tipo = 'Test'";
$result_total_pruebas = mysqli_query($con, $query_total_pruebas);
$row_total_pruebas = mysqli_fetch_assoc($result_total_pruebas);
$total_pruebas_totales = $row_total_pruebas['total_pruebas'];
echo '<script>console.log("Total pruebas disponibles: ' . $total_pruebas_totales . '")</script>';


// Calcular el porcentaje de actividades y pruebas completadas
$porcentaje_actividades = ($total_actividades_totales > 0) ? ($total_actividades / $total_actividades_totales) * 100 : 0;
$porcentaje_pruebas = ($total_pruebas_totales > 0) ? ($total_pruebas / $total_pruebas_totales) * 100 : 0;
echo '<script>console.log("Porcentaje actividades completadas: ' . $porcentaje_actividades . '%")</script>';
echo '<script>console.log("Porcentaje pruebas completadas: ' . $porcentaje_pruebas . '%")</script>';


// Calcular el progreso general
$total_completadas = $total_actividades + $total_pruebas;
$total_totales = $total_actividades_totales + $total_pruebas_totales;

$porcentaje_general = ($total_totales > 0) ? ($total_completadas / $total_totales) * 100 : 0;
echo '<script>console.log("Progreso general: ' . $porcentaje_general . '%")</script>';

// Cerrar la conexión
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
                            <h3 class="card-title">Welcome to Your English Learning Dashboard!</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <p>Get started with your English learning journey. Choose from a variety of activities and
                                tests to improve your language skills.</p>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="custom-info-box info-box activities esquinas-redondeados">
                                        <span class="info-box-icon"><img src="../Publico/iconos/i-activi.svg" alt="" style="max-height: 70px"></span>
                                        <div class="info-box-content">
                                            <h2 class="info-box-text">Activities</h2>
                                            <a href="panel.php?modulo=actividades" class="btn btn-activity text-ellipsis">
                                                <b>Do Activities</b>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-box tests esquinas-redondeados">
                                        <span class="info-box-icon"><img src="../Publico/iconos/i-tests.svg" alt="" style="max-height: 70px"></span>
                                        <div class="info-box-content">
                                            <h2 class="info-box-text">Tests</h2>
                                            <a href="panel.php?modulo=prueba" class="btn btn-test text-ellipsis">
                                                <b>Try Lessons</b>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-box training esquinas-redondeados">
                                        <span class="info-box-icon"><img src="../Publico/iconos/i-train.svg" alt="" style="max-height: 70px"></span>
                                        <div class="info-box-content">
                                            <h2 class="info-box-text">Training</h2>
                                            <a href="panel.php?modulo=repaso" class="btn btn-training text-ellipsis">
                                                <b>Train</b>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-box ranking esquinas-redondeados">
                                        <span class="info-box-icon"><img src="../Publico/iconos/i-rank.svg" alt="" style="max-height: 70px"></span>
                                        <div class="info-box-content">
                                            <h2 class="info-box-text">Ranking</h2>
                                            <a href="panel.php?modulo=ranking" class="btn btn-ranking text-ellipsis">
                                                <b>View Rank</b>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-box achievements esquinas-redondeados">
                                        <span class="info-box-icon"><img src="../Publico/iconos/i-achive.svg" alt="" style="max-height: 70px"></span>
                                        <div class="info-box-content">
                                            <h2 class="info-box-text">Achievements</h2>
                                            <a href="panel.php?modulo=logros" class="btn btn-achievements text-ellipsis">
                                                <b>Achievements</b>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-box bonification esquinas-redondeados">
                                        <span class="info-box-icon"><img src="../Publico/iconos/i-boni.svg" alt="" style="max-height: 70px"></span>
                                        <div class="info-box-content">
                                            <h2 class="info-box-text">Rewards</h2>
                                            <a href="panel.php?modulo=bonificacion" class="btn btn-bonification text-ellipsis">
                                                <b>Get Bonus</b>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-box streak esquinas-redondeados">
                                        <span class="info-box-icon"><img src="../Publico/iconos/i-streak.svg" alt="" style="max-height: 70px"></span>
                                        <div class="info-box-content">
                                            <h2 class="info-box-text">Streak</h2>
                                            <a href="panel.php?modulo=racha" class="btn btn-streak text-ellipsis">
                                                <b>Keep on fire!</b>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'teacher') { ?>
                                    <div class="col-md-4">
                                        <div class="info-box resources esquinas-redondeados">
                                            <span class="info-box-icon"><img src="../Publico/iconos/i-resources.svg" alt="" style="max-height: 70px"></span>
                                            <div class="info-box-content">
                                                <h2 class="info-box-text">Resources</h2>
                                                <a href="panel.php?modulo=recursos" class="btn btn-resources text-ellipsis">
                                                    <b>Manage Resources</b>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="info-box users esquinas-redondeados">
                                            <span class="info-box-icon"><img src="../Publico/iconos/i-users.svg" alt="" style="max-height: 70px"></span>
                                            <div class="info-box-content">
                                                <h2 class="info-box-text">Users</h2>
                                                <a href="panel.php?modulo=usuarios" class="btn btn-users text-ellipsis">
                                                    <b>Manage Users</b>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="progress mt-4">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $porcentaje_actividades; ?>%"
                                    aria-valuenow="<?php echo $porcentaje_actividades; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <span class="font-weight-bold">Activities: <?php echo $total_actividades; ?>/<?php echo $total_actividades_totales; ?></span>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <span class="font-weight-bold">Activities completed: <?php echo round($porcentaje_actividades, 2); ?>%</span>
                            </div>

                            <div class="progress mt-4">
                                <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $porcentaje_pruebas; ?>%"
                                    aria-valuenow="<?php echo $porcentaje_pruebas; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <span class="font-weight-bold">Tests: <?php echo $total_pruebas; ?>/<?php echo $total_pruebas_totales; ?></span>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <span class="font-weight-bold">Questions tests completed <?php echo round($porcentaje_pruebas, 2); ?>%</span>
                            </div>

                            <div class="progress mt-4">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porcentaje_general ?>%" aria-valuenow="<?= $porcentaje_general ?>" aria-valuemin="0" aria-valuemax="100">
                                    <span class="font-weight-bold">Total: <?php echo $total_completadas; ?>/<?php echo $total_totales; ?></span>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <span class="font-weight-bold">General Progress: <?php echo round($porcentaje_general, 2); ?>%</span>
                            </div>
                                
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
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