<?php
// Inicia el búfer de salida y la sesión
ob_start();
session_start();

// Muestra un mensaje si está definido
if (isset($_REQUEST['mensaje'])) {
    $mensaje = htmlspecialchars($_REQUEST['mensaje']);
    echo "<div class='alert alert-primary alert-dismissible fade show float-right' role='alert'>
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
                <span class='sr-only'>Close</span>
            </button>
            $mensaje
          </div>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplicación Inglés</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="Publico/css/fontawesome-free/css/all.min.css" />
    <!-- Theme style -->
    <link rel="stylesheet" href="Publico/css/adminlte.min.css">
    <!-- Mi css -->
    <link rel="stylesheet" href="Publico/css/style.css">
    <style>
        body {
            background-image: url('Publico/img/index/boy_welcome.svg'), url('Publico/img/index/welcome.svg');
            background-position: left bottom, calc(100% - 50px) calc(60% - 40px);
            background-repeat: no-repeat;
            background-size: auto 50%, auto 60%;
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">

                <div class="d-flex justify-content-center">
                    <img src="Publico/img/index/enter_credentials.svg" alt="" style="max-width: 80%; margin: 1rem">                    
                </div>
                <?php
                // Muestra alertas basadas en parámetros GET
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger" role="alert">
                            Incorrect username or password
                          </div>';
                }
                if (isset($_GET['registrarse'])) {
                    echo '<div class="alert alert-success" role="alert">
                            Here is a modal window to create a new user
                          </div>';
                }

                // Verifica si el usuario intentó iniciar sesión
                if (isset($_REQUEST['login'])) {
                    $username = $_REQUEST['username'] ?? '';
                    $password = $_REQUEST['passw'] ?? '';
                    include_once "Config/conexion.php";

                    // Consulta para validar usuario
                    $query = "SELECT * FROM usuario WHERE username='" . mysqli_real_escape_string($con, $username) . "';";
                    $res = mysqli_query($con, $query);
                    $row = mysqli_fetch_assoc($res);

                    if ($row && password_verify($password, $row['passw'])) {
                        // Asigna variables de sesión
                        $_SESSION['id_usuario'] = $row['id_usuario'];
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['nombre'] = $row['nombre'];
                        $_SESSION['foto_perfil'] = $row['foto_perfil'];
                        $_SESSION['rol'] = $row['rol'];
                        $_SESSION['fecha_creacion'] = $row['fecha_creacion'];
                        $_SESSION['descripcion'] = $row['descripcion'];
                        $_SESSION['puntos'] = $row['puntos'];

                        // Redirige al panel
                        header("Location: Vista/panel.php");
                        exit;
                    } else {
                        echo '<div class="alert alert-danger" role="alert">
                                Incorrect username or password
                              </div>';
                    }
                }
                ?>

                <form method="post">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Username" name="username">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Password" name="passw">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="centrado">
                        <div>
                            <button type="submit" class="btn btn-primary btn-block" name="login">Start session</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary btn-block" onclick="window.location.href='Controlador/controlador.php?var=1'">Create an account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="Publico/js/jquery.min.js"></script>
    <script src="Publico/ext/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Envía todo el contenido del búfer al navegador
ob_end_flush();
?>
