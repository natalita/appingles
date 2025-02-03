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
                            <h3 class="card-title">Welcome to Rewards!</h3>
                        </div>
                        <div class="card-body">
                            <?php 
                                // Obtener los puntos del usuario
                                $puntos_usuario = $_SESSION['puntos'];
                                echo "<h4 class='text-uppercase m-2'>Your points: $puntos_usuario</h4>";
                            ?>
                            <div class="row">
                                <?php
                                include_once "../Config/conexion.php";
                                // Consulta para obtener todas las bonificaciones
                                $query_bonificaciones = "SELECT * FROM bonificacion";
                                $result_bonificaciones = mysqli_query($con, $query_bonificaciones);

                                // Consulta para contar las bonificaciones no utilizadas del usuario actual
                                $id_usuario = $_SESSION['id_usuario'];
                                $query_cantidad_bonificaciones = "
                                    SELECT ub.id_bonificacion, COUNT(*) AS cantidad
                                    FROM usuario_bonificacion ub
                                    JOIN bonificacion b ON ub.id_bonificacion = b.id_bonificacion
                                    WHERE ub.id_usuario = $id_usuario
                                    AND (ub.estado = 'no utilizada' OR b.id_bonificacion IN (SELECT id_bonificacion FROM bonificacion WHERE nombre_bonificacion LIKE '%frame%'))
                                    GROUP BY ub.id_bonificacion
                                ";

                                $result_cantidad_bonificaciones = mysqli_query($con, $query_cantidad_bonificaciones);

                                // Crear un array asociativo para almacenar la cantidad de bonificaciones por cada bonificación
                                $cantidad_bonificaciones = array();

                                // Obtener la cantidad de bonificaciones por cada bonificación
                                while ($row = mysqli_fetch_assoc($result_cantidad_bonificaciones)) {
                                    $id_bonificacion = $row['id_bonificacion'];
                                    $cantidad_bonificaciones[$id_bonificacion] = $row['cantidad'];
                                }

                                // Verificar si hay bonificaciones
                                if (mysqli_num_rows($result_bonificaciones) > 0) {
                                    $botonDeshabilitadoUso = '';
                                    // Iterar sobre cada bonificación
                                    while ($row_bonificacion = mysqli_fetch_assoc($result_bonificaciones)) {
                                        $id_bonificacion = $row_bonificacion['id_bonificacion'];
                                        $nombre = $row_bonificacion['nombre_bonificacion'];
                                        $descripcion = $row_bonificacion['descripcion'];
                                        $costo = $row_bonificacion['costo'];
                                        $imagen = $row_bonificacion['imagen'];
                                        $maximo = $row_bonificacion['maximo'];

                                        // Establecer el estilo de la card según la bonificación
                                        if (isset($cantidad_bonificaciones[$id_bonificacion])) {
                                            if ($puntos_usuario < $costo) {
                                                $contenedorStyle = 'bg-light';
                                                $textStyle = 'text-muted';
                                                $botonDeshabilitado = 'disabled';
                                            } elseif ((strpos($nombre, 'Frame') !== false) && $cantidad_bonificaciones[$id_bonificacion] == $maximo) {
                                                $contenedorStyle = 'bg-info';
                                                $textStyle = 'text-light';
                                                $botonDeshabilitado = 'disabled';
                                            } elseif ($cantidad_bonificaciones[$id_bonificacion] > 0) {
                                                $contenedorStyle = 'bg-secondary';
                                                $textStyle = 'text-warning';
                                                $botonDeshabilitado = '';
                                            }
                                        } elseif ($puntos_usuario < $costo) {
                                            $contenedorStyle = 'bg-light';
                                            $textStyle = 'text-muted';
                                            $botonDeshabilitado = 'disabled';
                                        } else {
                                            $contenedorStyle = 'bg-dark';
                                            $textStyle = 'text-light';
                                            $botonDeshabilitado = '';
                                        }

                                        // Mostrar la bonificación en una tarjeta
                                        echo "<div class='col-md-4'>";
                                            echo "<div class='card $contenedorStyle'>";
                                                echo "<div class='card align-items-center justify-content-center contAchiImg text-dark'>";
                                                    echo "<img src='$imagen' class='achiImg' alt='Bonification Image'>";
                                                echo "</div>";
                                                echo "<div class='card-body $textStyle'>";
                                                    echo "<h5 class='card-title'>$nombre</h5>";
                                                    echo "<p class='card-text'>$descripcion</p>";
                                                    echo "<p class='card-text'>Cost: $costo points</p>";
                                                    if (isset($cantidad_bonificaciones[$id_bonificacion])) {
                                                        echo "<p class='card-text'>Quantity owned: {$cantidad_bonificaciones[$id_bonificacion]} / $maximo</p>";
                                                        $botonDeshabilitadoUso = ($cantidad_bonificaciones[$id_bonificacion] > 0) ? '' : 'disabled';
                                                    } else {
                                                        echo "<p class='card-text'>Quantity owned: 0 / $maximo</p>";
                                                    }
                                                    echo "<div class='d-flex justify-content-center'>";
                                                        // Botón para comprar bonificación
                                                        echo "<form action='../Modelo/procesar_compra.php' method='post'>";
                                                            echo "<input type='hidden' name='id_bonificacion' value='$id_bonificacion'>";
                                                            echo "<input type='hidden' name='costo_bonificacion' value='$costo'>";
                                                            echo "<input type='hidden' name='nombre_bonificacion' value='$nombre'>";
                                                            echo "<button type='submit' class='btn btn-primary m-2' $botonDeshabilitado>Buy</button>";
                                                        echo "</form>";
                                                        // Botón para usar bonificación
                                                        echo "<form action='../Modelo/procesar_uso_bonificacion.php' method='post'>";
                                                            if ($nombre != 'No lose streak') {
                                                                echo "<input type='hidden' name='id_bonificacion' value='$id_bonificacion'>";
                                                                echo "<input type='hidden' name='nombre_bonificacion' value='$nombre'>";
                                                                echo "<button type='submit' class='btn btn-primary m-2' $botonDeshabilitadoUso>Use</button>";
                                                            }
                                                        echo "</form>";
                                                    echo "</div>";
                                                echo "</div>";
                                            echo "</div>";
                                        echo "</div>";
                                    }
                                } else {
                                    echo "No bonifications found.";
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
