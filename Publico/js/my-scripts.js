let url = window.location.href;

//Codigo para mostrar o no la contraseña
var passwordEye = document.getElementById("passwordEye");
var passwordInput = document.getElementById("passwordInput");

url = window.location.href;
if (url.includes('modulo=editarUsuario')) {
  passwordEye.addEventListener("click", function() {
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        passwordEye.classList.remove("fa-eye");
        passwordEye.classList.add("fa-eye-slash");
    } 
    else {
        passwordInput.type = "password";
        passwordEye.classList.remove("fa-eye-slash");
        passwordEye.classList.add("fa-eye");
    }
  });
}

//Script para el boton deseleccionar radio button
function uncheckRadioButtons() {
  console.log("Deseleccionando radio buttons...");
    var radioButtons = document.getElementsByName("fotDefault");
    for (var i = 0; i < radioButtons.length; i++) {
      radioButtons[i].checked = false;
    }
  }

//Script para confirmar la edición de un producto

function btnEditarProducto(id) {
  window.selectedProductId = id;
  $.ajax({
    type: "POST",
    url: "getProducto.php",
    data: { id: id},
    dataType: "json",
    success: function (data) {
      $("#nombrePro").val(data.nombre);
      $("#precioPro").val(data.precio);
      $("#existenciaPro").val(data.existencia);
      $("#modalEditar").modal("show");
    }
  });
}

// Script para guardar los cambios de un producto 
async function GuCaProductos() {
  console.log(window.selectedProductId);
  const idPro = window.selectedProductId;
  const nombrePro = $('#nombrePro').val();
  const precioPro = $('#precioPro').val();
  const existenciaPro = $('#existenciaPro').val();

  console.log(idPro, nombrePro, precioPro, existenciaPro);
  // Agrega verificación para evitar inyecciones de código malicioso en los datos del formulario
  if (!idPro || !nombrePro || !precioPro || !existenciaPro) {
    console.error("Datos inválidos");
    return;
  }

  try {
    $.ajax({
      type: "POST",
      url: "updateProducto.php",
      data: {
        id: idPro,
        nombre: nombrePro,
        precio: precioPro,
        existencia: existenciaPro
      },
      success: function(response) {
        $('#modalEditar').modal('hide');
        $("#tablaProductos").load(window.location + " #tablaProductos");
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });
  } catch (error) {
    console.error(error);
  }
}

//Script para eliminar un producto
async function btnEliminarProducto(id) {
  const idPro = id;
  if (!idPro) {
    console.error("Datos inválidos");
    return;
  }
  $("#confirmModal").modal("show");
  $("#confirmBtn").click(function () {
    try {
    $.ajax({
      type: "POST",
      url: "deleteProducto.php",
      data: {
        id: idPro,
      },
      success: function(response) {
        $("#tablaProductos").load(window.location + " #tablaProductos");
        $('#confirmModal').modal('hide');
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });
  } catch (error) {
    console.error(error);
  }
  });
}

//Script para crear un nuevo producto

async function nuevoProducto(){
  console.log("Dentro de la funcion...");
  const nombrePro = $('#nPro').val();
  const precioPro = $('#pPro').val();
  const existenciaPro = $('#ePro').val();

  if (!nombrePro || !precioPro || !existenciaPro) {
    console.error("Datos inválidos");
    return;
  }

  try {
    $.ajax({
      type: "POST",
      url: "createProducto.php",
      data: {
        nombre: nombrePro,
        precio: precioPro,
        existencia: existenciaPro
      },
      success: function(response) {
        $('#modalAñadir').modal('hide');
        $("#tablaProductos").load(window.location + " #tablaProductos");
        $('#nPro').val('');
        $('#pPro').val('');
        $('#ePro').val('');
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });
  } catch (error) {
    console.error(error);
  }
}

//Funciones para la actividad ordenar con drag & drop
