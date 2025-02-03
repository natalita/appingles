<?php
include_once '../Config/conexion.php';

// Obtener el ID del usuario desde la sesión
$userId = $_SESSION['id_usuario'];

// Consulta SQL para obtener la última entrada de la tabla usuario para el usuario actual
$sql = "SELECT * FROM usuario WHERE id_usuario = $userId";
$resultado = mysqli_query($con, $sql);

// Verificar si se encontró alguna entrada en la tabla usuario para el usuario actual
if (mysqli_num_rows($resultado) > 0) {
    $row = mysqli_fetch_assoc($resultado);
    $nombreU = $row['nombre'];
    $descrip = $row['descripcion'];
}

// Consulta SQL para obtener la última entrada de la tabla racha para el usuario actual
$sqlRacha = "SELECT num_racha FROM racha WHERE id_usuario = $userId";
$resultadoRacha = mysqli_query($con, $sqlRacha);

// Verificar si se encontró alguna entrada en la tabla racha para el usuario actual
if (mysqli_num_rows($resultadoRacha) > 0) {
    $row = mysqli_fetch_assoc($resultadoRacha);
    $nRacha = $row['num_racha'];
}else{
    $nRacha = 0;
}

// Consultas SQL para obtener los resultados de los rankings
// 1. Ranking por número total de actividades completadas
$rankingActividadesQuery = "SELECT u.username, COUNT(DISTINCT p.id_actividad) AS total_actividades
                            FROM usuario u
                            INNER JOIN progreso p ON u.id_usuario = p.id_usuario
                            GROUP BY u.username
                            ORDER BY total_actividades DESC
                            LIMIT 5";

$resultActividades = mysqli_query($con, $rankingActividadesQuery);

if ($resultActividades) {
    $rankingActividades = array();

    while ($rowActividades = mysqli_fetch_assoc($resultActividades)) {
        $rankingActividades[] = $rowActividades;
    }
} else {
    echo json_encode(["success" => false, "error" => mysqli_error($con)]);
    exit;
}

// 2. Ranking por promedio de notas de Actividades
$rankingNotaActividadesQuery = "SELECT u.username, AVG(n.nota) AS avg_nota_actividades 
                                FROM usuario u
                                INNER JOIN nota n ON u.id_usuario = n.id_usuario
                                WHERE n.tipo = 'Actividad'
                                GROUP BY u.username
                                ORDER BY avg_nota_actividades DESC
                                LIMIT 5";

$resultNotaActividades = mysqli_query($con, $rankingNotaActividadesQuery);

if ($resultNotaActividades) {
    $rankingNotaActividades = array();

    while ($rowNotaActividades = mysqli_fetch_assoc($resultNotaActividades)) {
        $rankingNotaActividades[] = $rowNotaActividades;
    }
} else {
    echo json_encode(["success" => false, "error" => mysqli_error($con)]);
    exit;
}

// 3. Ranking por promedio de notas de Pruebas
$rankingNotaPruebasQuery = "SELECT u.username, AVG(n.nota) AS avg_nota_pruebas 
                            FROM usuario u
                            INNER JOIN nota n ON u.id_usuario = n.id_usuario
                            WHERE n.tipo = 'Prueba'
                            GROUP BY u.username
                            ORDER BY avg_nota_pruebas DESC
                            LIMIT 5";

$resultNotaPruebas = mysqli_query($con, $rankingNotaPruebasQuery);

if ($resultNotaPruebas) {
    $rankingNotaPruebas = array();

    while ($rowNotaPruebas = mysqli_fetch_assoc($resultNotaPruebas)) {
        $rankingNotaPruebas[] = $rowNotaPruebas;
    }
} else {
    echo json_encode(["success" => false, "error" => mysqli_error($con)]);
    exit;
}

// 4. Ranking por frecuencia de rachas
$rankingRachasQuery = "SELECT u.username, MAX(r.num_racha) AS frecuencia_rachas
                        FROM usuario u
                        LEFT JOIN racha r ON u.id_usuario = r.id_usuario
                        GROUP BY u.username
                        ORDER BY frecuencia_rachas DESC
                        LIMIT 5;";

$resultRachas = mysqli_query($con, $rankingRachasQuery);

if ($resultRachas) {
    $rankingRachas = array();

    while ($rowRachas = mysqli_fetch_assoc($resultRachas)) {
        $rankingRachas[] = $rowRachas;
    }
} else {
    echo json_encode(["success" => false, "error" => mysqli_error($con)]);
    exit;
}
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
                            <h3 class="card-title">Welcome to your Profile,
                                <b>
                                    <?php echo $_SESSION['username']; ?>!
                                </b>
                            </h3>
                        </div>
                        <div class="bg-secondary">
                            <a href="panel.php?modulo=editarUsuario" class="mt-3 mr-1 float-right btn btn-info">Edit Profile</a>
                        </div>
                        <div class="card-body bg-secondary text-dark">
                            <div class="row">                                
                                <div class="card col-md-6">
                                    <div class="m-3">
                                        <h4>
                                            <?php echo $_SESSION['username']; ?>

                                        </h4>
                                        <!-- Otros detalles del perfil, como descripción, fecha de registro, etc. -->

                                        <p>Name: <?php echo $nombreU; ?></p>

                                        <p>Description: <?php  echo $descrip; ?></p>

                                        <p>Member since:
                                            <?php echo date_format(date_create($_SESSION['fecha_creacion']), 'F d, Y'); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="card col-md-6">
                                    <div class="d-flex align-items-center justify-content-center" style="height: 100%; position: relative;">
                                        <?php
                                        // Obtener la imagen del marco activo
                                        $frameImage = getFrameImage($id_usuario, $con); // Aquí debes obtener la URL del marco activo

                                        // Si hay un marco activado, mostrarlo sobre la foto de perfil
                                        if ($frameImage) {
                                            echo "<div class='frame' style='background-image: url(\"$frameImage\"); position: absolute; height: 100%; top: 50%; left: 50%; transform: translate(-50%, -50%); border-radius: 30%;'></div>";
                                        }

                                        // Mostrar la imagen de perfil del usuario
                                        echo "<img src='" . $_SESSION['foto_perfil'] . "' alt='User Image' style='max-width: 60%; max-height: 70%; opacity: 0.9; border-radius: 30%; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); position: relative;'>";
                                        ?>
                                    </div>
                                </div>
                                <div class="card col-md-6">
                                    <div class="m-3">
                                        <h4>Days Streak:</h4>
                                        <p><?php echo $nRacha ?></p>
                                    </div>
                                </div>
                            </div>

                            
                            <!-- Aquí puedes incluir los datos adicionales del perfil, estadísticas y otros elementos que desees mostrar -->
                            <div class="row">
                                <!-- Ranking del Usuario en Cada Categoría -->
                                <?php
                                // Define las categorías y los resultados del ranking
                                $categorias = array(
                                    "Ranking by total number of activities completed" => $rankingActividades,
                                    "Ranking by grade point average of Activities" => $rankingNotaActividades,
                                    "Ranking by test grade point average" => $rankingNotaPruebas,
                                    "Ranking by streak frequency" => $rankingRachas
                                );

                                // Itera sobre cada categoría de ranking
                                foreach ($categorias as $categoria => $ranking) {
                                    echo "<div class='card col-md-6'>";
                                        echo "<div class='m-3'>";
                                            echo "<h4>$categoria</h4>";

                                            // Busca la posición del usuario en el ranking actual
                                            $posicionUsuario = null;
                                            foreach ($ranking as $index => $usuario) {
                                                if ($usuario['username'] === $_SESSION['username']) {
                                                    $posicionUsuario = $index + 1;
                                                    break;
                                                }
                                            }

                                            // Muestra la posición del usuario en la categoría actual
                                            if ($posicionUsuario !== null) {
                                                echo "<p>Your position in this ranking: #$posicionUsuario</p>";
                                            } else {
                                                echo "<p>You are not listed in this ranking.</p>";
                                            }

                                        echo "</div>"; 
                                    echo "</div>"; 
                                }
                                ?>
                            </div>

                            <!-- Puedes mostrar los logros -->
                           <div class="row">
                                <div class="col-md-12">
                                    <h4>Achievements:</h4>
                                </div>
                                <?php
                                // Consulta para obtener los logros del usuario
                                $query_logros_usuario = "SELECT l.id_logro, l.nombre_logro, l.descripcion, l.recompensa, l.imagen
                                                        FROM logro l
                                                        INNER JOIN usuario_logro ul ON l.id_logro = ul.id_logro
                                                        WHERE ul.id_usuario = $userId";
                                $result_logros_usuario = mysqli_query($con, $query_logros_usuario);

                                // Verificar si hay logros para el usuario
                                if (mysqli_num_rows($result_logros_usuario) > 0) {
                                    // Iterar sobre cada logro del usuario
                                    while ($row_logro_usuario = mysqli_fetch_assoc($result_logros_usuario)) {
                                        $id_logro = $row_logro_usuario['id_logro'];
                                        $nombre = $row_logro_usuario['nombre_logro'];
                                        $descripcion = $row_logro_usuario['descripcion'];
                                        $recompensa = $row_logro_usuario['recompensa'];
                                        $imagen = $row_logro_usuario['imagen'];

                                        // Establecer el estilo de la card
                                        $contenedorStyle = 'bg-dark';
                                        $textStyle = 'text-warning';

                                        // Mostrar el logro en una tarjeta
                                        echo "<div class='col-md-4'>";
                                        echo "<div class='card $contenedorStyle'>";
                                        echo "<div class='card align-items-center justify-content-center contAchiImg'>";
                                        echo "<img src='$imagen' class='achiImg' alt='Achievement Image'>";
                                        echo "</div>";
                                        echo "<div class='card-body $textStyle'>";
                                        echo "<h5 class='card-title'>$nombre</h5>";
                                        echo "<p class='card-text'>$descripcion</p>";
                                        echo "<p class='card-text'>Reward: $recompensa points</p>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                } else {
                                    echo "<div class='col-md-12'>No achievements found.</div>";
                                }
                            echo "</div>";
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
