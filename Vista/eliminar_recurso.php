<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <div class="col-lg-12">
                    <div class="card card-danger">
                        <div class="card-header d-flex justify-content-center">
                            <h3 class="card-title font-weight-bold">Danger Zone, here you delete resources!</h3>
                        </div>
                    </div>
                    <!-- /.card -->


                    <div class="col-lg-12">
                        <div class="card">
                            <div class="d-flex card-header">
                                <h3 class="text-primary col-md-10">Delete Resource</h3>
                                <a href="panel.php?modulo=recursos" class="btn btn-primary">Back</a>
                            </div>
                        </div>
                        <?php
                        include_once '../Modelo/zona_horaria.php';
                        include_once '../Config/conexion.php';

                        // Verificar si se ha enviado el formulario de selección de video a eliminar
                        if (isset($_POST['video_select'])) {
                            $selected_video_id = $_POST['video_select'];

                            // Realizar una nueva consulta para obtener el video seleccionado
                            $selected_query = mysqli_query($con, "SELECT * FROM `recurso` WHERE `id_recurso` = '$selected_video_id'") or die(mysqli_error($con));
                            $selected_video = mysqli_fetch_array($selected_query);

                            // Mostrar el video seleccionado a eliminar
                            if ($selected_video) {
                                echo '
                                    <div class="card d-flex align-items-center justify-content-center">
                                        <div class="col-md-4" style="word-wrap:break-word;">
                                            <br />
                                            <h4>Resource Name:</h4>
                                            <h5 class="text-primary">' . $selected_video['recurso_name'] . '</h5>
                                            <br />
                                            <h4>Description:</h4>
                                            <h5 class="text-primary">' . $selected_video['descripcion'] . '</h5>
                                        </div>
                                        <div class="col-md-8 text-center">
                                            <video width="100%" height="240" controls>
                                                <source src="' . $selected_video['location'] . '">
                                            </video>
                                          </div>
                                        <form action="" method="POST" class="text-center">
                                            <input type="hidden" name="delete_video_id" value="' . $selected_video_id . '">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm(\'¿Estás seguro de eliminar el video?\')">Delete Resource</button>
                                        </form>
                                      <br>
                                    </div>                         
                                ';
                            }
                        }
                        ?>

                        <?php

                        // Verificar si se ha enviado el formulario de eliminación de video
                        if (isset($_POST['delete_video_id'])) {
                            $delete_video_id = $_POST['delete_video_id'];

                            // Obtener la ubicación del archivo de video a eliminar
                            $query = mysqli_query($con, "SELECT `location` FROM `recurso` WHERE `id_recurso` = '$delete_video_id'");
                            $video_data = mysqli_fetch_assoc($query);
                            $video_file_location = $video_data['location'];

                            // Eliminar el archivo de video físico
                            if (unlink($video_file_location)) {
                                echo '<div class="alert alert-success">The file has been successfully deleted.</div>';
                            } else {
                                echo '<div class="alert alert-danger">Error deleting file.</div>';
                            }

                            // Obtener la ubicación del archivo de subtítulos a eliminar
                            $query = mysqli_query($con, "SELECT `vtt_location` FROM `recurso` WHERE `id_recurso` = '$delete_video_id'");
                            $video_data = mysqli_fetch_assoc($query);
                            $vtt_file_location = $video_data['vtt_location'];

                            // Verificar si existe un archivo de subtítulos y eliminarlo
                            if (!empty($vtt_file_location)) {
                                if (unlink($vtt_file_location)) {
                                    echo '<div class="alert alert-success">The subtitle file has been successfully deleted.</div>';
                                } else {
                                    echo '<div class="alert alert-danger">Error deleting subtitle file.</div>';
                                }
                            }

                            // Eliminar el video de la base de datos
                            $delete_query = mysqli_query($con, "DELETE FROM `recurso` WHERE `id_recurso` = '$delete_video_id'") or die(mysqli_error($con));

                            // if ($delete_query) {
                            //     echo '<script>window.location.href = "panel.php?modulo=recursos&mensaje=El video se ha eliminado correctamente";</script>';
                            // } else {
                            //     echo '<script>window.location.href = "panel.php?modulo=recursos&mensaje=Error al eliminar el video";</script>';
                            // }
                        }
                        ?>
                        <div>
                            <!-- Mostrar la lista de videos disponibles -->
                            <?php
                            // Realizar consulta para obtener los videos de la base de datos
                            $query = mysqli_query($con, "SELECT * FROM `recurso` ORDER BY `id_recurso` ASC") or die(mysqli_error($con));
                            ?>

                            <div class="col-md-12">

                                <form action="" method="POST">
                                    <div class="form-group">
                                        <label for="video_select">Select file:</label>
                                        <select class="form-control" name="video_select" id="video_select">
                                            <?php
                                            while ($fetch = mysqli_fetch_array($query)) {
                                                $selected = '';
                                                if (isset($_POST['video_select']) && $_POST['video_select'] == $fetch['id_recurso']) {
                                                    $selected = 'selected';
                                                }
                                                echo '<option value="' . $fetch['id_recurso'] . '" ' . $selected . '>' . $fetch['recurso_name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Load file to delete</button>
                                </form>
                            </div>
                        </div>
                        <br>
                        <br>
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