<?php
include_once '../Modelo/zona_horaria.php';
include_once '../Config/conexion.php';

$query = "SELECT * FROM usuario WHERE id_usuario = '" . $_SESSION['id_usuario'] . "';";

$id_usuario = $_SESSION['id_usuario'];
$fotoPerfil =  $_SESSION['foto_perfil'];
$targetFile = '';
$deleted = false;

$fileInfo = '';
$filename = '';
$directory = '';

$queryFields = '';

$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

$targetFile = $row['foto_perfil'];
// Obtener el nombre del archivo y el directorio

if (isset($_POST['editar'])){
  $id = mysqli_real_escape_string($con, $_POST['id_usuario'] ?? '');
  $username = mysqli_real_escape_string($con, $_POST['username'] ?? '');
  $name = mysqli_real_escape_string($con, $_POST['name'] ?? '');
  $passw = mysqli_real_escape_string($con, $_POST['passw'] ?? '');
  $description = mysqli_real_escape_string($con, $_POST['description'] ?? '');

  if ($username != '' || $name != '' || $description != '') {
    $queryFields = "username = '" . $username . "', nombre = '" . $name . "', descripcion = '" . $description . "'";
    if(isset($_FILES['fperfilEditar'])) {
      switch ($_FILES['fperfilEditar']['error']) {
        case 0:
          echo '<script>console.log("Error 0: No hay error");</script>';
          $fileInfo = pathinfo($targetFile);
          $filename = $fileInfo['filename'];
          $directory = $fileInfo['dirname'];
          echo '<script>console.log("Directorio: ' . $directory . '");</script>';
          echo '<script>console.log("Nombre de archivo: ' . $filename . '");</script>';

          $deleted = true;
          $fotoPerfil = $directory . '/' . $filename . '.' . pathinfo($_FILES['fperfilEditar']['name'], PATHINFO_EXTENSION);
          $queryFields .= ", foto_perfil = '" . $fotoPerfil . "'";
          break;
        case 1:
          echo '<script>console.log("Error 1: El archivo excede el tamaño máximo permitido");</script>';
          ?>
          <div class="alert alert-danger alert-dismissible fade show content-wrapper" role="alert">
            <p style="font-size: 25px;">
              <strong>Error:</strong>
                Error 1 El tamaño de la imagen seleccionada supera los 10MB. Por favor seleccione una imagen más pequeña.
              </p>
          </div>
          <?php
          return;
        case 2:
          echo '<script>console.log("Error 2: El archivo excede el tamaño máximo permitido");</script>';
          ?>
          <div class="alert alert-danger alert-dismissible fade show content-wrapper" role="alert">
            <p style="font-size: 25px;">
              <strong>Error:</strong>
                Error 2 El tamaño de la imagen seleccionada supera los 10MB. Por favor seleccione una imagen más pequeña.
            </p>
          </div>
          <?php
          return;
        case 3:
          echo '<script>console.log("Error 3: El archivo fue parcialmente subido");</script>';
          break;
        case 4:
          echo '<script>console.log("Error 4: No se subió ningún archivo");</script>';
          break;
        case 6:
          echo '<script>console.log("Error 6: No se encontró la carpeta temporal");</script>';
          break;
        case 7:
          echo '<script>console.log("Error 7: No se pudo escribir el archivo en el disco");</script>';
          break;
        case 8:
          echo '<script>console.log("Error 8: Una extensión de PHP detuvo la subida del archivo");</script>';
          break;
      }
      if($passw != ''){
        $queryFields .= ", passw = '" . password_hash($passw, PASSWORD_DEFAULT) . "'";
      }

      $queryActPerfil = "UPDATE usuario SET " . $queryFields . " WHERE id_usuario = '" . $id_usuario . "';";
      echo '<script>';
      echo 'console.log("1 -Query: ' . $queryFields . '");';
      echo '</script>';
      if (mysqli_query($con, $queryActPerfil)) {
        echo '<script>console.log("Valor en passw: '. $passw .'");</script>';
        $_SESSION['username'] = $username;
        $_SESSION['nombre'] = $name;
        $_SESSION['descripcion'] = $description;
        $_SESSION['foto_perfil'] = $fotoPerfil;

        if ($deleted) {
          echo '<script>console.log("Entra a eliminar.");</script>';
          // Intentar eliminar el archivo con cualquier extensión
          $extensions = ['jpeg', 'jpg', 'png', 'gif']; // Lista de extensiones a verificar

          foreach ($extensions as $ext) {
              $fileWithExt = $directory . '/' . $filename . '.' . $ext; // Agregar la extensión al archivo
              if (file_exists($fileWithExt)) { // Verificar si existe el archivo con esta extensión
                  if (unlink($fileWithExt)) { // Intentar eliminar el archivo
                      echo '<script>console.log("Archivo eliminado exitosamente: ' . $fileWithExt . '");</script>';
                      $fotoPerfil = $directory . '/' . $filename . '.' . pathinfo($_FILES['fperfilEditar']['name'], PATHINFO_EXTENSION);
                      echo '<script>console.log("Foto de perfil: ' . $fotoPerfil . '");</script>';
                  } else {
                      echo '<script>console.log("Error al eliminar el archivo: ' . $fileWithExt . '");</script>';
                  }
              }
          }

          // Subir la nueva imagen
          $fotoPerfil = $directory . '/' . $filename . '.' . pathinfo($_FILES['fperfilEditar']['name'], PATHINFO_EXTENSION);
          if (move_uploaded_file($_FILES["fperfilEditar"]["tmp_name"], $fotoPerfil)) {
              echo '<script>console.log("Nueva imagen subida exitosamente.");</script>';
              $nombreLogro = "Add Personalized Picture to Profile";

              // Consulta para obtener el id_logro por nombre_logro
              $logroQuery = "SELECT id_logro FROM logro WHERE nombre_logro = '$nombreLogro'";

              // Ejecutamos la consulta
              $logroResult = mysqli_query($con, $logroQuery);

              // Verificamos si encontramos el logro
              if (mysqli_num_rows($logroResult) > 0) {
                  // Si se encuentra, obtenemos el id_logro
                  $logroData = mysqli_fetch_assoc($logroResult);
                  $logroId = $logroData['id_logro'];

                  // Ahora puedes usar el $logroId en tu lógica
                  echo '<script>console.log("ID del logro: ' . $logroId . '");</script>';
              } else {
                  // Si no se encuentra el logro, manejar el error
                  echo '<script>console.log("Logro no encontrado.");</script>';
              }

              $logroQuery = "SELECT * FROM usuario_logro WHERE id_usuario = '$id_usuario' AND id_logro = '$logroId'";
              $logroResult = mysqli_query($con, $logroQuery);

              // 2. Si no existe el logro, asignarlo
              if (mysqli_num_rows($logroResult) == 0) {
                  // El logro no ha sido asignado, lo asignamos ahora
                  $insertLogroQuery = "INSERT INTO usuario_logro (id_usuario, id_logro, cantidad, completado) VALUES ('$id_usuario', '$logroId', '1', '1')";
                  if (mysqli_query($con, $insertLogroQuery)) {
                      echo '<script>console.log("Logro asignado correctamente: ' . $nombreLogro . '");</script>';
                      // Obtener la recompensa del logro
                      $logroRecompensaQuery = "SELECT recompensa FROM logro WHERE id_logro = '$logroId'";
                      $logroRecompensaResult = mysqli_query($con, $logroRecompensaQuery);

                      if (mysqli_num_rows($logroRecompensaResult) > 0) {
                          $logroRecompensaData = mysqli_fetch_assoc($logroRecompensaResult);
                          $recompensa = $logroRecompensaData['recompensa'];

                          // Actualizar los puntos del usuario
                          $updatePuntosQuery = "UPDATE usuario SET puntos = puntos + $recompensa WHERE id_usuario = '$id_usuario'";
                          if (mysqli_query($con, $updatePuntosQuery)) {
                              echo '<script>console.log("Puntos actualizados correctamente para el usuario: ' . $id_usuario . '");</script>';
                          } else {
                              echo '<script>console.log("Error al actualizar puntos: ' . mysqli_error($con) . '");</script>';
                          }
                      } else {
                          echo '<script>console.log("No se encontró la recompensa del logro.");</script>';
                      }
                  } else {
                      echo '<script>console.log("Error al asignar logro: ' . mysqli_error($con) . '");</script>';
                  }
              } else {
                  echo '<script>console.log("El logro ya ha sido asignado previamente.");</script>';
              }
          } else {
              echo '<script>console.log("Error al subir la nueva imagen.");</script>';
          }
        }else{
          echo '<script>console.log("No entra a eliminar.");</script>';
        }
        echo "<script>window.location.href = '" . dirname($_SERVER['PHP_SELF']) . "/panel.php?modulo=perfil&mensaje=User ". $_SESSION['username'] ." edited!';</script>";                
      }
    }
  }else{
    echo "<script>alert('All fields are required');</script>";
    return;
  }
}elseif (isset($_POST['canEditar'])){
  echo "<meta http-equiv='refresh' content='0;url=panel.php?modulo=perfil'/>";
}

mysqli_close($con);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Edit User</h1>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <!-- /.card-header -->
            <div class="card-body">
              <form action="panel.php?modulo=editarUsuario" method="post" enctype="multipart/form-data">
                <div class="form-group">
                  <label for="">Username</label>
                  <input type="text" name="username" class="form-control" value="<?php echo $row['username'] ?>"
                    required>
                </div>
                <div class="form-group">
                  <label for="">Name</label>
                  <input type="text" name="name" class="form-control" value="<?php echo $row['nombre'] ?>"
                    required>
                </div>
                <div class="form-group">
                  <label for="">Description</label>
                  <input type="text" name="description" class="form-control" value="<?php echo $row['descripcion'] ?>"
                    required>
                </div>
                <div class="form-group">
                  <label for="">Password</label>
                  <div class="input-group">
                    <input type="password" name="passw" class="form-control" id="passwordInput"
                      onfocus="verifyPassword()">
                    <div class="input-group-append">
                      <span class="input-group-text">
                        <i class="fa fa-eye" id="passwordEye"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="">Profile Photo</label>
                  <br>
                  <div style="display: flex; justify-content: space-evenly; border: 1px solid #ced4da;border-radius: .25rem; box-shadow: inset 0 0 0 transparent; align-items: center;">
                    <img src="<?php echo $_SESSION['foto_perfil']; ?>" alt="" style="width: 125px;">
                    <input type="file" name="fperfilEditar" class="form-control" value="UPLOAD">
                  </div>
                  <div class="form-group">
                    <br>
                    <input type="hidden" name="id_usuario" value="<?php echo $row['id_usuario'] ?>">
                    <button type="submit" class="btn btn-success" name="editar">Save</button>
                    <button type="button" class="btn btn-primary" name="canEditar"
                      onclick="window.location.href='panel.php?modulo=perfil'">Cancel</button>
                  </div>
              </form>
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