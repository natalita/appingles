<?php
include_once '../Config/conexion.php';
include_once '../Modelo/zona_horaria.php';

date_default_timezone_set($user_timezone);


?>
<!-- Content Wrapper. Contains page content -->
<br>
<div class="content-wrapper content-activities">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <div class="col-lg-12">
                    <div class="card card-primary">
                        <div class="row">
                            <?php
                            $MiendDate = '';

                            $userId = $_SESSION['id_usuario'];

                            $query1 = mysqli_query($con, "SELECT DISTINCT unidad.id_unidad, unidad.unidad
                            FROM unidad
                            JOIN recurso ON unidad.id_unidad = recurso.id_unidad") or die(mysqli_error($con));


                            $queryRacha = mysqli_query($con, "SELECT end_date FROM racha WHERE id_usuario = $userId");
                            $rowRacha = mysqli_fetch_assoc($queryRacha);
                            if($rowRacha){
                                $MiendDate = $rowRacha['end_date'];
                            }
                            echo "<script>console.log('El valor de en la base end_date es: " . $MiendDate . "');</script>";
                            echo "<script>console.log('El valor de time() en numero es: " . time() . "');</script>";
                            
                            echo "<script>console.log('La fecha de MiendDate es: " . date('Y-m-d H:i:s', strtotime($MiendDate)) . "');</script>";
                            echo "<script>console.log('La fecha de time con seg es: " . date('Y-m-d H:i:s', time()) . "');</script>";
                            $diffHoras = time() - strtotime($MiendDate);
                            echo "<script>console.log('La diferencia en seg es: " . $diffHoras . "');</script>";
                            if(date('Y-m-d', strtotime($MiendDate)) === date('Y-m-d', time())) {
                                $variableAImpri = 'true';
                            }                          
                            else {
                                $variableAImpri = 'false';
                            }
                            echo "<script>console.log('El valor de la variableAImpri es: " . $variableAImpri . "');</script>";

                            


                            while ($row = mysqli_fetch_assoc($query1)) {
                                $unidad = $row['unidad'];

                                echo '
                                    <div class="col-md-4 unidad-container">
                                        <div class="esquinas-redondeados unidad-box' . $unidad . '">
                                            <!-- Imagen de la unidad -->
                                            <div class="unidad-image-container">
                                                <img src="../Publico/img/unit/unit' . $unidad . '.svg" alt="Imagen Unidad" class="unidad-img">
                                            </div>

                                            <!-- Información de la unidad -->
                                            <div class="unidad-info-container">
                                                <!-- Título de la unidad -->
                                                <h3 class="unidad-title">Unit</h3>

                                                <!-- Select de recursos -->
                                                <select name="video_select" class="unidad-select' . $unidad . '" onchange="loadVideo(this)" data-iframe="iframe1">
                                                    <option value="">Select a resource</option>';

                                // Consulta para los recursos de la unidad
                                $unidadQuery = mysqli_query($con, "SELECT * FROM `recurso` WHERE id_unidad = '$unidad' ORDER BY `id_recurso` ASC") or die(mysqli_error($con));

                                // Itera sobre los recursos y crea las opciones
                                while ($recRow = mysqli_fetch_assoc($unidadQuery)) {
                                    $nombreArchivo = $recRow['recurso_name'];
                                    $tipoArchivo = $recRow['tipo_archivo'];
                                    $ubicacionArchivo = $recRow['location'];
                                    $idRecurso = $recRow['id_recurso'];

                                    // Verificar cuántas actividades están pendientes para este recurso
                                    $queryTotal = "SELECT COUNT(*) AS total_actividades FROM actividad WHERE id_recurso = '$idRecurso' AND tipo = 'Activity'";
                                    $resultTotal = mysqli_query($con, $queryTotal) or die(mysqli_error($con));
                                    $rowTotal = mysqli_fetch_assoc($resultTotal);
                                    $totalActividades = $rowTotal['total_actividades'];

                                    // Verificar cuántas actividades ya se han realizado para este recurso
                                    $query = "SELECT COUNT(DISTINCT actividad.id_actividad) AS num_actividades
                                            FROM actividad
                                            LEFT JOIN nota_actividad ON actividad.id_actividad = nota_actividad.id_actividad
                                            LEFT JOIN nota ON nota_actividad.id_nota = nota.id_nota
                                            WHERE actividad.id_recurso = '$idRecurso' AND nota.id_usuario = '$userId' AND actividad.tipo = 'Activity'";

                                    $result = mysqli_query($con, $query) or die(mysqli_error($con));
                                    $row = mysqli_fetch_assoc($result);
                                    $numActividadesRealizadas = $row['num_actividades'];

                                    if ($numActividadesRealizadas != $totalActividades) {
                                        // Mostrar el recurso solo si el usuario no ha completado todas las actividades
                                        echo '<option data-location="' . $ubicacionArchivo . '" data-id-recurso="' . $idRecurso . '">' . $nombreArchivo . '</option>';
                                    }
                                }

                                echo '
                                                </select>
                                            </div>    
                                        </div>
                                    </div>';
                            }
                            $queryActividad = "SELECT a.*, r.location, r.id_unidad 
                                                FROM actividad a
                                                JOIN recurso r ON a.id_recurso = r.id_recurso
                                                WHERE a.tipo = 'Activity' 
                                                AND a.id_actividad NOT IN (
                                                    SELECT na.id_actividad 
                                                    FROM nota_actividad na
                                                    JOIN nota n ON na.id_nota = n.id_nota
                                                    WHERE n.id_usuario = '$userId'
                                                )";
                            $resultActividad = mysqli_query($con, $queryActividad);                          

                            $questionsActividad = array();
                            $idActividad = 1;

                            // Convierte los resultados en un arreglo asociativo
                            while ($rowActividad = mysqli_fetch_assoc($resultActividad)) {

                                $opcionesActividad = explode(',', $rowActividad['opciones']); // Convertir la cadena de opciones en un arreglo
                                $questionsActividad[] = array(
                                    'id' => $idActividad,
                                    'tipo' => $rowActividad['tipo'],
                                    'id_recurso' => intval($rowActividad['id_recurso']),
                                    'id_actividad' => intval($rowActividad['id_actividad']),
                                    'id_unidad' => intval($rowActividad['id_unidad']),
                                    'descripcion' => $rowActividad['descripcion'],
                                    'pregunta' => $rowActividad['pregunta'],
                                    'respuesta' => $rowActividad['respuesta'],
                                    'opciones' => $opcionesActividad,
                                    'ruta_video' => $rowActividad['location']
                                );
                                $idActividad++;
                            }

                            // Convierte el arreglo de preguntas en formato JSON
                            $questionsJsonActividad = json_encode($questionsActividad, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                            


                            // Elimina las comillas solo alrededor de los nombres de las propiedades
                            $questionsJsonActividad = preg_replace('/"([^"]+)"\s*:/', '$1:', $questionsJsonActividad);
                            //imprimir con echo y console log $questionsJsonActividad
                            echo "<script>console.log('Contenido de questionsJsonActividad:');</script>";
                            echo "<script>console.log(" . json_encode($questionsJsonActividad, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . ");</script>";
                            echo '<script>var preguntasActividad = ' . $questionsJsonActividad . ';</script>';
                            ?>
                            <!-- /.card -->
                        </div>
                        <?php
                        echo '<div id="iframe-container" style="display: none;">
                                        <video id="video-iframe" controls style="width: 100%; height: 500px; border: none;">
                                        </video>
                                    </div>';
                        ?>
                        <!-- /.row -->

                    </div>
                        <select id="actividad-select" name="actividad_select" class="bg-dark" onchange="loadSelectedActivity()" >
                            <option value="">Select an activity</option>
                        </select>
                    </div>
                    <!-- /.container-fluid -->
                </div>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        </div>

        <!-- El estilo de este modal esta en my-css -->
        <div class="modal" id="myModal" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-body">
						<div class="col-md-12">
                            <span class="close">&times;</span>
							<div id="formulario-container"></div>
						</div>
					</div>
				<div style="clear:both;"></div>
			</div>
		</div>

    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
// Variable global para almacenar temporalmente las actividades asociadas
    var actividadesAsociadasTemp;
    
    //Respuesta de puntos ganados
    var puntosGanados = 10;

    //Select
    var conSelect = 0;
    //Multiple choice
    var conBMC = 0;
    var conMMC = 0;
    //Order
    var conMO = 0;
    //Complete
    var conMCom = 0;
    //Match
    var conMMat = 0;
    //Number
    var conMNu = 0;

    // Definir userId globalmente
    var userId = "<?php echo $userId; ?>";
    console.log("El valor de userId es: " + userId);

    // Función para actualizar la fecha de última actividad en la base de datos
    function actualizarTablaRacha() {
        console.log("Entra en la funcion para actualizar la tabla de racha");
        // Realizar una solicitud AJAX para actualizar la fecha de última actividad en la base de datos
        // Suponiendo que estés utilizando jQuery para realizar solicitudes AJAX
        $.ajax({
            url: '../Modelo/actRacha.php',
            method: 'POST',
            data: { userId: userId },
            dataType: 'json',
            success: function(response) {
                console.log('Fecha de última actividad actualizada correctamente.');
                console.log('Respuesta:', response);

                // Imprimir todas las propiedades del objeto response
                Object.keys(response).forEach(function(key) {
                    if(response[key] != ""){
                        console.log(key + ':', response[key]);
                    }
                });

                var mensaje = 'Your last activity date has been updated';
                //window.location.href = '../Vista/panel.php?modulo=actividades&mensaje=' + encodeURIComponent(mensaje);
            },
            error: function(xhr, status, error) {
                console.error('Error al actualizar la fecha de última actividad:', error);
            }
        });
    }

    function save_nota_actividad(tipoPregunta, id_unidad, id_actividad, ...args){
        console.log("Entra en la funcion para guardar la nota de la actividad con tipoPregunta: " + tipoPregunta + " y args: " + args)
            for (let i = 0; i < args.length; i++) {
            console.log('Argumento', i + 1, ':', args[i]);
        }
        let data = { tipoPregunta: tipoPregunta,  userId: userId, id_unidad: id_unidad, id_actividad: id_actividad, puntosGanados: puntosGanados};
        switch (tipoPregunta) {
            case 'select':
                // Si es una pregunta de tipo select
                data.n_intentos = args[0];
                data.n_opciones = args[1];
                break;
            case 'multiple choice':
                // Si es una pregunta de tipo multiple choice
                data.buenasMC = args[0];
                data.malasMC = args[1];
                data.totalMC = args[2];
                data.totalMTot = args[3];
                break;
            case 'order':
                // Si es una pregunta de tipo order
                data.n_intentosO = args[0];
                data.n_opcionesO = args[1];
                break;
            case 'complete':
                // Si es una pregunta de tipo complete
                data.n_intentosC = args[0];
                data.n_opcionesC = args[1];
                break;
            case 'match':
                // Si es una pregunta de tipo match
                data.n_intentosMat = args[0];
                data.n_opcionesMat = args[1];
                break;
            case 'number':
                // Si es una pregunta de tipo number
                data.n_intentosNum = args[0];
                data.n_opcionesNum = args[1];
                break;
            default:
                console.error('Tipo de pregunta no reconocido:', tipoPregunta);
                return;
        }

        // Realizar una solicitud AJAX para guardar la nota de la actividad en la base de datos
        // Suponiendo que estés utilizando jQuery para realizar solicitudes AJAX
        console.log("Se envia userId: " + userId);
        console.log("Se envia tipoPregunta: " + tipoPregunta);

        $.ajax({
            url: '../Modelo/save_nota_actividad.php',
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                console.log('Nota de actividad guardada correctamente.');
                console.log('Respuesta:', response);

                // Imprimir todas las propiedades del objeto response
                Object.keys(response).forEach(function(key) {
                    if(response[key] != ""){
                        console.log(key + ':', response[key]);
                    }
                });

                //Reiniciar los contadores
                //Select
                conSelect = 0;
                //Multiple choice
                conBMC = 0;
                conMMC = 0;
                //Order
                conMO = 0;
                //Complete
                conMCom = 0;
                //Match
                conMMat = 0;
                //Number
                conMNu = 0;


                // Actualizar el contador de puntos
                var puntosGanados = response.puntosGanados;
                var puntosActuales = parseInt($('#puntos-counter').text());
                var nuevosPuntos = puntosActuales + puntosGanados;
                $('#puntos-counter').text(nuevosPuntos);

                var mensaje = 'The activity ' + response.id_actividad + ' in the unity ' + response.id_unidad + ' has been registered with your note: ' + response.nota_actividad + ' also you earned ' + puntosGanados + ' points';

                actividadesHorarios(response.hora);
                
                window.location.href = '../Vista/panel.php?modulo=actividades&mensaje=' + encodeURIComponent(mensaje);
            },
            error: function(xhr, status, error) {
                console.error('Error al guardar la nota de actividad:', error);
            }
        });
    }

    function actividadesHorarios(hora){
        hora = '21:00:00'
        console.log("Llega a la funcion de las horas");
        console.log("La hora es: " + hora);
        var horaSplit = hora.split(":");
        var horaInt = parseInt(horaSplit[0]);

        // Obtener la hora en formato de 24 horas (0-23)
        if (horaInt < 9) {
            // Redirigir al archivo A
            
            let data = {userId: userId};
            
            // Realizar una solicitud AJAX para guardar la nota de la actividad en la base de datos
            console.log("Se envia userId: " + userId);

            $.ajax({
                url: '../Modelo/antes9am.php',
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    console.log('Respuesta exitosa de antes9am.');
                    console.log('Respuesta:', response);

                    // Imprimir todas las propiedades del objeto response
                    Object.keys(response).forEach(function(key) {
                        if(response[key] != ""){
                            console.log(key + ':', response[key]);
                        }
                    });

                    var mensaje = 'T';
                    //window.location.href = '../Vista/panel.php?modulo=actividades&mensaje=' + encodeURIComponent(mensaje);
                },
                error: function(xhr, status, error) {
                    console.error('Error al guardar la nota de actividad:', error);
                }
            });

        } else if (horaInt >= 21) {
            // Redirigir al archivo B


            let data = {userId: userId};
            
            // Realizar una solicitud AJAX para guardar la nota de la actividad en la base de datos
            console.log("Se envia userId: " + userId);

            $.ajax({
                url: '../Modelo/despues9pm.php',
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    console.log('Respuesta exitosa de despues9pm.');
                    console.log('Respuesta:', response);

                    // Imprimir todas las propiedades del objeto response
                    Object.keys(response).forEach(function(key) {
                        if(response[key] != ""){
                            console.log(key + ':', response[key]);
                        }
                    });

                    var mensaje = 'h';
                    //window.location.href = '../Vista/panel.php?modulo=actividades&mensaje=' + encodeURIComponent(mensaje);
                },
                error: function(xhr, status, error) {
                    console.error('Error al guardar la nota de actividad:', error);
                }
            });

        } else {
            console.log("La hora está entre las 9 am y las 9 pm");
            // Realizar alguna otra acción o redirigir a otra página si es necesario
        }
    }
    

    // Función para obtener actividades asociadas a un recurso
    function obtenerActividadesAsociadas(idRecursoSeleccionado) {
        console.log("En esta funcion llega con este valor: " + idRecursoSeleccionado);
        console.log(preguntasActividad);
        return preguntasActividad.filter(function (actividad) {
            return actividad.id_recurso === parseInt(idRecursoSeleccionado);
        });
    }

    //Funcion para cargar el select con actividades
    function loadSelectedActivity() {
        // Obtener el elemento select
        var actividadSelect = document.getElementById('actividad-select');

        // Obtener el valor seleccionado (id de la actividad)
        var selectedActivityId = actividadSelect.value;

        // Verificar que se haya seleccionado una actividad
        if (selectedActivityId) {
            // Encontrar la actividad correspondiente en actividadesAsociadasTemp
            var actividadSeleccionada = actividadesAsociadasTemp.find(function (actividad) {
                return actividad.id_actividad === parseInt(selectedActivityId);
            });

            // Verificar si se encontró la actividad
            if (actividadSeleccionada) {
                // Mostrar las preguntas de la actividad seleccionada
                mostrarPreguntas(actividadSeleccionada);
            } else {
                console.error('No se encontró la actividad con el id:', selectedActivityId);
            }
        } else {
            console.error('Ninguna actividad seleccionada.');
        }
    }


    // Función para cargar el video y las preguntas asociadas
    function loadVideo(selectElement) {
        var selectedOption = selectElement.options[selectElement.selectedIndex];
        var videoLocation = selectedOption.getAttribute('data-location');
        var idRecursoSeleccionado = selectedOption.getAttribute('data-id-recurso');
        console.log("Id del recurso: " + idRecursoSeleccionado);

        // Limpiar contenido anterior
        var formularioContainer = document.getElementById('formulario-container');
        formularioContainer.innerHTML = '';

        // Obtener el video y establecer su atributo src con la ubicación del video
        var videoElement = document.getElementById('video-iframe');
        videoElement.src = videoLocation;

        // Reproducir el video automáticamente
        videoElement.play();

        // Establecer el atributo "value" de la opción por defecto en todos los selects
        var defaultOption = document.querySelectorAll('.unidad-select option[value=""]');
        defaultOption.forEach(function (option) {
            option.selected = true;
        });

        // Mostrar el contenedor del iframe
        var iframeContainer = document.getElementById('iframe-container');
        iframeContainer.style.display = 'block';

        // Obtener actividades asociadas al id del recurso seleccionado
        actividadesAsociadasTemp = obtenerActividadesAsociadas(idRecursoSeleccionado);
        console.log("En este punto tiene : " + actividadesAsociadasTemp.length);
        
        //Esto lo agregue para poder ver las actividades en el select
        // Llenar el menú select con las actividades asociadas
        var actividadSelect = document.getElementById('actividad-select');
        actividadSelect.innerHTML = '<option value="">Select an activity</option>';

        actividadesAsociadasTemp.forEach(function (actividad) {
            var option = document.createElement('option');
            option.value = actividad.id_actividad;
            option.textContent = actividad.descripcion;
            actividadSelect.appendChild(option);
        });

    
        videoElement.addEventListener('ended', function () {
            // Verificar si hay actividades asociadas cada vez que el video termine
            if (actividadesAsociadasTemp && actividadesAsociadasTemp.length !== 0) {
                console.log("Actividades asociadas:", JSON.stringify(actividadesAsociadasTemp, null, 2));
                // Mostrar solo una actividad al azar
                var actividadSeleccionada = actividadesAsociadasTemp[Math.floor(Math.random() * actividadesAsociadasTemp.length)];
                console.log("El valor que se manda en actividadSeleccionada es: ",JSON.stringify(actividadSeleccionada, null, 2));
                mostrarPreguntas(actividadSeleccionada);
            } else {
                // Limpiar el contenedor del formulario si no hay actividades asociadas
                formularioContainer.innerHTML = '';
            }
        });
    }

    function mostrarPreguntas(actividadSeleccionada) {
        var tipoActividad = actividadSeleccionada.descripcion.toLowerCase();
        console.log("Tipo de actividad: " + tipoActividad);
        var formularioContainer = document.getElementById('formulario-container');
        formularioContainer.innerHTML = '';

        var formulario = document.createElement('form');
        var preguntaElement = document.createElement('p');
        preguntaElement.textContent = actividadSeleccionada.pregunta;
        formulario.appendChild(preguntaElement);

        // Construir el formulario según el tipo de actividad detectado
        if (tipoActividad === 'select') {
            actividadSeleccionada.opciones.forEach(function (opcion, opcionIndex) {
                // Crear elementos de opción para actividad de opciones
                var opcionElement = document.createElement('input');
                opcionElement.type = 'radio';
                opcionElement.name = 'pregunta';
                opcionElement.value = opcion;

                var labelElement = document.createElement('label');
                labelElement.textContent = opcion;

                var optionContainer = document.createElement('div');
                optionContainer.appendChild(opcionElement);
                optionContainer.appendChild(labelElement);

                formulario.appendChild(optionContainer);

                // Agregar evento para mostrar retroalimentación al hacer clic en una opción
                opcionElement.addEventListener('click', function () {
                    // Verificar si la opción seleccionada es la respuesta correcta
                    var respuestasCorrectasUsuario = opcionElement.value === actividadSeleccionada.respuesta;

                    // Mostrar retroalimentación
                    mostrarRetroalimentacion(respuestasCorrectasUsuario, actividadSeleccionada, tipoActividad);
                });
            });
        } else if (tipoActividad === 'match') {
            console.log("Valor de la respuesta: " + actividadSeleccionada.respuesta);
            var palabraIzquierda = null;
            var palabraDerecha = null;
            var seleccionActual = null; // Variable para mantener el estado de la selección actual

            // Utilizar opcionesBarajadas para obtener las opciones barajadas correctamente
            var opcionesBarajadas = opcionesBarajadas(actividadSeleccionada.opciones.join(','));

            var contenedorGeneral = document.createElement('div');
            contenedorGeneral.className = 'contenedor-general';
            contenedorGeneral.style.display = 'flex';

            var columnaIzquierda = document.createElement('div');
            columnaIzquierda.className = 'columna-izquierda';
            columnaIzquierda.setAttribute("style", "width: 30%; border: 2px dashed #5f1818;");

            // Contenedor SVG para las líneas
            var svgContainer = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svgContainer.id = 'svg-container';
            svgContainer.style = 'flex: 1; width: 100%;'; // El SVG ocupa el espacio restante
            svgContainer.style.pointerEvents = 'none'; // Permitir clics a través del SVG

            var columnaDerecha = document.createElement('div');
            columnaDerecha.className = 'columna-derecha';
            columnaDerecha.style = "width: 30%";

            opcionesBarajadas.forEach(function (opcion, opcionIndex) {
                var palabraElement = document.createElement('div');
                palabraElement.className = 'draggable-word';
                palabraElement.textContent = opcion;
                palabraElement.setAttribute("style", "margin: 10px; border: 2px dashed red;");

                palabraElement.addEventListener('click', function () {
                    console.log("Estoy haciendo clic");
                    if (palabraIzquierda === null || palabraDerecha === null) {
                        
                        // Determinar la selección actual
                        if (seleccionActual === null) {
                            seleccionActual = opcionIndex % 2 === 0 ? 'derecha' : 'izquierda';
                            console.log("El valor de opcionIndex: " + opcionIndex);
                        }

                        if ((seleccionActual === 'izquierda' && opcionIndex % 2 === 0) ||
                            (seleccionActual === 'derecha' && opcionIndex % 2 !== 0)) {
                            palabraIzquierda = palabraElement;
                        } else {
                            palabraDerecha = palabraElement;
                        }

                        // Eliminar línea existente si hay una
                        

                        // Calcular las coordenadas de las palabras en porcentajes relativos al contenedor
                        var rectIzquierda = palabraIzquierda.getBoundingClientRect();
                        var rectDerecha = palabraDerecha.getBoundingClientRect();

                        // Coordenadas relativas al svgContainer
                        var y1 = ((rectIzquierda.top + rectIzquierda.bottom) / 2 - svgContainer.getBoundingClientRect().top) / svgContainer.clientHeight * 100;

                        // Coordenadas relativas al svgContainer, asegurándote de que no exceda el ancho del svgContainer
                        var y2 = ((rectDerecha.top + rectDerecha.bottom) / 2 - svgContainer.getBoundingClientRect().top) / svgContainer.clientHeight * 100;
                        
                        if(seleccionActual === "izquierda"){
                            removeExistingLine(palabraDerecha,palabraIzquierda );
                            svgContainer.appendChild(createLine(y2, y1));
                        }else{
                            removeExistingLine(palabraIzquierda, palabraDerecha);
                            svgContainer.appendChild(createLine(y1, y2));
                        }
                        // Crear y agregar la nueva línea SVG

                        // Limpiar las palabras seleccionadas y resetear la selección actual
                        palabraIzquierda = null;
                        palabraDerecha = null;
                        seleccionActual = null;
                    }
                });

                if (opcionIndex % 2 === 0) {
                    columnaDerecha.appendChild(palabraElement);
                } else {
                    columnaIzquierda.appendChild(palabraElement);
                }
            });

            // Crear el botón "Send"
            var sendButton = document.createElement('button');
            sendButton.textContent = 'Check answer';
            sendButton.className = 'order-button';
            sendButton.type = 'button';
            sendButton.addEventListener('click', function () {
                // Lógica para comprobar respuestas
                var respuestasUsuario = obtenerRespuestasUsuarioUnir();
                var respuestasCorrectas = comprobarRespuestasUnir(respuestasUsuario, actividadSeleccionada.respuesta);

                // Mostrar retroalimentación
                mostrarRetroalimentacion(respuestasCorrectas, actividadSeleccionada, tipoActividad);
            });

            contenedorGeneral.appendChild(columnaIzquierda);
            contenedorGeneral.appendChild(svgContainer);
            contenedorGeneral.appendChild(columnaDerecha);
            contenedorGeneral.appendChild(sendButton);

            formulario.appendChild(contenedorGeneral);
        } else if (tipoActividad === 'complete') {
            // Div que contendrá las opciones y los espacios para completar
            var completarContainer = document.createElement('div');

            // Mostrar las respuestas arriba del formulario
            var respuestasDiv = document.createElement('div');
            respuestasDiv.className = 'text-center';
            respuestasDiv.textContent = 'Options: ' + actividadSeleccionada.respuesta;
            completarContainer.appendChild(respuestasDiv);

            // Div que contendrá las opciones
            var opcionesContainer = document.createElement('div');

            // Separar las opciones de la respuesta
            var respuestas = actividadSeleccionada.respuesta.split(',');

            // Iterar sobre cada opción
            actividadSeleccionada.opciones.forEach(function (opcion) {
                // Div para cada opción
                var opcionDiv = document.createElement('div');
                opcionDiv.className = 'row d-block';

                // Separar palabras en la opción
                var palabrasOpcion = opcion.split(' ');

                // Separar palabras en la respuesta
                var palabrasRespuesta = actividadSeleccionada.respuesta.split(',');

                // Iterar sobre cada palabra en la opción
                palabrasOpcion.forEach(function (palabraOpcion, index) {
                    // Limpiar la palabra de caracteres especiales
                    var palabraLimpia = palabraOpcion.replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g, "");
                    // Crear elemento para cada palabra
                    var palabraElement = document.createElement('span');
                    // Dar atributo de margin para que se separen entre palabras
                    palabraElement.innerHTML = palabraLimpia + ' ';
                    palabraElement.setAttribute("style", "margin: 4px;");

                    // Agregar un salto de línea después de cada palabra (excepto la última)
                    if (index < palabrasOpcion.length - 1) {
                        palabraElement.innerHTML += '<br>';
                    } else {
                        // No añadir salto de línea después de la última palabra
                        palabraElement.innerHTML += ' ';
                    }

                    // Verificar si la palabra está en la respuesta
                    if (palabrasRespuesta.includes(palabraLimpia)) {
                        console.log(`Valor en palabrasRespuesta: ${palabrasRespuesta} y el valor en palabraOpcion: ${palabraLimpia}`);
                        // Crear espacio para completar (input)
                        var completarEspacioElement = document.createElement('input');
                        completarEspacioElement.type = 'text';
                        completarEspacioElement.name = 'respuesta_completar';
                        opcionDiv.appendChild(completarEspacioElement);
                    } else {
                        // Mostrar la palabra normalmente
                        palabraElement.textContent = palabraOpcion + ' ';
                        opcionDiv.appendChild(palabraElement);
                    }
                });

                // Agregar div de opción al contenedor de opciones
                opcionesContainer.appendChild(opcionDiv);
            });

            // Agregar las opciones al contenedor principal
            completarContainer.appendChild(opcionesContainer);

            // Agregar un contenedor para el botón de comprobar respuestas
            var botonContainer = document.createElement('div');

            // Crear el botón para comprobar respuestas
            var comprobarBoton = document.createElement('button');
            comprobarBoton.textContent = 'Check Answers';
            comprobarBoton.type = 'button';
            comprobarBoton.addEventListener('click', function () {
                // Lógica para comprobar respuestas
                var respuestasUsuario = obtenerRespuestasUsuarioCompletar();
                var respuestasCorrectas = comprobarRespuestasCompletar(respuestasUsuario, actividadSeleccionada.respuesta);

                // Mostrar retroalimentación
                mostrarRetroalimentacion(respuestasCorrectas, actividadSeleccionada, tipoActividad);
            });

            // Agregar el botón al contenedor
            botonContainer.appendChild(comprobarBoton);

            // Agregar el contenedor del botón al formulario
            completarContainer.appendChild(botonContainer);

            // Agregar el contenedor principal al formulario
            formulario.appendChild(completarContainer);
        } else if (tipoActividad === 'order') {
            // Coloca las palabras en un distinto orden, aleatoriamente
            var opcionesBarajadas = actividadSeleccionada.opciones.sort(function () {
                return 0.5 - Math.random();
            });

            // Crear el contenedor principal
            var dragdropContainer = document.createElement('div');
            dragdropContainer.id = 'dragdropContainer';
            dragdropContainer.className = 'dragdrop-container';

            // Crear el contenedor para las palabras
            var wordContainer = document.createElement('div');
            wordContainer.id = 'wordContainer';
            wordContainer.className = 'word-container';
            wordContainer.style = 'width: 40%; min-height: 50%; margin: 20px; border: 2px dashed #c7d2e2;';

            // Crear un div para cada palabra
            opcionesBarajadas.forEach(function (opcion, opcionIndex) {
                var originalWord = document.createElement('div');
                originalWord.className = 'draggable-word';
                originalWord.textContent = opcion;

                // Agregar evento de clic a cada palabra
                originalWord.addEventListener("click", function () {
                    // Clonar la palabra original
                    var clonedWord = originalWord.cloneNode(true);

                    // Agregar evento para quitar la palabra al hacer clic
                    clonedWord.addEventListener("click", function () {
                        answerContainer.removeChild(clonedWord);
                        originalWord.style.display = 'block'; // Mostrar la palabra original nuevamente en el contenedor original
                    });

                    answerContainer.appendChild(clonedWord);
                    originalWord.style.display = 'none'; // Ocultar la palabra original en el contenedor original
                });

                wordContainer.appendChild(originalWord);
            });

            // Crear el contenedor para la respuesta
            var answerContainer = document.createElement('div');
            answerContainer.id = 'answerContainer';
            answerContainer.className = 'answer-container';
            answerContainer.textContent = 'ANSWER';
            answerContainer.style = 'width: 40%; min-height: 50%; margin: 20px; border: 2px dashed #5f1818;';

            // Agregar evento de clic para quitar palabra del contenedor de respuesta
            answerContainer.addEventListener("click", function () {
                var lastDroppedWord = answerContainer.lastElementChild;
                if (lastDroppedWord) {
                    wordContainer.appendChild(originalWord); // Devolver la palabra original al contenedor original
                    lastDroppedWord.parentNode.removeChild(lastDroppedWord); // Eliminar la palabra clonada del contenedor de respuesta
                    originalWord.style.display = 'block'; // Mostrar la palabra original nuevamente en el contenedor original
                }
            });

            // Crear el botón "Send"
            var sendButton = document.createElement('button');
            sendButton.textContent = 'Check answer';
            sendButton.className = 'order-button';
            sendButton.type = 'button';

            // Agregar los contenedores y el botón al contenedor principal
            dragdropContainer.appendChild(wordContainer);
            dragdropContainer.appendChild(answerContainer);
            dragdropContainer.appendChild(sendButton);

            // Agregar el contenedor principal al formulario
            formulario.appendChild(dragdropContainer);

            // Agregar evento al botón "Send"
            sendButton.addEventListener('click', function () {
                // Obtener todas las palabras en el contenedor de respuesta
                var answerWords = answerContainer.querySelectorAll('.draggable-word');

                // Obtener las palabras correctas en el orden correcto desde la respuesta en la base de datos
                var respuestaCorrecta = actividadSeleccionada.respuesta.split(' ');

                // Verificar si la respuesta del usuario coincide con la respuesta correcta
                var respuestasCorrectasUsuario = Array.from(answerWords).map(word => word.textContent).join(' ') === actividadSeleccionada.respuesta;

                // Mostrar retroalimentación
                mostrarRetroalimentacion(respuestasCorrectasUsuario, actividadSeleccionada, tipoActividad);
            });
            
        }else if(tipoActividad === 'multiple choice'){
            actividadSeleccionada.opciones.forEach(function (opcion, opcionIndex) {
                // Crear elementos de opción para actividad de opciones múltiples
                var opcionElement = document.createElement('input');
                opcionElement.type = 'checkbox';  // Cambiar a checkbox
                opcionElement.name = 'pregunta';
                opcionElement.value = opcion;

                var labelElement = document.createElement('label');
                labelElement.textContent = opcion;

                var optionContainer = document.createElement('div');
                optionContainer.appendChild(opcionElement);
                optionContainer.appendChild(labelElement);

                formulario.appendChild(optionContainer);
            });

            // Crear el botón "Check answer"
            var comprobarBoton = document.createElement('button');
            comprobarBoton.textContent = 'Check answer';
            comprobarBoton.className = 'order-button';
            comprobarBoton.type = 'button';
            
            // Agregar evento para comprobar respuestas
            comprobarBoton.addEventListener('click', function () {
                // Lógica para comprobar respuestas
                var respuestasUsuario = obtenerRespuestasUsuarioMultiple();
                var respuestasCorrectas = comprobarRespuestasMultiple(respuestasUsuario, actividadSeleccionada.respuesta);

                // Mostrar retroalimentación
                mostrarRetroalimentacion(respuestasCorrectas, actividadSeleccionada, tipoActividad);
            });

            formulario.appendChild(comprobarBoton);
        } else if (tipoActividad === 'number') {
            console.log("entramos en numerar");

            var optionContainer = document.createElement('div');

            // Obtener las opciones de la actividad seleccionada
            var opcionesActividad = actividadSeleccionada.opciones;
            //imprimr los valors de opciones en formato json
            console.log("Valores de opciones: " + JSON.stringify(opcionesActividad, null, 2));

            for (var i = 0; i < opcionesActividad.length; i ++) {
                // Crear span para la parte izquierda
                var txtIzquierda = document.createElement('div');
                txtIzquierda.textContent = opcionesActividad[i];

                // Crear select para la parte derecha
                var selectDerecha = document.createElement('select');
                selectDerecha.className = "respuestaNumerar";
                // Añadir opciones al select (números)
                for (var j = 1; j <= opcionesActividad.length; j++) {  // Ajusta según la cantidad de opciones
                    var option = document.createElement('option');
                    option.value = j;
                    option.text = j;
                    selectDerecha.appendChild(option);
                }

                // Agregar elementos al contenedor
                optionContainer.appendChild(txtIzquierda);
                optionContainer.appendChild(selectDerecha);
            }

            formulario.appendChild(optionContainer);

            // Crear el botón "Send"
            var sendButton = document.createElement('button');
            sendButton.textContent = 'Check answer';
            sendButton.className = 'order-button';
            sendButton.type = 'button';

            sendButton.addEventListener('click', function () {
                // Obtener las respuestas del usuario
                var respuestasUsuario = obtenerRespuestasUsuarioNumber();
                console.log("En la funcion principal Respuestas del usuario: " + JSON.stringify(respuestasUsuario, null, 2));

                // Obtener las respuestas correctas
                var respuestasCorrectas = actividadSeleccionada.respuesta.split(',');
                console.log("Respuestas correctas: " + respuestasCorrectas);

                // Verificar si las respuestas del usuario coinciden con las respuestas correctas
                var respuestasCorrectasUsuario = comprobarRespuestasNumber(respuestasUsuario, respuestasCorrectas);

                // Mostrar retroalimentación
                mostrarRetroalimentacion(respuestasCorrectasUsuario, actividadSeleccionada, tipoActividad);
            });

            formulario.appendChild(sendButton);
        }

        // Agregar el formulario al contenedor
        formularioContainer.appendChild(formulario);

        // Mostrar la ventana modal
        var modal = document.getElementById('myModal');
        modal.style.display = 'block';


        
        //NUMERAR
        function obtenerRespuestasUsuarioNumber(){
             var respuestasUsuario = [];

            // Iterar sobre los elementos select
            document.querySelectorAll('select.respuestaNumerar').forEach(function (select) {
                // Obtener el texto asociado al select
                var frase = select.previousElementSibling.textContent.trim();

                // Obtener el valor seleccionado y agregarlo al array de respuestasUsuario
                respuestasUsuario.push({ frase: frase, respuesta: select.value });
            });

            return respuestasUsuario;
        }

        function comprobarRespuestasNumber(respuestasUsuario, respuestasCorrectas) {
            // Imprimir todos los valores dentro de respuestasUsuario
            console.log("func comprobarRespuestas del usuario: " + JSON.stringify(respuestasUsuario, null, 2));
            var resultados = false;

            var unidoNumerar = '';
            respuestasUsuario.forEach(function (respuestaUsuario) {
                console.log("El valor de respuestaUsuario es " + JSON.stringify(respuestaUsuario, null, 2));
                unidoNumerar = unidoNumerar + respuestaUsuario.frase + respuestaUsuario.respuesta;
                console.log("Y el de unidoNumerar es " + JSON.stringify(unidoNumerar, null, 2));
            });

            // Separar en elementos unidoNumerar cada que encuentre un número, pero el número también se debe incluir
            unidoNumerar = unidoNumerar.split(/(\d+)/).filter(Boolean);
            console.log("El valor de unidoNumerar es " + JSON.stringify(unidoNumerar, null, 2));

            // Convertir respuestasCorrectas a un objeto para facilitar la comparación
            var respuestasCorrectasObj = {};
            for (var i = 0; i < respuestasCorrectas.length; i += 2) {
                respuestasCorrectasObj[respuestasCorrectas[i]] = parseInt(respuestasCorrectas[i + 1], 10);
                //imprimir el valor
                console.log("El valor de respuestasCorrectasObj es " + JSON.stringify(respuestasCorrectasObj, null, 2));
            }

           // Verificar si todos los pares de valores coinciden
            resultados = respuestasUsuario.every(function (respuestaUsuario) {
                // Obtener la frase y respuesta del usuario
                var fraseUsuario = respuestaUsuario.frase;
                var respuestaUsuarioValor = parseInt(respuestaUsuario.respuesta, 10);

                // Obtener la respuesta correcta para la frase del usuario
                var respuestaCorrecta = respuestasCorrectasObj[fraseUsuario];

                // Comparar respuesta del usuario con la respuesta correcta
                return respuestaUsuarioValor === respuestaCorrecta;
            });

            return resultados;
                }



        //OPCION MULTIPLE
        // Función para obtener las respuestas seleccionadas por el usuario en una actividad de opciones múltiples
        function obtenerRespuestasUsuarioMultiple() {
            var opcionesSeleccionadas = document.querySelectorAll('input[name="pregunta"]:checked');
            var respuestasUsuario = [];

            opcionesSeleccionadas.forEach(function (opcionSeleccionada) {
                respuestasUsuario.push(opcionSeleccionada.value);
            });

            // Imprime en consola los valores de respuestasUsuario
            console.log("Respuestas del usuario: " + respuestasUsuario);
            return respuestasUsuario;
        }

        // Función para comprobar las respuestas en una actividad de opciones múltiples
        function comprobarRespuestasMultiple(respuestasUsuario, respuestasCorrectas) {
            var respuestasCorrectasArray = respuestasCorrectas.split(",").map(function(item) {
                return item.trim();
            });

            // Inicializar el objeto opcionesCorrectas
            var opcionesCorrectas = {};

            // Iterar sobre las respuestas del usuario y marcar como true o false según si está en las respuestas correctas
            respuestasUsuario.forEach(function(opcion) {
                opcionesCorrectas[opcion] = respuestasCorrectasArray.includes(opcion);
            });

            console.log("2 -Respuestas del usuario: " + respuestasUsuario);
            console.log("Respuestas correcras: " + respuestasCorrectas);
            return opcionesCorrectas;
        }







        //ORDENAR
        //Funcion para barajar las opciones que llegan en la pregunta de unir
        function opcionesBarajadas(datos) {
            var opciones = datos.split(',');
            var preguntas = [];
            var respuestas = [];

            // Separar preguntas y respuestas
            for (var i = 0; i < opciones.length; i++) {
                if (i % 2 === 0) {
                    preguntas.push(opciones[i]);
                } else {
                    respuestas.push(opciones[i]);
                }
            }

            // Barajar solo las preguntas
            preguntas.sort(function () {
                return 0.5 - Math.random();
            });

            // Reconstruir el arreglo barajado con preguntas y respuestas asociadas
            var opcionesBarajadas = [];
            for (var j = 0; j < preguntas.length; j++) {
                opcionesBarajadas.push(preguntas[j]);
                opcionesBarajadas.push(respuestas[j]);
            }

            return opcionesBarajadas;
        }





        //COMPLETAR
        // Función para obtener las respuestas del usuario
        function obtenerRespuestasUsuarioCompletar() {
            var respuestasUsuario = [];
            // Obtener respuestas de los inputs (ajusta según tu implementación)
            var inputs = document.querySelectorAll('input[name="respuesta_completar"]');
            inputs.forEach(function (input) {
                //imprimir con console loge cada uno de respuestasUsuario.push(input.value);
                console.log("Respuestas del usuario: " + input.value);
                respuestasUsuario.push(input.value);
            });
            return respuestasUsuario;
        }

        // Función para comparar las respuestas del usuario con las respuestas correctas
        function comprobarRespuestasCompletar(respuestasUsuario, respuestasCorrectas) {
            console.log("Respuestas correctas:", respuestasCorrectas);

            // Convertir la cadena de respuestasCorrectas en un array de palabras
            var respuestasCorrectasArray = respuestasCorrectas.split(",").map(function (word) {
                return word.trim().toLowerCase();
            });

            var resultado = respuestasUsuario.every(function (respuestaUsuario, index) {
                var coincidencia = respuestaUsuario.trim().toLowerCase() === respuestasCorrectasArray[index];
                console.log(`Respuesta Usuario: ${respuestaUsuario}, Respuesta Correcta: ${respuestasCorrectasArray[index]}, Coincidencia: ${coincidencia}`);
                return coincidencia;
            });

            console.log("Resultado final:", resultado);

            return resultado;
        }








        //UNIR
        // Función para eliminar la línea existente entre dos palabras 
        function removeExistingLine(palabraIzquierda, palabraDerecha) {
            var existingLines = findExistingLines(palabraIzquierda, palabraDerecha);
            existingLines.forEach(line => {
                line.parentNode.removeChild(line);
            });
        }

        // Función para encontrar las líneas existentes entre dos palabras
        function findExistingLines(palabraIzquierda, palabraDerecha) {
            console.log(`Valor en palabraIzquierda: ${palabraIzquierda.textContent}`);
            console.log(`Valor en palabraDerecha: ${palabraDerecha.textContent}`);

            var lines = svgContainer.getElementsByTagName('line');
            var existingLines = [];

            for (var i = 0; i < lines.length; i++) {
                var line = lines[i];
                console.log(`Línea existente - dataset.from: ${line.dataset.from}, dataset.to: ${line.dataset.to}`);

                // Verificar si la línea está conectada a las mismas palabras
                if (
                    (line.dataset.from === palabraIzquierda.textContent && line.dataset.to !== palabraDerecha.textContent) ||
                    (line.dataset.from === palabraDerecha.textContent && line.dataset.to !== palabraIzquierda.textContent) ||
                    (line.dataset.to === palabraIzquierda.textContent && line.dataset.from !== palabraDerecha.textContent) ||
                    (line.dataset.to === palabraDerecha.textContent && line.dataset.from !== palabraIzquierda.textContent)
                ) {
                    console.log('¡Encontró una línea existente entre las mismas palabras!');
                    existingLines.push(line);
                }
            }

            console.log(`Número de líneas existentes encontradas: ${existingLines.length}`);
            return existingLines;
        }

        //Funcion para obtener las uniones que hizo el usuario
        function obtenerRespuestasUsuarioUnir(){
            var lines = svgContainer.getElementsByTagName('line');
            var respuestasUsuario = [];

            for (var i = 0; i < lines.length; i++) {
                var line = lines[i];
                var palabraIzquierda = line.dataset.from;
                var palabraDerecha = line.dataset.to;
                respuestasUsuario.push(palabraIzquierda, palabraDerecha);
            }
            //Imprime en consola los valores de respuestasUsuarios
            console.log("Respuestas del usuario: " + respuestasUsuario);
            return respuestasUsuario;
        }

        // Función para comprobar las respuestas
        function comprobarRespuestasUnir(respuestasUsuario, respuestasCorrectas) {
            // Obtener todas las líneas existentes
            var lines = svgContainer.getElementsByTagName('line');
            
            // Almacena las conexiones establecidas por el usuario
            var conexionesUsuario = respuestasUsuario;

            //imprimir todas las conexionesUsuario almacenadas como array
            for(var i=0; i<conexionesUsuario.length; i++){
                console.log("Conexiones del usuario: " + conexionesUsuario[i]);
            }

            var respuestasCorrectasArray = respuestasCorrectas.split(",");

            //imprimir los valores de respuestasCorrectas con un ciclo for
            for(var i=0; i<respuestasCorrectasArray.length; i++){
                console.log("Respuestas correctas: " + respuestasCorrectasArray[i]);
            }

            // Verificar si las conexiones del usuario coinciden con las respuestas correctas
            var conexionesCorrectas = 0;
            var respuestaUnir = false;

            for (var i = 0; i < conexionesUsuario.length; i+=2) {
                var op1 = conexionesUsuario[i];
                var op2 = conexionesUsuario[i + 1];

                for (var j = 0; j < respuestasCorrectasArray.length; j = j + 2) {
                    var op3 = respuestasCorrectasArray[j];
                    var op4 = respuestasCorrectasArray[j + 1];

                    console.log("Valor de op1: " + op1);
                    console.log("Valor de op2: " + op2);
                    console.log("Valor de op3: " + op3);
                    console.log("Valor de op4: " + op4);

                    if ((op1 === op3 && op2 === op4) || (op1 === op4 && op2 === op3)) {
                        conexionesCorrectas = conexionesCorrectas+1;
                        // Remover los elementos encontrados de ambos arrays
                        respuestasCorrectasArray.splice(j, 2);
                        break;
                    }
                }

                // Si no se encontró la conexión, entonces no es correcta
                if (conexionesCorrectas === conexionesUsuario.length/2) {
                    respuestaUnir = true;
                    console.log("respuestaUnir = true");
                }
            }

            return respuestaUnir;
        }
       
        // Función para crear una línea SVG
        function createLine(y1, y2) {
            try {
                var line = document.createElementNS("http://www.w3.org/2000/svg", "line");
                line.setAttribute("x1", "0%");
                line.setAttribute("y1", y1 + "%");
                line.setAttribute("x2", "100%");
                line.setAttribute("y2", y2 + "%");
                line.setAttribute("stroke", "Green");
                line.setAttribute("stroke-width", "2"); // Grosor relativo
                console.log(`Valores que se grafican al final y1: ${y1}, y2: ${y2} `);
                line.dataset.from = palabraIzquierda.textContent; // Almacenar información de las palabras conectadas
                line.dataset.to = palabraDerecha.textContent;
                return line;
            } catch (error) {
                console.error("Error creating line:", error);
                return null;
            }
        }
    }

    


    // Función para mostrar la retroalimentación después de responder preguntas
    function mostrarRetroalimentacion(respuestasCorrectasUsuario, actividadSeleccionada, tipoActividad) {
        //Multiple choice
        conBMC = 0;
        conMMC = 0;

        console.log('Mostrando retroalimentación');
        console.log('respuestasCorrectasUsuario: ' + JSON.stringify(respuestasCorrectasUsuario, null, 2));
        console.log('actividadSeleccionada: ' + JSON.stringify(actividadSeleccionada, null, 2));
        console.log('tipoActividad: ' + tipoActividad);
        console.log('Valor de actividadSeleccionada.id_unidad: ' + actividadSeleccionada.id_unidad);

        // Cambiar el fondo solo de la opción seleccionada si es una pregunta de opciones
        if (tipoActividad === 'select') {
            conSelect++;
            console.log("1");
            var opcionSeleccionada = document.querySelector('input[name="pregunta"]:checked');
            if (opcionSeleccionada) {
                var esCorrecta = respuestasCorrectasUsuario;
                console.log("Valor de esCorrecta: " + esCorrecta);
                opcionSeleccionada.parentNode.style.backgroundColor = esCorrecta ? '#7CFF7C' : '#FF7C7C';
                
                if(esCorrecta){
                    console.log("Valor de conSelect: " + conSelect);
                    console.log("Valor de actividadSeleccionada.opciones: " + actividadSeleccionada.opciones);
                    console.log("Valor de actividadSeleccionada.opciones.length: " + actividadSeleccionada.opciones.length);

                    actualizarTablaRacha();
                    save_nota_actividad(tipoActividad, actividadSeleccionada.id_unidad,  actividadSeleccionada.id_actividad,conSelect, actividadSeleccionada.opciones.length);                    
                }
            }
        }

        if (tipoActividad === 'multiple choice') {
            console.log("2");
            console.log("Llega con estos valores:", respuestasCorrectasUsuario);
            var nResBMC = actividadSeleccionada.respuesta.split(",").length;
            console.log("El numero de respuestas correctas es: " + nResBMC);
            var opSelecionadas = respuestasCorrectasUsuario;
            // Cambiar el fondo de todas las opciones correctas y seleccionadas
            Object.keys(opSelecionadas).forEach(function (key) {
                console.log("Es: " + key);
                console.log("ademas: " + respuestasCorrectasUsuario[key]);
                if(respuestasCorrectasUsuario[key]){
                    conBMC++;
                    console.log("Valor en conBMC: " + conBMC);
                }else{
                    conMMC++;
                    console.log("Valor en conMMC: " + conMMC);
                }
                var elemento = document.querySelector('input[value="' + key + '"]');
                //Esta seleccionada
                //Verificar si esta opcion esta dentro de actividadSeleccionada.respuesta
                if (actividadSeleccionada.respuesta.includes((key))){
                    //Si esta dentro de la respuesta
                    if (elemento) {
                        // Seleccionada y es correcta: pintar de verde
                        elemento.parentNode.style.backgroundColor = '#7CFF7C';
                    }
                } else {
                    if (elemento) {
                        // Seleccionada pero no es correcta: pintar de rojo
                        elemento.parentNode.style.backgroundColor = '#FF7C7C';
                    }
                }
            });

            // Mostrar mensaje de respuesta correcta o incorrecta
            respuestasCorrectasUsuario = false;
            actualizarTablaRacha();
            save_nota_actividad(tipoActividad, actividadSeleccionada.id_unidad,  actividadSeleccionada.id_actividad, conBMC, conMMC, nResBMC, actividadSeleccionada.opciones.length);
        }

        // Mostrar mensaje específico para el caso de ordenar si la respuesta es correcta
        if (tipoActividad === 'order') {
            if (respuestasCorrectasUsuario) {
                alert('Correct answer in the sorting activity!');
                actualizarTablaRacha();
                save_nota_actividad(tipoActividad, actividadSeleccionada.id_unidad,  actividadSeleccionada.id_actividad, conMO, actividadSeleccionada.opciones.length);
            } else {
                conMO++;
                alert('Incorrect order. Please try again.   ' + conMO);
            }
        }

        // Mostrar mensaje de respuesta correcta o incorrecta para la actividad de completar
        if (tipoActividad === 'complete') {
            var mensaje = respuestasCorrectasUsuario ? 'Correct answers!' : 'Incorrect answers. Please try again.';
            if (!respuestasCorrectasUsuario) {
                conMCom++; // Aumentar en 1 si las respuestas son incorrectas
                console.log("Valor de conMCom: " + conMCom);
            }else{
                actualizarTablaRacha();
                save_nota_actividad(tipoActividad, actividadSeleccionada.id_unidad,  actividadSeleccionada.id_actividad, conMCom, actividadSeleccionada.opciones.length);                
            }
            alert(mensaje);
        }

        if (tipoActividad === 'match') {
            var mensajeUnir = respuestasCorrectasUsuario ? 'Connections are correct!' : 'Connections are incorrect. Please try again.';
            if (!respuestasCorrectasUsuario) {
                conMMat++; // Aumentar en 1 si las respuestas son incorrectas
                console.log("Valor de conMMat: " + conMMat);
            }else{
                actualizarTablaRacha();
                save_nota_actividad(tipoActividad, actividadSeleccionada.id_unidad,  actividadSeleccionada.id_actividad, conMMat, actividadSeleccionada.opciones.length);                
            }
            alert(mensajeUnir);
        }

        if (tipoActividad === 'number') {
            console.log("Entra en numerar con estos valores:");
            Object.keys(respuestasCorrectasUsuario).forEach(function (palabra) {
                console.log(palabra, respuestasCorrectasUsuario[palabra]);
            });
            var mensajeNumber = respuestasCorrectasUsuario ? 'Numbers are correct!' : 'Numbers are incorrect. Please try again.';
            if (!respuestasCorrectasUsuario) {
                conMNu++; // Aumentar en 1 si las respuestas son incorrectas
                console.log("Valor de conMNu: " + conMNu);
            }else{
                actualizarTablaRacha();
                save_nota_actividad(tipoActividad, actividadSeleccionada.id_unidad,  actividadSeleccionada.id_actividad, conMNu, actividadSeleccionada.opciones.length);
            }
            alert(mensajeNumber);
        }

        // Cerrar la ventana modal si la respuesta es correcta (para todas las preguntas)
        if (respuestasCorrectasUsuario) {
            setTimeout(function () {
                var modal = document.getElementById('myModal');
                modal.style.display = 'none';
            }, 2000);  // Ajusta el tiempo según tus preferencias
        }
    }


    // Obtener el elemento de cierre y la ventana modal
    var modal = document.getElementById('myModal');
    var closeButton = document.getElementsByClassName('close')[0];

    // Cerrar la ventana modal al hacer clic en el botón de cierre
    closeButton.onclick = function () {
        modal.style.display = 'none';
    };

    // Cerrar la ventana modal al hacer clic en el fondo oscuro del modal
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
</script>