<?php
if (isset($_REQUEST['guardar'])) {
  include_once "../Config/conexion.php";
  include_once '../Modelo/zona_horaria.php';

  date_default_timezone_set($user_timezone);
  // Obtener la zona horaria actualmente configurada
  $current_timezone = date_default_timezone_get();

  // Imprimir la zona horaria
  echo "<script>console.log('La zona horaria actual es: " . $current_timezone . "');</script>";

  $username = mysqli_real_escape_string($con, $_POST['username'] ?? '');
  $nombre = mysqli_real_escape_string($con, $_POST['nombre'] ?? '');
  $description = mysqli_real_escape_string($con, $_POST['description'] ?? '');
  $password = mysqli_real_escape_string($con, $_POST['passw'] ?? '');
  $fecha_creacion = date('Y-m-d H:i:s');
  $rol = 'student';
  $puntos = 0;

  if ($username == '' || $nombre == '' || $password == '' || $rol == '') {
    $errorMessage = "All fields are required";
  } else {
    $password = password_hash($password, PASSWORD_DEFAULT);

    $fotoPerfil = "";

    //Hacer un query para ver si existe un usuario con el mismo username y si es así, mostrar un mensaje de error
    $queryUU = "SELECT * FROM usuario WHERE username = '$username';";
    $resultUU = mysqli_query($con, $queryUU);
    $rowUU = mysqli_fetch_assoc($resultUU);

    if ($rowUU) {
      $errorMessage = "The user name already exists";
    }else{
      $queryCN = "INSERT INTO usuario (username, nombre, passw, rol, foto_perfil, fecha_creacion, descripcion, puntos) VALUES ('$username', '$nombre','$password', '$rol', '$fotoPerfil', '$fecha_creacion', '$description', '$puntos');";

      $resultCN = mysqli_query($con, $queryCN);

      //Hacer un query para obtener el id del usuario
      echo "<script>console.log('Username: " . $username . "');</script>";
      $queryOI = "SELECT id_usuario FROM usuario WHERE username = '$username';";
      $resultOI = mysqli_query($con, $queryOI);
      $rowOI = mysqli_fetch_assoc($resultOI);
      $id_usuario = $rowOI['id_usuario'];

      if (isset($_FILES['fperfil']['tmp_name']) && !empty($_FILES['fperfil']['tmp_name'])) {
        $extension = pathinfo($_FILES["fperfil"]["name"], PATHINFO_EXTENSION);
        // Construimos la ruta de la imagen agregando la extensión al final
        $rutaImagen = "../Publico/usuarios/fotosPerfil/" . $id_usuario . "." . $extension;
        // Muestra la ruta de la imagen en la consola
        echo "<script>console.log('Ruta de la imagen: " . $rutaImagen . "');</script>";
      } else if (isset($_POST['fotDefault']) && $_POST['fotDefault'] == 'ftDefault') {
        $rutaImagen = "../Publico/img/user1.png";
      } else if (isset($_POST['fotDefault']) && $_POST['fotDefault'] == 'ftDefault2') {
        $rutaImagen = "../Publico/img/user2.png";
      } else{
        $rutaImagen = "../Publico/img/logo.svg";
      }

      //Hacer un query para actualizar el campo foto_perfil con la ruta de la imagen con el id del usuario
      $queryUP = "UPDATE usuario SET foto_perfil = '" . $rutaImagen . "' WHERE id_usuario = '$id_usuario';";
      $resultUP = mysqli_query($con, $queryUP);

      if($resultUP){
        move_uploaded_file($_FILES["fperfil"]["tmp_name"], $rutaImagen);
        echo "<meta http-equiv='refresh' content='0;url=..\index.php?mensaje=Usuario creado exitosamente'/>";
      }else{
        $errorMessage = "Error uploading image ". mysqli_error($con) ."";
        echo "<script>console.log('Error al subir la imagen". mysqli_error($con) ."');</script>";
      }
    }
  }
  mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Aplicación Ingles</title>
  
  <!-- Theme style -->
  <link rel="stylesheet" href="../Publico/css/adminlte.min.css">

  <!-- Mi css -->
  <link rel="stylesheet" href="../Publico/css/style.css">
</head>



<body style="background-color: #e9ecef;">
  <!-- Content Wrapper. Contains page content -->
  <div class="card ml-5 mr-5 mt-1">
    <!-- Content Header (Page header) -->
    <section class="card-header" style="background-color: #39bdf7">
      <div class="card-body text-dark">
        <div class="row mb-2">
          <div class="mx-auto esquinas-redondeados" style="background-color: #FFC700; padding: 10px">
            <h1 class="">CREATE AN ACCOUNT</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12" style="background-color: #39bdf7">
            <div class="card bg-light mr-5 ml-5" style="position: relative;">
              <!-- SVG en la esquina superior derecha -->
              <div class="svg-top-right">
                  <img src="../Publico/img/crear_usuario/superior_der.svg" alt="SVG 1">
              </div>

              <!-- SVG en la esquina inferior izquierda -->
              <div class="svg-bottom-left">
                  <img src="../Publico/img/crear_usuario/inferior_izq.svg" alt="SVG 2">
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="">Username</label>
                    <input type="text" name="username" class="form-control">
                  </div>
                  <div class="form-group">
                    <label for="">Name</label>
                    <input type="text" name="nombre" class="form-control">
                  </div>
                  <div class="form-group">
                    <label for="">Description</label>
                    <input type="text" name="description" class="form-control">
                  </div>
                  <div class="form-group">
                    <label for="">Password</label>
                    <input type="password" name="passw" class="form-control">
                  </div>


                  <div class="form-group">
                    <label for="">Profile photo</label>
                    <br>
                    <div style="display: flex; justify-content: space-evenly; border: 1px solid #ced4da;border-radius: .25rem; box-shadow: inset 0 0 0 transparent;">
                      <div>
                        <input type="radio" id="ftDeafault" name="fotDefault" value="ftDefault">
                        <img src="../Publico/img/user1.png" alt="" style='width: 150px;'>
                      </div>
                      <div>
                        <input type="radio" id="ftDeafault2" name="fotDefault" value="ftDefault2">
                        <img src="../Publico/img/user2.png" alt="" style='width: 150px;'>
                      </div>
                      <button type="button" class="btn bg-gradient-info btn-sm" id="btnDeseleccionar"
                        style="position: absolute; left: 20px" onclick="uncheckRadioButtons()">X</button>
                    </div>
                  </div>
                  
                  <br>
                  <div class="d-flex flex-column justify-content-center align-items-center">
                    <label for="">Select a default image or upload one of your choice</label>
                    <input type="file" name="fperfil" class="form-control" id="inpFoto" value="UPLOAD" style="max-width: 35rem;">
                    <div class="form-group" style="margin: 1rem">
                      <button type="submit" class="btn btn-success" name="guardar">Create</button>
                      <button type="button" class="btn btn-primary" id="btnCancelar"
                        onclick="window.location.href='../index.php'">Cancel</button>
                    </div>
                  </div>
                  <br>

                  <?php if (isset($errorMessage)): ?>
                    <div class="alert alert-primary alert-dismissible fade show position-fixed"
                      style="top: 20px; right: 20px;" role="alert">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                      </button>
                      <?php echo $errorMessage; ?>
                    </div>
                  <?php endif; ?>



                  <!-- <div class="form-group">
                    <button type="submit" class="btn btn-success" name="guardar">Create</button>
                    <button type="button" class="btn btn-primary" id="btnCancelar"
                      onclick="window.location.href='../index.php'">Cancel</button>
                  </div> -->
                </form>
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
  <script src="../Publico/js/my-scripts.js"></script>




  <!-- jQuery -->
  <script src="../Publico/js/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="../Publico/ext/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>