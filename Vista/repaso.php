<?php
// Conexión a la base de datos
include_once '../Config/conexion.php';

// Obtener el ID de usuario desde la sesión
$id_usuario = $_SESSION['id_usuario'];

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


// Consulta para contar el número total de actividades disponibles en la base de datos
$query_total_actividades = "SELECT COUNT(*) AS total_actividades FROM actividad WHERE tipo = 'Activity'";
$result_total_actividades = mysqli_query($con, $query_total_actividades);
$row_total_actividades = mysqli_fetch_assoc($result_total_actividades);
$total_actividades_totales = $row_total_actividades['total_actividades'];

// Consulta para contar el número total de pruebas disponibles en la base de datos
$query_total_pruebas = "SELECT COUNT(*) AS total_pruebas FROM actividad WHERE tipo = 'Test'";
$result_total_pruebas = mysqli_query($con, $query_total_pruebas);
$row_total_pruebas = mysqli_fetch_assoc($result_total_pruebas);
$total_pruebas_totales = $row_total_pruebas['total_pruebas'];

// Calcular el porcentaje de actividades y pruebas completadas
$porcentaje_actividades = ($total_actividades_totales > 0) ? ($total_actividades / $total_actividades_totales) * 100 : 0;
$porcentaje_pruebas = ($total_pruebas_totales > 0) ? ($total_pruebas / $total_pruebas_totales) * 100 : 0;

// Calcular el progreso general
$total_completadas = $total_actividades + $total_pruebas;
$total_totales = $total_actividades_totales + $total_pruebas_totales;

$porcentaje_general = ($total_totales > 0) ? ($total_completadas / $total_totales) * 100 : 0;

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
                            <h3 class="card-title">Welcome to your train session!</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <p>Get started with your English learning journey. Choose from a variety of activities and
                                tests to improve your language skills.</p>
                            <div class="row ">
                                <div class="col-md-4">
                                    <div class="info-box crossword esquinas-redondeados">
                                        <span class="info-box-icon"><img src="../Publico/iconos/i-crossword.svg" alt="" style="max-height: 70px"></span>
                                        <div class="info-box-content">
                                            <h2 class="info-box-text">Crossword</h2>
                                            <a href="panel.php?modulo=crucigrama"
                                                class="btn btn-crossword text-ellipsis"><b>Play</b></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-box hangman esquinas-redondeados">
                                        <span class="info-box-icon"><img src="../Publico/iconos/i-hangman.svg" alt="" style="max-height: 70px"></span>
                                        <div class="info-box-content">
                                            <h2 class="info-box-text">Hangman</h2>
                                            <a href="panel.php?modulo=ahorcado"
                                                class="btn btn-outline-light text-ellipsis"><b>Play</b></a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="info-box word_puzzle esquinas-redondeados">
                                        <span class="info-box-icon"><img src="../Publico/iconos/i-word_puzzle.svg" alt="" style="max-height: 70px"></span>
                                        <div class="info-box-content">
                                            <h4 class="info-box-text">Word search puzzle</h4>
                                            <a href="panel.php?modulo=sopaLetras"
                                                class="btn btn-outline-light text-ellipsis text-dark"><b>Play</b></a>
                                        </div>
                                    </div>
                                </div>
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
                                <span class="font-weight-bold">Tests completed: <?php echo round($porcentaje_pruebas, 2); ?>%</span>
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


<!-- Agrega el siguiente código al encabezado de tu página -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function () {
        // Agrega el evento click a cada botón para mostrar/ocultar el iframe correspondiente
        $(".toggleIframeBtn").click(function () {
            var iframeId = $(this).data("iframe");

            // Recargar el contenido del otro iframe
            var otherIframeId = (iframeId === "iframe1") ? "iframe2" : "iframe1";
            $("#" + otherIframeId + " iframe").attr("src", $("#" + otherIframeId + " iframe").attr("src"));

            $("#" + iframeId).slideToggle();
            $(".iframe-container").not("#" + iframeId).slideUp();
        });
    });
</script>