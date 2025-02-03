<?php
include_once '../Config/conexion.php';

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
$rankingRachasQuery = "SELECT u.username, COUNT(r.id_racha) AS frecuencia_rachas
                        FROM usuario u
                        LEFT JOIN racha r ON u.id_usuario = r.id_usuario
                        GROUP BY u.username
                        ORDER BY frecuencia_rachas DESC
                        LIMIT 5;
                        ";

$resultRachas = mysqli_query($con, $rankingRachasQuery);

if ($resultRachas) {
    $rankingRachas = array();

    while ($rowRachas = mysqli_fetch_assoc($resultRachas)) {
        echo "<script>console.log('Usuario: " . $rowRachas['username'] . ", Frecuencia de Rachas: " . $rowRachas['frecuencia_rachas'] . "');</script>";
        $rankingRachas[] = $rowRachas;
    }
} else {
    echo json_encode(["success" => false, "error" => mysqli_error($con)]);
    exit;
}

// Obtener el username del usuario actual
$usuarioActual = $_SESSION['username'];

// Buscar la posición del usuario en cada ranking
$posicionesUsuarios = array();
foreach ([$rankingActividades, $rankingNotaActividades, $rankingNotaPruebas, $rankingRachas] as $ranking) {
    // Inicializa el array de usuarios únicos y sus posiciones
    $usuariosUnicos = array();
    $posiciones = array();

    // Itera sobre cada usuario en el ranking
    foreach ($ranking as $index => $usuario) {
        // Si el usuario no está en el array de usuarios únicos, agrégalo y registra su posición
        if (!isset($usuariosUnicos[$usuario['username']])) {
            $usuariosUnicos[$usuario['username']] = true;
            $posiciones[$usuario['username']] = $index + 1;
        }
    }

    // Agrega las posiciones de los usuarios únicos al array general de posiciones
    $posicionesUsuarios[] = $posiciones;
}

echo "<script>";
foreach ($posicionesUsuarios as $index => $posiciones) {
    foreach ($posiciones as $usuario => $posicion) {
        echo "console.log('Índice: $index - Posición de $usuario en el ranking #" . ($index + 1) . ": $posicion');";
    }
}

echo "</script>";

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
                            <h3 class="card-title">Welcome to Ranking!</h3>
                        </div>
                    </div>
                    <!-- /.card -->

                    <!-- Ranking Section -->
                    <div class="card content-ranking">
                        <div class="card-header">
                            <h3 class="">Ranking Results</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light mb-4">
                                        <div class="card-header bg-info text-white">
                                            <h4 class="card-title">Ranking by total number of activities completed</h4>
                                        </div>
                                        <div class="card-body">
                                            <?php if (empty($rankingActividades)): ?>
                                                <p>No ranked users were found in this category.</p>
                                            <?php else: ?>
                                                <ul class="list-group">
                                                    <?php foreach ($rankingActividades as $index => $usuario): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            Username: <?= $usuario['username']; ?>
                                                            <span class="badge badge-primary badge-pill"><?= $usuario['total_actividades']; ?></span>
                                                            <?php 
                                                            
                                                            if (isset($posicionesUsuarios[0][$usuario['username']])):
                                                                // Obtener la posición
                                                                $posicion = $posicionesUsuarios[0][$usuario['username']];

                                                                // Mostrar el SVG correspondiente según la posición
                                                                if ($posicion == 1) {
                                                                    echo '<img src="../Publico/img/ranking/1.svg" alt="Posición 1" width="20" height="20" />';
                                                                } elseif ($posicion == 2) {
                                                                    echo '<img src="../Publico/img/ranking/2.svg" alt="Posición 2" width="20" height="20" />';
                                                                } elseif ($posicion == 3) {
                                                                    echo '<img src="../Publico/img/ranking/3.svg" alt="Posición 3" width="20" height="20" />';
                                                                }
                                                                ?>
                                                            <?php else: ?>
                                                                (Place: No Listed)
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card bg-light">
                                        <div class="card-header bg-info text-white">
                                            <h4 class="card-title">Ranking by Activity grade point average</h4>
                                        </div>
                                        <div class="card-body">
                                            <?php if (empty($rankingNotaActividades)): ?>
                                                <p>No ranked users were found in this category.</p>
                                            <?php else: ?>
                                                <ul class="list-group">
                                                    <?php foreach ($rankingNotaActividades as $index => $usuario): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            Username: <?= $usuario['username']; ?>
                                                            <span class="badge badge-primary badge-pill"><?= $usuario['avg_nota_actividades']; ?></span>
                                                            <?php if (isset($posicionesUsuarios[1][$usuario['username']])):
                                                                // Obtener la posición
                                                                $posicion = $posicionesUsuarios[1][$usuario['username']];

                                                                // Mostrar el SVG correspondiente según la posición
                                                                if ($posicion == 1) {
                                                                    echo '<img src="../Publico/img/ranking/1.svg" alt="Posición 1" width="20" height="20" />';
                                                                } elseif ($posicion == 2) {
                                                                    echo '<img src="../Publico/img/ranking/2.svg" alt="Posición 2" width="20" height="20" />';
                                                                } elseif ($posicion == 3) {
                                                                    echo '<img src="../Publico/img/ranking/3.svg" alt="Posición 3" width="20" height="20" />';
                                                                }
                                                                ?>
                                                            <?php else: ?>
                                                                (Place: No Listed)
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light mb-4">
                                        <div class="card-header bg-info text-white">
                                            <h4 class="card-title">Ranking by test grade point average</h4>
                                        </div>
                                        <div class="card-body">
                                            <?php if (empty($rankingNotaPruebas)): ?>
                                                <p>No ranked users were found in this category.</p>
                                            <?php else: ?>
                                                <ul class="list-group">
                                                    <?php foreach ($rankingNotaPruebas as $index => $usuario): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            Username: <?= $usuario['username']; ?>
                                                            <span class="badge badge-primary badge-pill"><?= number_format($usuario['avg_nota_pruebas'], 2); ?></span>
                                                            <?php if (isset($posicionesUsuarios[2][$usuario['username']])):
                                                                // Obtener la posición
                                                                $posicion = $posicionesUsuarios[2][$usuario['username']];

                                                                // Mostrar el SVG correspondiente según la posición
                                                                if ($posicion == 1) {
                                                                    echo '<img src="../Publico/img/ranking/1.svg" alt="Posición 1" width="20" height="20" />';
                                                                } elseif ($posicion == 2) {
                                                                    echo '<img src="../Publico/img/ranking/2.svg" alt="Posición 2" width="20" height="20" />';
                                                                } elseif ($posicion == 3) {
                                                                    echo '<img src="../Publico/img/ranking/3.svg" alt="Posición 3" width="20" height="20" />';
                                                                }
                                                                ?>
                                                            <?php else: ?>
                                                                (Place: No Listed)
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card bg-light">
                                        <div class="card-header bg-info text-white">
                                            <h4 class="card-title">Ranking by frequency of streaks</h4>
                                        </div>
                                        <div class="card-body">
                                            <?php if (empty($rankingRachas)): ?>
                                                <p>No ranked users were found in this category.</p>
                                            <?php else: ?>
                                                <ul class="list-group">
                                                    <?php foreach ($rankingRachas as $index => $usuario): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            Username: <?= $usuario['username']; ?>
                                                            <span class="badge badge-primary badge-pill"><?= $usuario['frecuencia_rachas']; ?></span>
                                                            <?php 
                                                            echo "<script>console.log('Esto es lo que tengo: ". $posicionesUsuarios[3][$usuario['username']] ."');</script>";
                                                            if (isset($posicionesUsuarios[3][$usuario['username']])):
                                                                // Obtener la posición
                                                                $posicion = $posicionesUsuarios[3][$usuario['username']];

                                                                // Mostrar el SVG correspondiente según la posición
                                                                if ($posicion == 1) {
                                                                    echo '<img src="../Publico/img/ranking/1.svg" alt="Posición 1" width="20" height="20" />';
                                                                } elseif ($posicion == 2) {
                                                                    echo '<img src="../Publico/img/ranking/2.svg" alt="Posición 2" width="20" height="20" />';
                                                                } elseif ($posicion == 3) {
                                                                    echo '<img src="../Publico/img/ranking/3.svg" alt="Posición 3" width="20" height="20" />';
                                                                }
                                                                ?>
                                                            <?php else: ?>
                                                                (Place: No Listed)
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
