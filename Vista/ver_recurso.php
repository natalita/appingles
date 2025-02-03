<?php
include_once '../Config/conexion.php';

// Obtener ID de recurso desde la URL si está presente
$selected_video_id = isset($_GET['id_recurso']) ? $_GET['id_recurso'] : null;

// Verificar si se ha enviado el formulario de selección de video
if (isset($_POST['video_select'])) {
    $selected_video_id = $_POST['video_select'];
}

// Si hay un ID de recurso, realizar la consulta para obtener detalles del recurso seleccionado
if ($selected_video_id) {
    $selected_query = mysqli_query($con, "SELECT * FROM `recurso` WHERE `id_recurso` = '$selected_video_id'") or die(mysqli_error($con));
    $selected_video = mysqli_fetch_array($selected_query);

    // Obtener totales de actividades y pruebas asociadas
    $total_actividades_query = mysqli_query($con, "SELECT COUNT(*) as total_actividades FROM `actividad` WHERE `id_recurso` = '$selected_video_id' AND `tipo` = 'Activity'") or die(mysqli_error($con));
    $total_actividades = mysqli_fetch_assoc($total_actividades_query);

    $total_pruebas_query = mysqli_query($con, "SELECT COUNT(*) as total_pruebas FROM `actividad` WHERE `id_recurso` = '$selected_video_id' AND `tipo` = 'Test'") or die(mysqli_error($con));
    $total_pruebas = mysqli_fetch_assoc($total_pruebas_query);
}

// Consulta para obtener la lista de videos
$query = mysqli_query($con, "SELECT * FROM `recurso` ORDER BY `id_recurso` ASC") or die(mysqli_error($con));
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card alert alert-primary alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        Welcome to Resources!
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="d-flex align-items-center justify-content-center card-header">
                                <h3 class="text-primary col-md-10">Explore Resources</h3>
                                <a href="panel.php?modulo=recursos" class="btn btn-primary">Back</a>
                            </div>
                        </div>

                        <?php if (isset($selected_video)): ?>
                            <div class="card-body row">
                                <div class="col-md-4 my-1">
                                    <h6>Resource Name:</h6>
                                    <h7 class="text-primary"><?= $selected_video['recurso_name'] ?></h7>
                                </div>
                                <div class="col-md-4 my-1">
                                    <h6>Unity Number:</h6>
                                    <h7 class="text-primary"><?= $selected_video['id_unidad'] ?></h7>
                                </div>
                                <div class="col-md-4 my-1">
                                    <h6>Number of Activities: <a href="#" data-toggle="modal" data-target="#agregarActividadModal" class="ml-2">+</a></h6>
                                    <h7 class="text-primary"><?= $total_actividades['total_actividades'] ?></h7>
                                </div>
                                <div class="col-md-4 my-1">
                                    <h6>Number of Tests: <a href="#" data-toggle="modal" data-target="#agregarPruebaModal" class="ml-2">+</a></h6>
                                    <h7 class="text-primary"><?= $total_pruebas['total_pruebas'] ?></h7>
                                </div>
                                <div class="col-md-4 my-1">
                                    <h6>Description:</h6>
                                    <h7 class="text-primary"><?= $selected_video['descripcion'] ?></h7>
                                </div>
                            </div>
                            <div class="text-center d-flex align-items-center justify-content-center">
                                <video width="70%" controls>
                                    <source src="<?= $selected_video['location'] ?>">
                                </video>
                            </div>
                        <?php endif; ?>

                        <!-- Video selector form -->
                        <div class="col-md-12">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="video_select">Select Resource:</label>
                                    <select class="form-control" name="video_select" id="video_select">
                                        <?php while ($fetch = mysqli_fetch_array($query)): ?>
                                            <option value="<?= $fetch['id_recurso'] ?>" <?= isset($selected_video_id) && $selected_video_id == $fetch['id_recurso'] ? 'selected' : '' ?>><?= $fetch['recurso_name'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary mb-4">Load selected resource</button>
                            </form>
                        </div>
                    </div>

                    <!-- Modal para añadir actividades -->
                    <div class="modal fade" id="agregarActividadModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="actividad_form" action="../Modelo/agregar_actividades.php" method="POST">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <!-- Agrega aquí el contenido del formulario para agregar actividades -->
                                        <div class="col-md-3"></div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Question</label>
                                                <textarea name="pregunta" class="form-control" rows="2"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Options (Separated by commas)</label>
                                                <textarea name="opciones" class="form-control" rows="3"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Answer</label>
                                                <textarea name="respuesta" class="form-control" rows="3"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Description</label>
                                                <select name="descripcion" class="form-control">
                                                    <option value=0 selected >Select</option>
                                                    <option value=1>Order</option>
                                                    <option value=2>Match</option>
                                                    <option value=3>Complete</option>
                                                    <option value=4>Multiple Choice</option>
                                                    <option value=5>Number</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Type</label>
                                                <input type="text" name="tipo" class="form-control" value="Activity"
                                                    readonly />
                                            </div>
                                            <input type="hidden" name="id_recurso"
                                                value="<?php echo $selected_video_id; ?>">
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><span
                                                class="glyphicon glyphicon-remove"></span>Close</button>
                                        <button type="submit" name="save" class="btn btn-primary"><span
                                                class="glyphicon glyphicon-save"></span>Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>


                    <!-- Modal para añadir tests -->
                    <div class="modal fade" id="agregarPruebaModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="actividad_form" action="../Modelo/agregar_actividades.php" method="POST">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <!-- Agrega aquí el contenido del formulario para agregar actividades -->
                                        <div class="col-md-3"></div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Question</label>
                                                <textarea name="pregunta" class="form-control" rows="2"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Options (Separated by commas)</label>
                                                <textarea name="opciones" class="form-control" rows="3"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Answer</label>
                                                <textarea name="respuesta" class="form-control" rows="3"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="descripcion" class="form-control" rows="2"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Type</label>
                                                <input type="text" name="tipo" class="form-control" value="Test"
                                                    readonly />
                                            </div>
                                            <input type="hidden" name="id_recurso"
                                                value="<?php echo $selected_video_id; ?>">
                                        </div>
                                    </div>
                                    <div style="clear:both;"></div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><span
                                                class="glyphicon glyphicon-remove"></span>Close</button>
                                        <button type="submit" name="save" class="btn btn-primary"><span
                                                class="glyphicon glyphicon-save"></span>Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function () {
        $(document).keydown(function (event) {
            if (event.keyCode == 8) {
                event.preventDefault();
                window.location.href = 'panel.php?modulo=recursos';
            }
        });
    });
</script>