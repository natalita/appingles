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
                            <h3 class="card-title">Welcome to Achievements!</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php
                                include_once '../Modelo/zona_horaria.php';
                                include_once '../Config/conexion.php';
                                
                                // Consulta para obtener las categorías de logros
                                $query_categorias = "SELECT DISTINCT categoria FROM logro ORDER BY categoria";
                                $result_categorias = mysqli_query($con, $query_categorias);

                                // Verificar si hay categorías
                                if (mysqli_num_rows($result_categorias) > 0) {
                                    // Iterar sobre cada categoría
                                    while ($row_categoria = mysqli_fetch_assoc($result_categorias)) {
                                        $categoria = $row_categoria['categoria'];

                                        // Consulta para obtener los logros de la categoría actual
                                        $query_logros = "SELECT * FROM logro WHERE categoria = '$categoria'";
                                        $result_logros = mysqli_query($con, $query_logros);

                                        // Verificar si hay logros en la categoría actual
                                        if (mysqli_num_rows($result_logros) > 0) {
                                            // Mostrar la categoría
                                            echo "<div class='card col-md-12 bg-secondary'>";
                                            echo "<h4 class='text-uppercase m-2 mx-auto'>$categoria</h4>";
                                            echo "<div class='row'>";

                                            // Iterar sobre cada logro de la categoría actual
                                            while ($row_logro = mysqli_fetch_assoc($result_logros)) {
                                                $id_logro = $row_logro['id_logro'];
                                                $nombre = $row_logro['nombre_logro'];
                                                $descripcion = $row_logro['descripcion'];
                                                $recompensa = $row_logro['recompensa'];
                                                $imagen = $row_logro['imagen'];

                                                $id_usuario = $_SESSION['id_usuario'];
                                                // Verificar si el usuario ha completado el logro
                                                $query_verificar_logro = "SELECT completado FROM usuario_logro WHERE id_usuario = $id_usuario AND id_logro = $id_logro";
                                                $result_verificar_logro = mysqli_query($con, $query_verificar_logro);

                                                if (mysqli_num_rows($result_verificar_logro) > 0) {
                                                    // Existe un registro para este usuario y logro
                                                    $rowCompletado = mysqli_fetch_assoc($result_verificar_logro);
                                                    $completado = $rowCompletado['completado'] == 1 ? true : false;
                                                } else {
                                                    // No existe un registro para este usuario y logro
                                                    $completado = false;
                                                }
                                                

                                                // Establecer el estilo de la card según si el logro está completado o no
                                                $imagenStyle = $completado ? 'color: initial;' : 'filter: grayscale(100%);';
                                                $contenedorStyle = $completado ? 'bg-dark' : 'bg-light';
                                                $textStyle = $completado ? 'text-warning' : 'text-dark';

                                                // Mostrar el logro en una tarjeta
                                                echo "<div class='col-md-4'>";
                                                    echo "<div class='card $contenedorStyle'>";
                                                        echo "<div class='card align-items-center justify-content-center contAchiImg'>";
                                                            echo "<img src='$imagen' class='achiImg' alt='Achievement Image' style='$imagenStyle'>";
                                                        echo "</div>";
                                                        echo "<div class='card-body $textStyle'>";
                                                            echo "<h5 class='card-title'>$nombre</h5>";
                                                            echo "<p class='card-text'>$descripcion</p>";
                                                            echo "<p class='card-text'>Reward: $recompensa points</p>";
                                                        echo "</div>";
                                                    echo "</div>";
                                                echo "</div>";
                                            }

                                            echo "</div>"; // Cerrar la fila
                                            echo "</div>"; // Cerrar la categoría
                                        }
                                    }
                                } else {
                                    echo "No achievements found.";
                                }
                                mysqli_close($con);
                                ?>
                            </div>
                        </div>
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
